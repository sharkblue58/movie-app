<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Requests\ForgetPasswordRequest;
use App\Http\Requests\UserLikedCategoriesRequest;

class UserController extends Controller
{
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
    public function storeUserLikedCategories(UserLikedCategoriesRequest $request)
    {

        $userId = auth('api')->id();
        $user = User::find($userId);


        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        $user->categories()->sync($request->category_ids);

        return response()->json([
            'message' => 'Categories synced successfully.',
        ], 201);
    }

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

    public function updateProfile(UpdateProfileRequest $request)
    {
        $userId = auth('api')->id();
        $user = User::find($userId);


        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        $user->update($request->all());
        return response()->json([
            'message' => 'Profile updated successfully.',
        ], 201);
    }

    public function getProfile(){
        $userId = auth('api')->id();
        $user = User::find($userId);


        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        return response()->json($user);
    }
}
