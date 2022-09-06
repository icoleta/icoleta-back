<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Point extends Model
{
    use HasFactory;

    function collectableItems() {
        return $this->belongsToMany(Residuum::class, 'point_residuum');
    }

    public function getPathAttribute($path)
    {
        $url = Storage::url('public/'.$path);
        return $url;
    }
}
