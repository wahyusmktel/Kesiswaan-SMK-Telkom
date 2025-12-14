# Aplikasi Izin - AI Coding Agent Instructions

**Project**: School Permission/Leave Management System (Aplikasi Izin)  
**Framework**: Laravel 12 + Blade Templates + Vite + TailwindCSS  
**Language**: Indonesian domain, PHP backend

## Architecture Overview

### Core Domain Model: Multi-Stage Permission System

This is a **school permission management system** with multiple permission types, each requiring approval workflows:

1. **Perizinan** (General Permit): Long-term permissions (medical leave, family issues). Single approver (Stakeholder/BK).
2. **IzinMeninggalkanKelas** (Class Exit Permit): Permission to leave class during lessons. Multi-stage: Guru Kelas → Guru Piket → Security verification.
3. **Keterlambatan** (Lateness): Student arrival delays. Tracked by Security, verified by appropriate role.
4. **Dispensasi** (Exemption): Academic task exemptions. Requires Kesiswaan approval.
5. **Prakerin** (Internship): Work-study program with industry placement, journaling, and supervisor monitoring.

**Key Insight**: Different permission types have different approval chains—avoid hardcoding approval flows. Use role-based access (Spatie Permission).

### Role-Based Access Structure (Spatie Permission)

-   **Admin**: System configuration, user management
-   **Kesiswaan** (Student Affairs): Dashboard monitoring, general permit approvals
-   **Guru Kelas** (Homeroom Teacher): Class roster management, class exit approvals
-   **Guru Piket** (Duty Teacher): Hall monitoring, class exit secondary approvals
-   **Security**: Physical exit verification, lateness documentation
-   **Wali Kelas** (Student Guardian): Views student permits/records
-   **Siswa** (Student): Submits permits, views own status
-   **Kurikulum** (Curriculum): Master data (subjects, schedules, classes)
-   **BK** (Guidance Counselor): Monitoring, student counseling permits
-   **Prakerin Supervisor**: Internship monitoring

Check [config/permission.php](config/permission.php) for Spatie configuration.

### Data Flow Patterns

#### Permit Submission & Status Tracking

Models use `status` enum columns: `pending` → `approved` → `rejected` | `completed`  
Approvers store: `{role}_approval_id`, `{role}_approved_at`, `alasan_penolakan` (rejection reason)

Example: [IzinMeninggalkanKelas](app/Models/IzinMeninggalkanKelas.php)

-   UUID generation on creation (line 36-41): Used for public verification links
-   Multi-stage tracking: `guru_kelas_approval_id`, `guru_piket_approval_id`, `security_verification_id`
-   Rejection always stored with `ditolak_oleh` user ID

#### Notifications

Database notifications sent on approval/rejection:

-   [IzinDisetujuiNotification](app/Notifications/IzinDisetujuiNotification.php): Approval notification
-   [IzinDitolakNotification](app/Notifications/IzinDitolakNotification.php): Rejection notification

Queue disabled by default (sync mode), but structure supports async in production.

#### Data Imports

Excel import handlers use Maatwebsite/Excel (v3.1):

-   [GuruImport](app/Imports/GuruImport.php): NUPTK validation, uniqueness enforcement
-   [MataPelajaranImport](app/Imports/MataPelajaranImport.php): Similar pattern
-   Import validates headings, applies custom validation rules
-   All imports inherit from `ToModel`, `WithHeadingRow`, `WithValidation`

### Master Data Models

-   **MasterGuru**: Teacher registry (keyed by NUPTK)
-   **MasterSiswa**: Student registry (keyed by NISN)
-   **Kelas**: Class definition (code, name)
-   **Rombel**: Class roster (links Kelas + students)
-   **MataPelajaran**: Subject definition
-   **JadwalPelajaran**: Class schedule (day, time, subject, teacher)
-   **JamPelajaran**: Time period definitions

These feed the permit workflows (students in Rombel, schedule for class exit approvals).

## Critical Developer Workflows

### Environment Setup

