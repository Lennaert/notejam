<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link      http://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Controller;

use App\Model\Document\Pad;
use App\Model\Document\User;
use Cake\Controller\Controller;
use Cake\Http\Exception\BadRequestException;
use Cake\Http\Exception\ForbiddenException;
use Cake\Http\Exception\NotFoundException;
use Cake\ORM\TableRegistry;
use Google\Cloud\Core\Exception\GoogleException;
use Google\Cloud\Firestore\FirestoreClient;

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @link http://book.cakephp.org/3.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller
{
    /** @var FirestoreClient */
    public $db;

    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * @return void
     * @throws BadRequestException
     */
    public function initialize()
    {
        parent::initialize();

        // Create the Cloud Firestore client
        try {
            $this->db = new FirestoreClient([
                'projectId' => env('GCE_PROJECT_ID'),
            ]);
        } catch (GoogleException $exception) {
            throw new BadRequestException('Firestore issue; ' . $exception->getMessage());
        }

        $this->loadComponent('Flash');
        $this->loadComponent('Auth', [
            'authenticate' => [
                'FirestoreForm' => [
                    'fields' => [
                        'username' => 'email',
                        'password' => 'password'
                    ]
                ]
            ],
            'loginAction' => [
                'controller' => 'Users',
                'action' => 'signin'
            ]
        ]);
        $this->Auth->allow(['signup', 'forgotpassword']);
    }

    /**
     * Get authenticated user
     *
     * @return User
     */
    protected function getUser()
    {
        $id = $this->request->session()->read('Auth.User.id');

        $docRef = $this->db->collection('users')->document($id);
        $snapshot = $docRef->snapshot();
        $user = new User($snapshot->data(), $snapshot->id());

        return $user;
    }

    /**
     * Build order statetment
     *
     * @param string $order Order param
     * @todo
     * @return array
     */
    public function buildOrderBy($order)
    {
        $config = [
            'name' => ['Notes.name' => 'ASC'],
            '-name' => ['Notes.name' => 'DESC'],
            'updated_at' => ['Notes.updated_at' => 'ASC'],
            '-updated_at' => ['Notes.updated_at' => 'DESC'],
        ];
        return $config[$order ? $order : 'updated_at'];
    }
}
