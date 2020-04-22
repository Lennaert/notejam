<?php
namespace App\Controller;

use App\Controller\AppController;
use App\Model\Document\Note;
use App\Model\Document\Pad;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Http\Exception\BadRequestException;
use Cake\Http\Exception\ForbiddenException;
use Cake\Http\Exception\NotFoundException;
use Cake\ORM\TableRegistry;
use Google\Cloud\Core\Exception\GoogleException;
use Google\Cloud\Firestore\FirestoreClient;

/**
 * Pads Controller
 *
 * @property \App\Model\Table\PadsTable $Pads
 */
class PadsController extends AppController
{


    /**
     * Set layout
     *
     * @param \Cake\Event\Event $event Event object
     * @return void
     */
    public function beforeRender(\Cake\Event\Event $event)
    {
        $this->viewBuilder()->layout('user');
    }

    /**
     * View pad
     *
     * @param string|null $id Pad id.
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function view($id = null)
    {
        $pad = $this->getPad($id);

        $notesRef = $this->db->collection('notes')->where('pad_id', '=', $pad->getId());

        $notes = [];
        foreach ($notesRef->documents() as $document) {
            $notes[] = (new Note($document->data(), $document->id()))->toArray();
        }

        $this->set('pad', $pad->toArray());
        $this->set('notes', $notes);
    }

    /**
     * Create a pad
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function create()
    {
        $pad = new Pad($this->request->getData(), uniqid());
        $pad->setUserId($this->Auth->user('id'));

        if ($this->request->is('post')) {
            $newPad = $this->db->collection('pads')->newDocument();
            if ($newPad->set($pad->toArray())) {
                $this->Flash->success(__('The pad has been created.'));
                return $this->redirect(['action' => 'view', 'id' => $pad->id]);
            }
        }

        $this->set(compact('pad'));
    }

    /**
     * Edit pad
     *
     * @param string|null $id Pad id.
     * @return void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $pad = $this->getPad($id);
        $docRef = $this->db->collection('pads')->document($pad->getId());

        if ($this->request->is(['patch', 'post', 'put'])) {
            $editedPad = (new Pad($this->request->getData(), $pad->getId()));
            $editedPad->setUserId($this->Auth->user('id'));

            if ($docRef->set($editedPad->toArray(), ['merge' => true])) {
                $this->Flash->success(__('The pad has been saved.'));
                return $this->redirect(['action' => 'view', 'id' => $pad->getId()]);
            } else {
                $this->Flash->error(__('The pad could not be saved. Please, try again.'));
            }
        }
        $this->set(compact('pad'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Pad id.
     * @return void Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $pad = $this->getPad($id);

        if ($this->request->is('post')) {
            if ($docRef = $this->db->collection('pads')->document($pad->getId())->delete()) {
                $this->Flash->success(__('The pad has been deleted.'));
                return $this->redirect(['_name' => 'index']);
            }
        }
        $this->set(compact('pad'));
    }

    /**
     * Get a pad
     *
     * @param $id
     * @return Pad|null
     */
    protected function getPad($id)
    {
        $docRef = $this->db->collection('pads')->document($id);
        $snapshot = $docRef->snapshot();

        $pad = null;
        if ($snapshot->exists()) {
            $pad = new Pad($snapshot->data(), $snapshot->id());
        } else {
            throw new NotFoundException('Cannot find the pad');
        }

        if ($pad->getUserId() != $this->Auth->user('id')) {
            throw new ForbiddenException('This pad does not belong to you');
        }

        return $pad;
    }
}
