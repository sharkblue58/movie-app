<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Contact;
use Illuminate\Http\Request;
use App\Http\Requests\ContactStoreRequest;

class ContactController extends Controller
{
    public function allContacts()
    {
        $contacts = Contact::all();
        return response()->json($contacts);
    }

    public function storeContact(ContactStoreRequest $request)
    {
        $userId = auth('api')->id();
        $user = User::find($userId);


        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        $request['status'] = 'unread';

        $user->contacts()->create($request->all());
        
    }

    public function setContactStatus(Request $request)
    {

        $request->validate([
            'status' => 'required|in:read,unread',
        ]);
        
        $contact = Contact::find($request->userId);
        $status = $request->status;
        if (!$contact) {
            return response()->json(['message' => 'Contact not found.'], 404);
        }

       $contact->update(['status' => $status]);
       return response()->json(["message"=>"Status updated successfully","contact"=>$contact->refresh()]);
    }
}
