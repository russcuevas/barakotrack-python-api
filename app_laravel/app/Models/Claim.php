<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Claim extends Model
{
    use HasFactory;

    protected $fillable = [
        'lost_item_id',
        'found_item_id',
        'user_id',
        'proof_description',
        'proof_image',
        'status',
        'admin_notes',
        'verified_by'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function foundItem()
    {
        return $this->belongsTo(FoundItem::class);
    }

    public function lostItem()
    {
        return $this->belongsTo(LostItem::class);
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
