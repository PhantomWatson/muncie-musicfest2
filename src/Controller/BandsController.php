<?php
namespace App\Controller;

use App\Controller\AppController;
use App\Media\Media;
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
            // Add band_id to song data so SongsTable::isUniqueTitle works
            $songs = $this->request->data('songs');
            if (is_array($songs)) {
                foreach ($songs as $i => $song) {
                    $this->request->data['songs'][$i]['band_id'] = $band->id;
                }
            }

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
            if ($this->warnIfRedundant($band)) {
                // Message already sent to view
            } elseif ($band->errors()) {
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
                $band = $this->Bands->save($band);

                $this->loadModel('Pictures');
                if ($this->request->data('primaryPictureId')) {
                    $pictureId = $this->request->data('primaryPictureId');
                    if (! $this->Pictures->makePrimary($pictureId, $band->id)) {
                        $this->Flash->error('There was an error making picture #'.$pictureId.' primary');
                    }
                    $band->pictures = $this->Pictures->getForBand($band->id)->toArray();
                }

                $picturesToDelete = $this->request->data('deletePictures');
                if (! empty($picturesToDelete)) {
                    foreach ($picturesToDelete as $pictureId) {
                        if (! $this->Pictures->deletePicture($pictureId, $band->id)) {
                            $this->Flash->error('There was an error deleting picture #'.$pictureId);
                        }
                    }
                    $band->pictures = $this->Pictures->getForBand($band->id)->toArray();
                }

                $this->loadModel('Songs');
                $songsToDelete = $this->request->data('deleteSongs');
                if (! empty($songsToDelete)) {
                    foreach ($songsToDelete as $songId) {
                        if (! $this->Songs->deleteSong($songId, $band->id)) {
                            $this->Flash->error('There was an error deleting song #'.$songId);
                        }
                    }
                    $band->songs = $this->Songs->getForBand($band->id)->toArray();
                }

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
        $Media = new Media();
        $result = $Media->uploadSong();
        if (! $result['success']) {
            $this->response->statusCode(403);
        }
        $this->set('result', $result);
        $this->viewBuilder()->layout('json');
        return $this->render('upload');
    }

    public function uploadPicture()
    {
        $Media = new Media();
        $result = $Media->uploadPicture();
        if (! $result['success']) {
            $this->response->statusCode(403);
        }
        $this->set('result', $result);
        $this->viewBuilder()->layout('json');
        return $this->render('upload');
    }

    /**
     * Display a flash message if a new application is being submitted
     * for a band that's already in the database
     *
     * @param Band $band
     * @return boolean TRUE if application is redundant
     */
    private function warnIfRedundant($band)
    {
        $bandName = $this->request->data('name');
        if (! (! $band->id && $this->Bands->nameExists($bandName))) {
            return false;
        }
        $adminEmail = Configure::read('adminEmail');
        $adminEmail = '<a href="mailto:'.$adminEmail.'">'.$adminEmail.'</a>';
        $msg = 'Hold up, it looks like someone already submitted an application for '.$bandName.'.';

        $existingBand = $this->Bands->find('all')
            ->select(['user_id'])
            ->where(['name' => $bandName])
            ->first();
        $otherUserId = $existingBand->user_id;
        $this->loadModel('Users');
        try {
            $otherUser = $this->Users->get($otherUserId);
            $applicantEmail = $otherUser->email;
            $applicantEmail = '<a href="mailto:'.$applicantEmail.'">'.$applicantEmail.'</a>';
            $msg .= ' If you\'d like to contact them, their email address is '.$applicantEmail.'.';
            $msg .= ' If you need help from an admin, hit up '.$adminEmail.'.';
        } catch (\Cake\Datasource\Exception\RecordNotFoundException $e) {
            $msg .= ' Furthermore, it looks like their user account has been removed from the database.';
            $msg .= ' Please hit up an admin at '.$adminEmail.' and let them know that this band needs to be transferred to your account.';
        }
        $this->Flash->error($msg);
        return true;
    }
}
