<?php

namespace App\Http\Controllers;

use App\Models\UserInbox;
use Illuminate\Support\Facades\Auth;

class InboxController extends Controller
{
    public function index()
    {
        UserInbox::where('id_user', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $messages = UserInbox::where('id_user', Auth::id())
            ->latest('created_at')
            ->paginate(10);

        return view('user.inbox', compact('messages'));
    }
}
