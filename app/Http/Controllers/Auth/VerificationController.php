<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class VerificationController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }

    public function verify(Request $request, User $user)
    {
        // check if urs is valid
        if (!URL::hasValidSignature($request)) {
            return response()->json([
                "errors" => [
                    "message" => "invalid verification link"
                ]
            ], 422);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                "errors" => [
                    "message" => "email address already verified"
                ]
            ], 422);
        }
        $user->markEmailAsVerified();
        event(new Verified($user));

        return response()->json([
            "message" => "Email succesfully verified"
        ], 200);
    }
    public function resend(Request $request)
    {
        $this->validate($request, [
            'email' => ['email', 'required']
        ]);
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json([
                "errors" => [
                    "message" => "no user could be found with this email"
                ]
            ], 422);
        }
        if ($user->hasVerifiedEmail()) {
            return response()->json([
                "errors" => [
                    "message" => "Email address already verified"
                ]
            ], 422);
        }
        $user->sendEmailVerificationNotification();

        return response()->json([
            "status" => "verification link resent"
        ]);
    }
}
