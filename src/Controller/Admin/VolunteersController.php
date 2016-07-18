<?php
namespace App\Controller\Admin;

use App\Controller\AppController;

/**
 * Volunteers Controller
 *
 * @property \App\Model\Table\VolunteersTable $Volunteers
 */
class VolunteersController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index()
    {
        $volunteers = $this->Volunteers->find('all')
            ->contain(['Jobs'])
            ->order(['name' => 'ASC']);

        $this->set([
            '_serialize', ['volunteers'],
            'pageTitle' => 'Volunteers',
            'volunteers' => $volunteers
        ]);
    }

    /**
     * Delete method
     *
     * @param string|null $id Volunteer id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $volunteer = $this->Volunteers->get($id);
        if ($this->Volunteers->delete($volunteer)) {
            $this->Flash->success(__('The volunteer has been deleted.'));
        } else {
            $this->Flash->error(__('The volunteer could not be deleted. Please, try again.'));
        }
        return $this->redirect(['action' => 'index']);
    }
}
