<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\TruthTableResource\Pages;
use App\Filament\Admin\Resources\TruthTableResource\RelationManagers;
use App\Http\Resources\TruthTableResource as ResourcesTruthTableResource;
use App\Models\TruthTable;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Get;

// use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

class TruthTableResource extends Resource
{
    protected static ?string $model = TruthTable::class;

    protected static ?string $navigationIcon = 'heroicon-o-table-cells';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('ic_id')
                ->relationship('ic', 'name')
                ->required()
                ->searchable()
                ->preload()
                ->label('IC Chip')
                ->live()
                ->afterStateUpdated(function ($state, Forms\Set $set) {
                    // Clear rows when IC changes
                    $set('rows', []);
//                    $set('input_count', null);
                }),

            Forms\Components\Select::make('input_count')
                ->label('Number of Inputs')
                ->options([
                    2 => '2 Inputs (A, B)',
//                    3 => '3 Inputs (A, B, C)',
                    4 => '4 Inputs (A, B, C, D)',
                ])
                ->default(2)
                ->required()
                ->live()
                ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                    // Generate all possible input combinations when input count changes
                    $inputCount = (int)$state;
                    $combinations = pow(2, $inputCount);
                    $rows = [];

                    for ($i = 0; $i < $combinations; $i++) {
                        $binary = str_pad(decbin($i), $inputCount, '0', STR_PAD_LEFT);
                        $rows[] = [
                            'input' => $binary,
                            'output' => false, // Default to false
                        ];
                    }

                    $set('rows', $rows);
                }),

//            Forms\Components\Section::make('Truth Table')
//                ->schema(function (Get $get) {
//                    $icId = $get('ic_id');
//                    $inputCount = (int)$get('input_count');
//                    $existingRows = $get('rows') ?? [];
//
//                    if (!$icId || !$inputCount) {
//                        return [];
//                    }
//
//                    // Get IC type to determine default outputs
//                    $icType = $icId ? \App\Models\Ic::find($icId)->type : null;
//                    $combinations = pow(2, $inputCount);
//                    $rows = [];
//
//                    for ($i = 0; $i < $combinations; $i++) {
//                        $binary = str_pad(decbin($i), $inputCount, '0', STR_PAD_LEFT);
//
//                        // Set intelligent default based on IC type
//                        $defaultOutput = self::getDefaultOutput($binary, $icType);
//
//                        // Use existing output if available, otherwise use calculated default
//                        $output = $existingRows[$binary]['output'] ?? $defaultOutput;
//
//                        $rows[] = Forms\Components\Grid::make($inputCount + 1)
//                            ->schema([
//                                // Display input bits
//                                ...array_map(fn($bit, $idx) => Forms\Components\Placeholder::make("bit_{$idx}")
//                                    ->label(['A', 'B', 'C', 'D'][$idx])
//                                    ->content($bit),
//                                    str_split($binary), range(0, $inputCount - 1)),
//
//                                // Output toggle with proper default
//                                Forms\Components\Toggle::make("rows.{$binary}.output")
//                                    ->inline()
//                                    ->default($output),
//
//                                Forms\Components\Hidden::make("rows.{$binary}.input")
//                                    ->default($binary)
//                            ]);
//                    }
//
//                    return $rows;
//                })

            Forms\Components\Section::make('Truth Table')
                ->schema([
                    Repeater::make('rows')
                        ->label('')
                        ->schema([
                            Forms\Components\TextInput::make('input')
                                ->label('Input')
                                ->readOnly()
                                ->extraInputAttributes(['class' => 'bg-gray-100 font-mono'])
//                                ->formatStateUsing(function ($state, Get $get) {
//                                    // Format input with labels (A=0, B=1, etc.)
//                                    if (!$state) return '';
//                                    return self::formatInputWithLabels($state);
//                                }),
                                ->formatStateUsing(function ($state, Get $get) {
                                    // Format input with labels (A=0, B=1, etc.)
                                    if (!$state || !is_string($state)) return '';

                                    // Additional safety check
                                    if (!preg_match('/^[01]+$/', $state)) {
                                        return $state; // Return as-is if not binary
                                    }

                                    return self::formatInputWithLabels($state);
                                }),
                            Forms\Components\Toggle::make('output')
                                ->label('Output')
                                ->onColor('success')
                                ->offColor('danger')
                                ->onIcon('heroicon-o-check-circle')
                                ->offIcon('heroicon-o-x-circle')
                                ->default(false),
                        ])
                        ->columns(2)
                        ->reorderable(false)
                        ->addable(false)
                        ->deletable(false)
                        ->columnSpanFull()
                        ->itemLabel(function (array $state): ?string {
                            return $state['input'] ?? null;
                        }),
                ])
                ->columnSpanFull()
                ->hidden(fn(Get $get) => empty($get('rows'))),
        ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('ic.name')
                    ->label('IC Name')
                    ->sortable()
                    ->searchable(),

                // Combined binary input with spaces
                Tables\Columns\TextColumn::make('input')
                    ->label('Input')
