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
}
