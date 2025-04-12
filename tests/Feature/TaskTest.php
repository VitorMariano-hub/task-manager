<?php

// tests/Feature/TaskTest.php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Task;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_create_task_with_valid_token()
    {
        // Criar um usuário
        $user = User::factory()->create([
            'password' => bcrypt($password = 'password'),
        ]);

        // Gerar um token
        $token = $user->createToken('TaskManager')->plainTextToken;

        // Enviar a requisição para criar uma tarefa com o token no cabeçalho
        $response = $this->postJson('/api/tasks', [
            'title' => 'Estudar Laravel',
            'description' => 'Aprender como testar APIs',
        ], [
            'Authorization' => 'Bearer ' . $token
        ]);

        // Verificar se a tarefa foi criada
        $response->assertStatus(201);
        $response->assertJson([
            'title' => 'Estudar Laravel',
            'description' => 'Aprender como testar APIs',
        ]);
    }

    // tests/Feature/TaskTest.php

    /** @test */
    public function user_can_update_task_with_valid_token()
    {
        // Criar um usuário
        $user = User::factory()->create([
            'password' => bcrypt($password = 'password'),
        ]);

        // Gerar um token
        $token = $user->createToken('TaskManager')->plainTextToken;

        // Criar uma tarefa para o usuário
        $task = Task::create([
            'title' => 'Tarefa Antiga',
            'description' => 'Descrição Antiga',
            'user_id' => $user->id,
        ]);

        // Atualizar a tarefa
        $response = $this->putJson("/api/tasks/{$task->id}", [
            'title' => 'Tarefa Atualizada',
            'description' => 'Descrição Atualizada',
        ], [
            'Authorization' => 'Bearer ' . $token
        ]);

        // Verificar se a tarefa foi atualizada
        $response->assertStatus(200);
        $response->assertJson([
            'title' => 'Tarefa Atualizada',
            'description' => 'Descrição Atualizada',
        ]);
    }

    // tests/Feature/TaskTest.php

    /** @test */
    public function user_can_delete_task_with_valid_token()
    {
        // Criar um usuário
        $user = User::factory()->create([
            'password' => bcrypt($password = 'password'),
        ]);

        // Gerar um token
        $token = $user->createToken('TaskManager')->plainTextToken;

        // Criar uma tarefa para o usuário
        $task = Task::create([
            'title' => 'Tarefa para Deletar',
            'description' => 'Descrição da tarefa',
            'user_id' => $user->id,
        ]);

        // Deletar a tarefa
        $response = $this->deleteJson("/api/tasks/{$task->id}", [], [
            'Authorization' => 'Bearer ' . $token
        ]);

        // Verificar se a tarefa foi deletada
        $response->assertStatus(204);
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

}
