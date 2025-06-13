<?php

namespace App\Filament\Admin\Resources\TruthTableResource\Pages;

use App\Filament\Admin\Resources\TruthTableResource;
use App\Models\TruthTable;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;


class EditTruthTable extends EditRecord
{
    protected static string $resource = TruthTableResource::class;


    protected function mutateFormDataBeforeFill(array $data): array
    {

        // Load all existing truth table entries for this IC
        $icId = $this->record->ic_id;
        $allRows = TruthTable::where('ic_id', $icId)
            ->orderBy('input')
            ->get();

        $inputCount = strlen($allRows->first()->input ?? '00');

        $rows = $allRows->map(function ($row) {
            return [
                'input' => $row->input,
                'output' => (bool)$row->output,
            ];
        })->toArray();

        return [
            'ic_id' => $icId,
            'input_count' => $inputCount,
            'rows' => $rows,
        ];

//        $existingEntries = TruthTable::where('ic_id', $this->record->ic_id)->get();

        // Determine input count from first entry
//        $firstEntry = $existingEntries->first();
//        $data['input_count'] = strlen($firstEntry->input);
//
//        // Prepare rows data in format: ['input' => ['input' => '...', 'output' => 0/1]]
//        $data['rows'] = [];
//        foreach ($existingEntries as $entry) {
//            $data['rows'][$entry->input] = [
//                'input' => $entry->input,
//                'output' => $entry->output,
//            ];
//        }
//        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $icId = $data['ic_id'];
        $rows = $data['rows'] ?? [];

        // Delete existing records
        TruthTable::where('ic_id', $icId)->delete();

        // Prepare all truth table rows for bulk insert
//        $recordsToInsert = [];
//        foreach ($rows as $input => $rowData) {
//            $recordsToInsert[] = [
//                'ic_id' => $icId,
//                'input' => $input,
//                'output' => (int)$rowData['output'],
//                'created_at' => now(),
//                'updated_at' => now(),
//            ];
//        }
        foreach ($rows as $row) {
            TruthTable::create([
                'ic_id' => $icId,
                'input' => $row['input'],
                'output' => $row['output'] ?? false,
            ]);
        }

//            TruthTable::insert($recordsToInsert);


        // Get the first record to return for Filament
         return TruthTable::where('ic_id', $icId)->first() ?? $record;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Truth table updated successfully';
    }
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->before(function () {
                    // Delete all truth table entries when deleting the IC
                    TruthTable::where('ic_id', $this->record->ic_id)->delete();
                }),
        ];
    }
}
