<?php

namespace App\Filament\Admin\Resources\TruthTableResource\Pages;

use App\Filament\Admin\Resources\TruthTableResource;
use App\Models\TruthTable;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateTruthTable extends CreateRecord
{
    protected static string $resource = TruthTableResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

//    protected function mutateFormDataBeforeCreate(array $data): array
//    {
//        // First record to return
//        $firstRecord = [
//            'ic_id' => $data['ic_id'],
//            'input' => $data['rows'][0]['input'] ?? '0',
//            'output' => $data['rows'][0]['output'] ?? 0,
//        ];
//
//        // Create a collection of all rows
//        $allRows = [];
//        foreach ($data['rows'] as $row) {
//            $allRows[] = [
//                'ic_id' => $data['ic_id'],
//                'input' => $row['input'],
//                'output' => (int)$row['output'],
//                'created_at' => now(),
//                'updated_at' => now(),
//            ];
//        }
//
//        // Store the rows in the session to be used after creation
//        session(['truth_table_rows' => $allRows]);
//
//        // Return only data for the first record
//        return $firstRecord;
//    }

    protected function handleRecordCreation(array $data): Model
    {
        $icId = $data['ic_id'];
        $rows = $data['rows'] ?? [];

//        dd($rows, $icId, $data);

        TruthTable::where('ic_id', $icId)->delete();

        // Create all truth table entries
        $bulkData = [];
        foreach ($rows as $row) {
                $bulkData[] = static::getModel()::updateOrCreate([
                    'ic_id' => $icId,
                    'input' => $row['input'],
                    'output' => $row['output'] ?? false,
                ]);
        }

        return $createdRecords[0] ?? static::getModel()::create([
            'ic_id' => $icId,
            'input' => '00',
            'output' => false,
        ]);
    }

//    protected function getCreatedNotificationTitle(): ?string
//    {
//        return 'Truth table created successfully';
//    }
}
