<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Google\Cloud\Firestore\FirestoreClient;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Company extends Model
{
    use HasFactory;

    public $firstore;
    public $collection;
    public $documents;

    public function __construct()
    {
        $this->firstore = new FirestoreClient();
        $this->collection = $this->firstore->collection('companies');
        $this->documents = $this->collection->documents()->rows();
    }

    /**
     * get all companies
     * 
     * @return array of companies
     */
    public function getAll()
    {
        $documents =  $this->documents;
        $companies = [];
        foreach ($documents as $document) {
            $id = $document->id();
            $companies[] = [
                'id' => $id,
                'data' => $document->data()
            ];
        }
        return $companies;
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
     * get user by email
     * 
     * @param  int $id
     * @return array of company
     */
    public function findByEmail($email)
    {
        $collection = $this->collection->where('email', '=', $email);
        $documents = $collection->documents();
        if ($documents->rows() != null) {
            $document = $documents->rows()[0];
            return $document;
        }
    }


    /**
     * get company by service
     * 
     * @param $service
     * @return array
     */
    public function findByService($service)
    {
        $collection = $this->collection->where('service', '=', $service);
        $documents = $collection->documents()->rows();
        return $documents;
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
        return [
            'id' => $document->id(),
            'data' => $document->snapshot()->data()
        ];
    }

    /**
     * get receipts by company id
     * 
     * @param  int $id
     * @return array of receipts
     */
    public function payments($id)
    {
        $collection = $this->firstore->collection('payments');
        $documents = $collection->where('company_id', '==', $id)->documents()->rows();
        $payments = [];
        foreach ($documents as $document) {
            $id = $document->id();
            $receipt = new Receipt();
            $client = new User();
            $client_name = $client->find($document->data()['user_id']);
            $get_receipt = $receipt->payment($id);
            $company = $this->find($document->data()['company_id']);
            $payments[] = [
                'id' => $document->id(),
                'client_name' => $client_name['data']['name'],
                'total' => $get_receipt['data']['total'],
                'date' => $get_receipt['data']['date']->get()->format('Y-m-d H:i:s'),
                // 'service_code' => $document->data()['service_code'],
                // 'price' => $document->data()['price'],
                // 'receipt' => [
                //     'id' => $get_receipt->id(),
                //     'payment_id' => $get_receipt->data()['payment_id'],
                //     'feeds' => $get_receipt->data()['feeds'],
                //     'total' => $get_receipt->data()['total'],
                //     'date' => $get_receipt->data()['date']->get()->format('Y-m-d H:i:s'),
                // ]
            ];
        }
        return $payments;
    }

}
