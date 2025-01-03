<?php

namespace App\Observers;

use App\Models\Task;

class TaskObserver
{
    /**
     * Handle the Task "created" event.
     */
    public function created(Task $task): void
    {
        $quotation = $task->quotations;

        if ($quotation) {
            // Hitung total task dan task yang selesai
            $totalTasks = $quotation->tasks()->count();
            $completedTasks = $quotation->tasks()->where('status', 'Completed')->count();

            // Hitung persentase penyelesaian
            $completionPercentage = ($totalTasks > 0)
                ? ($completedTasks / $totalTasks) * 100
                : 0;

            // Update completion_percentage di quotation
            $quotation->update([
                'completion_percentage' => $completionPercentage,
            ]);
        }
    }

    /**
     * Handle the Task "updated" event.
     */
    public function updated(Task $task): void
    {
        $quotation = $task->quotations;

        if ($quotation) {
            // Hitung total task dan task yang selesai
            $totalTasks = $quotation->tasks()->count();
            $completedTasks = $quotation->tasks()->where('status', 'Completed')->count();

            // Hitung persentase penyelesaian
            $completionPercentage = ($totalTasks > 0)
                ? ($completedTasks / $totalTasks) * 100
                : 0;

            // Update completion_percentage di quotation
            $quotation->update([
                'completion_percentage' => $completionPercentage,
            ]);
        }
    }

    /**
     * Handle the Task "deleted" event.
     */
    public function deleted(Task $task): void
    {
        //
    }

    /**
     * Handle the Task "restored" event.
     */
    public function restored(Task $task): void
    {
        //
    }

    /**
     * Handle the Task "force deleted" event.
     */
    public function forceDeleted(Task $task): void
    {
        //
    }
}
