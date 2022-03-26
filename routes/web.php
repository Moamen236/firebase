<?php

use App\Http\Controllers\UsersController;
use Illuminate\Support\Facades\Route;
use Kreait\Laravel\Firebase\Facades\Firebase;
use Google\Cloud\Firestore\FirestoreClient;
use Illuminate\Support\Facades\Hash;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    // return view('welcome');
    // $defaultAuth = Firebase::auth();
    // $defaultMessaging = Firebase::messaging();
    // $defaultDynamicLinks = Firebase::dynamicLinks();
    // $defaultRemoteConfig = Firebase::remoteConfig();
    // $defaultDatabase = Firebase::database();
    // $defaultStorage = Firebase::storage();
    // $defaultFirestore = Firebase::firestore();

    // $data = $defaultDatabase->getReference('/users')->set([
    //     'name' => 'Doe',
    //     'age' => 25,
    //     'gender' => 'male'
    // ]);

    $firestore = new FirestoreClient();

    // $collectionReference = $firestore->collection('users');
    // $documents = $collectionReference->documents();

    // $data = $defaultFirestore->;

    // $data = $defaultRemoteConfig->get();

    // dd($collectionReference, $documents);
    // dd('hi');

//    $new = $firestore->collection('users')->add([
//        'name' => 'John Doe',
//        'age' => 25,
//        'password' => Hash::make('123456')
//    ]);
//
//    dd($new);

//    $new = $firestore->collection('users')->document();

    // get data from firestore
//    $collections = $firestore->collection('users')->documents();
//    foreach ($collections as $collection) {
//        printf('Found subcollection with id: %s' . PHP_EOL, $collection->id());
//        echo '<br>';
//    }
//    dd($data);

    // $users = \App\Models\User::getFirstoreData();
    // dd($users);

    // $users = \App\Models\User::find('SlccC3BP2XDMjtaWi6qO');
    // $user = \App\Models\User::create([
    //     'name' => 'John Doe',
    //     'phone' => '123456789',
    //     'password' => Hash::make('123456')
    // ]);

    // $user = \App\Models\User::find('SlccC3BP2XDMjtaWi6qO');
    
    // // $payments = $user->payments;
    // dd($user);

    $password = hash('sha256', 'SlccC3BP2XDMjtaWi6qO$011');
    $id = uniqid();
    dd($password , $id);
});


// Route::get('/users' , [ UsersController::class, 'index' ]);
// Route::get('/users/{id}' , [ UsersController::class, 'show' ]);
// Route::post('/users' , [ UsersController::class, 'store' ]);

// composer require "grpc/grpc:^1.38"
// composer require google/cloud-firestore
//php artisan vendor:publish --provider="Kreait\Laravel\Firebase\ServiceProvider" --tag=config
