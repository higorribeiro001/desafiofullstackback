<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PhoneControllerTest extends TestCase
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

    public function createPhone($userId) {
        $dataCreate = [
            'num' => '0000-0000',
            'user_id' => $userId,
        ];

        $response = $this->post('/api/phone/', $dataCreate);

        return $response;
    }

    /** @test */
    public function test_get_phone() {
        $response = $this->get('/api/phone');
        $response->assertStatus(200);
    }

    /** @test */
    public function test_get_phone_by_id() {
        $responseUser = $this->createUser();
        $userId = $responseUser->json('id');
        $responsePost = $this->createPhone($userId);
        $phoneId = $responsePost->json('id');
        $response = $this->get('/api/phone/'.$phoneId);
        $this->delete('/api/user/'.$userId);
        $response->assertStatus(200);
    }

    /** @test */
    public function test_create_phone() {
        $responseUser = $this->createUser();
        $userId = $responseUser->json('id');
        $response = $this->createPhone($userId);
        $this->delete('/api/user/'.$userId);

        $response->assertStatus(201);
    }

    /** @test */
    public function test_patch_in_phone() {
        $responseUser = $this->createUser();
        $userId = $responseUser->json('id');
        $responsePost = $this->createPhone($userId);
        $phoneId = $responsePost->json('id');
        $dataPatch = [
            'num' => '1111-1111',
        ];
        $response = $this->patch('/api/phone/'.$phoneId, $dataPatch);
        $this->delete('/api/user/'.$userId);

        $response->assertStatus(200);
    }

    /** @test */
    public function test_put_in_phone() {
        $responseUser = $this->createUser();
        $userId = $responseUser->json('id');
        $responsePost = $this->createPhone($userId);
        $phoneId = $responsePost->json('id');
        $dataPut = [
            'num' => '1111-1111',
            'user_id' => $userId
        ];
        $response = $this->put('/api/phone/'.$phoneId, $dataPut);
        $this->delete('/api/user/'.$userId);

        $response->assertStatus(200);
    }

    /** @test */
    public function test_delete_phone() {
        $responseUser = $this->createUser();
        $userId = $responseUser->json('id');
        $responsePost = $this->createPhone($userId);
        $phoneId = $responsePost->json('id');

        $response = $this->delete('/api/phone/'.$phoneId);
        $this->delete('/api/user/'.$userId);

        $response->assertStatus(200);
    }
}
