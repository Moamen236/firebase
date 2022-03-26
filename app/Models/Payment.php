<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Google\Cloud\Firestore\FirestoreClient;

class Payment extends Model
{
    use HasFactory;

    public static function get()
    {
        $firestore = new FirestoreClient();
        $collectionReference = $firestore->collection('payments');
        $documents = $collectionReference->documents()->rows();
        return $documents;
    }

    public static function find($id)
    {
        $firestore = new FirestoreClient();
        $collectionReference = $firestore->collection('payments');
        $document = $collectionReference->document($id)->snapshot();
        return $document;
    }

    public static function create($data)
    {
        $firestore = new FirestoreClient();
        $collectionReference = $firestore->collection('payments');
        $document = $collectionReference->add($data);
        return $document->snapshot();
    }

    public static function updateCollection($id, $data)
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
