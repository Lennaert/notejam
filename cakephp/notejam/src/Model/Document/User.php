<?php
namespace App\Model\Document;

use App\Model\Document;
use Cake\Auth\DefaultPasswordHasher;

/**
 * User Entity.
 */
class User extends Document
{
    /** @var string */
    public $email;
    /** @var string */
    public $password;

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * Password setter
     *
     * @param string $value password
     * @return string
     */
    public function setPassword($value)
    {
        $hasher = new DefaultPasswordHasher();
        $this->password = $hasher->hash($value);
        return $this;
    }

    /**
     * Check if passwords matches
     *
     * @param string $password Password
     * @return boolean
     */
    public function checkPassword($password)
    {
        $hasher = new DefaultPasswordHasher();
        return $hasher->check($password, $this->password);
    }
}
