<?php
namespace App\Model;

/**
 * This is a Firestore Document
 * Quick implementation to transform a snapshot result into a `Document` object
 *
 * @package App\Model
 */
class Document
{
    /** @var string */
    public $id;

    /**
     * Construct a new document based on the data that is given to the constructor
     * The constructor will walk through an array of key/values and then checks if the document has that specific
     * property in the document. If there is, it will assign the value to it.
     *
     * Document constructor.
     * @param array|null $data The data from the firestore document
     * @param string|null $documentId The Firestore document ID
     */
    public function __construct(array $data = null, $documentId = null)
    {
        $classVars = get_class_vars($this);
        foreach ($data as $key => $value) {
            if (in_array($key, $classVars)) {
                $this->$key = $value;
            }
        }

        if ($documentId != null) {
            $this->id = $documentId;
        }

        return $this;
    }

    /**
     * Return a key/value array of this document
     * @return array
     */
    public function toArray()
    {
        $finalArray = [];
        $classVars = get_class_vars($this);
        foreach ($classVars as $key => $value) {
            $finalArray[$key] = $value;
        }

        return $finalArray;
    }
}
