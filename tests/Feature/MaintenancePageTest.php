<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class MaintenancePageTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Route::middleware('web')->get('/maintenance-test-public', function () {
            return 'public route';
        });

        Route::middleware('web')->get('/admin/maintenance-test', function () {
            return 'admin route';
        });

        Route::middleware('web')->get('/images/maintenance-test-logo.svg', function () {
            return response('asset route', 200, ['content-type' => 'image/svg+xml']);
        });
    }

    public function test_public_routes_show_maintenance_page_when_enabled(): void
    {
        config(['app.maintenance_page' => true]);

        $response = $this->get('/maintenance-test-public');

        $response->assertStatus(503);
        $response->assertSee('Website Under Maintenance', false);
        $response->assertSee('DP CÉLINE', false);
        $response->assertSee('maintenance-line', false);
    }

    public function test_public_routes_render_normally_when_disabled(): void
    {
        config(['app.maintenance_page' => false]);

        $response = $this->get('/maintenance-test-public');

        $response->assertOk();
        $response->assertSee('public route');
    }

    public function test_admin_routes_are_not_replaced_by_maintenance_page(): void
    {
        config(['app.maintenance_page' => true]);

        $response = $this->get('/admin/maintenance-test');

        $response->assertOk();
        $response->assertSee('admin route');
    }

    public function test_image_assets_are_not_replaced_by_maintenance_page(): void
    {
        config(['app.maintenance_page' => true]);

        $response = $this->get('/images/maintenance-test-logo.svg');

        $response->assertOk();
        $response->assertHeader('content-type', 'image/svg+xml');
    }
}
