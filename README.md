# Google Calendar Clone - Mobile API Backend

A test-driven, mobile API-first Laravel backend for a Google Calendar clone application. Features asynchronous email invitations, RESTful API with Sanctum authentication, and minimal Livewire admin dashboard.

## 🎯 Key Features

- ✅ **Mobile API-First Design**: RESTful API optimized for mobile apps
- ✅ **Asynchronous Email Processing**: Queued invitation emails (non-blocking)
- ✅ **Test-Driven Development**: Comprehensive feature tests included
- ✅ **Sanctum Authentication**: Token-based API authentication
- ✅ **Google OAuth Integration**: Social login via Laravel Socialite
- ✅ **Authorization Policies**: Secure resource access control
- ✅ **Spatie Query Builder**: Advanced filtering and sorting
- ✅ **Event Invitations**: Support for registered and unregistered users
- ✅ **Admin Dashboard**: Minimal Livewire CRUD interface

## 📋 Requirements

- PHP 8.1+
- Composer
- MySQL/PostgreSQL
- Laravel 10+
- Node.js & NPM (for Livewire admin)

## 🚀 Installation

### 1. Clone & Install Dependencies

```bash
git clone <repository-url>
cd Calendros
composer install
npm install
```

### 2. Environment Setup

```bash
cp .env.example .env
php artisan key:generate
```

Update `.env` with your configuration:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=calendros
DB_USERNAME=root
DB_PASSWORD=

# Queue Configuration
QUEUE_CONNECTION=database

# Mail Configuration (development)
MAIL_MAILER=log
MAIL_FROM_ADDRESS=noreply@calendros.app
MAIL_FROM_NAME="Calendros"

# Google OAuth
GOOGLE_CLIENT_ID=your_google_client_id
GOOGLE_CLIENT_SECRET=your_google_client_secret
GOOGLE_REDIRECT_URI=http://localhost:8000/api/auth/google/callback

# Sanctum
SANCTUM_STATEFUL_DOMAINS=localhost:8000
SESSION_DRIVER=cookie
```

### 3. Install Required Packages

```bash
composer require laravel/sanctum
composer require laravel/socialite
composer require spatie/laravel-permission
composer require spatie/laravel-query-builder
```

### 4. Database Setup

```bash
# Run migrations
php artisan migrate

# Create queue jobs table
php artisan queue:table
php artisan migrate

# Seed sample data (optional)
php artisan db:seed
```

### 5. Build Assets

```bash
npm run build
```

## 🧪 Running Tests

The application follows Test-Driven Development (TDD). All features have corresponding tests.

```bash
# Run all tests
php artisan test

# Run specific test suites
php artisan test --filter=ApiAuthenticationTest
php artisan test --filter=CalendarTest
php artisan test --filter=EventTest
php artisan test --filter=InviteTest

# Run with coverage
php artisan test --coverage

# Run critical async email test
php artisan test --filter=test_invitation_email_is_queued_not_sent_synchronously
```

### Key Test Files

- `tests/Feature/Auth/ApiAuthenticationTest.php` - API authentication
- `tests/Feature/CalendarTest.php` - Calendar CRUD operations
- `tests/Feature/EventTest.php` - Event management
- `tests/Feature/InviteTest.php` - **Invitation system with queue verification**

## 🔧 Running the Application

### Start Development Server

```bash
php artisan serve
```

API will be available at: `http://localhost:8000/api`

### Start Queue Worker

**IMPORTANT**: Queue worker must be running to process invitation emails.

```bash
# Development (processes jobs immediately)
php artisan queue:work

# With retry and timeout
php artisan queue:work --tries=3 --timeout=60

# Daemon mode (production)
php artisan queue:work --daemon
```

For production, use Supervisor to keep queue worker running. See Laravel docs.

## 📚 API Documentation

### Authentication Endpoints

#### Register
```http
POST /api/register
Content-Type: application/json

{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

**Response (201):**
```json
{
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com"
  },
  "token": "1|abc123..."
}
```

#### Login
```http
POST /api/login
Content-Type: application/json

