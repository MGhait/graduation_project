<?php

namespace App\Forms\Components;

use Filament\Forms\Components\Field;

class UserMapPicker extends Field
{
    protected string $view = 'forms.components.user-map-picker';

    protected function setUp(): void
    {
        parent::setUp();

        $this->afterStateHydrated(function (UserMapPicker $component, $state) {
            // Nothing special for now
        });

        $this->dehydrateStateUsing(function ($state, UserMapPicker $component) {
            // This won't save anything directly; you can customize later if needed.
            return null;
        });
    }
}
