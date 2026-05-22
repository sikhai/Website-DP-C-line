# Maintenance Page Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Add an environment-controlled maintenance page for public routes while keeping `/admin` available.

**Architecture:** A Laravel web middleware checks `MAINTENANCE_PAGE`. When enabled, it returns a standalone Blade maintenance page with HTTP 503 unless the request targets admin or static assets. The feature is disabled by setting the flag to false and can be removed by deleting the middleware registration, middleware class, view, and env documentation.

**Tech Stack:** Laravel, PHPUnit feature tests, Blade, inline CSS.

---

## File Structure

- Create `app/Http/Middleware/MaintenancePage.php`: request gate for maintenance mode.
- Modify `bootstrap/app.php`: append middleware to the web stack.
- Create `resources/views/maintenance.blade.php`: standalone maintenance page.
- Modify `.env.example`: document `MAINTENANCE_PAGE=false`.
- Create `tests/Feature/MaintenancePageTest.php`: feature coverage for enabled, disabled, admin bypass, and asset bypass behavior.

### Task 1: Middleware Behavior Tests

**Files:**
- Create: `tests/Feature/MaintenancePageTest.php`

- [ ] **Step 1: Write failing tests**

```php
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
    }

    public function test_public_routes_show_maintenance_page_when_enabled(): void
    {
        config(['app.maintenance_page' => true]);

        $response = $this->get('/maintenance-test-public');

        $response->assertStatus(503);
        $response->assertSee('Website đang được bảo trì', false);
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

        $response = $this->get('/images/logo.svg');

        $response->assertOk();
        $response->assertHeader('content-type', 'image/svg+xml');
    }
}
```

- [ ] **Step 2: Run tests and verify failure**

Run: `php artisan test --filter=MaintenancePageTest`
Expected: FAIL because public routes return normal content and no maintenance middleware exists yet.

### Task 2: Minimal Middleware and Registration

**Files:**
- Create: `app/Http/Middleware/MaintenancePage.php`
- Modify: `bootstrap/app.php`
- Modify: `config/app.php`

- [ ] **Step 1: Implement config flag**

Add to `config/app.php`:

```php
'maintenance_page' => env('MAINTENANCE_PAGE', false),
```

- [ ] **Step 2: Create middleware**

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MaintenancePage
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! config('app.maintenance_page')) {
            return $next($request);
        }

        if ($this->shouldBypass($request)) {
            return $next($request);
        }

        return response()
            ->view('maintenance', [], 503)
            ->header('Retry-After', '3600');
    }

    private function shouldBypass(Request $request): bool
    {
        return $request->is(
            'admin',
            'admin/*',
            'build/*',
            'css/*',
            'js/*',
            'images/*',
            'storage/*',
            'favicon.ico',
            'robots.txt'
        );
    }
}
```

- [ ] **Step 3: Register middleware**

In `bootstrap/app.php`, append the middleware to the web stack:

```php
$middleware->web(append: [
    \App\Http\Middleware\MaintenancePage::class,
]);
```

- [ ] **Step 4: Run tests and verify view failure**

Run: `php artisan test --filter=MaintenancePageTest`
Expected: FAIL because the `maintenance` view is missing.

### Task 3: Maintenance View and Env Documentation

**Files:**
- Create: `resources/views/maintenance.blade.php`
- Modify: `.env.example`

- [ ] **Step 1: Create standalone view**

Create an HTML page with the DPC logo, Vietnamese maintenance message, responsive layout, and no dependency on the main layout.

- [ ] **Step 2: Document env flag**

Add to `.env.example`:

```env
MAINTENANCE_PAGE=false
```

- [ ] **Step 3: Run focused tests**

Run: `php artisan test --filter=MaintenancePageTest`
Expected: PASS.

- [ ] **Step 4: Run full test suite**

Run: `php artisan test`
Expected: PASS, or document unrelated existing failures.

### Task 4: Manual Verification

**Files:**
- No code changes expected.

- [ ] **Step 1: Verify config-disabled path**

Run: `php artisan test --filter=MaintenancePageTest`
Expected: public routes render normally when `config(['app.maintenance_page' => false])`.

- [ ] **Step 2: Verify removability**

Confirm the feature can be disabled with `MAINTENANCE_PAGE=false` and fully removed by deleting four touched implementation areas.
