<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = new User();
        $users = $user->getAll();
        return $users;
    }

    /**
     * Store a newly created resource in storage.
     * 3del ya heshaaaaaam b3d el data
     * 7eta security
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();

        $validator =  Validator::make($request->all(),[
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:25',
            'password' => 'required|string',
            'uniq_id' => 'required|string'
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 'error',
                'message' => 'validation faild',
                'data' => $validator->errors()
            ], 400);
        }
        else{
            $user = new User();
            $createUser = $user->create($data);
        }
        if ($createUser)
            return response()->json(['success' => true, 'data' => $createUser], 200);
        else
            return response()->json(['error' => 'Something went wrong'], 500);
    }

    /**
     * Display the user resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = new User();
        $user = $user->find($id);
        if ($user)
            return $user;
        else
            return response()->json(['error' => 'User not found'], 404);
    }

    /**
     * Update the user resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $clients = new User();
        $wallets = new Wallet();

        $request->validate([
            'phone' => 'required|numeric',
            'password' => 'required|string',
            'balance' => 'required|numeric'
        ]);

        $client = $clients->findByPhone($request->phone);

        if ($client) {
            $client_data = $client->data();
            $client_password = $client_data['password'];
            $request_password = $request->password . $client_data['salt'];
            if ($client_password == $request_password) {
                $wallet = $wallets->findByUserId($client->id());
                $data = [
                    'client_id' => $client->id(),
                    'balance' => $request->balance
                ];
                $wallets->edit($wallet->id(), $data);
                return response()->json([
                    'status' => true,
                    'message' => 'Wallet updated successfully'
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Password is incorrect'
                ]);
            }
        } else {
            return response()->json(['error' => 'Phone number is incorrect']);
        }
    }

    /**
     * Remove all users from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy()
    {
        $user = new User();
        $get = $user->deleteAll();
        return response()->json([
            'status' => true,
            'message' => 'All users deleted successfully'
        ]);
    }

    /**
     * get all payments of client
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function payments(Request $request)
    {
        $client = new User();
        $payments = $client->payments($request->id);
        if ($payments)
            return response()->json([
                'status' => true,
                'data' => [
                    'payments' => $payments
                ]
            ]);
        else
            return response()->json(['error' => 'client not have payments']);
    }


    /**
     * pay and decrees from wallet.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function payWithWallet(Request $request)
    {
        $validator =  Validator::make($request->all(), [
            'client_id' => 'required|string',
            'password' => 'required|string',
            'amount' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'validation faild',
                'data' => $validator->errors()
            ]);
        }

        $wallet = new Wallet();
        $client = new User();
        $get_client = $client->find($request->client_id);
        $get_wallet = $wallet->findByUserId($request->client_id);
        if($get_client){
            $request_password = $request->password . $get_client['data']['salt'];
            if($request_password == $get_client['data']['password']){
                if($get_wallet){
                    if((int) $get_wallet->data()['balance'] > (int)$request->amount){
                        $data = [
                            'client_id' => $request->client_id,
                            'balance' => (int) $get_wallet->data()['balance'] - (int)$request->amount
                        ];
                        $wallet->edit($get_wallet->id(), $data);
                        return response()->json([
                            'status' => true,
                            'message' => 'Payment done successfully'
                        ]);
                    }
                    else{
                        return response()->json([
                            'status' => false,
                            'message' => 'Not enough balance'
                        ]);
                    }
                }
                else{
                    return response()->json([
                        'status' => false,
                        'message' => 'Wallet not found'
                    ]);
                }
            }
            else{
                return response()->json([
                    'status' => false,
                    'message' => 'Password is incorrect'
                ]);
            }
        }
        else{
            return response()->json([
                'status' => false,
                'message' => 'Client not found'
            ]);
        }
    }


}
