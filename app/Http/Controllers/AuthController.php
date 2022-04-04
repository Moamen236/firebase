<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Register new user.
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $data = $request->all();
        $users = new User();

        $validator =  Validator::make($request->all(),[
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:11',
            'password' => 'required|string',
            'salt' => 'required|string'
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => false,
                'message' => $validator->errors()
            ], 400);
        }
        else{
            $user_phone = $users->findByPhone($data['phone']);
            if ($user_phone){
                return response()->json([
                    'status' => false ,
                    'message' => 'Phone number already exist'
                ], 400);
            }else{
                $user = $users->create($data);
                return response()->json([
                    'status' => true,
                    'message' => 'User registered successfully',
                    'data' => [
                        'id' => $user['id'],
                        'name' => $user['data']['name'],
                        'phone' => $user['data']['phone'],
                    ]
                ], 200);
            }
        }
    }

    /**
     * Login user and create token
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $users = new User();

        $validator =  Validator::make($request->all(), [
            'phone' => 'required|string|max:11',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'validation failed',
                'data' => $validator->errors()
            ], 400);
        } else {
            $user = $users->findByPhone($request->phone);
            if ($user) {
                $user_data = $user->data();
                $user_password = $user_data['password'];
                $request_password = $request->password . $user_data['salt'];
                if ($user_password == $request_password) {
                    return response()->json([
                        'status' => true,
                        'message' => 'login success',
                        'data' => [
                            'id' => $user->id(),
                            'name' => $user->data()['name'],
                            'phone' => $user->data()['phone'],
                        ],
                    ], 200);
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'Password is incorrect'
                    ], 401);
                }
            } else {
                return response()->json(['error' => 'Phone number is incorrect'], 401);
            }
        }
        
    }

    /**
     * generate password
     *
     * @return \Illuminate\Http\Response
     */
    public function generate(Request $request)
    {
        $text = $request->text;
        $number = $request->number;
        $text_array = str_split($text);
        $number_array = str_split($number);
        $password = '';
        
        dd($text, $number, $password);
        // return response()->json([
        //     'status' => true,
        //     'message' => 'password generated successfully',
        //     'data' => [
        //         'password' => $password
        //     ]
        // ], 200);
    }

}