{
  "email": "john@example.com",
  "password": "password123"
}
```

#### Logout
```http
POST /api/logout
Authorization: Bearer {token}
```

#### Google OAuth
```http
GET /api/auth/google
```
Redirects to Google OAuth consent screen.

```http
GET /api/auth/google/callback
```
Callback URL for Google OAuth.

---

### Calendar Endpoints

All require `Authorization: Bearer {token}` header.

#### List Calendars
```http
GET /api/calendars
```

**Optional Query Parameters:**
- `sort=-created_at` - Sort by created date (descending)
- `filter[name]=Work` - Filter by name
- `filter[is_default]=1` - Filter default calendars

#### Create Calendar
```http
POST /api/calendars
Content-Type: application/json

{
  "name": "Work Calendar",
  "description": "My work events",
  "color": "#ff0000",
  "timezone": "America/New_York"
}
```

#### Get Calendar
```http
GET /api/calendars/{id}
```

#### Update Calendar
```http
PUT /api/calendars/{id}
Content-Type: application/json

{
  "name": "Updated Name",
  "color": "#00ff00"
}
```

#### Delete Calendar
```http
DELETE /api/calendars/{id}
```

---

### Event Endpoints

#### List Events in Calendar
```http
GET /api/calendars/{calendar_id}/events
```

**Optional Query Parameters:**
- `sort=start_time` - Sort by start time
- `filter[title]=Meeting` - Filter by title
- `filter[start_time][gte]=2025-10-01` - Events starting after date
- `filter[end_time][lte]=2025-10-31` - Events ending before date

#### Create Event
```http
POST /api/calendars/{calendar_id}/events
Content-Type: application/json

{
  "title": "Team Meeting",
  "description": "Weekly sync",
  "start_time": "2025-10-25 10:00:00",
  "end_time": "2025-10-25 11:00:00",
  "location": "Conference Room A",
  "is_all_day": false
}
```

#### Get Event
```http
GET /api/events/{id}
```

#### Update Event
```http
PUT /api/events/{id}
Content-Type: application/json

{
  "title": "Updated Meeting Title",
  "start_time": "2025-10-25 14:00:00",
  "end_time": "2025-10-25 15:00:00"
}
```

#### Delete Event
```http
DELETE /api/events/{id}
```

---

### Invitation Endpoints

#### Invite User to Event
```http
POST /api/events/{event_id}/invite
Content-Type: application/json
Authorization: Bearer {token}

{
  "invitee_email": "guest@example.com"
}
```

**Response (201):**
```json
{
  "data": {
    "id": 1,
    "event_id": 1,
    "user_id": null,
    "invitee_email": "guest@example.com",
    "status": "pending"
  }
}
```

**Important**: Email is **queued asynchronously**. No immediate email is sent.

#### List My Invites
```http
GET /api/invites
Authorization: Bearer {token}
```

#### List Invites for Event
```http
GET /api/events/{event_id}/invites
Authorization: Bearer {token}
```

#### Accept Invite
```http
PUT /api/invites/{invite_id}/accept
Authorization: Bearer {token}
```

#### Reject Invite
```http
PUT /api/invites/{invite_id}/reject
Authorization: Bearer {token}
```

---

## 🔐 Authorization

The API uses Laravel Policies for authorization:

- **CalendarPolicy**: Users can only access their own calendars
- **EventPolicy**: Users can only manage events in their calendars
- **InvitePolicy**: Users can accept/reject their own invites

Unauthorized requests return `403 Forbidden`.

## 📧 Email Invitation System

### How It Works

1. User creates invite via `POST /api/events/{event}/invite`
2. Invite record created in database
3. `SendEventInvitation` job **queued** (not executed immediately)
4. API responds immediately (non-blocking)
5. Queue worker processes job asynchronously
6. Email sent via configured mail driver

### Testing Queue Behavior

```php
// From tests/Feature/InviteTest.php
public function test_invitation_email_is_queued_not_sent_synchronously(): void
{
    Mail::fake();
    Queue::fake();

    // Create invite
    $response = $this->postJson("/api/events/{$event->id}/invite", [
        'invitee_email' => 'guest@example.com',
    ]);

    // Assert NO synchronous emails
    Mail::assertNothingOutbox();

    // Assert job WAS queued
    Queue::assertPushed(SendEventInvitation::class);
}
```

### Queue Configuration

**Development** (synchronous for testing):
```env
QUEUE_CONNECTION=sync
```

**Production** (asynchronous):
```env
QUEUE_CONNECTION=database
# or
QUEUE_CONNECTION=redis
```

Run worker:
```bash
php artisan queue:work
```

## 🖥️ Admin Dashboard

Minimal Livewire dashboard for admins to manage users, calendars, events, and view invite statuses.

**Access:** `/admin`

**Features:**
- User management (CRUD)
- Calendar overview
- Event management
- Invite status tracking

**Setup:**
```bash
# Create admin user
php artisan tinker
>>> $user = User::find(1);
>>> $user->assignRole('admin');
```

## 📱 Mobile App Integration

### Authentication Flow

1. **Register/Login**: Call `/api/register` or `/api/login`
2. **Store Token**: Save `token` from response securely
3. **API Requests**: Include `Authorization: Bearer {token}` header
4. **Google OAuth**: Use webview for `/api/auth/google` flow

### Recommended Mobile Features

- **Local Notifications**: Mobile app handles all reminders
- **Offline Support**: Cache calendars/events, sync when online
- **Calendar Sync**: Poll `/api/calendars` and `/api/events` endpoints
- **Invite Notifications**: Poll `/api/invites` or implement webhooks
- **Push Notifications**: Implement using Firebase/APNs (not in backend)

### API Best Practices

- Use appropriate HTTP methods (GET, POST, PUT, DELETE)
- Check response status codes (200, 201, 401, 403, 422, 500)
- Handle validation errors (422 returns field-specific errors)
- Implement token refresh logic
- Handle network errors gracefully

## 🏗️ Project Structure

```
app/
├── Http/
│   ├── Controllers/Api/     # API controllers
│   ├── Requests/            # Form validation
│   └── Resources/           # API responses
├── Jobs/                    # Queue jobs
├── Mail/                    # Mailables
├── Models/                  # Eloquent models
└── Policies/                # Authorization policies

