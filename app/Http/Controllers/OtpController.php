<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Mail\SendMail;
use Illuminate\Http\Request;
use App\Http\Requests\OTPRequest;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use App\Http\Requests\PasswordResetRequest;


/**
 * @OA\Tag(
 *     name="OTP",
 *     description="Endpoints for OTP verification , password reset and forgot password."
 * )
 */
class OtpController extends Controller
{

    /**
     * @OA\Post(
     *     path="/v1/auth/send-otp",
     *     tags={"OTP"},
     *     summary="Send OTP to user email",
     *     description="Generates a 6-digit OTP and sends it to the provided email address.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email"},
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OTP sent successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="OTP sent successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */

    public function sendOtp(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $otp = rand(100000, 999999);
        Cache::put('otp_' . $request->email, $otp, now()->addMinutes(3));

        $data = ['subject' => "OTP Verification", 'otp' => $otp];
        Mail::to($request->email)->queue(new SendMail($data));

        return response()->json(['message' => 'OTP sent successfully']);
    }

    /**
     * @OA\Post(
     *     path="/v1/auth/verify-otp",
     *     tags={"OTP"},
     *     summary="Verify OTP",
     *     description="Verifies the OTP entered by the user.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "otp"},
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *             @OA\Property(property="otp", type="integer", example=123456)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OTP verified successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="OTP verified successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid OTP",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Invalid OTP")
     *         )
     *     )
     * )
     */

    public function verifyOTP(OTPRequest $request)
    {


        $storedOtp = Cache::get('otp_' . $request->email);

        if ($storedOtp == $request->otp) {
            Cache::forget('otp_' . $request->email);
            return response()->json(['message' => 'OTP verified successfully']);
        }

        return response()->json(['error' => 'Invalid OTP'], 401);
    }
    /**
     * @OA\Post(
     *     path="/v1/auth/reset-password",
     *     tags={"OTP"},
     *     summary="Reset Password",
     *     description="Resets the user's password after OTP verification.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="newpassword123"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="newpassword123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Password reset successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Password reset successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Validation error")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User not found")
     *         )
     *     )
     * )
     */

    public function resetPassword(PasswordResetRequest $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        $user->update(['password' => $validated['password']]);

        return response()->json(['message' => 'Password reset successfully']);
    }
}
