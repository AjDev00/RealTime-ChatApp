<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;
    protected $fillable = [
        'message',
        'sender_id',
        'group_id',
        'receiver_id'
    ];

    //relationship between the sender and the message(this message that is being sent belongs to the user with the foreignKey).
    public function sender(){
        return $this->belongsTo(User::class, 'sender_id');
    }

    //relationship between the receiver and the message(this message should be received by the user with the foreignKey). 
    public function receiver(){
        return $this->belongsTo(User::class, 'receiver_id');
    }

    //relationship between the group and the message(this message sent belongs to this group)
    public function group(){
        return $this->belongsTo(Group::class);
    }

    //(a message can have multiple attachments).
    public function attachments(){
        return $this->hasMany(MessageAttachment::class);
    }
}
