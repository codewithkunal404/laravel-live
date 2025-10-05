<?php

namespace App\Http\Controllers;

use App\Models\taskModel;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;

class TaskController extends Controller
{


    public function index()
    {
        return view('task');
    }

    public function getlist(Request $request)
    {
        $filter = $request->query('filter');
        $status = $request->query('task_status');

        $query = taskModel::orderBy('position');

        if (!empty($filter)) {
            $query->where(function ($q) use ($filter) {
                $q->where('title', 'like', "%{$filter}%")
                    ->orWhere('description', 'like', "%{$filter}%");
            });
        }

        if (isset($status) && $status != "-1") {
            $status = $status == "1" ? 1 : 0;
            $query->where('is_completed',$status);
        }

        $data = $query->paginate(5);

        return view('tasklist', compact('data'));
    }


    public function store(Request $request)
    {
        try {
            $request->validate([
                "task_id" => "nullable|integer",
                "title" => "required|string|min:5|max:20",
                "description" => "required|string|min:5|max:40",
            ]);

            $max_position = taskModel::max('position') ?? 0;

            if ($request->task_id) {
                $task = taskModel::find($request->task_id);

                if (!$task) {
                    return response()->json(["status" => false, "msg" => "Task not found"], 404);
                }

                $original = $task->getOriginal();

                $task->title = $request->title;
                $task->description = $request->description;

                if ($task->isClean(['title', 'description'])) {
                    return response()->json(["status" => false, "msg" => "No changes found"], 200);
                }

                $task->save();

                return response()->json(["status" => true, "msg" => "Task updated successfully"], 200);
            }

            taskModel::create([
                'title' => $request->title,
                'description' => $request->description,
                'position' => $max_position + 1,
                'created_at' => Carbon::now(),
            ]);

            return response()->json(["status" => true, "msg" => "Task added successfully"], 200);

        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    public function destroy(Request $request)
    {
        try {
            $request->validate(['id' => "required|integer|exists:tasks,id"], ['id.required=>"Task id not matched']);
            $user = taskModel::find($request->id);
            $user->delete();
            return response()->json(["status" => true, "msg" => "Task Deleted Successfully"], 200);

        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }

    }

    public function toggletask(Request $request)
    {
        try {
            $request->validate(['id' => "required|integer|exists:tasks,id"], ['id.required=>"Task id not Found']);
            $user = taskModel::find($request->id);
            $user->is_completed = !$user->is_completed;
            $user->save();
            return response()->json(["status" => true, "msg" => $user->is_completed ? "Task Marked Complete Successfully" : "Task Marked InComplete Successfully "], 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }

    }
    public function taskEdit(Request $request)
    {
        try {
            $request->validate(['id' => "required|integer|exists:tasks,id"], ['id.required=>"Task id not Found']);
            $user = taskModel::where('id', $request->id)->first();
            return response()->json($user, 200);

        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }

    }

 public function reorder(Request $request)
{
    $order = $request->input('order'); 

    if (!is_array($order)) {
        return response()->json(['status' => false, 'msg' => 'Invalid data'], 400);
    }

    foreach ($order as $item) {
        if (isset($item['id'], $item['position'])) {
            taskModel::where('id', $item['id'])
                ->update(['position' => $item['position']]);
        }
    }

    return response()->json(['status' => true, 'msg' => 'Task order updated']);
}


}
