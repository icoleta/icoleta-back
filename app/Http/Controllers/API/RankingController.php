<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Courses;
use App\Models\Department;
use App\Models\Discard;
use App\Models\Point;
use App\Models\Semester;
use Database\Seeders\SemesterSeeder;
use Illuminate\Http\Request;

class RankingController extends Controller
{
    public function ranking(Request $request){
        if($request->category == 'weight'){
            $weight = 0;

            if($request->type && $request->type == 'semester'){
                $semester = Semester::with(['people' => function($query){ return $query->with('discards');}])->get();
    
                $semester->each(function ($semester) use ($weight){
                    
                    foreach($semester->people as $person){
                        $personDiscardWeight = 0;
                        foreach($person->discards as $discard){
                            $personDiscardWeight += $discard->weight;
                        }
                        $weight += $personDiscardWeight;
                    }
                    unset($semester->people);
                    $semester->sum = $weight;
                    $weight = 0;
                });
    
                return response()->json(['data'=> ['semester' =>$semester]]);
            }elseif($request->type && $request->type == 'course'){
                $courses = Courses::with(['people' => function($query){ return $query->with('discards');}])->get();
    
                $courses->each(function ($course) use ($weight){
                    
                    foreach($course->people as $person){
                        $personDiscardWeight = 0;
                        foreach($person->discards as $discard){
                            $personDiscardWeight += $discard->weight;
                        }
                        $weight += $personDiscardWeight;
                    }
                    unset($course->people);
                    $course->sum = $weight;
                    $weight = 0;
                });
    
                return response()->json(['data'=> ['courses' =>$courses]]);
    
            }else {
                $ranking = Discard::with(['person' => function($query){ return $query->with('course');}])->selectRaw('person_id, sum(weight) as sum')->orderBy('sum', 'desc')->groupBy('person_id')->get();            
    
                return response()->json(['data'=> ['ranking' =>$ranking]]);
            }
        }else{
            $count = 0;

            if($request->type && $request->type == 'semester'){
                $semester = Semester::with(['people' => function($query){ return $query->with('discards');}])->get();
    
                $semester->each(function ($semester) use ($count){
                    
                    foreach($semester->people as $person){
                        $count += count($person->discards);
                    }
                    unset($semester->people);
                    $semester->sum = $count;
                    $count = 0;
                });
    
                return response()->json(['data'=> ['semester' =>$semester]]);
            }elseif($request->type && $request->type == 'course'){
                $courses = Courses::with(['people' => function($query){ return $query->with('discards');}])->get();
    
                $courses->each(function ($course) use ($count){
                    
                    foreach($course->people as $person){
                        $count += count($person->discards);
                    }
                    unset($course->people);
                    $course->sum = $count;
                    $count = 0;
                });
    
                return response()->json(['data'=> ['courses' =>$courses]]);
    
            }else {
                $ranking = Discard::with(['person' => function($query){ return $query->with('course');}])->selectRaw('person_id, count(weight) as sum')->orderBy('sum', 'desc')->groupBy('person_id')->get();            
    
                return response()->json(['data'=> ['ranking' =>$ranking]]);
            }
        }
    }
}
