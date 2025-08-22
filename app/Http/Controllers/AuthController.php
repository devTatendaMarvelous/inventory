<?php

namespace App\Http\Controllers;

use App\Models\PasswordReset;
use App\Models\User;
use App\Notifications\ForgotPasswordNotification;
use App\Traits\Core;
use App\Traits\HasApiResponses;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    use Core,HasApiResponses;

    public function login(Request $request)
    {
        $credentials = $request->only(['email', 'password']);

        if (!$token = auth()->attempt($credentials)) {
            return $this->unauthorizedResponseHandler();
        }

        return $this->successResponseHandler('User Logged in!', $this->respondWithToken($token));
    }

    public function register(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);
        if ($validator->fails()) {
            return $this->errorValidationResponseHandler('Validation failed', $validator->errors());
        }
        // Create the user
        $user = User::create([
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        // Generate JWT token for the user
        $token = auth()->login($user);

        return $this->successResponseHandler('User Registered Successfully!', $this->respondWithToken($token));
    }


    public function sendPasswordResetLink(Request $request)
    {

        $token = Str::random(60);
        if (!$request->filled('email') || !User::whereEmail($request->email)->exists()) {
            return $this->notFoundResponseHandler('User not found!');
        }
        DB::beginTransaction();
        try {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            // Store token in the password_resets table
            PasswordReset::create([
                'email' => $request->email,
                'token' => $token,
                'created_at' => now(),
            ]);


            // Find the user and send the reset notification
            $user = User::where('email', $request->email)->first();
            $user->notify(new ForgotPasswordNotification($token, $request->email));
            DB::commit();
            return $this->successResponseHandler('Password reset link sent.');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->errorResponseHandler('An error occurred while processing', $exception->getMessage());
        }

    }


    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'token' => 'required',
            'password' => 'required|confirmed|min:6',
        ]);
        if ($validator->fails()) {
            return $this->errorValidationResponseHandler('Validation failed', $validator->errors());
        }


        $reset = DB::table('password_reset_tokens')->where('email', $request->email)->where('token', $request->token)->first();

        if (!$reset) {
            return $this->errorValidationResponseHandler('Invalid token or email');
        }

        // Check if the token is expired
        if (Carbon::parse($reset->created_at)->addMinutes(60)->isPast()) {
            return $this->errorValidationResponseHandler('Token expired');
        }

        // Reset the user's password
        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        // Delete the token after it's used
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();
        return $this->successResponseHandler('Password reset successfully!');

    }

    /**
     * Log out the user (invalidate the token).
     */
    public function logout()
    {
        try {
            // Get the current user's token
            $token = JWTAuth::getToken();
            // Invalidate the token (blacklist it)
            JWTAuth::invalidate($token);
            return $this->successResponseHandler('Successfully logged out.');
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return $this->unauthorizedResponseHandler('Invalid Token');
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return $this->unauthorizedResponseHandler('Token Expired');
        } catch (\Exception $e) {
            return $this->unauthorizedResponseHandler('Token not found');
        }
    }
}
