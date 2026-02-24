<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory; // Factory trait - testing ke liye fake data

    
    
    // Kaun se fields mass assign ho sakte hain?
    // Security feature - hacker extra fields na bhej de
    protected $fillable = ['name', 'user_id'];

        /**
     * RELATIONSHIPS - Tables ke beech connection
     */
    // Relationship: Category belongs to User
    // categories table mein user_id foreign key hai
    public function user()
    {
        return $this->belongsTo(User::class);
         // SQL: SELECT * FROM users WHERE id = categories.user_id
    }

    // Relationship One-to-Many: Category has many Tasks
    
    public function tasks()
    {
        return $this->hasMany(Task::class);
        // SQL: SELECT * FROM tasks WHERE category_id = categories.id
    }
}