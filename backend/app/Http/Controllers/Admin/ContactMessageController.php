<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

class ContactMessageController extends Controller
{
    /**
     * Display a listing of contact messages.
     */
    public function index(Request $request)
    {
        $status = $request->query('status', 'all');

        $query = ContactMessage::latest();

        if ($status === 'unread') {
            $query->where('status', 'unread');
        } elseif ($status === 'read') {
            $query->where('status', 'read');
        }

        $messages = $query->paginate(20)->withQueryString();

        return view('admin.contact_messages.index', compact('messages', 'status'));
    }

    /**
     * Display the specified contact message.
     */
    public function show(ContactMessage $message)
    {
        if ($message->status === 'unread') {
            $message->update(['status' => 'read']);
        }

        return view('admin.contact_messages.show', compact('message'));
    }

    /**
     * Toggle read/unread status.
     */
    public function toggleRead(ContactMessage $message)
    {
        $newStatus = $message->status === 'unread' ? 'read' : 'unread';
        $message->update(['status' => $newStatus]);

        return redirect()->back()->with('success', 'Status pesan berhasil diubah.');
    }

    /**
     * Remove the specified contact message from storage.
     */
    public function destroy(ContactMessage $message)
    {
        $message->delete();

        return redirect()->route('admin.contact-messages.index')->with('success', 'Pesan berhasil dihapus.');
    }
}
