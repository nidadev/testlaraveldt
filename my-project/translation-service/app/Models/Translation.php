<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Translation extends Model
{
    use HasFactory; // ✅ This is required

    protected $fillable = ['key', 'value'];

    protected $casts = [
        'value' => 'array',
    ];

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }
}