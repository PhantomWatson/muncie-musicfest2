<?php
namespace App\Controller\Admin;

use App\Controller\AppController;

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
        $this->Auth->deny();
    }

    public function index()
    {
        $this->set([
            'bands' => [
                'Bands Applied' => $this->Bands
                    ->find('list')
                    ->find('applied')
                    ->order(['Bands.name' => 'ASC']),
                'Bands Not Done Applying' => $this->Bands
                    ->find('list')
                    ->find('applicationIncomplete')
                    ->order(['Bands.name' => 'ASC'])
            ],
            'pageTitle' => 'Bands'
        ]);
    }

    public function view($id = null)
    {
        $band = $this->Bands->get($id);
        $bands = $this->Bands->find('list')->order(['Bands.name' => 'ASC']);
        $this->set([
            'band' => $band,
            'bands' => $bands,
            'fields' => [
                'tier',
                'genre',
                'hometown',
                'minimum_fee',
                'check_name',
                'website',
                'social_networking',
                'rep_name',
                'email',
                'phone',
                'address',
                'member_count',
                'member_names',
                'description',
                'message',
                'stage_setup'
            ],
            'pageTitle' => $band->name
        ]);
    }
}
