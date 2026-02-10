<?php

namespace App\Livewire\Venue;

use Livewire\Component;

class BillingSuccessBanner extends Component
{
    public bool $dismissed = false;

    public function dismiss(): void
    {
        $this->dismissed = true;
    }

    public function render()
    {
        $show = ! $this->dismissed && (request()->query('success') || request()->query('setup'));
        $isSetup = request()->query('setup') == '1';

        return view('livewire.venue.billing-success-banner', [
            'showBanner' => $show,
            'isSetup' => $isSetup,
        ]);
    }
}
