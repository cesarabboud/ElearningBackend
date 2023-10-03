<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'profilepicture',
        'role_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];
    public function getRole(){
        return $this->belongsTo(Role::class,'role_id','id');
    }
    public function getCourses(){
        return $this->hasMany(Course::class);
    }
    public function getReviews(){
        return $this->hasMany(Review::class);
    }
    public function getCart(){
        return $this->hasOne(Cart::class);
    }
    public function getOrders(){
        return $this->hasMany(Order::class);
    }
    public function getReplies(){
        return $this->hasMany(Reply::class);
    }
    public function getQuestions(){
        return $this->hasMany(Question::class);
    }
    public function getAnswers(){
        return $this->hasMany(Answer::class);
    }
    public function getFavorites(){
        return $this->belongsToMany(Course::class,'favorites','user_id','course_id')->withPivot('dateAdded');
    }
    public function getQuizzes(){
        return $this->hasMany(Quiz::class);
    }
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
}
