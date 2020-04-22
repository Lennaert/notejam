<?php
namespace App\Model\Document;

use App\Model\Document;
use Cake\i18n\Time;

/**
 * Note Entity.
 */
class Note extends Document
{
    /** @var string */
    public $pad_id;
    /** @var string */
    public $user_id;
    /** @var string */
    public $name;
    /** @var string */
    public $text;
    /** @var string */
    public $created_at;
    /** @var string */
    public $updated_at;

    /**
     * Get pretty date like "Yesterday", "2 days ago", "etc"
     *
     * @return string
     */
    public function getPrettyDate()
    {
        $time = new Time($this->updated_at);
        return $time->timeAgoInWords([
            'format' => 'd'
        ]);
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     * @return Note
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getPadId()
    {
        return $this->pad_id;
    }

    /**
     * @param string $pad_id
     * @return Note
     */
    public function setPadId($pad_id)
    {
        $this->pad_id = $pad_id;
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
     * @return Note
     */
    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
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
     * @return Note
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param string $text
     * @return Note
     */
    public function setText($text)
    {
        $this->text = $text;
        return $this;
    }

    /**
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * @param string $created_at
     * @return Note
     */
    public function setCreatedAt($created_at)
    {
        $this->created_at = $created_at;
        return $this;
    }

    /**
     * @return string
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * @param string $updated_at
     * @return Note
     */
    public function setUpdatedAt($updated_at)
    {
        $this->updated_at = $updated_at;
        return $this;
    }


}