database/
├── factories/               # Model factories
├── migrations/              # Database schema
└── seeders/                 # Seed data

tests/Feature/               # Feature tests
├── Auth/
├── CalendarTest.php
├── EventTest.php
└── InviteTest.php

resources/views/emails/      # Email templates
```

## 🔍 Troubleshooting

### Queue jobs not processing
- Ensure queue worker is running: `php artisan queue:work`
- Check `QUEUE_CONNECTION` in `.env`
- View failed jobs: `php artisan queue:failed`
- Retry failed: `php artisan queue:retry all`

### Tests failing
- Run `php artisan config:clear`
- Ensure test database is configured
- Check `.env.testing` file

### Google OAuth not working
- Verify credentials in `.env`
- Check redirect URI matches Google Console
- Ensure app is in testing mode (not production)

### Authorization errors (403)
- Verify policies are registered in `AuthServiceProvider`
- Check user owns the resource being accessed

## 📝 Development Notes

- Default calendar automatically created on user registration
- Invites support both registered users (user_id) and unregistered (email only)
- Cascading deletes: Deleting calendar deletes events and invites
- All timestamps in UTC, convert in mobile app for local timezone
- Color validation: Must be 7-character hex code (#RRGGBB)

## 🚀 Deployment

### Production Checklist

- [ ] Set `APP_ENV=production` in `.env`
- [ ] Set `APP_DEBUG=false`
- [ ] Use database/Redis queue driver
- [ ] Configure Supervisor for queue worker
- [ ] Setup SSL certificate
- [ ] Configure CORS for mobile app domains
- [ ] Enable rate limiting
- [ ] Setup monitoring (Sentry, Bugsnag, etc.)
- [ ] Configure backup strategy
- [ ] Setup CI/CD pipeline

### Supervisor Configuration

```ini
[program:calendros-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasec=3600
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/path/to/storage/logs/worker.log
stopwaitsecs=3600
```

## 📄 License

This project is open-sourced software licensed under the MIT license.

## 👥 Contributing

1. Fork the repository
2. Create feature branch (`git checkout -b feature/amazing-feature`)
3. Write tests for new features
4. Commit changes (`git commit -m 'Add amazing feature'`)
5. Push to branch (`git push origin feature/amazing-feature`)
6. Open Pull Request

## 📞 Support

For issues, questions, or contributions, please open an issue on GitHub.

---

**Built with ❤️ using Laravel, following TDD principles**
