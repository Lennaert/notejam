<?php
namespace App\Controller;

use App\Controller\AppController;
use App\Form\SettingsForm;
use App\Form\ForgotPasswordForm;
use App\Model\Document\User;
use Cake\Core\Configure;
use Cake\Log\Log;
use Cake\Network\Email\Email;
use Psr\Log\LogLevel;

/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 */
class UsersController extends AppController
{

    /**
     * Set layout
     *
     * @param \Cake\Event\Event $event Event object
     * @return void
     */
    public function beforeRender(\Cake\Event\Event $event)
    {
        $this->viewBuilder()->layout('anonymous');
    }

    /**
     * Signup action
     *
     * @todo check if the e-mail address already exists
     * @return void Redirects on successful signup, renders errors otherwise.
     */
    public function signup()
    {
        $user = new User();
        $user->setPassword($this->request->getData('password'));
        $user->setEmail($this->request->getData('email'));

        if ($this->request->is('post')) {
            $newUser = $this->db->collection('users')->newDocument();
            if ($newUser->set($user->toArray())) {
                Log::write(LogLevel::INFO, "UsersController:: New registration");
                $this->Flash->success(__('Now you can signin'));
                return $this->redirect(['action' => 'signin']);
            } else {
                $this->Flash->error(__('The user could not be saved. Please, try again.'));
            }
        }
        $this->set(compact('user'));
    }

    /**
     * Signin action
     *
     * @return void Redirects on successful signup, renders errors otherwise.
     */
    public function signin()
    {
        if ($this->request->is('post')) {
            $user = $this->Auth->identify();
            if ($user) {
                Log::write(LogLevel::INFO, "UsersController:: New Login");
                $this->Auth->setUser($user);
                return $this->redirect($this->Auth->redirectUrl());
            } else {
                Log::write(LogLevel::INFO, "UsersController:: Login failure");
            }
            $this->Flash->error('Your username or password is incorrect.');
        }
    }

    /**
     * Sign out action
     *
     * @return void Redirects on successful signin
     */
    public function signout()
    {
        return $this->redirect($this->Auth->logout());
    }

    /**
     * Account settings action
     *
     * @todo
     * @return void
     */
    public function settings()
    {

    }

    /**
     * Forgot password action
     *
     * @todo
     * @return void
     */
    public function forgotPassword()
    {

    }

    /**
     * Reset user's password
     *
     * @todo
     */
    protected function resetPassword()
    {

    }
}