//                    ->formatStateUsing(fn(string $state) => implode(' ', str_split($state)))
//                    ->formatStateUsing(fn(string $state) => self::formatInputWithLabels($state))
                    ->formatStateUsing(function (string $state) {
                        // Add safety check for empty or invalid input
                        if (empty($state) || !ctype_digit(str_replace(['0', '1'], '', $state))) {
                            return $state; // Return as-is if not binary
                        }
                        return self::formatInputWithLabels($state);
                    })
                    ->searchable()
//                    ->toggleable(isToggledHiddenByDefault: true) // Hide by default if you prefer
                    ->fontFamily('mono'),

                // Individual input columns
//                Tables\Columns\TextColumn::make('input_a')
//                    ->label('A')
//                    ->getStateUsing(fn($record) => $record->input[0] ?? '[x]')
//                    ->alignCenter(),
//
//                Tables\Columns\TextColumn::make('input_b')
//                    ->label('B')
//                    ->getStateUsing(fn($record) => $record->input[1] ?? '[x]')
//                    ->alignCenter(),
//
//                Tables\Columns\TextColumn::make('input_c')
//                    ->label('C')
//                    ->getStateUsing(fn($record) => $record->input[2] ?? '[x]')
//                    ->alignCenter(),
//
//                Tables\Columns\TextColumn::make('input_d')
//                    ->label('D')
//                    ->getStateUsing(fn($record) => $record->input[3] ?? '[x]')
//                    ->alignCenter(),

                Tables\Columns\TextColumn::make('output')
                    ->label('Output')
                    ->alignCenter()
                    ->sortable()
                    ->weight('bold')
                    ->color(fn($state) => match ($state) {
                        0 => 'danger',
                        1 => 'success',
                        default => 'gray',
                    })
                    ->icon(fn($state) => match ($state) {
                        0 => 'heroicon-o-x-circle',
                        1 => 'heroicon-o-check-circle',
                        default => null,
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

            ])
            ->filters([
                Tables\Filters\SelectFilter::make('ic')
                    ->relationship('ic', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->groups([
                Tables\Grouping\Group::make('ic.name')
                    ->label('By IC Chip')
                    ->collapsible(),
            ])
            ->defaultGroup('ic.name')
            ->defaultSort('ic.name')
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }


//    private static function formatInputWithLabels(string $binary): string
//    {
//        $labels = ['A', 'B', 'C', 'D'];
//        $inputs = str_split($binary);
//        $formatted = [];
//
//        foreach ($inputs as $index => $value) {
//            $formatted[] = $labels[$index] . '=' . $value;
//        }
//
//        return implode(', ', $formatted);
//    }

    private static function formatInputWithLabels(string $binary): string
    {
        $inputs = str_split($binary);
        $formatted = [];

        foreach ($inputs as $index => $value) {
            // Generate label dynamically (A, B, C, D, E, F, etc.)
            $label = chr(65 + $index); // 65 is ASCII for 'A'
            $formatted[] = $label . '=' . $value;
        }

        return implode(', ', $formatted);
    }
    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTruthTables::route('/'),
            'create' => Pages\CreateTruthTable::route('/create'),
            'edit' => Pages\EditTruthTable::route('/{record}/edit'),
        ];
    }
}
