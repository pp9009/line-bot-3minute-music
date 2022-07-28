<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Music extends Model
{
    use HasFactory;

    public const CREATED_AT = 'register_date';
    public const UPDATED_AT = null;

    protected $fillable = [
        'uri',
        'artists',
        'popularity',
        'duration_ms',
        'isrc',
        'register_date',
    ];

    protected $table = 'music_data';

    protected $primaryKey = 'id';
}
