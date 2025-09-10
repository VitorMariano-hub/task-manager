<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\Auth\AuthController;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

Route::post('login', [AuthController::class, 'login']); 

Route::middleware('auth:sanctum')->group(function () {
    Route::get('tasks', [TaskController::class, 'index']);
    Route::post('tasks', [TaskController::class, 'store']);
    Route::put('tasks/{task}', [TaskController::class, 'update']);
    Route::delete('tasks/{task}', [TaskController::class, 'destroy']);
});

Route::post('create-temp-user', function() {
    if (!User::where('email', 'vitor@email.com')->exists()) {
        User::create([
            'name' => 'Vitor Mariano',
            'email' => 'vitor@email.com',
            'password' => Hash::make('123456'),
        ]);
    }

    return response()->json(['message' => 'Usuário criado']);
});
