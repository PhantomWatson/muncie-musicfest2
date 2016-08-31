<?php
namespace App\Controller;

use App\Controller\AppController;
use App\Media\Media;
use Cake\Core\Configure;
use Cake\Filesystem\File;
use Cake\Network\Exception\ForbiddenException;
use Cake\Network\Exception\InternalErrorException;
use Cake\Network\Exception\NotFoundException;
use Cake\ORM\TableRegistry;
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
        $this->Auth->allow(['index', 'view', 'schedule']);
    }

    /**
     * Method for /bands/apply
     *
     * @return \Cake\Network\Response|null
     */
    public function apply()
    {
        if (! Configure::read('bandApplicationsOpen')) {
            if ($this->Auth->user('role') == 'admin') {
                $msg = 'This page isn\'t accessible to the general public right now,';
                $msg .= ' but you\'re an admin so it\'s cool.';
                $this->Flash->set($msg);
            } else {
                $this->Flash->error('Sorry, the band application period for this year\'s festival has ended. :(');
                return $this->redirect([
                    'prefix' => false,
                    'controller' => 'Pages',
                    'action' => 'home'
                ]);
            }
        }
        $steps = $this->Bands->getApplicationSteps();
        $band = $this->getBandForApplication();

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

            if ($this->warnIfRedundant($band)) {
                // Message already sent to view
            } elseif ($band->errors()) {
                $this->Flash->error('Whoops, looks like there\'s an error you\'ll have to correct before your proceed.');
            } else {
                $displayThanks = $band->application_step != 'done' && $nextStep == 'done';
                $band->application_step = $nextStep;
                $band = $this->processMedia($band);
                $band = $this->Bands->save($band);
                if ($nextStep == 'done') {
                    if ($displayThanks) {
                        $msg = 'Thanks for applying to Muncie MusicFest! ';
                        $msg .= 'We\'ll be in touch later this August to let you know if you\'re booked. ';
                        $msg .= 'At any time, you can log in, review, and update your information.';
                    } else {
                        $msg = 'Application updated';
                    }
                } else {
                    $msg = 'Information saved';
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
        $Media = new Media();
        $uploadMax = ini_get('upload_max_filesize');
        $postMax = ini_get('post_max_size');
        $this->set([
            'band' => $band,
            'fileSizeLimit' => min($uploadMax, $postMax),
            'pageTitle' => $title,
            'picturesLimit' => $Media->getPicturesLimit(),
            'songsLimit' => $Media->getSongsLimit(),
            'steps' => $steps
        ]);
    }

    public function index()
    {
        $bands = $this->Bands->find('all')
            ->select(['id', 'name', 'genre', 'slug'])
            ->where(['confirmed' => 'confirmed'])
            ->matching('Slots')
            ->contain(['Pictures' => function ($q) {
                return $q
                    ->order(['is_primary' => 'DESC']);
            }])
            ->order(['name' => 'ASC'])
            ->all();
        $bands = $this->Bands->sortIgnoringThe($bands);
        $this->set([
            'pageTitle' => 'Muncie MusicFest 2016 Bands',
            'bands' => $bands
        ]);
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

    /**
     * Handles updating and deleting of songs and pictures
     *
     * @param Band $band
     * @return Band
     */
    private function processMedia($band)
    {
        $this->loadModel('Pictures');
        if ($this->request->data('primaryPictureId')) {
            $pictureId = $this->request->data('primaryPictureId');
            if (! $this->Pictures->makePrimary($pictureId, $band->id)) {
                $this->Flash->error('There was an error making picture #'.$pictureId.' primary');
            }
            $band->pictures = $this->Pictures->getForBand($band->id)->toArray();
        }

        return $band;
    }

    /**
     * Return an existing band or an entity ready to be added as
     * a new band to the database
     *
     * @return Band
     */
    private function getBandForApplication()
    {
        // Return existing band
        $userId = $this->Auth->user('id');
        $band = $this->Bands->getForUser($userId);
        if (! empty($band)) {
            return $band;
        }

        // Return new band
        $band = $this->Bands->newEntity();
        $steps = $this->Bands->getApplicationSteps();
        $band->application_step = $steps[0];
        $band->tier = 'new';
        $band->member_count = 1;
        $band->minimum_fee = 0;
        $band->members_under_21 = 1;
        $band->email = $this->Auth->user('email');
        $band->user_id = $userId;
        return $band;
    }

    public function deletePicture()
    {
        $this->viewBuilder()->layout('json');
        $pictureId = $this->request->data('pictureId');
        $this->loadModel('Pictures');
        $picture = $this->Pictures->get($pictureId);
        $band = $this->Bands->get($picture->band_id);
        $ownerId = $band->user_id;
        if ($this->Auth->user('id') != $ownerId) {
            throw new ForbiddenException('Sorry, you\'re not authorized to delete that picture');
        }
        $result = $this->Pictures->deletePicture($pictureId, $picture->band_id);
        if (! $result) {
            throw new InternalErrorException('Sorry, there was an error deleting that image');
        }
    }

    public function deleteSong()
    {
        $this->viewBuilder()->layout('json');
        $songId = $this->request->data('songId');
        $this->loadModel('Songs');
        $song = $this->Songs->get($songId);
        $band = $this->Bands->get($song->band_id);
        $ownerId = $band->user_id;
        if ($this->Auth->user('id') != $ownerId) {
            throw new ForbiddenException('Sorry, you\'re not authorized to delete that song');
        }
        $result = $this->Songs->deleteSong($songId, $song->band_id);
        if (! $result) {
            throw new InternalErrorException('Sorry, there was an error deleting that song');
        }
    }

    /**
     * Method for /band/:slug
     *
     * Meant for the general public, so this will only allow bands that are booked and confirmed to be viewed.
     *
     * @param $slug
     */
    public function view($slug = null)
    {
        $band = $this->Bands->find('all')
            ->where(['slug' => $slug])
            ->contain([
                'Slots.Stages',
                'Pictures' => function ($q) {
                    return $q->order(['is_primary' => 'DESC']);
                },
                'Songs'
            ])
            ->first();

        if (! $band) {
            throw new NotFoundException('Sorry, we couldn\'t find the band you were looking for');
        }

        if (empty($band['slots']) || $band['confirmed'] != 'confirmed') {
            throw new ForbiddenException('Sorry, that band is not currently on the Muncie MusicFest lineup');
        }

        $back = $this->request->query('back');
        $backLabel = ($back == 'schedule') ? 'Back to Schedule' : 'Back to Lineup';
        $backUrl = ($back == 'schedule') ? ['action' => 'schedule'] : ['action' => 'index'];

        $this->set([
            'pageTitle' => $band['name'],
            'band' => $band,
            'backLabel' => $backLabel,
            'backUrl' => $backUrl
        ]);
    }

    public function schedule()
    {
        $stagesTable = TableRegistry::get('Stages');
        $stages = $stagesTable
            ->find('all')
            ->contain([
                'Slots' => function ($q) {
                    return $q
                        ->contain([
                            'Bands' => function ($q) {
                                return $q->select([
                                    'id',
                                    'name',
                                    'genre',
                                    'hometown',
                                    'slug'
                                ])
                                    ->where(['confirmed' => 'confirmed'])
                                    ->contain([
                                        'Pictures' => function ($q) {
                                            return $q->order(['is_primary' => 'DESC']);
                                        }
                                    ]);
                            }
                        ])
                        ->order(['Slots.time' => 'ASC']);
                }
            ])
            ->order(['name' => 'ASC'])
            ->toArray();

        $slotsTable = TableRegistry::get('Slots');
        foreach ($stages as $i => &$stage) {
            if (empty($stage['slots'])) {
                unset($stages[$i]);
                continue;
            }

            $hasBookedBands = false;
            foreach ($stage['slots'] as $slot) {
                if ($slot['band']) {
                    $hasBookedBands = true;
                }
            }
            if (! $hasBookedBands) {
                unset($stages[$i]);
                continue;
            }

            $stage->slots = $slotsTable->sortSlots($stage->slots);
        }

        $this->set([
            'pageTitle' => 'Muncie MusicFest 2016 Schedule',
            'stages' => $stages
        ]);
    }
}
