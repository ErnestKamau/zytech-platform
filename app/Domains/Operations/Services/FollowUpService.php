<?php

namespace App\Domains\Operations\Services;

use App\Core\Enums\FollowUpStatus;
use App\Core\Services\BaseService;
use App\Models\FollowUp;
use App\Models\User;

final class FollowUpService extends BaseService
{
    public function __construct(
        private readonly ActivityLogger $activities,
        private readonly AssignmentService $assignments,
    ) {}

    /**
     * @param  array{
     *     client_id?: ?string,
     *     quotation_request_id?: ?string,
     *     order_id?: ?string,
     *     sales_order_id?: ?string,
     *     assigned_to?: ?string,
     *     due_at?: ?\DateTimeInterface|string,
     *     notes?: ?string,
     * }  $data
     */
    public function create(array $data): FollowUp
    {
        return $this->transaction(function () use ($data): FollowUp {
            $followUp = FollowUp::query()->create([
                'client_id' => $data['client_id'] ?? null,
                'quotation_request_id' => $data['quotation_request_id'] ?? null,
                'order_id' => $data['order_id'] ?? null,
                'sales_order_id' => $data['sales_order_id'] ?? null,
                'assigned_to' => $data['assigned_to'] ?? null,
                'due_at' => $data['due_at'] ?? null,
                'status' => FollowUpStatus::Open,
                'notes' => $data['notes'] ?? null,
            ]);

            if (! empty($data['assigned_to'])) {
                $this->assignments->assign($followUp, User::query()->findOrFail($data['assigned_to']));
            }

            $this->activities->log($followUp, 'follow_up.created', [
                'assigned_to' => $followUp->assigned_to,
            ]);

            return $followUp->refresh();
        });
    }

    public function complete(FollowUp $followUp): FollowUp
    {
        $followUp->forceFill([
            'status' => FollowUpStatus::Done,
            'completed_at' => now(),
        ])->save();

        $this->activities->log($followUp, 'follow_up.completed');

        return $followUp->refresh();
    }

    public function cancel(FollowUp $followUp): FollowUp
    {
        $followUp->forceFill([
            'status' => FollowUpStatus::Cancelled,
            'completed_at' => now(),
        ])->save();

        $this->activities->log($followUp, 'follow_up.cancelled');

        return $followUp->refresh();
    }

    public function reassign(FollowUp $followUp, User $user): FollowUp
    {
        return $this->transaction(function () use ($followUp, $user): FollowUp {
            $this->assignments->assign($followUp, $user);
            $followUp->forceFill(['assigned_to' => $user->id])->save();
            $this->activities->log($followUp, 'follow_up.reassigned', [
                'assigned_to' => $user->id,
            ]);

            return $followUp->refresh();
        });
    }
}
