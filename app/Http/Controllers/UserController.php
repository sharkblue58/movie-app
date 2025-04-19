<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\HasImageUpload;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Requests\ForgetPasswordRequest;


/**
 * @OA\Tag(
 *     name="User",
 *     description="Endpoints for user management."
 * )
 */
class UserController extends Controller
{
    use HasImageUpload;

    /**
     * @OA\Get(
     *     path="/v1/auth/users/categories",
     *     summary="Get categories personalized by the authenticated user",
     *     description="Returns a list of categories liked by the authenticated user.",
     *     operationId="userLikedCategories",
     *     tags={"User"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Category")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User not found.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function userLikedCategories()
    {
        $userId = auth('api')->id();
        $user = User::find($userId);

        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }
        $categories = $user->categories()->get();
        return response()->json($categories);
    }


    /**
     * @OA\Post(
     *     path="/forget-password",
     *     tags={"User"},
     *     summary="Change the authenticated user's password",
     *     description="Allows an authenticated user to change their password by providing the current and new password.",
     *     operationId="forgetPassword",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"current_password", "new_password", "new_password_confirmation"},
     *             @OA\Property(property="current_password", type="string", format="password", minLength=8, example="oldpassword123"),
     *             @OA\Property(property="new_password", type="string", format="password", minLength=8, example="newpassword123"),
     *             @OA\Property(property="new_password_confirmation", type="string", format="password", example="newpassword123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Password changed successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Password changed successfully.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found or current password is incorrect.",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User not found.")
     *         )
     *     )
     * )
     */
    public function forgetPassword(ForgetPasswordRequest $request)
    {
        $userId = auth('api')->id();
        $user = User::find($userId);


        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['message' => 'Current password is incorrect.'], 404);
        }

        $user->update(['password' => $request->new_password]);
        return response()->json([
            'message' => 'Password changed successfully.',
        ], 201);
    }
    /**
     * @OA\Post(
     *     path="/v1/auth/profile",
     *     summary="Update authenticated user's profile",
     *     description="Updates the profile of the currently authenticated user, including optional image upload.",
     *     operationId="updateUserProfile",
     *     tags={"User"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *        
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"name", "phone", "gender", "birth_date", "address"},
     *                 @OA\Property(
     *                     property="name",
     *                     type="string",
     *                     maxLength=50,
     *                     example="Ahmed Mohamed"
     *                 ),
     *                 @OA\Property(
     *                     property="phone",
     *                     type="string",
     *                     maxLength=20,
     *                     example="+20123456789"
     *                 ),
     *                 @OA\Property(
     *                     property="gender",
     *                     type="string",
     *                     enum={"male", "female"},
     *                     example="male"
     *                 ),
     *                 @OA\Property(
     *                     property="birth_date",
     *                     type="string",
     *                     format="date",
     *                     example="1995-05-15"
     *                 ),
     *                 @OA\Property(
     *                     property="address",
     *                     type="string",
     *                     maxLength=255,
     *                     example="123 Tahrir Street, Cairo"
     *                 ),
     *                 @OA\Property(
     *                     property="profile_image",
     *                     type="string",
     *                     format="binary",
     *                     description="Optional profile image (jpeg, png, webp max: 2MB)"
     *                 )
     *             )
     *         )
     *     ),

     *     @OA\Response(
     *         response=201,
     *         description="Profile updated successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Profile updated successfully.")
     *         )
     *     ),

     *     @OA\Response(
     *         response=404,
     *         description="User not found.",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User not found.")
     *         )
     *     ),

     *     @OA\Response(
     *         response=422,
     *         description="Validation errors",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 additionalProperties=@OA\Property(type="array", @OA\Items(type="string"))
     *             )
     *         )
     *     )
     * )
     */

    public function updateProfile(UpdateProfileRequest $request)
    {
        $validated = $request->validated();
        $userId = auth('api')->id();
        $user = User::find($userId);
        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        if ($request->hasFile('profile_image')&& $request->file('profile_image')->isValid()) {
            $image = $request->file('profile_image');
            $path = $this->storeImage($image, 'users');
            $validated['profile_image'] = $path;
            $userOldImage = $user->profile_image;
            if ($userOldImage) {
                $this->deleteImage($userOldImage);
            }
        }
        $user->update($validated);
        return response()->json([
            'message' => 'Profile updated successfully.',
        ], 201);
    }
    /**
     * @OA\Get(
     *     path="/v1/auth/profile",
     *     summary="Get authenticated user profile",
     *     tags={"User"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful response with user profile",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=4),
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="profile_image", type="string", nullable=true, example=null),
     *             @OA\Property(property="email", type="string", example="john@example.com"),
     *             @OA\Property(property="birth_date", type="string", format="date", nullable=true, example=null),
     *             @OA\Property(property="gender", type="string", nullable=true, example=null),
     *             @OA\Property(property="phone", type="string", nullable=true, example=null),
     *             @OA\Property(property="address", type="string", nullable=true, example=null),
     *             @OA\Property(property="email_verified_at", type="string", format="date-time", nullable=true, example=null),
     *             @OA\Property(property="created_at", type="string", format="date-time", example="2025-04-16T17:03:21.000000Z"),
     *             @OA\Property(property="updated_at", type="string", format="date-time", example="2025-04-16T17:03:21.000000Z")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */

    public function getProfile()
    {
        $userId = auth('api')->id();
        $user = User::find($userId);


        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        return response()->json($user);
    }
}
