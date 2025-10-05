<?php

use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect()->route('tasks.view');
});

Route::get('/task-list',[TaskController::class,'index'])->name("tasks.view");
Route::get('/task-list-data',[TaskController::class,'getlist'])->name("tasks.list");
Route::post('/task-save',[TaskController::class,'store'])->name("tasks.store");
Route::post('/task-delete',[TaskController::class,'destroy'])->name("tasks.delete");
Route::post('/task-toggle',[TaskController::class,'toggletask'])->name("tasks.toggle");
Route::post('/task-edit',[TaskController::class,'taskEdit'])->name("tasks.edit");
Route::post('/tasks/reorder', [TaskController::class, 'reorder'])->name('tasks.reorder');
