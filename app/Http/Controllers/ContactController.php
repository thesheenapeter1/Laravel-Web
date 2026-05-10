<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;


class ContactController extends Controller
{
    public function index()
    {
        return view('contact');
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'message' => 'required|string'
        ]);

        $name = auth()->check() ? auth()->user()->name : 'Guest';

        $message = Message::create([
            'user_id' => auth()->id(),
            'name' => $name,
            'email' => $request->email,
            'message' => $request->message,
        ]);

        // Send Email Notification
        try {
            \Illuminate\Support\Facades\Mail::raw("New Message Received from: {$name} ({$request->email})\n\nMessage:\n{$request->message}", function ($mail) {
                $mail->to('thesheenapeter2006@gmail.com')
                     ->subject('New Contact Message - Aura by Kiyara');
            });
        } catch (\Exception $e) {
            // Log error or ignore if mail server not configured
            \Illuminate\Support\Facades\Log::error('Mail sending failed: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Thank you for your message! We will get back to you soon.');
    }

    public function userMessages()
    {
        $messages = Message::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();
        return view('user-messages', compact('messages'));
    }
}
