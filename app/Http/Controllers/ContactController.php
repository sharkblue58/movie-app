<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Contact;
use Illuminate\Http\Request;
use App\Traits\HasImageUpload;
use Illuminate\Support\Facades\Auth;


/**
 * @OA\Tag(
 *     name="Contacts",
 *     description="Endpoints for contact management."
 * )
 */
class ContactController extends Controller
{
    use HasImageUpload;

    /**
 * @OA\Post(
 *     path="/v1/auth/contacts",
 *     summary="Create a new contact (help or report) by authenticated user",
 *     description="Allows the authenticated user to submit a help or report contact message with optional image attachment.",
 *     operationId="storeContact",
 *     tags={"Contacts"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 required={"subject", "message", "type"},
 *                 @OA\Property(
 *                     property="subject",
 *                     type="string",
 *                     maxLength=255,
 *                     example="Login Issue"
 *                 ),
 *                 @OA\Property(
 *                     property="message",
 *                     type="string",
 *                     example="I can't log into my account."
 *                 ),
 *                 @OA\Property(
 *                     property="type",
 *                     type="string",
 *                     enum={"help", "report"},
 *                     example="help"
 *                 ),
 *                 @OA\Property(
 *                     property="attachment",
 *                     type="file",
 *                     description="Optional image attachment (jpeg, png, jpg, gif, svg)"
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Contact submitted successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Contact submitted successfully"),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="user_id", type="integer", example=5),
 *                 @OA\Property(property="subject", type="string", example="Login Issue"),
 *                 @OA\Property(property="message", type="string", example="I can't log into my account."),
 *                 @OA\Property(property="type", type="string", example="help or report"),
 *                 @OA\Property(property="attachment", type="string", example="storage/attachments/img_1234.jpg"),
 *                 @OA\Property(property="created_at", type="string", example="2025-04-17 12:34:56"),
 *                 @OA\Property(property="updated_at", type="string", example="2025-04-17 12:34:56"),
 *             )
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
 *         response=422,
 *         description="Validation Error",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="The given data was invalid."),
 *             @OA\Property(
 *                 property="errors",
 *                 type="object",
 *                 example={"subject": {"The subject field is required."}}
 *             )
 *         )
 *     )
 * )
 */

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|in:help,report',
            'attachment' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $user = User::find(Auth::id());
        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        $attachmentPath = null;

        if ($request->hasFile('attachment')) {
            $attachmentPath = $this->storeImage($request->file('attachment'), 'attachments');
        }

        $contact = Contact::create([
            'user_id' => $user->id,
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'type' => $validated['type'],
            'attachment' => $attachmentPath,
        ]);

        return response()->json([
            'message' => 'Contact submitted successfully',
            'data' => $contact,
        ], 201);
    }

    /**
 * @OA\Patch(
 *     path="/v1/auth/contacts/{id}/status",
 *     summary="Update the status of a contact",
 *     description="Allows authorized users to update the status of a contact to 'read' or 'unread'. Requires the 'edit contacts' permission.",
 *     operationId="updateContactStatus",
 *     tags={"Contacts"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID of the contact to update",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"status"},
 *             @OA\Property(
 *                 property="status",
 *                 type="string",
 *                 enum={"read", "unread"},
 *                 example="read"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Contact status updated successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Contact status updated successfully"),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="user_id", type="integer", example=5),
 *                 @OA\Property(property="subject", type="string", example="Login Issue"),
 *                 @OA\Property(property="message", type="string", example="I can't log into my account."),
 *                 @OA\Property(property="type", type="string", example="help"),
 *                 @OA\Property(property="status", type="string", example="read"),
 *                 @OA\Property(property="attachment", type="string", example="storage/attachments/img_1234.jpg"),
 *                 @OA\Property(property="created_at", type="string", example="2025-04-17 12:34:56"),
 *                 @OA\Property(property="updated_at", type="string", example="2025-04-17 12:45:00")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation Error",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="The given data was invalid."),
 *             @OA\Property(
 *                 property="errors",
 *                 type="object",
 *                 example={"status": {"The status field is required."}}
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Contact not found",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="No query results for model [App\\Models\\Contact] 1")
 *         )
 *     )
 * )
 */

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:read,unread',
        ]);
    
        $contact = Contact::findOrFail($id);
        $contact->status = $request->status;
        $contact->save();
    
        return response()->json([
            'message' => 'Contact status updated successfully',
            'data' => $contact,
        ]);
    }
    

}
