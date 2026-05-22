# Maintenance Page Design

## Goal

Show a professional temporary maintenance page for public visitors while keeping the admin area available. The change must be easy to enable, disable, and remove after maintenance is finished.

## Scope

- Apply the maintenance page to public web routes.
- Exclude `/admin` so administrators can still access Voyager and manage content.
- Exclude static assets such as images, CSS, JavaScript, build files, storage files, favicon, and robots.txt so the maintenance page can render correctly.
- Do not change existing public route definitions or controller actions unless needed for integration.

## Recommended Approach

Add a Laravel web middleware controlled by an environment flag:

```env
MAINTENANCE_PAGE=true
```

When the flag is enabled, the middleware returns a dedicated maintenance Blade view for public requests. When disabled or removed, normal routing resumes.

## User Experience

The maintenance page will be a standalone Blade view, not based on the existing main layout. It will display the current logo from `public/images/logo.svg`, a concise professional message, and simple contact or retry guidance. The layout should be responsive and polished without depending on the homepage JavaScript.

Suggested message:

> Website đang được bảo trì và nâng cấp trải nghiệm. Vui lòng quay lại sau.

## Architecture

- `app/Http/Middleware/MaintenancePage.php`
  - Reads `MAINTENANCE_PAGE` through Laravel config/env.
  - Allows admin and asset paths to continue normally.
  - Returns `resources/views/maintenance.blade.php` with HTTP 503 for public routes while enabled.
- `bootstrap/app.php`
  - Registers the middleware on the web middleware stack.
- `.env.example`
  - Documents `MAINTENANCE_PAGE=false`.
- `resources/views/maintenance.blade.php`
  - Contains standalone HTML/CSS and logo.

## Removal

To disable temporarily, set:

```env
MAINTENANCE_PAGE=false
```

To remove completely, delete the middleware registration, middleware class, maintenance view, and the `.env.example` entry.

## Testing

- Verify `/` returns the maintenance page when `MAINTENANCE_PAGE=true`.
- Verify another public route returns the maintenance page when enabled.
- Verify `/admin` is not replaced by the maintenance page.
- Verify asset URLs such as `/images/logo.svg` still load.
- Verify normal routes return after setting `MAINTENANCE_PAGE=false`.
