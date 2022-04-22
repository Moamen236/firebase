<?php

namespace App\Models;

use App\Models\Receipt;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Google\Cloud\Firestore\FirestoreClient;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public $firstore;
    public $collection;
    public $documents;

    public function __construct()
    {
        $this->firstore = new FirestoreClient();
        $this->collection = $this->firstore->collection('users');
        $this->documents = $this->collection->documents()->rows();
    }

    /**
     * get all users
     * 
     * @return array of users
     */
    public function getAll()
    {
        $documents =  $this->documents;
        $users = [];
        foreach ($documents as $document) {
            $id = $document->id();
            $users[] = [
                'id' => $id,
                'data' => $document->data()
            ];
        }
        return $users;
    }

    /**
     * get user by id
     * 
     * @param  int $id
     * @return array of user
     */
    public function find($id){
        $document = $this->collection->document($id)->snapshot();
        if ($document->exists()) {
            $user = [
                'id' => $document->id(),
                'data' => $document->data()
            ];
            return $user;
        }
        return false;
    }

    /**
     * get user by phone
     * 
     * @param  int $id
     * @return array of user
     */
    public function findByPhone($phone){
        $collection = $this->collection->where('phone', '=', $phone);
        $documents = $collection->documents();
        if ($documents->rows() != null) {
            $document = $documents->rows()[0];
            return $document;
        }
    }

    /**
     * create user
     * 
     * @param  array $data
     * @return array of user
     */
    public function create(array $data){
        $document = $this->collection->add($data);
        return [
            'id' => $document->id(),
            'data' => $document->snapshot()->data()
        ];
    }

    /**
     * update user
     * 
     * @param  int $id
     * @param  array $data
     * @return array of user
     */
    public function edit ($id, array $data){
        $document = $this->collection->document($id);
        $document->set($data);
        return $document->snapshot();
    }

    /**
     * get receipts by client id
     * 
     * @param  int $id
     * @return array of receipts
     */
    public function payments($id)
    {
        $receipt = new Receipt();
        $company = new Company();
        $collection = $this->firstore->collection('payments');
        $documents = $collection->where('user_id', '=', $id)->documents()->rows();
        // return $documents;
        $payments = [];
        foreach ($documents as $document) {
            $id = $document->id();
            $get_company = null;
            if ($document->data()['company_id'] != null) {
                $get_company = $company->find($document->data()['company_id']);
                $company_name = $get_company['data']['name'];
            }
            $get_receipt = $receipt->payment($id);
            $payments[] = [
                'id' => $document->id(),
                'company_name' => $company_name,
                'total' => $get_receipt['data']['total'] ?? 0,
                'date' => $get_receipt['data']['date'] ?? '',
                // 'user_id' => $document->data()['user_id'],
                // 'service_code' => $document->data()['service_code'],
                // 'price' => $document->data()['price'],
                // 'receipt' => [
                //     'id' => $get_receipt->id(),
                //     // 'payment_id' => $get_receipt->data()['payment_id'],
                //     // 'feeds' => $get_receipt->data()['feeds'],
                //     'total' => $get_receipt->data()['total'],
                //     'date' => $get_receipt->data()['date']->get()->format('Y-m-d H:i:s'),
                // ]
            ];
        }
        return $payments;
    }

    /**
     * delete all users
     * 
     * @return bool true
     */
    public function deleteAll(){
        $documents = $this->collection->documents();
        foreach ($documents as $document) {
            $item = $this->collection->document($document->id());
            $item->delete();
        }

        return true;
    }

}
