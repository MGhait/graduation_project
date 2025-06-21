<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ICResource\Pages;
use App\Filament\Admin\Resources\ICResource\RelationManagers;
use App\Helpers\UniqueSlug;
use App\Models\IC;
use App\Models\Image;
use Filament\Forms;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class ICResource extends Resource
{
    protected static ?string $model = IC::class;

    protected static ?string $modelLabel = "IC";

    protected static ?string $navigationIcon = 'heroicon-o-cpu-chip';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['mainImage']);
    }

    protected static function wizardForm(): array // need some UI customization
    {
        return [
            Wizard::make([
                Wizard\Step::make('Main Data')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Code')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('commName')
                            ->label("Commercial Name")
                            ->maxLength(255)
                            ->default(null)
                            ->reactive()
                            ->afterStateUpdated(fn(Forms\Set $set, ?string $state) => $set('slug', UniqueSlug::make($state, IC::class))),
                        Forms\Components\TextInput::make('slug') // to handled later
                        ->label("Slug")
                            ->required()
                            ->disabled()
                            ->dehydrated(true)
                            ->maxLength(255),
                        Forms\Components\TextInput::make('manName')
                            ->label('Manufacture Name')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('videoUrl')
                            ->maxLength(255),
                    ]),
                Wizard\Step::make('Details') // need to check relation here
                    ->schema([
                        Section::make('Description')
                            ->schema([
                                Forms\Components\MarkdownEditor::make('icDetail.description')
                                    ->required(fn (string $operation): bool => $operation === 'create')
                                    ->columnSpanFull()->visibleOn(['create', 'edit']),
                                Textarea::make('icDetail.description')
                                    ->visibleOn('view')->columnSpanFull()
                            ])->collapsible(),
                        Section::make('Images')
                            ->schema([
                                Forms\Components\FileUpload::make('chip_image')
                                    ->label('Chip Image')
                                    ->image()
                                    ->disk('public')
                                    ->directory('images')
                                    ->required(fn(string $operation): bool => $operation === 'create')
                                    ->saveUploadedFileUsing(function ($file) {
                                        $filename = time() . '-' . $file->getClientOriginalName();
                                        $storedPath = $file->storeAs('images', $filename, 'public');
                                        return basename($storedPath);
                                    })
                                    ->columns(1),

                                Forms\Components\FileUpload::make('logic_diagram_image')
                                    ->label('Logic Diagram')
                                    ->image()
                                    ->disk('public')
                                    ->directory('images')
                                    ->required(fn(string $operation): bool => $operation === 'create')
                                    ->saveUploadedFileUsing(function ($file) {
                                        $filename = time() . '-' . $file->getClientOriginalName();
                                        $storedPath = $file->storeAs('images', $filename, 'public');
                                        return basename($storedPath);
                                    })
                                    ->columns(1),
                            ])
                            ->visibleOn(['create', 'edit'])
                            ->columns(2)->collapsible(),
                    Section::make('Current Images')
                        ->schema([
                            ViewField::make('current_chip_image_preview')
                                ->label("Current Chip Image")
                                ->view('filament.components.image-preview')
                                ->visibleOn(['edit'])
                                ->visible(fn($record) => $record?->icDetail?->chipImage)
                                ->dehydrated(true)
                                ->afterStateHydrated(function ($component, $state, $record) {
                                    if ($record && $record->icDetail && $record->icDetail->chipImage) {
                                        $component->state($record->icDetail->chipImage->url);
                                    }
                                }),

                            ViewField::make('current_logic_diagram_preview')
                                ->label("Current Logic Diagram")
                                ->view('filament.components.image-preview')
                                ->visibleOn(['edit'])
                                ->visible(fn($record) => $record?->icDetail?->logicDiagram)
                                ->dehydrated(false)
                                ->afterStateHydrated(function ($component, $state, $record) {
                                    if ($record && $record->icDetail && $record->icDetail->logicDiagram) {
                                        $component->state($record->icDetail->logicDiagram->url);
                                    }
                                }),
                        ])->columns(2)->collapsible()->visibleOn(['edit', 'view']),
                    ]),
                Wizard\Step::make('Meta')
                    ->schema([
                        Forms\Components\Group::make([
                            Section::make('IC Image')
                                ->schema([
                                    Forms\Components\Group::make(
                                        [
                                            FileUpload::make('uploaded_image')
                                                ->label(function ($operation) {
                                                    return $operation == 'edit' ? 'Edit Image' : 'Add Image';
                                                })
                                                ->image()
                                                ->disk('public')
                                                ->directory('images')// Directory Where Images Will Be Stored
                                                ->saveUploadedFileUsing(function ($file) {
                                                    $filename = time() . '-' . $file->getClientOriginalName();
                                                    $storedPath = $file->storeAs('images', $filename, 'public');
                                                    return basename($storedPath);
                                                })->visibleOn(['create', 'edit']),
                                            // This field shows the current image
                                            ViewField::make('current_image')
                                                ->label("Preview Image")
                                                ->view('filament.components.image-preview')
                                                ->visibleOn(['edit', 'view'])
                                                ->visible(fn($get) => !$get('uploaded_image'))
                                                ->visible(fn($record) => $record?->mainImage)
                                                ->dehydrated(false)
                                                ->afterStateHydrated(function ($component, $state, $record) {
                                                    if ($record && $record->mainImage) {
                                                        $component->state($record->mainImage->url);
                                                    }
                                                })->columns(1),
                                        ]
                                    ),
                                    Forms\Components\Group::make(
                                        [
                                            FileUpload::make('blog_diagram_image')
                                                ->label(function ($operation) {
                                                    return $operation == 'edit' ? 'Edit Blog Diagram' : 'Add Blog Diagram';
                                                })
                                                ->image()
                                                ->disk('public')
                                                ->directory('images')
                                                ->saveUploadedFileUsing(function ($file) {
                                                    $filename = time() . '-' . $file->getClientOriginalName();
                                                    $storedPath = $file->storeAs('images', $filename, 'public');
                                                    return basename($storedPath);
                                                })->visibleOn(['create', 'edit'])->columns(1),

                                            ViewField::make('Current Blog Diagram')
                                                ->label("Preview Blog Diagram")
                                                ->view('filament.components.image-preview')
                                                ->visibleOn(['edit', 'view'])
                                                ->visible(fn($get) => !$get('blog_diagram_image'))
                                                ->visible(fn($record) => $record?->blogDiagram)
                                                ->dehydrated(false)
                                                ->afterStateHydrated(function ($component, $state, $record) {
                                                    if ($record && $record->blogDiagram) {
                                                        $component->state($record->blogDiagram->url);
                                                    }
                                                }),
                                        ]
                                    ),

                                ])->columns(2)->collapsible(),
                            Section::make('Datasheet')
                                ->schema([
                                    Forms\Components\Group::make([
                                            FileUpload::make('datasheet_file')
                                                ->label(function ($operation) {
                                                    return $operation == 'edit' ? 'Edit Datasheet' : 'Add Datasheet';
                                                })
                                                ->disk('public')
                                                ->visibility('public')
                                                ->saveUploadedFileUsing(function ($file) {
                                                    $filename = time() . '-' . $file->getClientOriginalName();
                                                    $storedPath = $file->storeAs('files', $filename, 'public');
                                                    return basename($storedPath);
                                                })->visibleOn(['create', 'edit']),
                                            ViewField::make('pdf_preview')
                                                ->label('Current Datasheet')
                                                ->view('filament.components.pdf-preview')
                                                ->visible(fn($record) => $record?->file)
                                                ->dehydrated(false)
                                                ->afterStateHydrated(function ($component, $state, $record) {
                                                    if ($record && $record->file) {
                                                        $component->state($record->file->url);
                                                    }
                                                }),
                                            ]
                                    ),
                                ])->collapsible(),
                        ])->columns(2),
                    ]),
            ])->columnSpanFull(),
        ];
    }

    protected static function tabForm(): array
    {
        return [
            Forms\Components\Tabs::make('Tabs')
                ->tabs([
                    Forms\Components\Tabs\Tab::make('Main Data')
                        ->schema([
                            Forms\Components\TextInput::make('name')
                                ->label('Name or Code')
                                ->required()
                                ->maxLength(255),
                            Forms\Components\TextInput::make('commName')
                                ->label("Commercial Name")
                                ->maxLength(255)
                                ->default(null)
                                ->reactive()
                                ->afterStateUpdated(fn(Forms\Set $set, ?string $state) => $set('slug', UniqueSlug::make($state, IC::class))),
                            Forms\Components\TextInput::make('slug') // to handled later
                            ->label("Slug")
                                ->required()
                                ->disabled()
                                ->dehydrated(true)
                                ->maxLength(255),
                            Forms\Components\TextInput::make('manName')
                                ->label('Manufacture Name')
                                ->maxLength(255),
                            Forms\Components\TextInput::make('videoUrl')
                                ->maxLength(255),
                        ]),
                    Forms\Components\Tabs\Tab::make('Details')
                        ->schema([
                            Section::make('Description')
                                ->schema([
                                    Forms\Components\MarkdownEditor::make('icDetail.description')->label("")
                                        ->required(fn(string $operation): bool => $operation === 'create')
                                        ->columnSpanFull()->visibleOn(['create', 'edit']),
                                    Forms\Components\Textarea::make('icDetail.description')->label("")
                                        ->visibleOn('view')->columnSpanFull()
                                ])->collapsible(),
                            Section::make('Images')
                                ->schema([
                                    Forms\Components\FileUpload::make('chip_image')
                                        ->label('Chip Image')
                                        ->image()
                                        ->disk('public')
                                        ->directory('images')
                                        ->required(fn(string $operation): bool => $operation === 'create')
                                        ->saveUploadedFileUsing(function ($file) {
                                            $filename = time() . '-' . $file->getClientOriginalName();
                                            $storedPath = $file->storeAs('images', $filename, 'public');
                                            return basename($storedPath);
                                        })
                                        ->columns(1),

                                    Forms\Components\FileUpload::make('logic_diagram_image')
                                        ->label('Logic Diagram')
                                        ->image()
                                        ->disk('public')
                                        ->directory('images')
                                        ->required(fn(string $operation): bool => $operation === 'create')
                                        ->saveUploadedFileUsing(function ($file) {
                                            $filename = time() . '-' . $file->getClientOriginalName();
                                            $storedPath = $file->storeAs('images', $filename, 'public');
                                            return basename($storedPath);
                                        })
                                        ->columns(1),
                                ])
                                ->visibleOn(['create', 'edit'])
                                ->columns(2)->collapsible(),
                            Section::make(fn($operation) => $operation === 'edit' ? "Old Images" : "Detailed Images")
                                ->schema([
                                    ViewField::make('current_chip_image_preview')
                                        ->label("Current Chip Image")
                                        ->view('filament.components.image-preview')
                                        ->visibleOn(['edit'])
//                                        ->visible(fn($record) => $record?->icDetail?->chipImage)
                                        ->dehydrated(false)
                                        ->afterStateHydrated(function ($component, $state, $record) {
                                            if ($record && $record->icDetail && $record->icDetail->chipImage) {
                                                $component->state($record->icDetail->chipImage->url);
                                            }
                                        })
                                        ->columnSpan(1)
                                        ->visible(fn (Get $get) => !$get('chip_image')),
                                    Forms\Components\Placeholder::make('placeholder_for_chip_image')
                                        ->label("")
                                        ->columnSpan(1)
                                        ->visible(fn(Get $get) => $get('chip_image')),
                                    ViewField::make('current_logic_diagram_preview')
                                        ->label("Current Logic Diagram")
                                        ->view('filament.components.image-preview')
                                        ->visibleOn(['edit'])
                                        ->visible(fn($record) => $record?->icDetail?->logicDiagram)
                                        ->dehydrated(false)
                                        ->afterStateHydrated(function ($component, $state, $record) {
                                            if ($record && $record->icDetail && $record->icDetail->logicDiagram) {
                                                $component->state($record->icDetail->logicDiagram->url);
                                            }
                                        })
                                        ->columnSpan(1)
                                        ->visible(fn(Get $get) => !$get('logic_diagram_image')),

                                ])
                                ->columns(2)
                                ->collapsible()
                                ->visibleOn(['edit', 'view'])->visible(fn(Get $get) => !$get('logic_diagram_image') || !$get('chip_image')),
                        ]),
                    Forms\Components\Tabs\Tab::make('Meta')
                        ->schema([
                            Forms\Components\Group::make([
                                Section::make('IC Image')
                                    ->schema([
                                        Forms\Components\Group::make(
                                            [
                                                FileUpload::make('uploaded_image')
                                                    ->label('Edit IC Image') // if we kept it for the edit only later...
                                                    ->image()
                                                    ->disk('public')
                                                    ->directory('images')
                                                    ->saveUploadedFileUsing(function ($file) {
                                                        $filename = time() . '-' . $file->getClientOriginalName();
                                                        $storedPath = $file->storeAs('images', $filename, 'public');
                                                        return basename($storedPath);
                                                    }),
                                                ViewField::make('current_image')
                                                    ->label("Current IC Image")
                                                    ->view('filament.components.image-preview')
                                                    ->visibleOn(['edit', 'view'])
                                                    ->visible(fn($get) => !$get('uploaded_image'))
                                                    ->dehydrated(false)
                                                    ->afterStateHydrated(function ($component, $state, $record) {
                                                        if ($record && $record->mainImage) {
                                                            $component->state($record->mainImage->url);
                                                        }
                                                    })->columns(1),
                                            ]
                                        ),
                                        Forms\Components\Group::make(
                                            [
                                                FileUpload::make('blog_diagram_image')
                                                    ->label('Edit Blog Diagram Image')
                                                    ->image()
                                                    ->disk('public')
                                                    ->directory('images')
                                                    ->saveUploadedFileUsing(function ($file) {
                                                        $filename = time() . '-' . $file->getClientOriginalName();
                                                        $storedPath = $file->storeAs('images', $filename, 'public');
                                                        return basename($storedPath);
                                                    }),
                                                ViewField::make('Current Blog Diagram Image')
                                                    ->label("Preview Blog Diagram")
                                                    ->view('filament.components.image-preview')
                                                    ->visibleOn(['edit', 'view'])
                                                    ->visible(fn($get) => !$get('blog_diagram_image'))
                                                    ->dehydrated(false)
                                                    ->afterStateHydrated(function ($component, $state, $record) {
                                                        if ($record && $record->blogDiagram) {
                                                            $component->state($record->blogDiagram->url);
                                                        }
                                                    }),
                                            ]
                                        ),
                                    ])->columns(2)->collapsible(),
                                Section::make('Datasheet')
                                    ->schema([
                                        Forms\Components\Group::make([
                                            FileUpload::make('datasheet_file')
                                                ->label("Edit Datasheet File")
                                                ->disk('public')
                                                ->visibility('public')
                                                ->saveUploadedFileUsing(function ($file) {
                                                    $filename = time() . '-' . $file->getClientOriginalName();
                                                    $storedPath = $file->storeAs('files', $filename, 'public');
                                                    return basename($storedPath);
                                                }),
                                            ViewField::make('pdf_preview')
                                                ->label('Current Datasheet')
                                                ->view('filament.components.pdf-preview')
                                                ->visibleOn(['edit', 'view'])
                                                ->visible(fn($get) => !$get('datasheet_file'))
                                                ->dehydrated(false)
                                                ->afterStateHydrated(function ($component, $state, $record) {
                                                    if ($record && $record->file) {
                                                        $component->state($record->file->url);
                                                    }
                                                }),
                                        ]),
                                    ])->collapsible(),
                            ])->columns(2),
                        ]),
                ])
                ->columnSpanFull(),
        ];
    }
    protected static function viewForm(): array
    {
        return [
            Section::make('Basic Info')
                ->schema([
                    Forms\Components\TextInput::make('name')->disabled(),
                    Forms\Components\TextInput::make('commName')->disabled(),
                    Forms\Components\TextInput::make('slug')->disabled(), // can be deleted later
                ])->collapsible()->columns(2), // will be changed according to ui

            Section::make('Description')
                ->schema([
                    MarkdownEditor::make('icDetail.description')->label("")->disabled(),
                ]),

            Section::make('Images')
                ->schema(components: [
                    ViewField::make('current_image')
                        ->label('IC Image')
                        ->view('filament.components.image-preview')
                        ->afterStateHydrated(function ($component, $state, $record) {
                            if ($record && $record->mainImage) {
                                $component->state($record->mainImage->url);
                            }
                        }),

                    ViewField::make('blog_diagram')
                        ->label('Blog Diagram')
                        ->view('filament.components.image-preview')
                        ->afterStateHydrated(function ($component, $state, $record) {
                            if ($record && $record->blogDiagram) {
                                $component->state($record->blogDiagram->url);
                            }
                        }),
                    ViewField::make('chip_image')
                        ->label('Chip Image')
                        ->view('filament.components.image-preview')
                        ->visible(fn($record) => $record?->icDetail?->chipImage)
                        ->afterStateHydrated(function ($component, $state, $record) {
                            if ($record && $record->icDetail && $record->icDetail->chipImage) {
                                $component->state($record->icDetail->chipImage->url);
                            }
                        }),

                    ViewField::make('logic_diagram')
                        ->label('Logic Diagram Image')
                        ->view('filament.components.image-preview')
                        ->visible(fn($record) => $record?->icDetail?->logicDiagram)
                        ->afterStateHydrated(function ($component, $state, $record) {
                            if ($record && $record->icDetail && $record->icDetail->logicDiagram) {
                                $component->state($record->icDetail->logicDiagram->url);
                            }
                        }),
                ])
                ->collapsible()
                ->columns(2),
            Section::make('Datasheet')
                ->schema([
                    ViewField::make('datasheet')
                        ->label('Datasheet')
                        ->view('filament.components.pdf-preview')
                        ->afterStateHydrated(function ($component, $state, $record) {
                            if ($record && $record->file) {
                                $component->state($record->file->name);
                            }
                        })->columnSpanFull(),
                ])->collapsible()
        ];
    }

    public static function form(Form $form): Form
    {
        $operation = $form->getOperation();

        return match ($operation) {
            'create' => $form->schema(self::wizardForm()),
            'edit' => $form->schema(self::tabForm()),
            'view' => $form->schema(self::viewForm()),
//            default => $form->schema(self::viewForm()), // fallback
        };
    }

