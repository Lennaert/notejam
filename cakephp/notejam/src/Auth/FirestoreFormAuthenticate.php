<?php
namespace App\Auth;

use App\Model\Document\Note;
use App\Model\Document\User;
use Cake\Auth\BaseAuthenticate;
use Cake\Http\Exception\BadRequestException;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Google\Cloud\Core\Exception\GoogleException;
use Google\Cloud\Firestore\FirestoreClient;

/**
 * FirestoreForm authentication adapter for AuthComponent.
 *
 * Allows you to authenticate users based on form POST data.
 * Usually, this is a login form that users enter information into.
 *
 * ### Using Form auth
 *
 * Load `AuthComponent` in your controller's `initialize()` and add 'Form' in 'authenticate' key
 *
 * ```
 * $this->loadComponent('FirestoreAuth', [
 *     'authenticate' => [
 *         'Form' => [
 *             'fields' => ['username' => 'email', 'password' => 'passwd'],
 *             'finder' => 'auth',
 *         ]
 *     ]
 * ]);
 * ```
 *
 * When configuring FormAuthenticate you can pass in config to which fields, model and finder
 * are used. See `BaseAuthenticate::$_defaultConfig` for more information.
 *
 * @see https://book.cakephp.org/3/en/controllers/components/authentication.html
 */
class FormAuthenticate extends BaseAuthenticate
{
    /**
     * Checks the fields to ensure they are supplied.
     *
     * @param \Cake\Http\ServerRequest $request The request that contains login information.
     * @param array $fields The fields to be checked.
     * @return bool False if the fields have not been supplied. True if they exist.
     */
    protected function _checkFields(ServerRequest $request, array $fields)
    {
        foreach ([$fields['username'], $fields['password']] as $field) {
            $value = $request->getData($field);
            if (empty($value) || !is_string($value)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Authenticates the identity contained in a request. Will use the `config.userModel`, and `config.fields`
     * to find POST data that is used to find a matching record in the `config.userModel`. Will return false if
     * there is no post data, either username or password is missing, or if the scope conditions have not been met.
     *
     * @param \Cake\Http\ServerRequest $request The request that contains login information.
     * @param \Cake\Http\Response $response Unused response object.
     * @return array|false False on login failure. An array of User data on success.
     */
    public function authenticate(ServerRequest $request, Response $response)
    {
        $fields = $this->_config['fields'];
        if (!$this->_checkFields($request, $fields)) {
            return false;
        }

        // Create the Cloud Firestore client
        try {
            $db = new FirestoreClient([
                'projectId' => env('GCE_PROJECT_ID'),
            ]);
        } catch (GoogleException $exception) {

        }

        $userRef = $db->collection('users')->where('email', '=', $request->getData($fields['username']));

        if ($userRef->documents()->size() == 0) {
            return false;
        }

        foreach ($userRef->documents() as $document) {
            $user = new User($document->data(), $document->id());
            if ($user->checkPassword($request->getData($fields['password']))) {
                return $user->toArray();
            }
        }

        return false;
    }
}
