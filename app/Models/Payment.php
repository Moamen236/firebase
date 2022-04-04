<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Google\Cloud\Firestore\FirestoreClient;

class Payment extends Model
{
    use HasFactory;

    public $firstore;
    public $collection;
    public $documents;

    public function __construct()
    {
        $this->firstore = new FirestoreClient();
        $this->collection = $this->firstore->collection('payments');
        $this->documents = $this->collection->documents()->rows();
    }

    public function getAll()
    {
        return $this->documents;
    }

    public function find($id)
    {
        $firestore = new FirestoreClient();
        $collectionReference = $firestore->collection('payments');
        $document = $collectionReference->document($id)->snapshot();
        return $document;
    }

    public function create($data)
    {
        $firestore = new FirestoreClient();
        $collectionReference = $firestore->collection('payments');
        $document = $collectionReference->add($data);
        return $document->snapshot();
    }

    public function updateCollection($id, $data)
    {
        $firestore = new FirestoreClient();
        $collectionReference = $firestore->collection('payments');
        $document = $collectionReference->document($id);
        $document->set($data);
        return $document;
    }

    public static function deleteCollection($id)
    {
        $firestore = new FirestoreClient();
        $collectionReference = $firestore->collection('payments');
        $document = $collectionReference->document($id);
        $document->delete();
        return $document;
    }
}
