<?php

namespace App\Observers;

use App\Models\Starship;
use App\Models\User;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {

        $navesBase = Starship::whereNull('user_id')->with('pilots')->get();

        foreach ($navesBase as $nave) {
            $clon = $user->starships()->create([
                'name' => $nave->name,
                'model' => $nave->model,
                'manufacturer' => $nave->manufacturer,
                'cost_in_credits' => $nave->cost_in_credits,
                'image2_url' => $nave->image2_url,
            ]);

            // Copiar los pilotos vinculados a la nave original
            $clon->pilots()->attach($nave->pilots->pluck('id'));
        }
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        //
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        //
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}
