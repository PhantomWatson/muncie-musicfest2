<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Bands Controller
 *
 * @property \App\Model\Table\BandsTable $Bands
 */
class BandsController extends AppController
{
    public function apply()
    {
        $band = $this->Bands->newEntity();
        if ($this->request->is('post')) {

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
}
