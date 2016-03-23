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
    }

    public function apply()
    {
        $band = $this->Bands->newEntity();
        if ($this->request->is('post')) {
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

    public function loginFirst()
    {
        $this->set('pageTitle', 'Log in first');
    }
}
