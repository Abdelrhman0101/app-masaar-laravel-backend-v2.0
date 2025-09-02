<?php

// app/Models/AppSetting.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppSetting extends Model
{
    protected $table = 'app_settings';
    protected $guarded = [];
    public $timestamps = false;
}
