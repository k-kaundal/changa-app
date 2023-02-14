<?php
   
namespace App\Http\Controllers\API;
   
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\Models\User;
use App\Models\VerificationCode;
use Carbon\Carbon;
use Illuminate\Support\Str;

class AuthController extends BaseController
{
    public function signin(Request $request)
    {
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){ 
            $authUser   = User::where('id', Auth::user()->id)->first();
            $token =  $authUser->createToken('token-name', ['server:update'])->plainTextToken;
            $authUser->update([
                'api_token' => $token
            ]);
            $success['token'] =  $authUser->api_token; 
            $success['first_name'] =  $authUser->first_name;
            $success['user_data'] = $authUser;
            return $this->sendResponse($success, 'User signed in');
        } 
        else{ 
            return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);
        } 
    }
    public function signup(Request $request)
    {
        // $table->string('first_name');
        // $table->string('last_name')->nullable();
        // $table->string('username')->unique();
        // $table->string('customer_id')->nullable()->nullable();
        // $table->string('email')->unique();
        // $table->string('profile_pic')->nullable();
        // $table->string('background_pic')->nullable();
        // $table->string('website')->nullable();
        // $table->string('address')->nullable();
        



        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'username' => 'required|unique:users',
            'mobile_no' =>'required',
            'email' => 'required|email',
            'password' => 'required',
            'confirm_password' => 'required|same:password',
        ]);
   
        if($validator->fails()){
            return $this->sendError('Error validation', $validator->errors());       
        }
         

        $input = $request->all();
        $input['password'] =  \Hash::make($input['password']);
        
        $user = User::create($input);
        $token = $user->createToken('token-name', ['server:update'])->plainTextToken;
        $user->update([
            'api_token' => $token
        ]);
        $verification_code = $this->generateOtp($user->mobile_no);
        $otp =  $this->sendSmsNotificaition($request->mobile_no,$verification_code->otp);
        $success['otp_message']=$otp;
        $success['user_id']= $user->id;
        $success['token']=$user->api_token;
        return $this->sendResponse($success, 'User created successfully.');
    }


    public function generateOtp($mobile_no)
    {
        $user = User::where('mobile_no', $mobile_no)->first();

        # User Does not Have Any Existing OTP
        $verificationCode = VerificationCode::where('user_id', $user->id)->latest()->first();

        $now = Carbon::now();

        if($verificationCode && $now->isBefore($verificationCode->expire_at)){
            return $verificationCode;
        }

        // Create a New OTP
        return VerificationCode::create([
            'user_id' => $user->id,
            'otp' => rand(123456, 999999),
            'expire_at' => Carbon::now()->addMinutes(10)
        ]);
    }


    public function otpVerification(Request $request)
   {
    #Validation
    $request->validate([
        'user_id' => 'required|exists:users,id',
        'otp' => 'required'
    ]);

    #Validation Logic
    $verificationCode   = VerificationCode::where('user_id', $request->user_id)->where('otp', $request->otp)->first();

    $now = Carbon::now();
    if (!$verificationCode) {
        return $this->sendError('Error','Your OTP is not correct.');
    }elseif($verificationCode && $now->isAfter($verificationCode->expire_at)){
        return $this->sendError('Error','Your OTP has been expired.');
    }

    $user = User::whereId($request->user_id)->first();

    if($user){
        // Expire The OTP
        $verificationCode->update([
            'expire_at' => Carbon::now()
        ]);
        $success['user_data'] = $user;
        return $this->sendResponse($success,'Your OTP verifictation completed.');
    }
}

   
}