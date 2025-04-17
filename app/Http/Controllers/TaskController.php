<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

/**
 * @OA\Info(
 *     title="Task Manager API",
 *     description="API para gerenciar tarefas",
 *     version="1.0.0"
 * )
 * 
 */

class TaskController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/tasks",
     *     summary="Listar tarefas do usuário autenticado",
     *     tags={"Tasks"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de tarefas retornada com sucesso"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Não autenticado"
     *     )
     * )
     */
    public function index()
    {
        try {
            $timezone = 'America/Sao_Paulo';
            $startOfDay = Carbon::today($timezone)->startOfDay()->timezone('UTC');
            $endOfDay = Carbon::today($timezone)->endOfDay()->timezone('UTC');

            $tasks = Task::with('user:id,name')
                ->where('user_id', auth()->id())
                ->whereBetween('created_at', [$startOfDay, $endOfDay])
                ->get()
                ->map(function ($task) {
                    $task->time_left = $task->time_left;
                    return $task;
                });

            return response()->json($tasks);
        } catch (\Exception $e) {
            \Log::error('Erro ao listar tarefas: ' . $e->getMessage());

            return response()->json([
                'message' => 'Ocorreu um erro ao buscar as tarefas. Por favor, tente novamente mais tarde.'
            ], 500);
        }
    }
     
    /**
     * @OA\Post(
     *     path="/api/tasks",
     *     summary="Criar nova tarefa",
     *     tags={"Tasks"},
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title", "description"},
     *             @OA\Property(property="title", type="string", example="Comprar leite"),
     *             @OA\Property(property="description", type="string", example="Ir ao mercado e comprar leite")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Tarefa criada com sucesso"),
     *     @OA\Response(response=422, description="Erro de validação"),
     *     @OA\Response(response=401, description="Não autenticado"),
     *     @OA\Response(response=400, description="Limite de tarefas atingido para hoje")
     * )
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
            ]);

            $user = auth()->user();

            if (Task::userReachedDailyLimit($user)) {
                return response()->json(['message' => 'Você atingiu o limite de 10 tarefas diárias.'], 400);
            }

            $task = Task::create([
                'title' => $request->title,
                'description' => $request->description,
                'user_id' => $user->id,
            ]);

            return response()->json($task, 201);
        } catch (\Exception $e) {
            \Log::error('Erro ao criar tarefa: ' . $e->getMessage());

            return response()->json([
                'message' => 'Ocorreu um erro interno. Por favor, tente novamente mais tarde.',
            ], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/tasks/{id}",
     *     summary="Atualizar tarefa existente",
     *     tags={"Tasks"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID da tarefa",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", example="Novo título"),
     *             @OA\Property(property="description", type="string", example="Nova descrição"),
     *             @OA\Property(property="status", type="string", example="concluída")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Tarefa atualizada com sucesso"),
     *     @OA\Response(response=403, description="Acesso negado"),
     *     @OA\Response(response=401, description="Não autenticado")
     * )
     */
    public function update(Request $request, Task $task)
    {
        if ($task->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $task->update($request->only('title', 'description', 'status'));

        return response()->json($task);
    }

    /**
     * @OA\Delete(
     *     path="/api/tasks/{id}",
     *     summary="Excluir tarefa",
     *     tags={"Tasks"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID da tarefa",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=204, description="Tarefa excluída com sucesso"),
     *     @OA\Response(response=403, description="Acesso negado"),
     *     @OA\Response(response=401, description="Não autenticado")
     * )
     */
    public function destroy(Task $task)
    {
        if ($task->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $task->delete();

        return response()->json(null, 204);
    }
}
