<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Filesystem\File;
use Cake\Network\Exception\InternalErrorException;
use Cake\Routing\Router;
use Cake\Utility\Inflector;

/**
 * Bands Controller
 *
 * @property \App\Model\Table\BandsTable $Bands
 */
class BandsController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        $this->Auth->allow(['index']);
    }

    public function apply()
    {
        $steps = $this->Bands->getApplicationSteps();

        // Get existing record or prepare to make a new one
        $userId = $this->Auth->user('id');
        $band = $this->Bands->getForUser($userId);
        if (empty($band)) {
            $band = $this->Bands->newEntity();
            $band->application_step = $steps[0];
            $band->tier = 'new';
            $band->member_count = 1;
            $band->minimum_fee = 0;
            $band->members_under_21 = 1;
            $band->email = $this->Auth->user('email');
        }

        // Prepare for advancing step
        if ($band->application_step == 'done') {
            $nextStep = 'done';
        } else {
            $stepKey = array_search($band->application_step, $steps);
            if ($stepKey === false) {
                throw new InternalErrorException('Application is currently on unrecognized step "'.$band->application_step.'"');
            }
            $nextStep = ($stepKey == count($steps) - 1) ? 'done' : $steps[$stepKey + 1];
        }

        if ($this->request->is(['post', 'put'])) {

            // Only use validation rules pertaining to the fields in this step
            $validationSet = ($band->application_step == 'done')
                ? 'default'
                : 'application'.ucfirst($band->application_step);
            $band = $this->Bands->patchEntity(
                $band,
                $this->request->data(),
                ['validate' => $validationSet]
            );

            $band->user_id = $this->Auth->user('id');
            if ($band->errors()) {
                $this->Flash->error('Whoops, looks like there\'s an error you\'ll have to correct before your proceed.');
            } else {
                if ($nextStep == 'done') {
                    if ($band->application_step == 'done') {
                        $msg = 'Information updated';
                    } else {
                        $msg = 'Thanks for applying to Muncie MusicFest! ';
                        $msg .= 'We\'ll be in touch later this August to let you know if you\'re booked. ';
                        $msg .= 'At any time, you can log in, review, and update your information.';
                    }
                } else {
                    $msg = 'Information saved';
                }
                $band->application_step = $nextStep;
                $this->Bands->save($band);
                if ($msg) {
                    $this->Flash->success($msg);
                }
            }
        }

        if ($band->application_step == 'done') {
            $title = 'Review / Update Your Band Information';
        } else {
            $stepKey = array_search($band->application_step, $steps);
            $title = 'Apply to Perform (Step '.($stepKey + 1).' of '.count($steps).')';
        }
        $this->set([
            'band' => $band,
            'pageTitle' => $title,
            'steps' => $steps
        ]);
    }

    public function index()
    {
        $this->set('pageTitle', 'Muncie MusicFest 2016 Bands');
    }

    public function uploadSong()
    {
        $uploadDir = ROOT.DS.APP_DIR.DS.'webroot'.DS.'music'.DS;
        $fileTypes = ['mp3'];
        $fileParts = pathinfo($_FILES['Filedata']['name']);
        $newFilename = $this->createSongFilename($_POST['bandId'], $fileParts);
        $result = $this->upload($uploadDir, $fileTypes, $newFilename);
        $this->set('result', $result);
        if ($result['success']) {
            // Determine length (in seconds) of song
            $getID3 = new \getID3;
            $trackInfo = $getID3->analyze($uploadDir.$result['filename']);
            $seconds = round($trackInfo['playtime_seconds']);
            echo '[['.$seconds.']]';

            $this->loadModel('Songs');
            $song = $this->Songs->newEntity([
                'band_id' => $_POST['bandId'],
                'title' => $result['trackName'],
                'filename' => $result['filename'],
                'seconds' => $seconds
            ]);
            if ($song->errors()) {
                $result['success'] = false;
                $result['message'] = 'Error saving song to database: '.json_encode($song->errors());
                $this->set('result', $result);
                $this->response->statusCode(403);
            } else {
                $this->Songs->save($song);
            }
        } else {
            $this->response->statusCode(403);
        }
        return $this->render('upload');
    }

    /**
     * Handles an upload of a band-associated media file, makes
     * sure the filename is prefixed with the bandname, prevents
     * overwriting, and enforces allowed file types.
     *
     * @param string $uploadDir
     * @param array $fileTypes
     * @return array
     */
    private function upload($uploadDir, $fileTypes, $newFilename)
    {
        $this->viewBuilder()->layout('json');
        $verifyToken = md5(Configure::read('uploadToken').$_POST['timestamp']);

        if (empty($_FILES) || $_POST['token'] != $verifyToken) {
            return [
                'message' => 'Security code incorrect',
                'success' => false
            ];
        }

        // Validate extension
        $fileParts = pathinfo($_FILES['Filedata']['name']);
        $extension = strtolower($fileParts['extension']);
        if (! in_array($extension, $fileTypes)) {
            return [
                'message' => 'Invalid file type ('.$extension.')',
                'success' => false
            ];
        }

        // Abort if file exists
        $targetFile = $uploadDir.$newFilename;
        $existingFile = new File($targetFile);
        if ($existingFile->exists()) {
            return [
                'message' => 'File has already been uploaded',
                'success' => false
            ];
        }

        // Move file
        $tempFile = $_FILES['Filedata']['tmp_name'];
        if (move_uploaded_file($tempFile, $targetFile)) {
            return [
                'message' => 'Upload successful',
                'success' => true,
                'trackName' => $trackName,
                'filename' => $newFilename
            ];
        }
    }

    /**
     * Pulled from http://stackoverflow.com/a/2021729
     *
     * @param string $filename
     * @return string
     */
    private function sanitizeFilename($filename)
    {
        // Remove anything which isn't a word, whitespace, number
        // or any of the following caracters -_~,;![]().
        // If you don't need to handle multi-byte characters
        // you can use preg_replace rather than mb_ereg_replace
        // Thanks @Łukasz Rysiak!
        $filename = mb_ereg_replace("([^\w\s\d\-_~,;!\[\]\(\).])", '', $filename);
        // Remove any runs of periods (thanks falstro!)
        $filename = mb_ereg_replace("([\.]{2,})", '', $filename);

        return $filename;
    }

    /**
     * Creates a full filename, enforcing various rules
     *
     * @param int $bandId
     * @param array $fileParts results of pathinfo()
     * @return string
     */
    private function createSongFilename($bandId, $fileParts)
    {
        // Get band name
        $band = $this->Bands->get($bandId);
        $bandName = trim($band->name);
        if ($bandName == '') {
            $bandName = 'Unknown Band '.date('Ydm');
        }

        // Make sure filename is prefixed with band name and sanitized
        $originalFilename = $fileParts['filename'];
        $bandPrefix = "$bandName - ";
        $strpos = stripos($originalFilename, $bandPrefix);
        if ($strpos === 0) {
            $trackName = substr($originalFilename, strlen($bandPrefix));
        } else {
            $trackName = $originalFilename;
        }
        $newFilename = $bandPrefix.trim($trackName);
        $extension = strtolower($fileParts['extension']);
        $newFilename .= '.'.$extension;
        $newFilename = $this->sanitizeFilename($newFilename);

        return $newFilename;
    }
}
