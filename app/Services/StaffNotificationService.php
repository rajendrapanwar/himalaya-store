<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Notification as NotificationFacade;

class StaffNotificationService
{
    /** @param list<string> $roles */
    public function send(Notification $notification, array $roles = []): void
    {
        $staff = User::query()
            ->where(function ($query) {
                $query->where('is_admin', true)
                    ->orWhereIn('role', ['administrator', 'manager', 'catalogue', 'fulfillment', 'support']);
            })
            ->when($roles !== [], fn ($query) => $query->where(function ($staffQuery) use ($roles) {
                $staffQuery->whereIn('role', $roles);
                if (in_array('administrator', $roles, true)) {
                    $staffQuery->orWhere(function ($legacyAdminQuery) {
                        $legacyAdminQuery->where('is_admin', true)
                            ->where(function ($roleQuery) {
                                $roleQuery->whereNull('role')->orWhereIn('role', ['customer', 'administrator']);
                            });
                    });
                }
            }))
            ->get();

        if ($staff->isNotEmpty()) {
            NotificationFacade::send($staff, $notification);
        }
    }
}
