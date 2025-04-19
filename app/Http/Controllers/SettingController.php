<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Tag(
 *     name="Settings",
 *     description="Endpoints for user settings."
 * )
 */
class SettingController extends Controller
{
    /**
     * @OA\Get(
     *     path="/v1/auth/settings/user",
     *     summary="Get current user's settings",
     *     description="Fetch the settings of the currently authenticated user.",
     *     tags={"Settings"},
     *     security={{"bearerAuth": {}}},
     *     responses={
     *         @OA\Response(
     *             response=200,
     *             description="Successfully fetched the user's settings",
     *             @OA\JsonContent(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", description="Setting ID"),
     *                 @OA\Property(property="user_id", type="integer", description="User ID associated with the setting"),
     *                 @OA\Property(property="is_light_mode", type="boolean", description="Indicates if light mode is enabled"),
     *                 @OA\Property(property="is_full_screen", type="boolean", description="Indicates if full-screen mode is enabled"),
     *                 @OA\Property(property="is_notifiable", type="string", description="Notification preference (e.g., 'yes', 'no')"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", description="Creation timestamp"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", description="Last update timestamp")
     *             )
     *         ),
     *         @OA\Response(
     *             response=401,
     *             description="Unauthorized - Invalid or missing token",
     *         ),
     *         @OA\Response(
     *             response=404,
     *             description="User not found",
     *         )
     *     }
     * )
     */
    public function currentUserSetting()
    {
        $user = User::find(Auth::id());
        $setting = $user->setting;
        return response()->json($setting);
    }


    /**
     * @OA\Patch(
     *     path="/v1/auth/settings/toggle/{key}",
     *     summary="Toggle user setting",
     *     description="Toggles a specified user setting (like light mode, fullscreen, notifications). If the setting does not exist, it returns an error.",
     *     operationId="toggleSetting",
     *     tags={"Settings"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="key",
     *         in="path",
     *         required=true,
     *         description="The key of the setting to toggle (e.g., 'is_light_mode', 'is_full_screen', 'is_notifiable')",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Setting toggled successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="is_light_mode toggled successfully."),
     *             @OA\Property(property="setting", type="object", 
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="user_id", type="integer", example=1),
     *                 @OA\Property(property="is_light_mode", type="boolean", example=true),
     *                 @OA\Property(property="is_full_screen", type="boolean", example=false),
     *                 @OA\Property(property="is_notifiable", type="boolean", example=true),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-04-17T10:00:00Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-04-17T10:10:00Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid setting key",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="Invalid setting key.")
     *         )
     *     )
     * )
     */
    public function toggleSetting($key)
    {

        $user = User::find(Auth::id());

        $defaultValues = [
            'is_light_mode' => true,
            'is_full_screen' => false,
            'is_notifiable' => true,
        ];

        if (!array_key_exists($key, $defaultValues)) {
            return response()->json(['error' => 'Invalid setting key.'], 400);
        }

        $settings = $user->setting ?? $user->setting()->create($defaultValues);

        $settings->$key = !$settings->$key;
        $settings->save();

        return response()->json([
            'message' => "$key toggled successfully.",
            'setting' => $settings
        ]);
    }
}
