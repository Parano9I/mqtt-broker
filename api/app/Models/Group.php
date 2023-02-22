<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'role'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'groups_users')
            ->withPivot('role_id')->withTimestamps()->using(GroupUser::class);
    }

    public function user(User $user)
    {
        return $this->users()->find($user);
    }

    public function getUserRole(User $user)
    {
        return $this->user($user)->pivot->role_id;
    }
}
