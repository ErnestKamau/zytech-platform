<?php

namespace App\Domains\Operations\Services;

use App\Models\AssignmentHistory;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

final class AssignmentService
{
    public function assign(Model $subject, User $user, ?User $assignedBy = null): AssignmentHistory
    {
        AssignmentHistory::query()
            ->where('subject_type', $subject->getMorphClass())
            ->where('subject_id', $subject->getKey())
            ->whereNull('unassigned_at')
            ->update(['unassigned_at' => now()]);

        return AssignmentHistory::query()->create([
            'subject_type' => $subject->getMorphClass(),
            'subject_id' => $subject->getKey(),
            'user_id' => $user->id,
            'assigned_by' => ($assignedBy ?? Auth::user())?->getKey(),
            'assigned_at' => now(),
        ]);
    }
}