```bash
# Copy .env.example to .env (auto-done on composer install)
# Generate APP_KEY
php artisan key:generate

# Run migrations (auto-done on project create)
php artisan migrate

# Seed permissions/roles if added
php artisan db:seed
```

### Local Development

```bash
# Terminal 1: Backend (Artisan hot reload)
php artisan serve

# Terminal 2: Frontend (Vite dev server)
npm run dev

# Or run concurrently via Composer
composer run dev  # Uses 'concurrently' to run both
```

### Testing

```bash
# Run all tests
./vendor/bin/phpunit

# Run specific test suite
./vendor/bin/phpunit --testsuite Feature
./vendor/bin/phpunit --testsuite Unit

# Coverage report (configured in phpunit.xml)
./vendor/bin/phpunit --coverage-html coverage
```

**Test Config**: Uses in-memory SQLite (`DB_DATABASE=:memory:`), array cache, sync queue.

### Code Style

```bash
# Laravel Pint for PHP code style
./vendor/bin/pint
./vendor/bin/pint app/Models/MyModel.php  # Single file
```

### Database Migrations

```bash
php artisan make:migration create_new_table
php artisan migrate
php artisan migrate:rollback
```

Migrations auto-run on dev with `composer post-create-project-cmd`. Always add foreign key constraints and cascading deletes where appropriate.

## Project-Specific Conventions

### File Organization by Role

Controllers organized into subdirectories by user role:

-   `Http/Controllers/Siswa/` - Student controllers
-   `Http/Controllers/GuruKelas/` - Homeroom teacher controllers
-   `Http/Controllers/Kesiswaan/` - Student affairs controllers
-   `Http/Controllers/BK/` - Guidance controllers
-   `Http/Controllers/Piket/` - Duty teacher controllers
-   `Http/Controllers/Kurikulum/` - Curriculum controllers
-   `Http/Controllers/Security/` - Security verification controllers
-   `Http/Controllers/Prakerin/` - Internship controllers

**Each role has its own Dashboard** (e.g., [Siswa/DashboardController](app/Http/Controllers/Siswa/DashboardController.php), [Kesiswaan/DashboardController](app/Http/Controllers/Kesiswaan/DashboardController.php))

### Model Relationships: Self-Referential User Patterns

[User.php](app/Models/User.php) uses self-referential relations for teacher-student hierarchy:

```php
// Homeroom teacher relationship
public function waliKelas() { return $this->belongsTo(User::class, 'wali_kelas_id'); }
public function siswa() { return $this->hasMany(User::class, 'wali_kelas_id'); }
```

Similar pattern for approval chains: approver user IDs stored as foreign keys in permit tables.

### Explicit Table Names

Models explicitly define table names (not pluralized convention):

```php
protected $table = 'perizinan';  // Not 'perizinan' auto-pluralized
protected $table = 'izin_meninggalkan_kelas';
```

This is intentional—table names are Indonesian domain terms, not auto-pluralized.

### Authentication & Authorization

-   Uses Spatie/Laravel-Permission for roles/permissions
-   Auth config in [config/auth.php](config/auth.php)
-   Gate/Middleware checks in controllers: `$this->authorize('...')` or `auth()->user()->hasRole('...')`

### Middleware & Route Protection

Routes grouped by authenticated user role. Check [routes/web.php](routes/web.php) for structure:

-   Public routes: Verification links (no auth required)
-   Protected routes: Grouped by `auth`, then by role middleware

### Frontend Stack

-   **Vite** for asset bundling ([vite.config.js](vite.config.js))
-   **Blade** for server-side templating
-   **TailwindCSS** + TailwindForms for styling
-   **Alpine.js** for interactive components
-   **Axios** for AJAX requests

Build with `npm run build` for production.

### Notification System

-   Database notifications (stored in `notifications` table)
-   Routed via Laravel's `Notifiable` trait
-   No email channel configured by default (uses `database` channel)
-   Can be extended to add SMS/email channels in production

### Sweet Alert Integration

Configured via `realrashid/sweet-alert` package ([config/sweetalert.php](config/sweetalert.php)). Use in controllers:

