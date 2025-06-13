<?php

namespace App\Filament\Admin\Resources\ICResource\Pages;

use App\Filament\Admin\Resources\ICResource;
use App\Models\File;
use App\Models\ICDetails;
use App\Models\Image;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CreateIC extends CreateRecord
{
    protected static string $resource = ICResource::class;
    private $icDetailsData = [];

    protected function getRedirectUrl(): string
    {
        return $this->previousUrl ?? $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Handle image upload
        if (isset($data['uploaded_image'])) {
            $imageUrl = $data['uploaded_image'];
            $image = Image::create([
                'url' => $imageUrl,
            ]);
            $data['image'] = $image->id;
        }
        unset($data['uploaded_image']);

        // Handle blog diagram upload
        if (isset($data['blog_diagram_image'])) {
            $diagramUrl = $data['blog_diagram_image'];
            $diagram = Image::create([
                'url' => $diagramUrl,
            ]);
            $data['blog_diagram'] = $diagram->id;
        }
        unset($data['blog_diagram_image']);

        // Handle datasheet upload
        if (isset($data['datasheet_file'])) {
            $fullName = $data['datasheet_file'];
            $datasheetPath = 'files/' . $fullName;
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

        $this->icDetailsData = [
            'description' => $data['icDetail']['description'] ?? null,
            'chip_image' => $data['chip_image'] ?? null,
            'logic_diagram_image' => $data['logic_diagram_image'] ?? null,
        ];

        unset($data['icDetail'], $data['chip_image'], $data['logic_diagram_image']);

        return $data;
    }

//    protected function afterCreate(): void
//    {
//        // Create IC details after the main IC record is created
//        if (!empty($this->icDetailsData)) {
//            $icDetailsData = ['ic_id' => $this->record->id];
//
//            // Handle chip image
//            if (isset($this->icDetailsData['chip_image'])) {
//                $chipImage = Image::create([
//                    'url' => $this->icDetailsData['chip_image'],
//                ]);
//                $icDetailsData['chip'] = $chipImage->id;
//            }
//
//            // Handle logic diagram image
//            if (isset($this->icDetailsData['logic_diagram_image'])) {
//                $logicDiagramImage = Image::create([
//                    'url' => $this->icDetailsData['logic_diagram_image'],
//                ]);
//                $icDetailsData['logic_diagram'] = $logicDiagramImage->id;
//            }
//
//            // Add description
//            if (isset($this->icDetailsData['description'])) {
//                $icDetailsData['description'] = $this->icDetailsData['description'];
//            }
//
//            // Create the IC details record
//            ICDetails::create($icDetailsData);
//        }
//    }


    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        return DB::transaction(function () use ($data) {
            // Create the main IC record
            $record = static::getModel()::create($data);

            // Create IC details if we have the data
            if (!empty($this->icDetailsData)) {
                $this->createIcDetails($record);
            }

            return $record;
        });
    }

    private function createIcDetails($icRecord): void
    {
        $icDetailsData = ['ic_id' => $icRecord->id];

        // Handle chip image
        if (isset($this->icDetailsData['chip_image'])) {
            $chipImage = Image::create([
                'url' => $this->icDetailsData['chip_image'],
            ]);
            $icDetailsData['chip'] = $chipImage->id;
        }

        // Handle logic diagram image
        if (isset($this->icDetailsData['logic_diagram_image'])) {
            $logicDiagramImage = Image::create([
                'url' => $this->icDetailsData['logic_diagram_image'],
            ]);
            $icDetailsData['logic_diagram'] = $logicDiagramImage->id;
        }

        // Add description (from MarkdownEditor)
        if (isset($this->icDetailsData['description'])) {
            $icDetailsData['description'] = $this->icDetailsData['description'];
        }

        // Create the IC details record
        ICDetails::create($icDetailsData);
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
}
