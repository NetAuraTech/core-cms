<?php

namespace Netauratech\CoreCms\Tests\Stubs;

use Illuminate\Database\Eloquent\Model;

class DummyMedia extends Model
{
    protected $table = 'media_dummy';
    protected $guarded = [];
    public $timestamps = false;
}