//    public static function form(Form $form): Form
//    {
//        // Check current URL to determine form type
//        $url = request()->url();
//        $query = request()->query();
//
//        dump($url, $query);
//        if (str_contains($url, '/create')) {
//            return $form->schema(self::wizardForm());
//        } elseif (str_contains($url, '/edit')) {
//            return $form->schema(self::tabForm());
//        } elseif (preg_match('/\/\d+$/', $url) && !str_contains($url, 'Manager')) { // View page (ends with ID) // it will cause a problem in the parameter and features and pacakges as it change the url
//            return $form->schema(self::viewForm());
//        }
//
//        // Fallback to wizard form
//        return $form->schema(self::viewForm());
//    }
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('mainImage.url')
                    ->disk('public') // Match your storage disk configuration
                    ->visibility('public')
                    ->getStateUsing(function ($record) {
                        if ($record->mainImage && $record->mainImage->url) {
                            // Construct the full URL to the image
                            return '/images/' . $record->mainImage->url;
                        }
                        return null;
                    })
                    ->toggleable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('commName')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('mainImage.url')
                    ->disk('public') // Match your storage disk configuration
                    ->visibility('public')
                    ->getStateUsing(function ($record) {
                        if ($record->mainImage && $record->mainImage->url) {
                            // Construct the full URL to the image
                            return '/images/' . $record->mainImage->url;
                        }
                        return null;
                    })
                    ->toggleable(),

                ImageColumn::make('blogDiagram.url')
                    ->disk('public')
                    ->visibility('public')
                    ->getStateUsing(function ($record) {
                        if ($record->blogDiagram && $record->blogDiagram->url) {
                            return '/images/' . $record->blogDiagram->url;
                        }
                        return null;
                    })
                    ->toggleable(),
                ImageColumn::make('icDetail.chipImage.url')
                    ->disk('public')
                    ->visibility('public')
                    ->getStateUsing(function ($record) {
                        if ($record->icDetail && $record->icDetail->chipImage) {
                            return '/images/' . $record->icDetail->chipImage->url;
                        }
                        return null;
                    })
                    ->toggleable(),
                ImageColumn::make('icDetail.blogDiagram.url')
                    ->disk('public')
                    ->visibility('public')
                    ->getStateUsing(function ($record) {
                        if ($record->icDetail && $record->icDetail->blogDiagram) {
                            return '/images/' . $record->icDetail->blogDiagram->url;
                        }
                        return null;
                    })
                    ->toggleable(),

                Tables\Columns\TextColumn::make('views')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('likes')
                    ->numeric()
                    ->sortable(),
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
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                    Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ParametersRelationManager::class,
            RelationManagers\FeaturesRelationManager::class,
            RelationManagers\PackagesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListICS::route('/'),
            'create' => Pages\CreateIC::route('/create'),
            'view' => Pages\ViewIC::route('/{record}'),
            'edit' => Pages\EditIC::route('/{record}/edit'),
        ];
    }
}
