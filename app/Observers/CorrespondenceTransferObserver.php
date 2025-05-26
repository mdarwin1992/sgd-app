<?php

namespace App\Observers;

use App\Models\CorrespondenceTransfer;
use App\Models\Notification;
use App\Models\Office;

class CorrespondenceTransferObserver
{
    /**
     * Handle the CorrespondenceTransfer "created" event.
     *
     * @param \App\Models\CorrespondenceTransfer $correspondenceTransfer
     * @return void
     */
    public function created(CorrespondenceTransfer $correspondenceTransfer)
    {
        $office = Office::findOrFail($correspondenceTransfer->office_id);

        Notification::create([
            'user_id' => $office->user_id,
            'correspondence_transfer_id' => $correspondenceTransfer->id,
        ]);
    }

    /**
     * Handle the CorrespondenceTransfer "updated" event.
     *
     * @param \App\Models\CorrespondenceTransfer $correspondenceTransfer
     * @return void
     */
    public function updated(CorrespondenceTransfer $correspondenceTransfer)
    {
        //
    }

    /**
     * Handle the CorrespondenceTransfer "deleted" event.
     *
     * @param \App\Models\CorrespondenceTransfer $correspondenceTransfer
     * @return void
     */
    public function deleted(CorrespondenceTransfer $correspondenceTransfer)
    {
        //
    }

    /**
     * Handle the CorrespondenceTransfer "restored" event.
     *
     * @param \App\Models\CorrespondenceTransfer $correspondenceTransfer
     * @return void
     */
    public function restored(CorrespondenceTransfer $correspondenceTransfer)
    {
        //
    }

    /**
     * Handle the CorrespondenceTransfer "force deleted" event.
     *
     * @param \App\Models\CorrespondenceTransfer $correspondenceTransfer
     * @return void
     */
    public function forceDeleted(CorrespondenceTransfer $correspondenceTransfer)
    {
        //
    }
}
