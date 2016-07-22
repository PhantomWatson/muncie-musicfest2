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
                    ->order(['Bands.name' => 'ASC'])
                    ->toArray(),
                'Bands Not Done Applying' => $this->Bands
                    ->find('list')
                    ->find('applicationIncomplete')
                    ->order(['Bands.name' => 'ASC'])
                    ->toArray()
            ],
            'pageTitle' => 'Bands'
        ]);
    }

    public function view($id = null)
    {
        $band = $this->Bands->get($id, [
            'contain' => ['Songs', 'Pictures']
        ]);
        $playlist = [];
        foreach ($band->songs as $song) {
            $playlist[] = [
                'title' => $song->title,
                'artist' => $band->name,
                'mp3' => '/music/' . $song->filename
            ];
        }
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
            'pageTitle' => $band->name,
            'playlist' => $playlist
        ]);
    }
}
