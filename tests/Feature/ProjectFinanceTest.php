<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Client;
use App\Models\Project;
use App\Models\ChangeOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectFinanceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Since we have migrations with soft deletes, ensure fresh DB
        $this->client = Client::create([
            'name' => 'John Doe',
            'phone' => '123456',
            'email' => 'john@example.com'
        ]);
        $this->category = Category::create([
            'name' => 'House',
            'active' => true,
            'home_page' => true,
        ]);
    }

    public function test_project_auto_creates_payment_terms()
    {
        $project = Project::create([
            'name' => 'Project A',
            'client_id' => $this->client->id,
            'category_id' => $this->category->id,
            'start_date' => '2026-06-28',
            'status' => 1,
            'final_total' => 100000000,
        ]);

        $this->assertCount(3, $project->paymentTerms);
        $this->assertEquals(50000000, $project->paymentTerms->first()->amount);
    }

    public function test_project_rest_total_is_calculated_correctly()
    {
        $project = Project::create([
            'name' => 'Project B',
            'client_id' => $this->client->id,
            'category_id' => $this->category->id,
            'start_date' => '2026-06-28',
            'status' => 1,
            'final_total' => 200000000,
            'paid_total' => 50000000,
        ]);

        $this->assertEquals(150000000, $project->rest_total);
    }

    public function test_approved_change_order_updates_project_final_total()
    {
        $project = Project::create([
            'name' => 'Project C',
            'client_id' => $this->client->id,
            'category_id' => $this->category->id,
            'start_date' => '2026-06-28',
            'status' => 1,
            'final_total' => 100000000,
            'paid_total' => 0,
        ]);

        $changeOrder = ChangeOrder::create([
            'project_id' => $project->id,
            'name' => 'Add pool',
            'type' => 'Addition',
            'amount' => 50000000,
            'status' => 'Draft',
        ]);

        $this->assertEquals(100000000, $project->fresh()->final_total);

        // Approve
        $changeOrder->status = 'Approved';
        $changeOrder->save();

        $this->assertEquals(150000000, $project->fresh()->final_total);
    }
}
