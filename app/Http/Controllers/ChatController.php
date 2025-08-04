<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ChatController extends Controller
{
    public function user(User $user)
    {
        $authUser = Auth::user();

        $conversations = Conversation::with(['user1', 'user2'])
            ->where('user_id1', $authUser->id)
            ->orWhere('user_id2', $authUser->id)
            ->get()
            ->map(function ($conv) use ($authUser) {
                $otherUser = $conv->user_id1 == $authUser->id ? $conv->user2 : $conv->user1;

                return [
                    'id' => $conv->id,
                    'name' => $otherUser->name,
                    'user_id' => $otherUser->id,
                    'is_group' => false,
                    'blocked_at' => null,
                    'last_message_date' => $conv->updated_at->toDateTimeString(),
                ];
            });

        $groups = $authUser->groups->map(function ($group) {
            return [
                'id' => $group->id,
                'name' => $group->name,
                'user_id' => null,
                'is_group' => true,
                'blocked_at' => null,
                'last_message_date' => $group->updated_at->toDateTimeString(),
            ];
        });

        $allConversations = $conversations->merge($groups);

        return Inertia::render('ChatLayout', [
            'conversation' => $allConversations,
            'selectedConversation' => [
                'id' => $user->id,
                'name' => $user->name,
                'user_id' => $user->id,
                'is_group' => false,
            ],
        ]);
    }

    public function group(Group $group)
    {
        $authUser = Auth::user();

        $conversations = Conversation::with(['user1', 'user2'])
            ->where('user_id1', $authUser->id)
            ->orWhere('user_id2', $authUser->id)
            ->get()
            ->map(function ($conv) use ($authUser) {
                $otherUser = $conv->user_id1 == $authUser->id ? $conv->user2 : $conv->user1;

                return [
                    'id' => $conv->id,
                    'name' => $otherUser->name,
                    'user_id' => $otherUser->id,
                    'is_group' => false,
                    'blocked_at' => null,
                    'last_message_date' => $conv->updated_at->toDateTimeString(),
                ];
            });

        $groups = $authUser->groups->map(function ($groupItem) {
            return [
                'id' => $groupItem->id,
                'name' => $groupItem->name,
                'user_id' => null,
                'is_group' => true,
                'blocked_at' => null,
                'last_message_date' => $groupItem->updated_at->toDateTimeString(),
            ];
        });

        $allConversations = $conversations->merge($groups);

        return Inertia::render('ChatLayout', [
            'conversation' => $allConversations,
            'selectedConversation' => [
                'id' => $group->id,
                'name' => $group->name,
                'is_group' => true,
            ],
        ]);
    }
}