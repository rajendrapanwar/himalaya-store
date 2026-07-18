<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

class ContactMessageController extends Controller
{
    public function index(Request $request)
    {
        $messages = ContactMessage::when($request->status, fn ($query, $status) => $query->where('status', $status))->latest()->paginate(20)->withQueryString();

        return view('admin.contact-messages.index', compact('messages'));
    }

    public function update(Request $request, ContactMessage $contactMessage)
    {
        $data = $request->validate(['status' => 'required|in:new,in_progress,replied,closed']);
        $contactMessage->update($data + ['replied_at' => $data['status'] === 'replied' ? now() : $contactMessage->replied_at]);

        return back()->with('success', 'Contact request updated.');
    }

    public function destroy(ContactMessage $contactMessage)
    {
        $contactMessage->delete();

        return back()->with('success', 'Contact request deleted.');
    }
}
