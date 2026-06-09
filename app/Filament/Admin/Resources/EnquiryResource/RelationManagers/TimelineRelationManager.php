<?php

namespace App\Filament\Admin\Resources\EnquiryResource\RelationManagers;

use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

use Filament\Actions;

class TimelineRelationManager extends RelationManager
{
    protected static string $relationship = 'timeline';

    protected static ?string $title = 'Enquiry Follow-Up History & Timeline';

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Forms\Components\Select::make('status_to')
                    ->label('Update Status')
                    ->required()
                    ->options([
                        'New' => 'New',
                        'Follow Up' => 'Follow Up',
                        'Interested' => 'Interested',
                        'Not Interested' => 'Not Interested',
                    ]),
                Forms\Components\DatePicker::make('follow_up_date')
                    ->label('Next Follow Up Date'),
                Forms\Components\Textarea::make('notes')
                    ->label('Follow Up Notes / Remarks')
                    ->required()
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('notes')
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Logged By'),
                Tables\Columns\TextColumn::make('status_from')
                    ->label('Old Status')
                    ->badge()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('status_to')
                    ->label('New Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'New' => 'info',
                        'Follow Up' => 'warning',
                        'Interested' => 'success',
                        'Not Interested' => 'danger',
                        'Admitted' => 'gray',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('notes')
                    ->label('Notes/Log')
                    ->wrap(),
                Tables\Columns\TextColumn::make('follow_up_date')
                    ->label('Next Follow Up')
                    ->date(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Actions\CreateAction::make()
                    ->label('Log New Follow Up')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['user_id'] = auth()->id();
                        $enquiry = $this->getOwnerRecord();
                        $data['status_from'] = $enquiry->status;
                        
                        // Update the parent enquiry status and next follow-up date
                        $enquiry->update([
                            'status' => $data['status_to'],
                            'follow_up_date' => $data['follow_up_date'],
                        ]);
                        
                        return $data;
                    }),
            ])
            ->actions([
                Actions\ActionGroup::make([
                    Actions\EditAction::make(),
                    Actions\DeleteAction::make(),
                ]),
            ]);
    }
}
