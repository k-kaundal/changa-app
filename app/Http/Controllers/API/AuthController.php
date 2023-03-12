<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\Models\User;
use App\Models\VerificationCode;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends BaseController {
    public function signin( Request $request ) {
        if ( Auth::attempt( [ 'email' => $request->email, 'password' => $request->password ] ) ) {

            $authUser   = User::where( 'id', Auth::user()->id )->first();
            $token =  $authUser->createToken( 'token-name', [ 'server:update' ] )->plainTextToken;
            $authUser->update( [
                'api_token' => $token
            ] );
            $success[ 'token' ] =  $authUser->api_token;

            $success[ 'first_name' ] =  $authUser->first_name;
            $success[ 'user_data' ] = $authUser;
            return $this->sendResponse( $success, 'User signed in' );
        } else {

            return $this->sendError( 'Unauthorised.', [ 'error'=>'Unauthorised' ] );
        }

    }

    public function forgotPassword( Request $request ) {

        $validator = Validator::make( $request->all(), [
            'email' =>'required|email:exists:users'
        ] );

        if ( $validator->fails() ) {
            return $this->sendError( 'Error validation', $validator->errors() );

        }

        $user = User::where( 'email', $request->email )->first();

        if ( $user->id != null ) {
            $verification_code = $this->generateOtp( $user->mobile_no );
            $otp =  $this->sendSmsNotificaition( $user->mobile_no, $verification_code->otp );
            $success[ 'otp_message' ] = $otp;
            $success[ 'user_id' ] = $user->id;
            $success[ 'token' ] = $user->api_token;
            return $this->sendResponse( $success, 'OTP send successfully to '.$user->mobile_no );
        } else {
            return $this->sendError( 'User not found' );

        }

    }

    public function signup( Request $request ) {

        $validator = Validator::make( $request->all(), [
            'first_name' => 'required',
            'username' => 'required|unique:users',
            'mobile_no' =>'required',
            'email' => 'required|email',
            'password' => 'required',
            'confirm_password' => 'required|same:password',
        ] );

        if ( $validator->fails() ) {
            return $this->sendError( 'Error validation', $validator->errors() );

        }

        $input = $request->all();
        $input[ 'password' ] =  \Hash::make( $input[ 'password' ] );

        $user = User::create( $input );
        $token = $user->createToken( 'token-name', [ 'server:update' ] )->plainTextToken;
        $user->update( [
            'api_token' => $token
        ] );
        $verification_code = $this->generateOtp( $user->mobile_no );
        $otp =  $this->sendSmsNotificaition( $request->mobile_no, $verification_code->otp );
        $success[ 'otp_message' ] = $otp;
        $success[ 'user_id' ] = $user->id;
        $success[ 'token' ] = $user->api_token;
        return $this->sendResponse( $success, 'User created successfully.' );
    }

    public function generateOtp( $mobile_no ) {
        $user = User::where( 'mobile_no', $mobile_no )->first();

        # User Does not Have Any Existing OTP
        $verificationCode = VerificationCode::where( 'user_id', $user->id )->latest()->first();

        $now = Carbon::now();

        if ( $verificationCode && $now->isBefore( $verificationCode->expire_at ) ) {
            return $verificationCode;
        }

        // Create a New OTP
        return VerificationCode::create( [
            'user_id' => $user->id,
            'otp' => rand( 123456, 999999 ),
            'expire_at' => Carbon::now()->addMinutes( 10 )
        ] );
    }

    public function otpVerification( Request $request ) {
        #Validation
        $request->validate( [
            'user_id' => 'required|exists:users,id',
            'otp' => 'required'
        ] );

        #Validation Logic
        $verificationCode   = VerificationCode::where( 'user_id', $request->user_id )->where( 'otp', $request->otp )->first();

        $now = Carbon::now();
        if ( !$verificationCode ) {
            return $this->sendError( 'Error', 'Your OTP is not correct.' );
        } elseif ( $verificationCode && $now->isAfter( $verificationCode->expire_at ) ) {
            return $this->sendError( 'Error', 'Your OTP has been expired.' );
        }

        $user = User::whereId( $request->user_id )->first();

        if ( $user ) {
            // Expire The OTP
            $verificationCode->update( [
                'expire_at' => Carbon::now()
            ] );
            $success[ 'user_data' ] = $user;
            return $this->sendResponse( $success, 'Your OTP verifictation completed.' );
        }

    }

    public function resetPassword( Request $request ) {
        $validator = Validator::make( $request->all(), [
            'user_id' => 'required|exists:users,id',
            'password' => 'required',
            'confirm_password' => 'required|same:password',
        ] );
        if ( $validator->fails() ) {
            return $this->sendError( 'Error validation', $validator->errors() );

        }

        $change = User::where( 'id', $request->user_id )->first()->update( [ 'password' => Hash::make( $request->password ) ] );
        if ( $change ) {
            $user = User::whereId( $request->user_id )->first();
            $success[ 'user_data' ] = $user;
            return $this->sendResponse( $success, 'Your password updated.' );
        } else {
            return $this->sendResponse( 'Error', 'Try again' );
        }
    }

}