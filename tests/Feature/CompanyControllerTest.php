<?php

namespace Tests\Feature;

use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CompanyControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_creates_a_company_successfully(): void
    {
        $data = [
            'name' => 'Test Company',
            'nip' => '123456789',
            'address' => 'Main Street 12',
            'city' => 'Warsaw',
            'postal_code' => '00-123',
        ];

        $response = $this->postJson('/api/companies', $data);

        $response
            ->assertStatus(201)
            ->assertJsonPath('data.name', 'Test Company')
            ->assertJsonStructure([
                'data' => ['id', 'name', 'nip', 'address', 'city', 'postal_code']
            ]);

        $this->assertDatabaseHas('companies', ['nip' => '123456789']);
    }

    #[Test]
    public function it_fails_to_create_a_company_with_invalid_data(): void
    {
        $data = [
            'name' => '',
            'nip' => '',
            'address' => '',
            'city' => '',
            'postal_code' => '',
        ];

        $response = $this->postJson('/api/companies', $data);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'nip', 'address', 'city', 'postal_code']);
    }

    #[Test]
    public function it_shows_a_company_successfully(): void
    {
        $company = Company::factory()->create();

        $response = $this->getJson("/api/companies/{$company->id}");

        $response
            ->assertStatus(200)
            ->assertJsonPath('data.id', $company->id)
            ->assertJsonStructure([
                'data' => ['id', 'name', 'nip', 'address', 'city', 'postal_code']
            ]);
    }

    #[Test]
    public function it_returns_404_if_company_not_found(): void
    {
        $response = $this->getJson('/api/companies/999999');

        $response->assertStatus(404);
    }

    #[Test]
    public function it_updates_a_company_successfully(): void
    {
        $company = Company::factory()->create();

        $update = [
            'name' => 'Updated Name',
            'nip' => $company->nip,
            'address' => $company->address,
            'city' => $company->city,
            'postal_code' => $company->postal_code,
        ];

        $response = $this->putJson("/api/companies/{$company->id}", $update);

        $response
            ->assertStatus(200)
            ->assertJsonPath('data.name', 'Updated Name');

        $this->assertDatabaseHas('companies', [
            'id' => $company->id,
            'name' => 'Updated Name'
        ]);
    }

    #[Test]
    public function it_returns_422_on_invalid_update_data(): void
    {
        $company = Company::factory()->create();

        $response = $this->putJson("/api/companies/{$company->id}", [
            'name' => '',
            'nip' => '',
            'address' => '',
            'city' => '',
            'postal_code' => '',
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'nip', 'address', 'city', 'postal_code']);
    }

    #[Test]
    public function it_returns_404_when_updating_nonexistent_company(): void
    {
        $response = $this->putJson('/api/companies/999999', [
            'name' => 'Nonexistent',
            'nip' => '123456789',
            'address' => 'Somewhere',
            'city' => 'Nowhere',
            'postal_code' => '00-000',
        ]);

        $response->assertStatus(404);
    }

    #[Test]
    public function it_deletes_a_company_successfully(): void
    {
        $company = Company::factory()->create();

        $response = $this->deleteJson("/api/companies/{$company->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('companies', ['id' => $company->id]);
    }

    #[Test]
    public function it_returns_404_when_deleting_nonexistent_company(): void
    {
        $response = $this->deleteJson('/api/companies/999999');

        $response->assertStatus(404);
    }
}
