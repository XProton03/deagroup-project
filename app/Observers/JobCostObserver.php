<?php

namespace App\Observers;

use App\Models\JobCost;
use App\Models\Task;
use App\Models\Quotation;

class JobCostObserver
{
    /**
     * Handle the JobCost "created" event.
     */
    public function created(JobCost $jobCost): void
    {
        // Ambil task terkait
        // $task = $jobCost->tasks;

        // // Jika task memiliki quotation terkait
        // if ($task && $task->quotations) {
        //     $quotation = $task->quotations;

        //     // Tambahkan job_cost terbaru ke price_tasks
        //     $additionalCost = $jobCost->mandays + $jobCost->transports + $jobCost->accomodations;
        //     //$quotation->increment('price_tasks', $additionalCost);
        //     // Update completion_percentage di quotation
        //     $quotation->update([
        //         'price_tasks' => $additionalCost,
        //     ]);
        // }
        $this->updateQuotationPriceTasks($jobCost);
    }

    /**
     * Handle the JobCost "updated" event.
     */
    public function updated(JobCost $jobCost): void
    {
        $this->updateQuotationPriceTasks($jobCost);
    }

    /**
     * Handle the JobCost "deleted" event.
     */
    public function deleted(JobCost $jobCost): void
    {
        $this->updateQuotationPriceTasks($jobCost);
    }

    /**
     * Handle the JobCost "restored" event.
     */
    public function restored(JobCost $jobCost): void
    {
        //
    }

    /**
     * Handle the JobCost "force deleted" event.
     */
    public function forceDeleted(JobCost $jobCost): void
    {
        //
    }

    protected function updateQuotationPriceTasks(JobCost $jobCost)
    {
        // Ambil task terkait
        $task = $jobCost->tasks;

        if ($task) {
            // Hitung total job_costs untuk task ini
            $totalJobCosts =
                $task->job_costs()->sum('mandays') +
                $task->job_costs()->sum('transports') +
                $task->job_costs()->sum('accomodations');

            // Update price_tasks di quotation yang terkait
            $quotation = $task->quotations;
            if ($quotation) {
                $quotation->price_tasks = $quotation->tasks->sum(function ($task) {
                    return $task->job_costs()->sum('mandays') +
                        $task->job_costs()->sum('transports') +
                        $task->job_costs()->sum('accomodations');
                });

                $quotation->save();
            }
        }
    }
}
