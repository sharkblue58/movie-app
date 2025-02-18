<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Requests\SocialLoginRequest;
use App\Models\SocialEmail;

class SocialEmailController extends Controller
{
    public function socialLogin(SocialLoginRequest $request)
    {

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
