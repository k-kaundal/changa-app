<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Mail\BasicMail;
use App\Mail\OtpMail;
use Validator;
use App\Models\User;
use App\Models\VerificationCode;
use Carbon\Carbon;
use Carbon\Exceptions\Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AuthController extends BaseController {


    
    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'phone' => 'required|string|max:15',
            'otp' => 'required|string|max:4',
        ]);
    
        if($validator->fails()){
            return $this->sendError($validator->errors()->all());
        }
    
        $user = User::where('email', $request->email)->where('phone', $request->phone)->first();
    
        if (!$user) {
            return response(['errors' => ['User not found']], 422);
        }
    
        if ($user->otp !== $request->otp) {
            return response(['errors' => ['Invalid OTP']], 422);
        }
    
        $user->email_verified = 1;
        $user->phone_verified = 1;
        $user->save();
    
        $token = $user->createToken('authToken')->accessToken;
    
        return response(['user' => $user->makeHidden(['password']), 'access_token' => $token]);
    }
    

    
    public function forgotPassword( Request $request ) {

        $validator = Validator::make( $request->all(), [
            'email' =>'required|email:exists:users'
        ] );

        if($validator->fails()){
            return $this->sendError($validator->errors()->all());
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


    public function generateOtp( $mobile_no ) {
        $user = User::where( 'phone', $mobile_no )->first();

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
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'otp' => 'required'
        ] );
        if($validator->fails()){
            return $this->sendError($validator->errors()->all());
        }

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
        if($validator->fails()){
            return $this->sendError($validator->errors()->all());
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


    
    //register api
    public function register(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|max:191',
            'email' => 'required|email|unique:users|max:191',
            'username' => 'required|unique:users|max:191',
            'phone' => 'required|unique:users|max:191',
            'password' => 'required|min:6|max:191',
            'user_type' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError($validator->errors()->all());
        }

        if (!filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
            return  $this->sendError("Invalid email");
        }
try{
    $user = User::create([
        'first_name' => $request->first_name,
        'email' => $request->email,
        'username' => $request->username,
        'phone' => $request->phone,
        'password' => Hash::make($request->password),
        'user_type' => $request->user_type,
    ]);

    if (!is_null($user)) {
        $token = $user->createToken( 'token-name', [ 'server:update' ] )->plainTextToken;
        $user->update( [
            'api_token' => $token
        ] );
        $verification_code = $this->generateOtp( $user->phone );
        $otp =  $this->sendSmsNotificaition( $request->phone, $verification_code->otp );
        // Mail::to($request->email)->send(new BasicMail([
        //     'subject' => 'Your OTP Code',
        //     'message' => $otp,
        // ]));
        $data = array('email'=>$user->email,
          'OTP'=>$otp);
        // Mail::send(['text'=>'mail'], $data, function($message) {
        //     $message->to(Auth::user->email, 'Changa App')->subject
        //        ('OTP for verification');
        //     $message->from('kaundal.k.k@gmail.com','Kaundal');
        //  });
        $success[ 'register_user_data' ] = $user;
        $success[ 'otp_message' ] = $otp;
        $success[ 'token' ] = $token;
        return $this->sendResponse( $success, 'User Created.' );
       
    }else{
        return $this->sendError("User registration failed");
    }
}catch(Exception $e){
    return $this->sendError($e);
}
        
       
        // send the OTP to the user's email and phone (you can use a third-party service for this)
        // for example, to send an email using Laravel's built-in Mail class:
       
    }

    // send otp
    public function sendOTPSuccess(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'email_verified' => 'required|integer',
        ]);

        if($validator->fails()){
            return $this->sendError($validator->errors()->all());
        }

        if(!in_array($request->email_verified,[0,1])){
            return response()->error([
                'message' => __('email verify code must have to be 1 or 0'),
            ]);
        }

        $user = User::where('id', $request->user_id)->update([
            'email_verified' =>  $request->email_verified
        ]);

        if(is_null($user)){
            return response()->error([
                'message' => __('Something went wrong, plese try after sometime,'),
            ]);
        }

        return response()->success([
            'message' => __('Email Verify Success'),
        ]);
    }

    public function sendOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
        ]);
        if($validator->fails()){
            return $this->sendError($validator->errors()->all());
        }

        $otp_code = sprintf("%d", random_int(123456, 999999));
        $user_email = User::where('email', $request->email)->first();

        if (!is_null($user_email)) {
            try {
                $message_body = __('Here is your otp code') . ' <span class="verify-code">' . $otp_code . '</span>';
                Mail::to($request->email)->send(new BasicMail([
                    'subject' => __('Your OTP Code'),
                    'message' => $message_body
                ]));
            } catch (\Exception $e) {
                return response()->error([
                    'message' => __($e->getMessage()),
                ]);
            }

            return response()->success([
                'email' => $request->email,
                'otp' => $otp_code,
            ]);

        } else {
            return response()->error([
                'message' => __('Email Does not Exists'),
            ]);
        }

    }

    //reset password
    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required',
        ]);
        if($validator->fails()){
            return $this->sendError($validator->errors()->all());
        }

        $email = $request->email;
        $user = User::select('email')->where('email', $email)->first();
        if (!is_null($user)) {
            User::where('email', $user->email)->update([
                'password' => Hash::make($request->password),
            ]);
            return response()->success([
                'message' => 'success',
            ]);
        } else {
            return response()->error([
                'message' => __('Email Not Found'),
            ]);
        }
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|max:191',
            'password' => 'required',
        ]);
        if($validator->fails()){
            return $this->sendError($validator->errors()->all());
        }

        $login_type = 'email';
        if (!filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
            $login_type = 'username';
        }
        
        $user = User::select('id', 'email', 'password','username')->where([$login_type => $request->email])->first();



        if (!$user || !Hash::check($request->password, $user->password)) {
          
    
            
            return $this->sendError( sprintf(__('Invalid %s or Password'),ucFirst($login_type)) );
        
        } else {
            $token =  $user->createToken( 'token-name', [ 'server:update' ] )->plainTextToken;
            $user->update( [
                'api_token' => $token
            ] );

            $success[ 'login_user_data' ] = $user;
            $success[ 'token' ] = $token;
            return $this->sendResponse( $success, 'Login Done' );
            
        }
    }

    //logout
    public function logout($id){
        $user = User::select('api_token')->where('id', $id)->first();
        $token =  $user->createToken( 'token-name', [ 'server:update' ] )->plainTextToken;
            $user->update( [
                'api_token' => $token
            ] );
        return $this->sendResponse(
            'Logout Success'
        ,'');
    }

    //User Profile
    public function profile(){

        $user_id = auth('sanctum')->id();

        $user = User::with('country','city','area')
            ->select('id','name','email','phone','address','about','country_id','service_city','service_area','post_code','image','country_code')
            ->where('id',$user_id)->first();


        $profile_image =  get_attachment_image_by_id($user->image);

        return response()->success([
            'user_details' => $user,
            'profile_image' => $profile_image,
        ]);
    }

    // change password after login
    public function changePassword(Request $request){
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|min:6',
            'new_password' => 'required|min:6',
        ]);
        if($validator->fails()){
            return $this->sendError($validator->errors()->all());
        }

        $user = User::select('id','password')->where('id', auth('sanctum')->user()->id)->first();
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->error([
                'message' => __('Current Password is Wrong'),
            ]);
        }
        User::where('id',auth('sanctum')->user()->id)->update([
            'password' => Hash::make($request->new_password),
        ]);
        return response()->success([
            'current_password' => $request->current_password,
            'new_password' => $request->new_password,
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = auth('sanctum')->user();
        $user_id = auth('sanctum')->user()->id;

        $validator = Validator::make($request->all(), [
            'name' => 'required|max:191',
            'email' => 'required|max:191|email|unique:users,email,'.$user_id,
            'phone' => 'required|max:191',
        ]);
        if($validator->fails()){
            return $this->sendError($validator->errors()->all());
        }

        if($request->file('file')){
            MediaHelper::insert_media_image($request,'web');
            $last_image_id = DB::getPdo()->lastInsertId();
        }
        $old_image = User::select('image')->where('id',$user_id)->first();
        $user_update = User::where('id',$user_id)
            ->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'image' => $last_image_id ?? $old_image->image,
                'service_city' => $request->service_city ?? $user->service_city,
                'service_area' => $request->service_area ?? $user->service_area,
                'country_id' => $request->country_id ?? $user->country_id,
                'post_code' => $request->post_code,
                'country_code' => $request->country_code,
                'address' => $request->address,
                'about' => $request->about,
                'state' => $request->service_city,
            ]);

        if($user_update){
            return response()->success([
                'message' =>__('Profile Updated Success'),
            ]);
        }
    }


    //social login
    public function socialLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);
        if($validator->fails()){
            return $this->sendError($validator->errors()->all());
        }
        if (!filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
            return response()->error([
                'message' => __('invalid Email'),
            ]);
        }

        $username = $request->isGoogle === 0 ?  'fb_'.Str::slug($request->displayName) : 'gl_'.Str::slug($request->first_name);
        $user = User::select('id', 'email', 'username','user_type')
            ->where('user_type' , $request->user_type)
            ->where('email', $request->email)
            ->Orwhere('username', $username)
            ->first();

        if (is_null($user)) {
            $user = User::create([
                'first_name' => $request->first_name,
                'email' => $request->email,
                'username' => $username,
                'password' => Hash::make(\Str::random(8)),
                'user_type' => $request->user_type,
                'google_id' => $request->isGoogle === 1 ? $request->id : null,
                'facebook_id' => $request->isGoogle === 0 ? $request->id : null
            ]);
        }


        $token =  $user->createToken( 'token-name', [ 'server:update' ] )->plainTextToken;
        $user->update( [
            'api_token' => $token
        ] );

        $success[ 'login_user_data' ] = $user;
        $success[ 'token' ] = $token;
        return $this->sendResponse( $success, 'Login Done' );
       
    }

}