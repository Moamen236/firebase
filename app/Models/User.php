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

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'phone',
        'password',
        'uniq_id'
    ];

    public static function getAll(){
        $firestore = new FirestoreClient();
        $collectionReference = $firestore->collection('users');
        $documents = $collectionReference->documents()->rows();
        return $documents;
    }

    public static function find($id){
        $firestore = new FirestoreClient();
        $collectionReference = $firestore->collection('users');
        $document = $collectionReference->document($id)->snapshot();
        return $document;
    }

    public static function create($data){
        $firestore = new FirestoreClient();
        $collectionReference = $firestore->collection('users');
        $document = $collectionReference->add($data);
        return $document->snapshot();
    }

    public function payments()
    {
        $firestore = new FirestoreClient();
        $collectionReference = $firestore->collection('payments');
        $documents = $collectionReference->where('user_id', '==', $this->id)->documents()->rows();
        return $documents;
    }

}
