<?php

namespace App\Filament\Admin\Resources\MessageResource\Pages;

use App\Filament\Admin\Resources\MessageResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewMessage extends ViewRecord
{
    protected static string $resource = MessageResource::class;

    protected function getHeaderActions(): array
    {
       return [];
    }

    public function mount(int|string $record): void
    {
        parent::mount($record);

        // Automatically mark the message as read
        if ($this->record->status === 'unread') {
            $this->record->update(['status' => 'read']);
        }
    }
}
