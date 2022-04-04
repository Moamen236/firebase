<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Google\Cloud\Firestore\FirestoreClient;


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
     * delete user
     * 
     * @param  int $id
     * @return array of user
     */
    public function payments($id)
    {
        $collection = $this->firstore->collection('payments');
        $documents = $collection->where('user_id', '==', $id)->documents()->rows();
        $payments = [];
        foreach ($documents as $document) {
            $payments[] = [
                'id' => $document->id(),
                'data' => $document->data()
            ];
        }
        return $payments;
    }

}
