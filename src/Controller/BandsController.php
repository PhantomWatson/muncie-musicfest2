<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Routing\Router;

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
        $userId = $this->Auth->user('id');
        $band = $this->Bands->getForUser($userId);

        if (empty($band)) {
            $band = $this->Bands->newEntity();
        }
        if ($this->request->is(['post', 'put'])) {
            $band = $this->Bands->patchEntity($band, $this->request->data());
            $band->user_id = $this->Auth->user('id');
            $errors = $band->errors();
            if (empty($errors)) {
                $this->Bands->save($band);
                $this->set(['pageTitle' => 'You\'re Applied!']);
                return $this->render('applied_thanks');
            } else {
                $this->Flash->error('Whoops, looks like there\'s an error you\'ll have to correct before your proceed.');
            }
        } else {
            $band->tier = 'new';
            $band->member_count = 1;
            $band->minimum_fee = 0;
            $band->members_under_21 = 1;
            $band->email = $this->Auth->user('email');
        }
        $this->set([
            'band' => $band,
            'pageTitle' => 'Apply to Perform'
        ]);
    }

    public function index()
    {
        $this->set('pageTitle', 'Muncie MusicFest 2016 Bands');
    }
}
