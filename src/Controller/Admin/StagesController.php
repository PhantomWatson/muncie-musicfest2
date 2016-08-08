<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;

/**
 * Stages Controller
 *
 * @property \App\Model\Table\StagesTable $Stages
 */
class StagesController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index()
    {
        $stages = $this->Stages->find('all')
            ->contain([
                'Slots' => function ($q) {
                    return $q
                        ->order(['time' => 'ASC'])
                        ->contain(['Bands']);
                }
            ])
            ->order(['name' => 'ASC'])
            ->toArray();

        $slotsTable = TableRegistry::get('Slots');
        foreach ($stages as &$stage) {
            $stage->slots = $slotsTable->sortSlots($stage->slots);
        }

        $this->set([
            'pageTitle' => 'Stages',
            'stages' => $stages
        ]);
        $this->set('_serialize', ['stages']);
    }

    /**
     * Add method
     *
     * @return \Cake\Network\Response|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $stage = $this->Stages->newEntity();
        if ($this->request->is('post')) {
            $stage = $this->Stages->patchEntity($stage, $this->request->data);
            if ($this->Stages->save($stage)) {
                $this->Flash->success(__('The stage has been saved.'));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The stage could not be saved. Please, try again.'));
            }
        }
        $this->set([
            'pageTitle' => 'Add a New Stage',
            'stage' => $stage
        ]);
        $this->set('_serialize', ['stage']);
        $this->render('form');
    }

    /**
     * Edit method
     *
     * @param string|null $id Stage id.
     * @return \Cake\Network\Response|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $stage = $this->Stages->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $stage = $this->Stages->patchEntity($stage, $this->request->data);
            if ($this->Stages->save($stage)) {
                $this->Flash->success(__('The stage has been saved.'));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The stage could not be saved. Please, try again.'));
            }
        }
        $this->set([
            'pageTitle' => 'Edit Stage',
            'stage' => $stage
        ]);
        $this->set('_serialize', ['stage']);
        $this->render('form');
    }

    /**
     * Delete method
     *
     * @param string|null $id Stage id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $stage = $this->Stages->get($id);
        if ($this->Stages->delete($stage)) {
            $this->Flash->success(__('The stage has been deleted.'));
        } else {
            $this->Flash->error(__('The stage could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Method for /admin/stages/slots/:stageId
     *
     * @param int $stageId
     */
    public function slots($stageId)
    {
        $stage = $this->Stages->find('all')
            ->where(['id' => $stageId])
            ->contain([
                'Slots' => function ($q) {
                    return $q
                        ->order(['time' => 'ASC'])
                        ->contain(['Bands']);
                }
            ])
            ->first();

        if ($this->request->is(['patch', 'post', 'put'])) {
            $stage = $this->Stages->patchEntity($stage, $this->request->data);
            $slotsTable = TableRegistry::get('Slots');
            if ($this->request->data('addSlot')) {
                $newSlot = $slotsTable->newEntity([
                    'stage_id' => $stageId,
                    'band_id' => null,
                    'time' => $this->request->data('newSlot')
                ]);
                $slotsTable->save($newSlot);
                $stage->slots[] = $newSlot;
            }
            if ($this->request->data('deleteSlots')) {
                $slotsTable->deleteAll([
                    'id IN' => $this->request->data('deleteSlots')
                ]);
                foreach ($this->request->data('deleteSlots') as $slotId) {
                    foreach ($stage->slots as $i => $slot) {
                        if ($slot->id == $slotId) {
                            unset($stage->slots[$i]);
                        }
                    }
                }
            }

            if ($this->Stages->save($stage)) {
                $this->Flash->success('Stage slots updated');
                $this->redirect([]);
            } else {
                $this->Flash->error('There was an error updating those stage slots');
            }
        }

        $slotsTable = TableRegistry::get('Slots');
        $stage->slots = $slotsTable->sortSlots($stage->slots);
        $bandsTable = TableRegistry::get('Bands');
        $bands = $bandsTable->find('all')
            ->select(['id', 'name', 'minimum_fee', 'application_step'])
            ->contain(['Slots'])
            ->order(['name' => 'ASC']);

        $bandsForJs = [];
        foreach ($bands as $band) {
            $bandsForJs[$band->id] = [
                'name' => $band->name,
                'booked' => ! empty($band->slots),
                'application_done' => $band->application_step == 'done'
            ];
        }

        $this->set([
            'pageTitle' => $stage->name . ' - Slots',
            'stage' => $stage,
            'bands' => $bands,
            'bandsForJs' => $bandsForJs
        ]);
    }
}
