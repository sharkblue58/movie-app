<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use App\Models\SocialEmail;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Requests\SocialLoginRequest;
use Illuminate\Support\Facades\Validator;

class SocialEmailController extends Controller
{
    /**
     * @OA\Post(
     *     path="/v1/auth/social-login",
     *     summary="Login using social providers like Google, Facebook, Twitter",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"access_token", "provider"},
     *             @OA\Property(property="access_token", type="string", example="ya29.a0AfH6SM..."),
     *             @OA\Property(property="provider", type="string", enum={"twitter", "google", "facebook"}, example="google")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful login",
     *         @OA\JsonContent(
     *             @OA\Property(property="access_token", type="string", example="eyJ0eXAiOiJKV..."),
     *             @OA\Property(property="refresh_token", type="string", example="eyJhbGciOiJI..."),
     *             @OA\Property(
     *                 property="user",
     *                 type="object",
     *                 example={
     *                     "id": 1,
     *                     "name": "John Doe",
     *                     "email": "john@example.com"
     *                 }
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Validation error"),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 example={
     *                     "provider": {"The selected provider is invalid."}
     *                 }
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error from provider or authentication failure",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Invalid credentials from provider.")
     *         )
     *     )
     * )
     */

    public function socialLogin(SocialLoginRequest $request)
    {
        $validator = Validator::make($request->all(), [
            'access_token' => 'required|string',
            'provider' => 'required|string|in:twitter,google,facebook',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $providerAccessToken = $request->get('access_token');
            $provider = $request->get('provider');
            $providerUser = Socialite::driver($provider)->userFromToken($providerAccessToken);
        } catch (Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ]);
        }

        if (filled($providerUser)) {
            $user = $this->findOrCreate($providerUser, $provider);
        } else {
            return response()->json([
                'message' => 'User not found',
            ]);
        }

        // Save the original TTL
        $originalTTL = config('jwt.ttl');

        // Temporarily set TTL to generate refresh token
        JWTAuth::factory()->setTTL(config('jwt.refresh_ttl'));
        $refreshToken = JWTAuth::fromUser($user);

        // Restore the original TTL
        JWTAuth::factory()->setTTL($originalTTL);
        $accessToken = JWTAuth::fromUser($user);

        return response()->json([
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'user' => $user,
        ]);
    }


    protected function findOrCreate($providerUser, $provider)
    {
        $linkedSocialAccount = SocialEmail::query()->where('provider', $provider)
            ->where('provider_id', $providerUser->getId())
            ->first();

        if ($linkedSocialAccount) {
            return $linkedSocialAccount->user;
        } else {

            $user = null;

            if ($email = $providerUser->getEmail()) {
                $user = User::query()->where('email', $email)->first();
            }

            if (! $user) {
                $user = User::query()->create([
                    'name' => $providerUser->getName(),
                    'email' => $providerUser->getEmail(),
                ]);
                $user->markEmailAsVerified();
            }

            $user->SocialEmail()->create([
                'provider_id' => $providerUser->getId(),
                'provider' => $provider,
                'avatar' => $providerUser->getAvatar(),
            ]);

            if ($user->roles->isEmpty()) {
                $user->assignRole('user');
            }

            return $user;
        }
    }
}
