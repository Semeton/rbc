# User Management Module Plan

## 1. Overview

This document outlines a comprehensive yet simplified User Management System for the RBC Trucking Management System. The goal is to provide robust user management capabilities while maintaining simplicity and ease of use.

## 2. Core Requirements

### 2.1 User Roles & Permissions

-   **Admin:** Full system access and user management
-   **Accountant:** Payment management and financial reports
-   **Operations Manager:** Transaction management and truck dispatch
-   **Data Entry Staff:** Limited to adding records

### 2.2 Key Features

-   User CRUD operations
-   User invitation system with email
-   Role-based access control
-   User profile management
-   User activity tracking
-   Session management
-   Bulk user operations

## 3. Database Schema

### 3.1 User Invitations Table

```sql
CREATE TABLE user_invitations (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    role ENUM('admin', 'accountant', 'operations_manager', 'staff') NOT NULL,
    token VARCHAR(255) UNIQUE NOT NULL,
    invited_by BIGINT UNSIGNED NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    accepted_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (invited_by) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_invitations_email (email),
    INDEX idx_user_invitations_token (token),
    INDEX idx_user_invitations_expires_at (expires_at)
);
```

### 3.2 User Sessions Table

```sql
CREATE TABLE user_sessions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    session_id VARCHAR(255) NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_sessions_user_id (user_id),
    INDEX idx_user_sessions_session_id (session_id),
    INDEX idx_user_sessions_last_activity (last_activity)
);
```

### 3.3 User Preferences Table

```sql
CREATE TABLE user_preferences (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    preferences JSON NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_preferences (user_id)
);
```

### 3.4 Enhanced Users Table

```sql
ALTER TABLE users ADD COLUMN status ENUM('active', 'inactive', 'suspended') DEFAULT 'active';
ALTER TABLE users ADD COLUMN last_login_at TIMESTAMP NULL;
ALTER TABLE users ADD COLUMN email_verified_at TIMESTAMP NULL;
ALTER TABLE users ADD INDEX idx_users_status (status);
ALTER TABLE users ADD INDEX idx_users_last_login_at (last_login_at);
```

## 4. Backend Implementation

### 4.1 User Service (`app/User/Services/UserService.php`)

```php
<?php

namespace App\User\Services;

use App\Models\User;
use App\Models\UserInvitation;
use App\Services\AuditTrailService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class UserService
{
    public function __construct(
        private readonly AuditTrailService $auditTrailService
    ) {}

    public function getPaginatedUsers(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = User::with(['invitations', 'sessions'])
            ->latest('created_at');

        // Apply filters
        if (isset($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', "%{$filters['search']}%")
                  ->orWhere('email', 'like', "%{$filters['search']}%");
            });
        }

        if (isset($filters['role'])) {
            $query->where('role', $filters['role']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->paginate($perPage);
    }

    public function createUser(array $data): User
    {
        $user = User::create($data);

        $this->auditTrailService->log(
            'create',
            'User',
            "User '{$user->name}' was created"
        );

        return $user;
    }

    public function updateUser(User $user, array $data): User
    {
        $user->update($data);

        $this->auditTrailService->log(
            'update',
            'User',
            "User '{$user->name}' was updated"
        );

        return $user;
    }

    public function deleteUser(User $user): bool
    {
        $name = $user->name;
        $result = $user->delete();

        if ($result) {
            $this->auditTrailService->log(
                'delete',
                'User',
                "User '{$name}' was deleted"
            );
        }

        return $result;
    }

    public function inviteUser(string $email, string $role, User $invitedBy): UserInvitation
    {
        $token = Str::random(64);
        $expiresAt = now()->addDays(7);

        $invitation = UserInvitation::create([
            'email' => $email,
            'role' => $role,
            'token' => $token,
            'invited_by' => $invitedBy->id,
            'expires_at' => $expiresAt,
        ]);

        $this->auditTrailService->log(
            'create',
            'UserInvitation',
            "User invitation sent to '{$email}' for role '{$role}'"
        );

        return $invitation;
    }

    public function acceptInvitation(string $token, array $userData): User
    {
        $invitation = UserInvitation::where('token', $token)
            ->where('expires_at', '>', now())
            ->whereNull('accepted_at')
            ->firstOrFail();

        $user = User::create([
            'name' => $userData['name'],
            'email' => $invitation->email,
            'role' => $invitation->role,
            'password' => $userData['password'],
            'email_verified_at' => now(),
        ]);

        $invitation->update(['accepted_at' => now()]);

        $this->auditTrailService->log(
            'create',
            'User',
            "User '{$user->name}' was created from invitation"
        );

        return $user;
    }

    public function updateUserStatus(User $user, string $status): bool
    {
        $oldStatus = $user->status;
        $result = $user->update(['status' => $status]);

        if ($result) {
            $this->auditTrailService->log(
                'update',
                'User',
                "User '{$user->name}' status changed from '{$oldStatus}' to '{$status}'"
            );
        }

        return $result;
    }

    public function getUserActivity(User $user, int $limit = 10): Collection
    {
        return $this->auditTrailService->getUserActivity($user->id, $limit);
    }
}
```

