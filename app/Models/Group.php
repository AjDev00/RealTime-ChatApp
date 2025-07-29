<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'description',
        'owner_id',
        'last_message_id'
    ];

    //defining relationship between groups and users(many users can belong to a group)
    public function users(){
        return $this->belongsToMany(User::class, 'group_users');
    }

    //a group hasMany messages.
    public function messages(){
        return $this->hasMany(Message::class);
    }

    //a group belongs to a user.
    public function owner(){
        return $this->belongsTo(User::class);
    }
}
