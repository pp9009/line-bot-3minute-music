<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tracks extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'external_url',
        'artists',
        'popularity',
        'duration_ms',
        'isrc',
    ];

    protected $table = 'tracks';

    protected $primaryKey = 'external_url';
}
