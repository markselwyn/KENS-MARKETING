<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

final class SystemAudit
{
    /**
     * Record a business action with enough context to identify who did what.
     */
    public static function record(
        string $module,
        string $action,
        string $description,
        ?Model $subject = null,
        array $details = [],
        ?Model $actorOverride = null
    ): void {
        $actor = $actorOverride ?? Auth::user();

        $activity = activity('system')
            ->withProperties([
                'module' => $module,
                'action' => $action,
                'actor_name' => $actor?->name ?? 'System / Guest',
                'actor_role' => $actor ? strtolower(trim((string) $actor->role)) : 'system',
                'ip_address' => request()->ip(),
                'details' => $details,
            ]);

        if ($actor) {
            $activity->causedBy($actor);
        }

        if ($subject) {
            $activity->performedOn($subject);
        }

        $activity->log($description);
    }
}
