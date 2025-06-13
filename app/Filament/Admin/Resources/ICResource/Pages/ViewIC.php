<?php

namespace App\Filament\Admin\Resources\ICResource\Pages;

use App\Filament\Admin\Resources\ICResource;
use App\Filament\Admin\Resources\ICResource\RelationManagers\FeaturesRelationManager;
use App\Filament\Admin\Resources\ICResource\RelationManagers\ParametersRelationManager;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewIC extends ViewRecord
{
    protected static string $resource = ICResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Load IC details data for form display
        if ($this->record->icDetail) {
            $data['icDetail'] = [
                'description' => $this->record->icDetail->description,
            ];

            // Load existing images for form display
            if ($this->record->icDetail->chipImage) {
                $data['chip_image'] = $this->record->icDetail->chipImage->url;
            }

            if ($this->record->icDetail->logicDiagram) {
                $data['logic_diagram_image'] = $this->record->icDetail->logicDiagram->url;
            }
        }

        // Load existing main images for form display
        if ($this->record->mainImage) {
            $data['uploaded_image'] = $this->record->mainImage->url;
        }

        if ($this->record->blogDiagram) {
            $data['blog_diagram_image'] = $this->record->blogDiagram->url;
        }

        // Load existing datasheet for form display
        if ($this->record->file) {
            $data['datasheet_file'] = basename($this->record->file->path);
        }

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
