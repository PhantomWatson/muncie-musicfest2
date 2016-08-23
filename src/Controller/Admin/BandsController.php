<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Network\Exception\InternalErrorException;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;

/**
 * Bands Controller
 *
 * @property \App\Model\Table\BandsTable $Bands
 */
class BandsController extends AppController
{

    /**
     * Initialize method
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();
        $this->Auth->deny();
    }

    /**
     * Method for /admin/bands
     *
     * @return void
     */
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

    /**
     * Method for /admin/bands/view/:id
     *
     * @param int|null $id
     * @return void
     */
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
        $back = $this->request->query('back');
        if (!$back) {
            $back = [
                'prefix' => 'admin',
                'controller' => 'Bands',
                'action' => 'index'
            ];
        }
        $this->set([
            'back' => $back,
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

    /**
     * Method for /admin/bands/emails, which shows lists of all of the band email addresses in various categories
     *
     * @return void
     */
    public function emails()
    {
        $lists = [];

        $lists['All bands'] = $this->Bands->find('list', [
            'keyField' => 'id',
            'valueField' => 'email'
        ])
            ->order(['email' => 'ASC'])
            ->toArray();

        $lists['Bands with complete applications'] = $this->Bands->find('list', [
            'keyField' => 'id',
            'valueField' => 'email'
        ])
            ->where(['application_step' => 'done'])
            ->order(['email' => 'ASC'])
            ->toArray();

        $lists['Bands with incomplete applications'] = $this->Bands->find('list', [
            'keyField' => 'id',
            'valueField' => 'email'
        ])
            ->where(['application_step <>' => 'done'])
            ->order(['email' => 'ASC'])
            ->toArray();

        $lists['Booked and confirmed bands'] = $this->Bands->find('list', [
            'keyField' => 'id',
            'valueField' => 'email'
        ])
            ->where(['confirmed' => 'confirmed'])
            ->matching('Slots')
            ->order(['email' => 'ASC'])
            ->toArray();

        $slotsTable = TableRegistry::get('Slots');
        $slots = $slotsTable->find('all')
            ->select(['band_id'])
            ->where(function ($exp, $q) {
                return $exp->isNotNull('band_id');
            })
            ->toArray();
        $bandsWithSlots = Hash::extract($slots, '{n}.band_id');

        $lists['Bands neither booked nor dropped out'] = $this->Bands->find('list', [
            'keyField' => 'id',
            'valueField' => 'email'
        ])
            ->where([
                'OR' => [
                    function ($exp, $q) {
                        return $exp->isNull('confirmed');
                    },
                    'confirmed <>' => 'dropped out'
                ],
                function ($exp, $q) use ($bandsWithSlots) {
                    return $exp->notIn('id', $bandsWithSlots);
                }
            ])
            ->order(['email' => 'ASC'])
            ->toArray();

        $this->set([
            'pageTitle' => 'Band email lists',
            'lists' => $lists
        ]);
    }

    public function basicInfo()
    {
        $bands = $this->Bands->find('all')
            ->select(['id', 'name', 'genre', 'hometown', 'minimum_fee', 'application_step'])
            ->order(['name' => 'ASC']);
        $this->set([
            'pageTitle' => 'Bands - Basic Info',
            'bands' => $bands
        ]);
    }

    public function booking()
    {
        $slotsTable = TableRegistry::get('Slots');
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
                                    'minimum_fee',
                                    'rep_name',
                                    'email',
                                    'phone',
                                    'admin_notes',
                                    'confirmed'
                                ]);
                            }
                        ])
                        ->order(['Slots.time' => 'ASC']);
                }
            ])
            ->order(['name' => 'ASC'])
            ->toArray();

        foreach ($stages as &$stage) {
            $stage->slots = $slotsTable->sortSlots($stage->slots);
        }

        $this->set([
            'pageTitle' => 'Booking Overview',
            'stages' => $stages
        ]);
    }

    /**
     * Method for /admin/bands/edit-confirmation
     *
     * @param int $bandId Band ID
     * @param string $confirmed Confirmation state
     * @return void
     * @throws InternalErrorException
     */
    public function editConfirmation($bandId, $confirmed)
    {
        $band = $this->Bands->get($bandId);
        if ($confirmed == '0') {
            $confirmed = null;
        }
        $this->Bands->patchEntity($band, ['confirmed' => $confirmed]);
        if (! $this->Bands->save($band)) {
            throw new InternalErrorException('There was an error updating that band\'s confirmation state.');
        }
    }

    /**
     * Method for /admin/bands/edit-notes
     *
     * @param int $bandId Band ID
     * @return void
     * @throws InternalErrorException
     */
    public function editNotes($bandId)
    {
        $band = $this->Bands->get($bandId);
        $this->Bands->patchEntity($band, ['admin_notes' => $this->request->data('admin_notes')]);
        if (! $this->Bands->save($band)) {
            throw new InternalErrorException('There was an error updating that band\'s notes.');
        }
    }
}
