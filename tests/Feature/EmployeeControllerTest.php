<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class EmployeeControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_creates_an_employee_successfully(): void
    {
        $company = Company::factory()->create();

        $data = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'phone' => '123456789',
            'company_id' => $company->id,
        ];

        $response = $this->postJson('/api/employees', $data);

        $response
            ->assertStatus(201)
            ->assertJsonPath('data.first_name', 'John')
            ->assertJsonStructure([
                'data' => ['id', 'first_name', 'last_name', 'email', 'phone', 'company_id', 'created_at', 'updated_at', 'company']
            ]);

        $this->assertDatabaseHas('employees', ['email' => 'john@example.com']);
    }

    #[Test]
    public function it_fails_to_create_an_employee_with_invalid_data(): void
    {
        $data = [
            'first_name' => '',
            'last_name' => '',
            'email' => '',
            'phone' => '',
            'company_id' => '',
        ];

        $response = $this->postJson('/api/employees', $data);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['first_name', 'last_name', 'email', 'company_id']);
    }

    #[Test]
    public function it_shows_an_employee_successfully(): void
    {
        $employee = Employee::factory()->create();

        $response = $this->getJson("/api/employees/{$employee->id}");

        $response
            ->assertStatus(200)
            ->assertJsonPath('data.id', $employee->id)
            ->assertJsonStructure([
                'data' => ['id', 'first_name', 'last_name', 'email', 'phone', 'company_id', 'created_at', 'updated_at', 'company_id']
            ]);
    }

    #[Test]
    public function it_returns_404_if_employee_not_found(): void
    {
        $response = $this->getJson('/api/employees/999999');

        $response->assertStatus(404);
    }

    #[Test]
    public function it_updates_an_employee_successfully(): void
    {
        $employee = Employee::factory()->create();

        $update = [
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'email' => 'jane@example.com',
            'phone' => '987654321',
            'company_id' => $employee->company_id,
        ];

        $response = $this->putJson("/api/employees/{$employee->id}", $update);

        $response
            ->assertStatus(200)
            ->assertJsonPath('data.first_name', 'Jane');

        $this->assertDatabaseHas('employees', [
            'id' => $employee->id,
            'email' => 'jane@example.com'
        ]);
    }

    #[Test]
    public function it_returns_422_on_invalid_update_data(): void
    {
        $employee = Employee::factory()->create();

        $response = $this->putJson("/api/employees/{$employee->id}", [
            'first_name' => '',
            'last_name' => '',
            'email' => '',
            'phone' => '',
            'company_id' => '',
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['first_name', 'last_name', 'email', 'company_id']);
    }

    #[Test]
    public function it_returns_404_when_updating_nonexistent_employee(): void
    {
        $response = $this->putJson('/api/employees/999999', [
            'first_name' => 'Nonexistent',
            'last_name' => 'User',
            'email' => 'nonexistent@example.com',
            'phone' => '',
            'company_id' => 1,
        ]);

        $response->assertStatus(404);
    }

    #[Test]
    public function it_deletes_an_employee_successfully(): void
    {
        $employee = Employee::factory()->create();

        $response = $this->deleteJson("/api/employees/{$employee->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('employees', ['id' => $employee->id]);
    }

    #[Test]
    public function it_returns_404_when_deleting_nonexistent_employee(): void
    {
        $response = $this->deleteJson('/api/employees/999999');

        $response->assertStatus(404);
    }
}
