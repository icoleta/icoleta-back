<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discard extends Model
{
    use HasFactory;

    protected $table = 'discards';

    public function person(){
        return $this->belongsTo(Person::class, 'person_id', 'id');
    }
}