### 4.2 Invitation Service (`app/User/Services/InvitationService.php`)

```php
<?php

namespace App\User\Services;

use App\Mail\UserInvitationMail;
use App\Models\User;
use App\Models\UserInvitation;
use Illuminate\Support\Facades\Mail;

class InvitationService
{
    public function sendInvitation(UserInvitation $invitation): bool
    {
        try {
            Mail::to($invitation->email)->send(
                new UserInvitationMail($invitation)
            );

            return true;
        } catch (\Exception $e) {
            \Log::error('Failed to send user invitation', [
                'invitation_id' => $invitation->id,
                'email' => $invitation->email,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }

    public function resendInvitation(UserInvitation $invitation): bool
    {
        if ($invitation->expires_at->isPast()) {
            $invitation->update([
                'expires_at' => now()->addDays(7)
            ]);
        }

        return $this->sendInvitation($invitation);
    }

    public function cancelInvitation(UserInvitation $invitation): bool
    {
        return $invitation->delete();
    }
}
```

### 4.3 Role Service (`app/User/Services/RoleService.php`)

```php
<?php

namespace App\User\Services;

use App\Traits\UserRolesAndPermission;

class RoleService
{
    public function getAllRoles(): array
    {
        return [
            'admin' => 'Admin',
            'accountant' => 'Accountant',
            'operations_manager' => 'Operations Manager',
            'staff' => 'Data Entry Staff',
        ];
    }

    public function getRolePermissions(string $role): array
    {
        return UserRolesAndPermission::permissions($role);
    }

    public function userHasPermission(User $user, string $permission): bool
    {
        $userPermissions = $this->getRolePermissions($user->role);

        return in_array('*', $userPermissions) ||
               array_key_exists($permission, $userPermissions);
    }

    public function getRoleDisplayName(string $role): string
    {
        return UserRolesAndPermission::roles($role);
    }
}
```

## 5. Frontend Implementation

### 5.1 User Index Component (`app/Livewire/User/Index.php`)

```php
<?php

namespace App\Livewire\User;

use App\User\Services\UserService;
use App\User\Services\RoleService;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $role = '';
    public string $status = '';

    public function updated(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->role = '';
        $this->status = '';
        $this->resetPage();
    }

    #[Computed]
    public function users()
    {
        $filters = array_filter([
            'search' => $this->search,
            'role' => $this->role,
            'status' => $this->status,
        ]);

        return app(UserService::class)->getPaginatedUsers($filters);
    }

    #[Computed]
    public function roles()
    {
        return app(RoleService::class)->getAllRoles();
    }

    public function render()
    {
        return view('livewire.user.index');
    }
}
```

### 5.2 User Invitation Component (`app/Livewire/User/Invitation.php`)

```php
<?php

namespace App\Livewire\User;

use App\Mail\UserInvitationMail;
use App\Models\UserInvitation;
use App\User\Services\InvitationService;
use App\User\Services\UserService;
use Livewire\Component;

class Invitation extends Component
{
    public string $email = '';
    public string $role = 'staff';

    protected $rules = [
        'email' => 'required|email|unique:users,email|unique:user_invitations,email',
        'role' => 'required|in:admin,accountant,operations_manager,staff',
    ];

    public function sendInvitation(): void
    {
        $this->validate();

        $userService = app(UserService::class);
        $invitationService = app(InvitationService::class);

        $invitation = $userService->inviteUser(
            $this->email,
            $this->role,
            auth()->user()
        );

        if ($invitationService->sendInvitation($invitation)) {
            $this->dispatch('invitation-sent', [
                'message' => 'Invitation sent successfully!'
            ]);

            $this->reset(['email', 'role']);
        } else {
            $this->dispatch('invitation-error', [
                'message' => 'Failed to send invitation. Please try again.'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.user.invitation');
    }
}
```

## 6. Email Templates

### 6.1 User Invitation Mail (`app/Mail/UserInvitationMail.php`)

```php
<?php

namespace App\Mail;

use App\Models\UserInvitation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public UserInvitation $invitation
    ) {}

    public function build()
    {
        return $this->subject('Invitation to Join RBC Trucking Management System')
                    ->view('emails.user-invitation')
                    ->with([
                        'invitation' => $this->invitation,
                        'acceptUrl' => route('invitation.accept', $this->invitation->token),
                    ]);
    }
}
```

### 6.2 Invitation Email Template (`resources/views/emails/user-invitation.blade.php`)

