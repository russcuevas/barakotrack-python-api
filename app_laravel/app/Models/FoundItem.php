<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FoundItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'description',
        'date_found',
        'location',
        'storage_location',
        'image_path',
        'feature_vector',
        'status'
    ];

    protected $casts = [
        'feature_vector' => 'array',
        'date_found' => 'date'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function claims()
    {
        return $this->hasMany(Claim::class);
    }
}
