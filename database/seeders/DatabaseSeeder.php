<?php

namespace Database\Seeders;

use App\Models\Conversation;
use App\Models\Group;
use App\Models\Message;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
            'is_admin' => true,
        ]);
        User::factory()->create([
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => bcrypt('password'),
        ]);
        User::factory(10)->create(); //12 users in total(john, jane + 10more)
        
        for($i = 0; $i < 5; $i++){
            $group = Group::factory()->create([
                'owner_id' => 1,
            ]); //create 5 groups making user with id 1 the owner.

            $users = User::inRandomOrder()->limit(rand(2, 5))->pluck('id'); //pick 2 to 5 random users to join the group.
            $group->users()->attach(array_unique([1, ...$users])); //add the 2 to 5 users to the group including the owner with id 1(array_unique ensures there are no duplicates).
        }

        Message::factory(1000)->create(); //create 1000 fake messages(some personal, some group).
        $messages = Message::whereNull('group_id')->orderBy('created_at')->get(); //get all the messages where the group id is null(select all 1-to-1 messages).

        $conversations = $messages->groupBy(function ($message){
            return collect([$message->sender_id, $message->receiver_id])->sort()->implode('_'); //collect the two id's(sender and receiver), sort and implode.
        })
        ->map(function ($groupedMessages) {
            return [
                'user_id1' => $groupedMessages->first()->sender_id,
                'user_id2' => $groupedMessages->first()->receiver_id,
                'last_message_id' => $groupedMessages->last()->id,
                'created_at' => new Carbon(),
                'updated_at' => new Carbon(),
            ];
        })->values();

        Conversation::insertOrIgnore($conversations->toArray()); //insert all conversations into the database. if a conversation already exists, skip it.
    }
}
