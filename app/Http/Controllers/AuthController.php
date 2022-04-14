<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Otp;
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
        $companies = new Company();

        $validator = $this->validateRegisterData($request);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'validation failed',
                'data' => $validator->errors()
            ]);
        }

        if($request->type == 'user'){
            $user = $users->findByPhone($request->phone);
            if ($user) {
                return response()->json([
                    'status' => false,
                    'message' => 'Phone number already exist'
                ]);
            }else{
                $user = $users->create([
                    'name' => $request->name,
                    'phone' => $request->phone,
                    'password' => $request->password,
                    'salt' => $request->salt,
                ]);
                $this->generateOtp($user);
                return response()->json([
                    'status' => true,
                    'message' => 'User registered successfully',
                    'data' => [
                        'id' => $user['id'],
                        'name' => $user['data']['name'],
                        'phone' => $user['data']['phone'],
                    ]
                ]);
            }
        }elseif($request->type == 'company'){
            $company = $companies->findByEmail($request->email);
            if ($company) {
                return response()->json([
                    'status' => false,
                    'message' => 'Email Address already exist'
                ]);
            }else{
                $company = $companies->create($data);
                // $this->generateOtp($company);
                return response()->json([
                    'status' => true,
                    'message' => 'Company registered successfully',
                    'data' => [
                        'id' => $company['id'],
                        'name' => $company['data']['name'],
                        'email' => $company['data']['email'],
                        'bank_account' => $company['data']['bank_account'],
                        'commercial' => $company['data']['commercial'],
                        'tax_number' => $company['data']['tax_number'],
                        'personal_id' => $company['data']['personal_id'],
                    ]
                ]);
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
        $companies = new Company();

        $validator = $this->validateLoginData($request);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'validation failed',
                'data' => $validator->errors()
            ]);
        } 

        if($request->type == 'user'){
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
        }elseif($request->type == 'company'){
            $company = $companies->findByEmail($request->email);
            if ($company) {
                $company_data = $company->data();
                $company_password = $company_data['password'];
                $request_password = $request->password;
                if ($company_password == $request_password) {
                    return response()->json([
                        'status' => true,
                        'message' => 'login success',
                        'data' => [
                            'id' => $company->id(),
                            'name' => $company->data()['name'],
                            'email' => $company->data()['email'],
                            'bank_account' => $company->data()['bank_account'],
                            'commercial' => $company->data()['commercial'],
                            'tax_number' => $company->data()['tax_number'],
                            'personal_id' => $company->data()['personal_id'],
                        ],
                    ]);
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'Password is incorrect'
                    ]);
                }
            } else {
                return response()->json(['error' => 'Email Address is incorrect']);
            }
        }
        
    }

    /**
     * validate Register data
     *
     * @return \Illuminate\Http\Response
     */
    public function validateRegisterData(Request $request)
    {
        if ($request->type == 'user') {
            $validator =  Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'phone' => 'required|string|max:11',
                'password' => 'required|string',
                'salt' => 'required|string'
            ]);
        } elseif ($request->type == 'company') {
            $validator =  Validator::make($request->all(), [
                'name' => 'required|string',
                'email' => 'required|email',
                'password' => 'required|string',
                'bank_account' => 'required|numeric',
                'commercial' => 'required|numeric',
                'tax_number' => 'required|numeric',
                'personal_id' => 'required|numeric',
            ]);
        }
        
        return $validator;
    }

    /**
     * validate Login data
     *
     * @return \Illuminate\Http\Response
     */
    public function validateLoginData(Request $request)
    {
        if ($request->type == 'user') {
            $validator =  Validator::make($request->all(), [
                'phone' => 'required|string|max:11',
                'password' => 'required|string',
            ]);
        } elseif ($request->type == 'company') {
            $validator =  Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|string',
            ]);
        }

        return $validator;
    }


    /**
     * generate otp code
     *
     * @return \Illuminate\Http\Response
     */
    public function generateOtp($user)
    {
        $random_otp = rand(1000, 9999);
        // $otp_hash = hash('sha256', $random_otp);
        $otp_hash = $random_otp;

        $otp = new Otp();
        $otp->create([
            'user_id' => $user['id'],
            'otp' => $otp_hash
        ]);
    }

}
