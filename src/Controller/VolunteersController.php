<?php
namespace App\Controller;

use App\Controller\AppController;
use App\Mailer\Mailer;
use Cake\Network\Exception\ForbiddenException;

/**
 * Volunteers Controller
 *
 * @property \App\Model\Table\VolunteersTable $Volunteers
 */
class VolunteersController extends AppController
{
    /**
     * Add method
     *
     * @return \Cake\Network\Response|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $volunteer = $this->Volunteers->newEntity();
        if ($this->request->is('post')) {
            $volunteer = $this->Volunteers->patchEntity($volunteer, $this->request->data);
            $saved = $this->Volunteers->save($volunteer);
            if ($saved) {
                $this->Flash->success(__('Signup complete. Thanks! We\'ll be in touch before the festival.'));
                Mailer::sendVolunteerSignupEmail($saved->id);
                return $this->redirect([
                    'controller' => 'Pages',
                    'action' => 'home'
                ]);
            } else {
                $this->Flash->error(__('There was an error submitting your signup information. Please try again.'));
            }
        }
        $jobs = $this->Volunteers->Jobs->find('list', ['limit' => 200])->toArray();
        sort($jobs);
        $this->set(compact('volunteer', 'jobs'));
        $this->set([
            '_serialize' => ['volunteer'],
            'pageTitle' => 'Volunteer Signup'
        ]);
    }

    /**
     * Edit method
     *
     * @param string|null $id Volunteer id.
     * @param string|null $key Security key.
     * @return \Cake\Network\Response|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null, $key = null)
    {
        $volunteer = $this->Volunteers->get($id, [
            'contain' => ['Jobs']
        ]);
        $correctKey = $this->Volunteers->getSecurityHash($volunteer->email);
        if ($key != $correctKey) {
            throw new ForbiddenException('Security key mismatch. Make sure you\'re using the full URL sent to you. ' . $correctKey);
        }
        if ($this->request->is(['patch', 'post', 'put'])) {
            $volunteer = $this->Volunteers->patchEntity($volunteer, $this->request->data);
            if ($this->Volunteers->save($volunteer)) {
                $this->Flash->success(__('The volunteer has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The volunteer could not be saved. Please, try again.'));
            }
        }
        $jobs = $this->Volunteers->Jobs->find('list', ['limit' => 200]);
        $this->set(compact('volunteer', 'jobs'));
        $this->set([
            '_serialize' => ['volunteer'],
            'pageTitle' => 'Update Volunteer Info'
        ]);
    }
}
