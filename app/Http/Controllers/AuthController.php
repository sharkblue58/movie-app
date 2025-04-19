<?php

namespace App\Http\Controllers;


use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RefreshTokenRequest;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\TokenResource;
use App\Http\Requests\RegisterRequest;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Http\Middleware\RefreshToken;


/**
 * @OA\Tag(
 *     name="Authentication",
 *     description="Endpoints for user authentication and JWT token management."
 * )
 */

class AuthController extends Controller
{

    /**
     * @OA\Post(
     *     path="/v1/auth/register",
     *     tags={"Authentication"},
     *     summary="Register a new user",
     *     description="Creates a new user account and assigns a default role.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email", "password"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", example="john@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="secret123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User registered successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User registered successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */

    public function register(RegisterRequest $request)
    {

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
        ]);

        // Assign default role
        $user->assignRole('user');

        return response()->json(['message' => 'User registered successfully'], 201);
    }

    /**
     * @OA\Post(
     *     path="/v1/auth/login",
     *     tags={"Authentication"},
     *     summary="User login",
     *     description="Authenticates the user and returns JWT access and refresh tokens.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", example="john@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="secret123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="access_token", type="string", example="eyJhbGciOiJIUzI1NiIsInR5c..."),
     *             @OA\Property(property="refresh_token", type="string", example="eyJhbGciOiJIUzI1NiIsInR5c..."),
     *             @OA\Property(property="token_type", type="string", example="bearer"),
     *             @OA\Property(property="access_expires_in", type="integer", example=3600),
     *             @OA\Property(property="refresh_expires_in", type="integer", example=7200)
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - Invalid credentials"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */


    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        try {
            // Generate access token
            if (!$accessToken = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $user = User::find(JWTAuth::user()->id);

            // Save the original TTL
            $originalTTL = config('jwt.ttl');

            // Generate refresh token with extended expiration
            JWTAuth::factory()->setTTL(config('jwt.refresh_ttl'));
            $refreshToken = JWTAuth::fromUser($user);

            // Restore the original TTL
            JWTAuth::factory()->setTTL($originalTTL);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Could not create token: ' . $e->getMessage()], 500);
        }

        return response()->json([
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'token_type' => 'bearer',
            'access_expires_in' => config('jwt.ttl') * 60, // in seconds
            'refresh_expires_in' => config('jwt.refresh_ttl') * 60, // in seconds
        ]);
    }


    /**
     * @OA\Post(
     *     path="/v1/auth/logout",
     *     tags={"Authentication"},
     *     summary="User logout",
     *     description="Invalidates the JWT token and logs out the user.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Logout successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Successfully logged out")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Logout error"
     *     )
     * )
     */

    public function logout()
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
            return response()->json(['message' => 'Successfully logged out']);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Could not log out'], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/v1/auth/refresh",
     *     tags={"Authentication"},
     *     summary="Refresh JWT access token",
     *     description="Generates a new access token using a refresh token.",
     *      security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="New access token generated",
     *         @OA\JsonContent(
     *             @OA\Property(property="access_token", type="string", example="new_access_token_here"),
     *             @OA\Property(property="token_type", type="string", example="bearer"),
     *             @OA\Property(property="access_expires_in", type="integer", example=3600)
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Access token cannot be refreshed"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Token expired or invalid"
     *     )
     * )
     */

    public function refresh()
    {
        try {
            // Get the current token from the request
            $currentToken = JWTAuth::getToken();
            $payload = JWTAuth::getPayload($currentToken);

            // Check if the token is an access token or a refresh token
            $expiration = $payload->get('exp');
            $currentTime = now()->timestamp;

            if ($expiration < $currentTime) {
                return response()->json(['error' => 'Token has expired'], 401);
            }

            // Check if the token is a refresh token based on its expiration time
            $isRefreshToken = $expiration - $currentTime > config('jwt.ttl') * 60;

            if (!$isRefreshToken) {
                return response()->json(['error' => 'Access tokens cannot be refreshed'], 403);
            }

            // Generate a new access token
            $newAccessToken = JWTAuth::refresh($currentToken);

            return response()->json([
                'access_token' => $newAccessToken,
                'token_type' => 'bearer',
                'access_expires_in' => config('jwt.ttl') * 60 // in seconds
            ]);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Could not refresh token: ' . $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/v1/auth/me",
     *     tags={"Authentication"},
     *     summary="Get current authenticated user",
     *     description="Returns details of the currently authenticated user.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Authenticated user data",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", example="john@example.com")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - Token missing or invalid"
     *     )
     * )
     */


    public function me()
    {
        $user = Auth::user();
        return new UserResource($user);
    }
}
