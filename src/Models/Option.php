<?php

namespace Netauratech\CoreCms\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'created_at'
    ];

    protected $primaryKey = 'key';
    protected $keyType = 'string';
    public $incrementing = false;
}