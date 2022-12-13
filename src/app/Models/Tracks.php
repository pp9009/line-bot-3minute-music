<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tracks extends Model
{
    use HasFactory;

    public const UPDATED_AT = null;

    protected $fillable = [
        'external_url',
        'artists',
        'popularity',
        'duration_ms',
        'isrc',
    ];

    protected $table = 'tracks';
}
