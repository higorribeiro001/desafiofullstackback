<?php

namespace Tests\Feature;

use Tests\TestCase;

// php artisan test --filter=UserControllerTest
class UserControllerTest extends TestCase
{
    public function createUser() {
        $dataCreate = [
            'name' => 'John Doe',
            'email' => 'johndoe2024@example.com',
            'company' => 'johndoe&cia',
            'password' => '1234U@John',
        ];

        $response = $this->post('/api/user/', $dataCreate);

        return $response;
    }
    /** @test */
    public function test_get_user() {
        $response = $this->get('/api/user');
        $response->assertStatus(200);
    }

    /** @test */
    public function test_get_user_by_id() {
        $responsePost = $this->createUser();
        $userId = $responsePost->json('id');
        $response = $this->get('/api/user/'.$userId);
        $this->delete('/api/user/'.$userId);
        $response->assertStatus(200);
    }

    /** @test */
    public function test_create_user() {
        $response = $this->createUser();
        $userId = $response->json('id');
        $this->delete('/api/user/'.$userId);

        $response->assertStatus(201);
    }

    /** @test */
    public function test_patch_in_user() {
        $responsePost = $this->createUser();
        $userId = $responsePost->json('id');
        $dataPatch = [
            'name' => 'John Doe Test',
        ];
        $response = $this->patch('/api/user/'.$userId, $dataPatch);
        $this->delete('/api/user/'.$userId);

        $response->assertStatus(200);
    }

    /** @test */
    public function test_put_in_user() {
        $responsePost = $this->createUser();
        $userId = $responsePost->json('id');
        $dataPut = [
            'name' => 'John Doe Test Put',
            'email' => 'johndoe2025@example.com',
            'company' => 'johndoe&ciaTest',
            'password' => '1234U@JohnTest',
        ];
        $response = $this->put('/api/user/'.$userId, $dataPut);
        $this->delete('/api/user/'.$userId);

        $response->assertStatus(200);
    }

    /** @test */
    public function test_delete_user() {
        $responsePost = $this->createUser();
        $userId = $responsePost->json('id');

        $response = $this->delete('/api/user/'.$userId);

        $response->assertStatus(200);
    }
}