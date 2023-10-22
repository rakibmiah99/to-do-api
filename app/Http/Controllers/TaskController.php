<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class TaskController extends Controller
{
    function create(Request $request){
        $validator = Validator::make($request->all(),[
            'name' => 'required|string|unique:tasks,name',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'start_time' => 'nullable|date_format:H:i:s',
            'end_time' => 'nullable|date_format:H:i:s',
        ]);


        if ($validator->fails()){
            return response([
                'success' => false,
                'message' => 'validation error!',
                'errors' => validationFormatter($validator->errors())
            ], 422);
        }
        else {
            try {
                $data = $request->only(['name', 'start_date', 'end_date', 'start_time', 'end_time', 'priority', 'card_id']);
                DB::beginTransaction();
                Task::create($data);
                DB::commit();
                return response([
                    'success' => true,
                    'message' => 'created.',
                ]);
            }
            catch (\Exception $exception){
                return response([
                    'success' => false,
                    'message' => 'created failed.',
                    'error' => [
                        'name' =>  $exception->getMessage()
                    ]
                ], 500);
            }
        }
    }

    function getAll(){
        return Task::with('user:id,name', 'card:id,name')->get();
    }
}
