<?php

namespace App\Http\Controllers;

use Mail;
use Exception;
use App\Models\User;
use App\Mail\OTPMail;
use App\Helper\JWTToken;
use Illuminate\Http\Request;

class UserController extends Controller
{
    function UserRegistration(Request $request)
    {
        $request->validate([
            'firstName' => 'required',
            'lastName' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:5|max:12',
            'mobile' => 'required|min:11|max:11',
        ]);

        try {
            User::create([
                'firstName' => $request->input('firstName'),
                'lastName' => $request->input('lastName'),
                'email' => $request->input('email'),
                'password' => $request->input('password'),
                'mobile' => $request->input('mobile')
            ]);

            return response()->json(['status' => 'success', 'message' => 'User Registration Successful']);
        } catch (Exception $e) {
            return response()->json(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }

    function UserLogin(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required|min:5|max:12'
        ]);

        $count = User::where('email', '=', $request->input('email'))->where('password', '=', $request->input('password'))->count();
        $token = JWTToken::CreateToken($request->input('email'));
        if ($count == 1) {
            return response()->json(['status' => 'success', 'message' => 'Login Successful'])->cookie('token', $token, 60*24*30);
        } else {
            return response()->json(['status' => 'failed', 'message' => 'Login Failed']);
        }
    }

    function SendOTPCode(Request $request)
    {

        $request->validate([
            'email' => 'required|email',
        ]);

        $email = $request->input('email');
        $otp = rand(1000, 9999);
        $count = User::where('email', '=', $email)->count();

        if ($count == 1) {
            Mail::to($email)->send(new OTPMail($otp));
            User::where('email', '=', $email)->update([
                'otp' => $otp
            ]);
            return response()->json(['status' => 'success', 'message' => '4 Digit OTP has been sent to your email.']);
        } else {
            return response()->json(['status' => 'failed', 'message' => 'unauthorized']);
        }
    }

    function VerifyOTPCode(Request $request)
    {
        $email = $request->input('email');
        $otp = $request->input('otp');
        $count = User::where('email', '=', $email)->where('otp', '=', $otp)->count();

        if ($count == 1) {
            User::where('email', '=', $email)->where('otp', '=', $otp)->update([
                'otp' => '0'
            ]);
            $token = JWTToken::CreateTokenForResetPassword($email);
            return response()->json(['status' => 'success', 'message' => 'OTP Verified Successful'])->cookie('token', $token, 60*24*30);
        } else {
            return response()->json(['status' => 'failed', 'message' => 'unauthorized']);
        }
    }

    function ResetPassword(Request $request)
    {

        try {
            $email = $request->header('email');
            $password = $request->input('password');
            User::where('email', '=', $email)->update([
                'password' => $password
            ]);
            return response()->json(['status' => 'success', 'message' => 'Password Reset Successful']);
        } catch (Exception $e) {
            return response()->json(['status' => 'failed', 'message' => 'something went wrong']);
        }

    }
}