```blade
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invitation to Join RBC Trucking Management System</title>
</head>
<body>
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; font-family: Arial, sans-serif;">
        <h2>You're Invited to Join RBC Trucking Management System</h2>

        <p>Hello,</p>

        <p>You have been invited to join the RBC Trucking Management System as a <strong>{{ $invitation->role_display }}</strong>.</p>

        <p>To accept this invitation and create your account, please click the button below:</p>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ $acceptUrl }}"
               style="background-color: #3B82F6; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; display: inline-block;">
                Accept Invitation
            </a>
        </div>

        <p>This invitation will expire on <strong>{{ $invitation->expires_at->format('F j, Y \a\t g:i A') }}</strong>.</p>

        <p>If you have any questions, please contact your system administrator.</p>

        <hr style="margin: 30px 0; border: none; border-top: 1px solid #E5E7EB;">

        <p style="color: #6B7280; font-size: 14px;">
            This is an automated message. Please do not reply to this email.
        </p>
    </div>
</body>
</html>
```

## 7. Routes

### 7.1 User Management Routes

```php
// User Management Routes
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/users', \App\Livewire\User\Index::class)->name('users.index');
    Route::get('/users/create', \App\Livewire\User\Create::class)->name('users.create');
    Route::get('/users/{user}', \App\Livewire\User\Show::class)->name('users.show');
    Route::get('/users/{user}/edit', \App\Livewire\User\Edit::class)->name('users.edit');

    // User Invitations
    Route::get('/users/invitations', \App\Livewire\User\Invitation::class)->name('users.invitations');
    Route::get('/users/invitations/{invitation}/resend', \App\Livewire\User\Invitation::class)->name('users.invitations.resend');
    Route::delete('/users/invitations/{invitation}', \App\Livewire\User\Invitation::class)->name('users.invitations.cancel');
});

// Public invitation acceptance
Route::get('/invitation/{token}', \App\Livewire\User\AcceptInvitation::class)->name('invitation.accept');
Route::post('/invitation/{token}', \App\Livewire\User\AcceptInvitation::class)->name('invitation.accept.store');

// User Profile (accessible by all authenticated users)
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', \App\Livewire\User\Profile::class)->name('profile');
    Route::put('/profile', \App\Livewire\User\Profile::class)->name('profile.update');
});
```

## 8. Security Considerations

### 8.1 Access Control

-   Only admins can manage users
-   Users can only view their own profile
-   Invitation tokens are cryptographically secure
-   Invitations expire after 7 days

### 8.2 Data Protection

-   Passwords are hashed using Laravel's built-in hashing
-   Email addresses are validated and unique
-   User sessions are tracked and can be invalidated
-   All user actions are logged in the audit trail

### 8.3 Rate Limiting

-   Invitation sending is rate limited
-   Password reset attempts are rate limited
-   Login attempts are rate limited

## 9. Testing Strategy

### 9.1 Unit Tests

-   UserService methods
-   InvitationService methods
-   RoleService methods
-   Email sending functionality

### 9.2 Feature Tests

-   User CRUD operations
-   Invitation workflow
-   Role-based access control
-   Profile management

### 9.3 Integration Tests

-   Email delivery
-   Database transactions
-   Audit trail integration
-   Session management

## 10. Performance Considerations

### 10.1 Database Optimization

-   Proper indexing on frequently queried columns
-   Pagination for user lists
-   Eager loading of relationships
-   Soft deletes for user data

### 10.2 Caching

-   Role permissions caching
-   User session caching
-   Invitation token caching

### 10.3 Background Jobs

-   Email sending queued
-   Audit trail cleanup
-   Session cleanup

## 11. User Experience

### 11.1 Interface Design

-   Clean, intuitive user management interface
-   Responsive design for mobile devices
-   Clear role and permission indicators
-   Bulk operations for efficiency

### 11.2 Workflow

-   Simple invitation process
-   Clear acceptance workflow
-   Easy profile management
-   Intuitive user search and filtering

## 12. Maintenance & Monitoring

### 12.1 Monitoring

-   User activity tracking
-   Failed login attempts
-   System performance metrics

### 12.2 Maintenance

-   Regular cleanup of expired invitations
-   Session cleanup
-   Audit trail archiving
-   Database optimization

## 13. Future Enhancements

### 13.1 Advanced Features

-   Two-factor authentication
-   Single sign-on (SSO) integration
-   Advanced user analytics
-   Custom role creation
-   User groups and teams

### 13.2 Integrations

-   API access management
-   Webhook notifications

## 14. Conclusion

This User Management Module provides a comprehensive yet simplified solution for managing users in the RBC Trucking Management System. It balances functionality with ease of use, ensuring that administrators can efficiently manage users while maintaining security and audit compliance.

The modular architecture allows for easy extension and maintenance, while the comprehensive testing strategy ensures reliability and performance. The system is designed to scale with the organization's growth while maintaining simplicity for end users.
