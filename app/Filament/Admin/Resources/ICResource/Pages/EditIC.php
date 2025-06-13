<?php

namespace App\Filament\Admin\Resources\ICResource\Pages;

use App\Filament\Admin\Resources\ICResource;
use App\Models\File;
use App\Models\ICDetails;
use App\Models\Image;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class EditIC extends EditRecord
{
    protected static string $resource = ICResource::class;
    protected $imagesToDelete = [];
    protected $filesToDelete = [];

    private $icDetailsData = [];

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
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Store IC details data for later processing
        $this->icDetailsData = [
            'description' => $data['icDetail']['description'] ?? null,
            'chip_image' => $data['chip_image'] ?? null,
            'logic_diagram_image' => $data['logic_diagram_image'] ?? null,
        ];


        // Handle image upload
        if (isset($data['uploaded_image']) && $data['uploaded_image'] !== $this->record->mainImage?->url) {
            if ($this->record->image) {
                $this->imagesToDelete[] = $this->record->image;
            }
            $imageUrl = $data['uploaded_image'];
            $image = Image::create([
                'url' => $imageUrl,
            ]);
            $data['image'] = $image->id;
        }
        unset($data['uploaded_image']);

        // Handle blog diagram upload
        if (isset($data['blog_diagram_image']) && $data['blog_diagram_image'] !== $this->record->blogDiagram?->url) {
            if ($this->record->blog_diagram) {
                $this->imagesToDelete[] = $this->record->blog_diagram;
            }
            $diagramUrl = $data['blog_diagram_image'];
            $diagram = Image::create([
                'url' => $diagramUrl,
            ]);
            $data['blog_diagram'] = $diagram->id;
        }
        unset($data['blog_diagram_image']);

        // Handle datasheet upload
        if (isset($data['datasheet_file']) && $data['datasheet_file'] !== basename($this->record->file?->path ?? '')) {
            if ($this->record->datasheet_file_id) {
                $this->filesToDelete[] = $this->record->datasheet_file_id;
            }
            $fullName = $data['datasheet_file'];
            $datasheetPath = 'files/' .  $fullName;
            $fileExtension = pathinfo($datasheetPath, PATHINFO_EXTENSION);
            $name = pathinfo($fullName, PATHINFO_FILENAME);

            $datasheet = File::create([
                'name' => $name,
                'path' => $datasheetPath,
                'type' => $this->getFileType($fileExtension),
            ]);
            $data['datasheet_file_id'] = $datasheet->id;
        }
        unset($data['datasheet_file']);

        return $data;
    }

    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        return DB::transaction(function () use ($record, $data) {
            // Update the main IC record
            $record->update($data);

            // Update IC details
            if (!empty($this->icDetailsData)) {
                $this->updateIcDetails($record);
            }

            // Clean up old files/images
            $this->cleanupOldFiles();

            return $record;
        });
    }

    private function updateIcDetails($icRecord): void
    {
        $icDetails = $icRecord->icDetail;

        if (!$icDetails) {
            // Create new IC details if doesn't exist
            $icDetails = new ICDetails(['ic_id' => $icRecord->id]);
        }

        // Update description
        if (isset($this->icDetailsData['description'])) {
            $icDetails->description = $this->icDetailsData['description'];
        }

        // Handle chip image update
        if (isset($this->icDetailsData['chip_image'])) {
            $currentUrl = $icDetails->chipImage?->url;
            if ($this->icDetailsData['chip_image'] !== $currentUrl) {
                // Mark old image for deletion
                if ($icDetails->chip) {
                    $this->imagesToDelete[] = $icDetails->chip;
                }

                // Create new image
                $chipImage = Image::create([
                    'url' => $this->icDetailsData['chip_image'],
                ]);
                $icDetails->chip = $chipImage->id;
            }
        }

        // Handle logic diagram image update
        if (isset($this->icDetailsData['logic_diagram_image'])) {
            $currentUrl = $icDetails->logicDiagramImage?->url;
            if ($this->icDetailsData['logic_diagram_image'] !== $currentUrl) {
                // Mark old image for deletion
                if ($icDetails->logic_diagram) {
                    $this->imagesToDelete[] = $icDetails->logic_diagram;
                }

                // Create new image
                $logicDiagramImage = Image::create([
                    'url' => $this->icDetailsData['logic_diagram_image'],
                ]);
                $icDetails->logic_diagram = $logicDiagramImage->id;
            }
        }
        $icDetails->save();
    }

    private function cleanupOldFiles(): void
    {
        // Delete old files
        if (!empty($this->filesToDelete)) {
            $oldFiles = File::whereIn('id', $this->filesToDelete)->get();
            foreach ($oldFiles as $file) {
                Storage::disk('public')->delete($file->path);
            }
            File::whereIn('id', $this->filesToDelete)->delete();
        }

        // Delete old images
        if (!empty($this->imagesToDelete)) {
            $oldImages = Image::whereIn('id', $this->imagesToDelete)->get();
            foreach ($oldImages as $image) {
                Storage::disk('public')->delete('images/' . $image->url);
            }
            Image::whereIn('id', $this->imagesToDelete)->delete();
        }
    }

    protected function getFileType(string $extension): string
    {
        return match (strtolower($extension)) {
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            default => 'application/octet-stream',
        };
    }

    // cleanup method done this part
//    protected function afterSave(): void
//    {
//        if (!empty($this->filesToDelete)) {
//            File::whereIn('id', $this->filesToDelete)->delete();
//        }
//
//        if (!empty($this->imagesToDelete)) {
//            Image::whereIn('id', $this->imagesToDelete)->delete();
//        }
//    }
    protected function getHeaderActions(): array
    {
        return [
//            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
