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
 * Notes Controller
 *
 */
class NotesController extends AppController
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
     * Index method
     *
     * @return void
     */
    public function index()
    {
        $this->set(
            'notes',
            $this->Notes->find('all', ['contain' => 'Pads'])
                ->where(['Notes.user_id' => $this->getUser()->id])
                ->order($this->buildOrderBy($this->request->query('order')))
        );
    }

    /**
     * View note
     *
     * @param string|null $id Note id.
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function view($id = null)
    {
        $note = $this->getNote($id);
        $this->set('note', $note->toArray());
    }

    /**
     * Create note action
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function create()
    {
        $note = new Note($this->request->getData(), uniqid());
        $note->setUserId($this->Auth->user('id'));

        if ($this->request->is('post')) {
            $newNote = $this->db->collection('notes')->newDocument();
            if ($newNote->set($note->toArray())) {
                $this->Flash->success(__('The note has been created.'));
                return $this->redirect(['action' => 'view', 'id' => $newNote->getId()]);
            }
        }

        // current pad
        $pad = $this->request->query('pad');

        $pads = collection($this->getUser()->pads)->combine('id', 'name')->toArray();
        $this->set(compact('note', 'pads', 'pad'));
    }


    /**
     * Edit note
     *
     * @param string|null $id Note id.
     * @return void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $note = $this->getNote($id);
        $docRef = $this->db->collection('notes')->document($note->getId());

        if ($this->request->is(['patch', 'post', 'put'])) {
            $editedNote = (new Note($this->request->getData(), $note->getId()));
            $editedNote->setUserId($this->Auth->user('id'));

            if ($docRef->set($editedNote->toArray(), ['merge' => true])) {
                $this->Flash->success(__('The note has been saved.'));
                return $this->redirect(['action' => 'view', 'id' => $note->getId()]);
            } else {
                $this->Flash->error(__('The note could not be saved. Please, try again.'));
            }
        }

        $pads = collection($this->getUser()->pads)->combine('id', 'name')->toArray();
        $this->set(compact('note', 'pads'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Note id.
     * @return void Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $note = $this->getNote($id);

        if ($this->request->is('post')) {
            if ($docRef = $this->db->collection('notes')->document($note->getId())->delete()) {
                $this->Flash->success(__('The note has been deleted.'));
                return $this->redirect(['_name' => 'index']);
            }
        }
        $this->set(compact('note'));
    }

    /**
     * Get note
     *
     * @param int $id Note id
     * @return Note
     */
    protected function getNote($id)
    {
        $docRef = $this->db->collection('notes')->document($id);
        $snapshot = $docRef->snapshot();

        $note = null;
        if ($snapshot->exists()) {
            $note = new Note($snapshot->data(), $snapshot->id());
        } else {
            throw new NotFoundException('Cannot find the note');
        }

        if ($note->getUserId() != $this->Auth->user('id')) {
            throw new ForbiddenException('This note does not belong to you');
        }

        return $note;
    }
}
