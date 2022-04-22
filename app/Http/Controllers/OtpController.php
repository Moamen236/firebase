<?php

namespace App\Http\Controllers;

use App\Models\Otp;
use Illuminate\Http\Request;

class OtpController extends Controller
{
    /**
     * get all otps
     * 
     * @return array of payment`
     */
    public function index ()
    {
        $otp = new Otp();
        $otp = $otp->getAll();
        return $otp;
    }

    /**
     * check otp by user id
     * 
     * @param Request $request
     * @return array
     */
    public function check(Request $request)
    {
        $user_id = $request->user_id;
        // $otp_num  = hash('sha256' , $request->otp_num);
        $otp_num  =  $request->otp_num;

        $otp = new Otp();
        $find_otp = $otp->userOtp($user_id);
        // dd($find_otp);
        if($find_otp){
            if($find_otp == $otp_num){
                $this->destroy($request);
                return response()->json([
                    'status' => true,
                    'message' => 'OTP is valid'
                ]);
            }else{
                return response()->json([
                    'status' => false,
                    'message' => 'OTP is invalid'
                ]);
            }
        }else{
            return response()->json([
                'status' => false,
                'message' => 'user not found'
            ]);
        }
    }

    /**
     * resend otp
     * 
     * @param Request $request
     * @return array
     */
    public function resend(Request $request)
    {
        $this->destroy($request);
        $this->generateOtp($request);
        return response()->json([
            'status' => true,
            'message' => 'OTP is resent successfully'
        ]);

    }

    /**
     * Destroy otp
     * 
     * @param Request $request
     * @return array
     */
    public function destroy(Request $request)
    {
        $user_id = $request->user_id;
        $otp = new Otp();
        $otp->deleteOtp($user_id);
    }

    /**
     * generate otp code
     *
     * @return \Illuminate\Http\Response
     */
    public function generateOtp($request)
    {
        $random_otp = rand(1000, 9999);
        $otp_hash = hash('sha256', $random_otp);

        $otp = new Otp();
        $otp->create([
            'user_id' => $request->user_id,
            'otp' => $otp_hash
        ]);
    }
}
