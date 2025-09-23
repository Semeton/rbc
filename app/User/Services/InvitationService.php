<?php

declare(strict_types=1);

namespace App\User\Services;

use App\Mail\UserInvitationMail;
use App\Models\User;
use App\Models\UserInvitation;
use App\Services\AuditTrailService;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class InvitationService
{
    public function __construct(
        private readonly AuditTrailService $auditTrailService
    ) {}

    /**
     * Send invitation email
     */
    public function sendInvitation(UserInvitation $invitation): bool
    {
        try {
            Mail::to($invitation->email)->send(new UserInvitationMail($invitation));

            $this->auditTrailService->log(
                'create',
                'UserInvitation',
                "Invitation email sent to '{$invitation->email}'"
            );
            
            return true;
        } catch (\Exception $e) {
            $this->auditTrailService->log(
                'error',
                'UserInvitation',
                "Failed to send invitation email to '{$invitation->email}': {$e->getMessage()}"
            );
            
            return false;
        }
    }

    /**
     * Resend invitation
     */
    public function resendInvitation(UserInvitation $invitation): bool
    {
        if ($invitation->isExpired()) {
            // Extend expiration date
            $invitation->update(['expires_at' => now()->addDays(7)]);
        }

        return $this->sendInvitation($invitation);
    }

    /**
     * Cancel invitation
     */
    public function cancelInvitation(UserInvitation $invitation): bool
    {
        if ($invitation->isAccepted()) {
            return false; // Cannot cancel accepted invitation
        }

        $result = $invitation->delete();

        if ($result) {
            $this->auditTrailService->log(
                'delete',
                'UserInvitation',
                "Invitation to '{$invitation->email}' was cancelled"
            );
        }

        return $result;
    }

    /**
     * Validate invitation token
     */
    public function validateInvitationToken(string $token): ?UserInvitation
    {
        return UserInvitation::where('token', $token)
            ->where('expires_at', '>', now())
            ->whereNull('accepted_at')
            ->first();
    }

    /**
     * Get invitation by token
     */
    public function getInvitationByToken(string $token): ?UserInvitation
    {
        return UserInvitation::where('token', $token)->first();
    }

    /**
     * Get invitation statistics
     */
    public function getInvitationStats(): array
    {
        return [
            'total' => UserInvitation::count(),
            'pending' => UserInvitation::pending()->count(),
            'accepted' => UserInvitation::accepted()->count(),
            'expired' => UserInvitation::expired()->count(),
            'by_role' => UserInvitation::selectRaw('role, COUNT(*) as count')
                ->groupBy('role')
                ->pluck('count', 'role')
                ->toArray(),
        ];
    }
}
