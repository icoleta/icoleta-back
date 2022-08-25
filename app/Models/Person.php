<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    use HasFactory;
    
    public function course(){
        return $this->belongsTo(Courses::class, 'course_id', 'id');
    }
    
    public function semester(){
        return $this->belongsTo(Semester::class, 'semester_id', 'id');
    }

    public function discards(){
        return $this->hasMany(Discard::class, 'person_id', 'id');
    } 
}
