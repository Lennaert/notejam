<?php
namespace App\Model\Document;

use App\Model\Document;

/**
 * Pad Entity.
 */
class Pad extends Document
{
    /** @var string */
    public $name;
    /** @var string */
    public $user_id;


    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     * @return Pad
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Pad
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * @param string $user_id
     * @return Pad
     */
    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
        return $this;
    }


}
