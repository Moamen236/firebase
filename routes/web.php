<?php


use Google\Cloud\Core\Timestamp;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use phpseclib3\Crypt\PublicKeyLoader;
use App\Http\Controllers\OtpController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UsersController;
use Google\Cloud\Firestore\FirestoreClient;
use Kreait\Laravel\Firebase\Facades\Firebase;
use phpseclib3\Crypt\RSA;

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
    return view('welcome');
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

    // $firestore = new FirestoreClient();

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

    // $password = hash('sha256', 'SlccC3BP2XDMjtaWi6qO$011');
    // $id = uniqid();
    // dd($password , $id);
});

Route::get('/test', function () {
    // $company = new \App\Models\Company();
    // $payments = $company->payments('1c4e987159cf4cb4b763');
    // $stamp = new Timestamp( new \DateTime('now') );

    // dd($payments , $stamp);
    // $receipt = new \App\Models\Receipt();
    // $payment = $receipt->payment('DhBnC9FvdL4JiBNKEfgN');
    // dd($payment);
    // $publicKey = RSA::createKey(1024, 10);
    // $privateKey = RSA::createKey(1024, 10);
    // $loader = PublicKeyLoader::load($publicKey);
    // //save key to storage
    // $loader->saveToAsciiFile('public.key');
    // dd($loader);
    // create public key
    $private_key = file_get_contents(storage_path('app/private.key'));
    $key = RSA::loadPrivateKey($private_key);
    // $key->setPublicKey(file_get_contents(storage_path('app/public.key')));
    // $key->setPrivateKey(file_get_contents(storage_path('app/private.key')));
    $text = 'HyM/159F42+Ym2Eh5hNljem/a6+E+9pFfwMu98npdlQWnl3gyGivYjtNUG4VwvJAWwRnudcCZLzh/pGKHL5Vmg==';
    $decrypted = $key->decrypt($text);
    dd($decrypted);
});

Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::post('/getOtp', [OtpController::class, 'check'])->name('getOtp');
Route::post('/deleteOtp', [OtpController::class, 'destroyOtp'])->name('deleteOtp');

Route::post('/generate', [AuthController::class, 'generatePassword'])->name('generate');

Route::get('/charge_wallet', function () {
    return view('charge_wallet');
});
Route::post('/charge_wallet', [UsersController::class, 'chargeWallet'])->name('charge_wallet');



// Route::get('/users' , [ UsersController::class, 'index' ]);
// Route::get('/users/{id}' , [ UsersController::class, 'show' ]);
// Route::post('/users' , [ UsersController::class, 'store' ]);

// composer require "grpc/grpc:^1.38"
// composer require google/cloud-firestore
//php artisan vendor:publish --provider="Kreait\Laravel\Firebase\ServiceProvider" --tag=config
