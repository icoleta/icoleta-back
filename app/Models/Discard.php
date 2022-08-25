<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discard extends Model
{
    use HasFactory;

    public function person() {
        return $this->belongsTo(Person::class, 'person_id', 'id');
    }

    public function point() {
        return $this->belongsTo(Point::class, 'point_id', 'id');
    }

    public function residuum() {
        return $this->belongsTo(Residuum::class, 'residuum_id', 'id');
    }
}
