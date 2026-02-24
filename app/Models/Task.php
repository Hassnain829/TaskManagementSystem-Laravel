<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title', 'description', 'status', 'due_date', 'category_id', 'user_id'
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    // Relationships...
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    
    /**
     * 🔍 SCOPE FOR SEARCH - Yeh method query builder ko extend karta hai
     * Scope ka naam: scopeSearch → use: search()
     */
    public function scopeSearch($query, $searchTerm)
    {
        // Agar search term empty hai to kuch mat karo
        if (empty($searchTerm)) {
            return $query;
        }
        
        // Multiple columns mein search
        return $query->where(function($q) use ($searchTerm) {
            $q->where('title', 'LIKE', "%{$searchTerm}%")
              ->orWhere('description', 'LIKE', "%{$searchTerm}%")
              ->orWhere('id', 'LIKE', "%{$searchTerm}%")
              ->orWhere('status', 'LIKE', "%{$searchTerm}%");
              
            // Date check
            if (strtotime($searchTerm)) {
                $q->orWhere('due_date', 'LIKE', "%{$searchTerm}%");
            }
            
            // Category search
            $q->orWhereHas('category', function($catQuery) use ($searchTerm) {
                $catQuery->where('name', 'LIKE', "%{$searchTerm}%");
            });
        });
    }
    
    /**
     * 📊 SCOPE FOR STATUS FILTER
     */
    public function scopeFilterByStatus($query, $status)
    {
        if (!empty($status)) {
            return $query->where('status', $status);
        }
        return $query;
    }
    
    /**
     * 🗂️ SCOPE FOR CATEGORY FILTER
     */
    public function scopeFilterByCategory($query, $categoryId)
    {
        if (!empty($categoryId)) {
            return $query->where('category_id', $categoryId);
        }
        return $query;
    }
    
    /**
     * 📅 SCOPE FOR DATE RANGE
     */
    public function scopeDateFrom($query, $date)
    {
        if (!empty($date)) {
            return $query->whereDate('due_date', '>=', $date);
        }
        return $query;
    }
    
    public function scopeDateTo($query, $date)
    {
        if (!empty($date)) {
            return $query->whereDate('due_date', '<=', $date);
        }
        return $query;
    }
}