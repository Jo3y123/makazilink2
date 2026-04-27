<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class MessageController extends Controller
{
    public function index()
    {
        $conversations = Tenant::with('user')
            ->whereHas('messages')
            ->get()
            ->map(function ($tenant) {
                $lastMessage = Message::where('tenant_id', $tenant->id)
                    ->latest()
                    ->first();
                $unread = Message::where('tenant_id', $tenant->id)
                    ->where('is_read', false)
                    ->whereHas('sender', fn($q) => $q->where('role', 'tenant'))
                    ->count();
                $tenant->last_message = $lastMessage;
                $tenant->unread_count = $unread;
                return $tenant;
            })
            ->sortByDesc(fn($t) => $t->last_message?->created_at)
            ->values();

        return view('messages.index', compact('conversations'));
    }

    public function show(Tenant $tenant)
    {
        Message::where('tenant_id', $tenant->id)
            ->where('is_read', false)
            ->whereHas('sender', fn($q) => $q->where('role', 'tenant'))
            ->update(['is_read' => true]);

        $messages = Message::where('tenant_id', $tenant->id)
            ->with('sender')
            ->oldest()
            ->get();

        return view('messages.show', compact('tenant', 'messages'));
    }

    public function reply(Request $request, Tenant $tenant)
    {
        $request->validate([
            'body' => 'nullable|string|max:1000',
            'file' => 'nullable|file|max:5120|mimes:jpg,jpeg,png,gif,webp,pdf,doc,docx',
        ]);

        $filePath = null;
        $fileName = null;
        $fileType = null;

        if ($request->hasFile('file')) {
            $file     = $request->file('file');
            $filePath = $file->store('messages', 'public');
            $fileName = $file->getClientOriginalName();
            $fileType = $file->getMimeType();
        }

        if (!$request->body && !$filePath) {
            return back()->with('error', 'Please enter a message or attach a file.');
        }

        Message::create([
            'sender_id'   => auth()->id(),
            'receiver_id' => $tenant->user_id,
            'tenant_id'   => $tenant->id,
            'body'        => $request->body,
            'file_path'   => $filePath,
            'file_name'   => $fileName,
            'file_type'   => $fileType,
            'is_read'     => false,
        ]);

        return back()->with('success', 'Reply sent.');
    }

    public function tenantInbox()
    {
        $user   = auth()->user();
        $tenant = $user->tenant;

        if (!$tenant) {
            return view('messages.tenant', ['messages' => collect(), 'tenant' => null]);
        }

        Message::where('tenant_id', $tenant->id)
            ->where('receiver_id', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $messages = Message::where('tenant_id', $tenant->id)
            ->with('sender')
            ->oldest()
            ->get();

        return view('messages.tenant', compact('messages', 'tenant'));
    }

    public function tenantSend(Request $request)
    {
        $request->validate([
            'body' => 'nullable|string|max:1000',
            'file' => 'nullable|file|max:5120|mimes:jpg,jpeg,png,gif,webp,pdf,doc,docx',
        ]);

        $user   = auth()->user();
        $tenant = $user->tenant;

        if (!$tenant) {
            return back()->with('error', 'Tenant profile not found.');
        }

        $filePath = null;
        $fileName = null;
        $fileType = null;

        if ($request->hasFile('file')) {
            $file     = $request->file('file');
            $filePath = $file->store('messages', 'public');
            $fileName = $file->getClientOriginalName();
            $fileType = $file->getMimeType();
        }

        if (!$request->body && !$filePath) {
            return back()->with('error', 'Please enter a message or attach a file.');
        }

        $staff = User::whereIn('role', ['admin', 'caretaker'])->first();

        Message::create([
            'sender_id'   => $user->id,
            'receiver_id' => $staff ? $staff->id : $user->id,
            'tenant_id'   => $tenant->id,
            'body'        => $request->body,
            'file_path'   => $filePath,
            'file_name'   => $fileName,
            'file_type'   => $fileType,
            'is_read'     => false,
        ]);

        return back()->with('success', 'Message sent successfully.');
    }

    public function unreadCount()
    {
        $count = Message::where('is_read', false)
            ->whereHas('sender', fn($q) => $q->where('role', 'tenant'))
            ->count();

        return response()->json(['count' => $count]);
    }
}