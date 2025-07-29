<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Message>
 */
class MessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $senderId = $this->faker->randomElement([0, 1]);
        //if the senderId is 0, pick a random id from the users table that is not 1 and make it the senderId, then receiverId = 1
        if($senderId === 0){
            $senderId = $this->faker
            ->randomElement(\App\Models\User::where('id', '!=', 1)
            ->pluck('id')
            ->toArray());
            $receiverId = 1;
        } else{
            $receiverId = $this->faker->randomElement(\App\Models\User::pluck('id')->toArray()); //else if the senderId is 1, pick a random user with another id that is not 1 to be the receiver.
        }

        $groupId = null;

        //50% of chance of it's been a group chat.
        if($this->faker->boolean(50)){
            $groupId = $this->faker->randomElement(\App\Models\Group::pluck('id')->toArray()); //pick a random id from the groups table to the groupId
            $group = \App\Models\Group::find($groupId); //find the group associated with the groupId
            $senderId = $this->faker->randomElement($group->users->pluck('id')->toArray()); //pick a random user id from the group to be the sender.
            $receiverId = null; //since it's a group chat, there's no one receiver.
        } 

        return [
            'sender_id' => $senderId,
            'receiver_id' => $receiverId,
            'group_id' => $groupId,
            'message' => $this->faker->realText(200),
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