```php
alert()->success('Title', 'Message');
```

## Integration Points & External Dependencies

### Excel Import/Export

**Package**: `maatwebsite/excel` (v3.1)  
**Usage**: Data imports for guru, siswa, mata pelajaran via upload forms  
**Pattern**: Implement `ToModel`, `WithHeadingRow`, `WithValidation` interfaces  
**Validation**: Custom rules + messages returned in `rules()` and `customValidationMessages()`

### PDF Generation

**Package**: `barryvdh/laravel-dompdf` (v3.1)  
**Usage**: Generate permit letters/reports as PDFs  
**Pattern**: Render Blade template, return as PDF response

### QR Codes

**Package**: `simplesoftwareio/simple-qrcode` (v4.2)  
**Usage**: Generate verification QR codes (used with public verification links)

### Database Support

-   **Default**: SQLite (local dev)
-   **Production**: MySQL/MariaDB (config switchable via `.env`)
-   Foreign key constraints enabled
-   Migrations auto-run with strict mode

### Vue/React Integration

**Not used**—this is a traditional Laravel app with Blade + Alpine.js for interactivity.

## Cross-Component Communication

### Approval Workflows

Permits flow through multiple components via status updates + notifications:

1. Student submits (controller creates model)
2. Approval stage 1 (approver updates status, triggers notification)
3. Approval stage 2 (next approver updates status)
4. Final verification (Security/Kesiswaan confirms)
5. Archive/complete

**Key**: Always check `status` before allowing state transitions. Use model scopes for status queries.

### Dashboard Aggregation

Each role's dashboard queries its relevant permits:

-   **Kesiswaan**: Monitors all permits, rejection counts
-   **Guru Kelas**: Only students in their kelas, class exit permits
-   **Siswa**: Personal permits + status history
-   **BK**: Student behavioral/medical permits

Scopes should filter by role + user ID.

### UUID for Public Links

[IzinMeninggalkanKelas](app/Models/IzinMeninggalkanKelas.php) (line 36-41) generates UUID on creation. Used in verification routes:

```
/verifikasi/surat/{uuid}
```

Allows public verification without authentication (security checked via UUID obfuscation).

## Key Files Reference

| File                                           | Purpose                                                |
| ---------------------------------------------- | ------------------------------------------------------ |
| [routes/web.php](routes/web.php)               | All route definitions, organized by role               |
| [config/permission.php](config/permission.php) | Spatie permission models & table names                 |
| [app/Models/User.php](app/Models/User.php)     | Auth user + self-referential teacher-student relations |
| [app/Http/Controllers/](app/Http/Controllers/) | Role-organized controllers                             |
| [database/migrations/](database/migrations/)   | Schema definitions                                     |
| [resources/views/](resources/views/)           | Blade templates                                        |
| [vite.config.js](vite.config.js)               | Asset bundling, hot reload config                      |
| [phpunit.xml](phpunit.xml)                     | Test suite configuration                               |

## Common Tasks

### Adding a New Permission Type

1. Create migration + model (define approval chain fields)
2. Create controller in appropriate role directory
3. Create Blade views for form + approval
4. Add notification class
5. Register routes in [routes/web.php](routes/web.php)
6. Seed permissions/roles if needed

### Changing Approval Workflow

Models store approver IDs + timestamps explicitly. Update:

-   Model fillables + casts
-   Controller approval logic (status transitions)
-   Migration schema
-   Tests for state transitions

### Adding Excel Import

Implement new Import class in [app/Imports/](app/Imports/):

-   Inherit `ToModel`, `WithHeadingRow`, `WithValidation`
-   Define validation rules
-   Handle uniqueness constraints (keys like NUPTK, NISN)

### Frontend Changes

Blade templates in [resources/views/](resources/views/). Use Vite for CSS/JS:

-   `npm run dev` for hot reload
-   `npm run build` for production
-   TailwindCSS classes auto-purge unused styles

---

**Last Updated**: December 2025  
**Target Audience**: AI coding agents assisting with feature development, bug fixes, and data migrations
