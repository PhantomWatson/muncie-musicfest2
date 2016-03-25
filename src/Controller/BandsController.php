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
}
