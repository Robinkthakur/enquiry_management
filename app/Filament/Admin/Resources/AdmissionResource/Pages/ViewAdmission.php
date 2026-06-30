<?php

namespace App\Filament\Admin\Resources\AdmissionResource\Pages;

use App\Filament\Admin\Resources\AdmissionResource;
use App\Models\Admission;
use App\Services\FirebaseService;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Pages\ViewRecord;

class ViewAdmission extends ViewRecord
{
    protected static string $resource = AdmissionResource::class;

    protected string $view = 'filament.admin.resources.admission.view';

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),

            Actions\Action::make('send_message')
                ->label('Send Message')
                ->color('success')
                ->icon('heroicon-o-paper-airplane')
                ->form([
                    Forms\Components\TextInput::make('title')
                        ->required()
                        ->default('Important Update'),
                    Forms\Components\Textarea::make('message')
                        ->required()
                        ->rows(4)
                        ->placeholder('Type your custom message here...'),
                ])
                ->action(function (Admission $record, array $data): void {
                    $user = $record->user ?? null;
                    if (!$user) {
                        \Filament\Notifications\Notification::make()
                            ->title('Error')
                            ->body('Student user account not found.')
                            ->danger()
                            ->send();
                        return;
                    }

                    $firebaseService = app(FirebaseService::class);
                    $results = $firebaseService->sendToUser($user, $data['title'], $data['message'], [
                        'type' => 'custom_announcement'
                    ]);

                    $tokensCount = count($results);
                    $successCount = count(array_filter($results));

                    if ($tokensCount === 0) {
                        \Filament\Notifications\Notification::make()
                            ->title('No Registered Devices')
                            ->body('The student has no registered FCM tokens for push notifications.')
                            ->warning()
                            ->send();
                    } else {
                        \Filament\Notifications\Notification::make()
                            ->title('Message Sent')
                            ->body("Message successfully sent to {$successCount} of {$tokensCount} active device(s).")
                            ->success()
                            ->send();
                    }
                })
        ];
    }
}
