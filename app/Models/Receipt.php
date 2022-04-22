<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Google\Cloud\Firestore\FirestoreClient;


class Receipt extends Model
{
    use HasFactory;

    public $firstore;
    public $collection;
    public $documents;

    public function __construct()
    {
        $this->firstore = new FirestoreClient();
        $this->collection = $this->firstore->collection('receipts');
        $this->documents = $this->collection->documents()->rows();
    }

    /**
     * get all receipts
     * 
     * @return array of receipts`
     */
    public function getAll()
    {
        $documents =  $this->documents;
        $receipts = [];
        foreach ($documents as $document) {
            $id = $document->id();
            $receipts[] = [
                'id' => $id,
                'data' => $document->data()
            ];
        }
        return $receipts;
    }

    /**
     * get receipt by id
     * 
     * @param  int $id
     * @return array of receipt
     */
    public function find($id){
        $document = $this->collection->document($id)->snapshot();
        if ($document->exists()) {
            $receipt = [
                'id' => $document->id(),
                'data' => $document->data()
            ];
            return $receipt;
        }
        return false;
    }

    /**
     * create receipt
     * 
     * @param  array $data
     * @return array of receipt
     */
    public function create(array $data){
        $document = $this->collection->add($data);
        return $document->snapshot();
    }

    /**
     * get one payment by receipt id
     * 
     * @param  int $id
     * @return array of payments
     */
    public function payment($id)
    {
        $documents = $this->collection->where('payment_id', '=', $id)->documents();
        if ($documents->rows() != null) {
            $document = $documents->rows()[0];
            return [
                'id' => $document->id(),
                'data' => $document->data()
            ];
        }
        return false;
    }
}
