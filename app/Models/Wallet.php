<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Google\Cloud\Firestore\FirestoreClient;

class Wallet extends Model
{
    use HasFactory;

    public $firstore;
    public $collection;
    public $documents;

    public function __construct()
    {
        $this->firstore = new FirestoreClient();
        $this->collection = $this->firstore->collection('wallets');
        $this->documents = $this->collection->documents()->rows();
    }

    /**
     * get all wallets
     * 
     * @return array of wallets`
     */
    public function getAll()
    {
        $documents =  $this->documents;
        $wallets = [];
        foreach ($documents as $document) {
            $id = $document->id();
            $wallets[] = [
                'id' => $id,
                'data' => $document->data()
            ];
        }
        return $wallets;
    }

    /**
     * get client by id
     * 
     * @param  int $id
     * @return array of client
     */
    public function find($id)
    {
        $document = $this->collection->document($id)->snapshot();
        if ($document->exists()) {
            $client = [
                'id' => $document->id(),
                'data' => $document->data()
            ];
            return $client;
        }
        return false;
    }

    /**
     * create client
     * 
     * @param  array $data
     * @return array of client
     */
    public function create(array $data)
    {
        $document = $this->collection->add($data);
        return $document->snapshot();
    }

}
