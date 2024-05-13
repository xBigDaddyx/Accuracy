<?php

namespace Xbigdaddyx\Accuracy\Filament\Accuracy\Resources\CartonBoxResource\Pages;

use Xbigdaddyx\Accuracy\Filament\Accuracy\Resources\CartonBoxResource;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Filament\Forms\Components\Repeater;

class CreateCartonBox extends CreateRecord
{
    use CreateRecord\Concerns\HasWizard;
    protected static string $resource = CartonBoxResource::class;
    public function hasSkippableSteps(): bool
    {
        return true;
    }
    protected function getCreatedNotification(): ?Notification
    {
        //dd(static::getRecord()->id);
        return Notification::make()
            ->success()
            ->title('Carton box created')
            ->body('The carton box has been created successfully.')
            ->sendToDatabase(auth()->user());
        // ->actions([
        //     Action::make('view')
        //         ->button()
        //         ->url(route('filament.accuracy.resources.carton-boxes.view', ['tenant' => Filament::getTenant(), 'record' => static::getRecord()->first()->id]), shouldOpenInNewTab: true),
        // ]);
    }
    protected function getRedirectUrl(): string
    {
        return $this->previousUrl ?? $this->getResource()::getUrl('index');
    }
    protected function getSteps(): array
    {
        return [
            Forms\Components\Wizard\Step::make('Packing List')
                ->description('Select which packing list for this carton box.')
                ->schema([
                    Grid::make(2)
                        ->schema([
                            Forms\Components\Select::make('type')
                                ->required()
                                ->options([
                                    'SOLID' => 'SOLID',
                                    'MULTIPLE' => 'MULTIPLE',
                                    'MIX' => 'MIX',
                                    'RATIO' => 'RATIO',
                                ])
                                ->live()
                                ->label('Carton Box Type'),
                            Forms\Components\Select::make('packing_list_id')
                                ->label('Packing List')
                                ->searchable()
                                ->relationship('packingList', 'po', modifyQueryUsing: fn (Builder $query) => $query->whereBelongsTo(Filament::getTenant()))
                                ->required()
                                ->getOptionLabelFromRecordUsing(fn (Model $record) => "PO: {$record->po} - {$record->buyer->name} {$record->buyer->country} - {$record->style_no}"),

                        ])

                ]),
            Forms\Components\Wizard\Step::make('Attributes')
                ->description('Add information attribute for this carton box.')
                ->schema([
                    Repeater::make('attributes')
                        ->addable(false)
                        ->deletable(false)
                        ->relationship()
                        ->grid(2)
                        ->columns(2)
                        ->schema([
                            Forms\Components\TextInput::make('size')
                                ->helperText('Should be the size of attribute')
                                ->hint('Size Attribute')
                                ->hintIcon('tabler-ruler-measure')
                                ->hintColor('primary'),
                            Forms\Components\TextInput::make('tag')
                                ->live()
                                ->afterStateUpdated(function (?string $state, ?string $old, Set $set) {
                                    if (str_contains($state, 'LPN')) {

                                        $explode = explode('&', $state ?? '');
                                        $result = str_replace('item_number=', '', $explode[1]);
                                        return $set('tag', $result);
                                    }
                                })
                                ->helperText('Should be the tag of attribute')
                                ->hint('Tag Attribute')
                                ->hintIcon('tabler-tag')
                                ->hintColor('primary'),
                            Forms\Components\TextInput::make('color')
                                ->helperText('Should be the name of attribute')
                                ->hint('Color Attribute')
                                ->hintIcon('tabler-color-swatch')
                                ->hintColor('primary'),
                            Forms\Components\TextInput::make('quantity')
                                ->numeric()
                                ->helperText('Should be the quantities of attribute')
                                ->hint('Quantity Attribute')
                                ->hintIcon('tabler-calculator')
                                ->hintColor('primary'),
                        ]),

                ])->hidden(fn (Get $get): bool => $get('type') === 'RATIO' || $get('type') === 'MIX'),
            // Forms\Components\Wizard\Step::make('Element')
            //     ->description('Add information attribute for this carton box.')
            //     ->schema([
            //         Grid::make(2)
            //             ->schema([
            //                 Forms\Components\TextInput::make('size')
            //                     ->hidden(fn (Get $get): bool => $get('type') === 'RATIO')
            //                     ->label('Size'),
            //                 Forms\Components\TextInput::make('color')
            //                     ->hidden(fn (Get $get): bool => $get('type') === 'RATIO')
            //                     ->label('Color'),

            //             ])
            //     ])->hidden(fn (Get $get): bool => $get('type') === 'RATIO'),
            Forms\Components\Wizard\Step::make('Identity and Quantity')
                ->description('Add information about identity and quantity for this carton box.')
                ->schema([
                    Grid::make(2)
                        ->schema([
                            Forms\Components\TextInput::make('quantity')
                                ->required()
                                ->label('Quantity'),
                            Forms\Components\TextInput::make('carton_number')
                                ->default(0)
                                ->label('Carton Number'),
                            Forms\Components\TextInput::make('box_code')
                                ->label('Box Code')
                                ->required()->columnSpanFull(),

                        ])
                ]),
        ];
    }
}
