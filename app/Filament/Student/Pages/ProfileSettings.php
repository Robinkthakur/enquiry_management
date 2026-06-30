<?php

namespace App\Filament\Student\Pages;

use Filament\Pages\Page;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Notifications\Notification;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Admission;

class ProfileSettings extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected string $view = 'filament.student.pages.profile-settings';

    protected static ?string $title = 'Profile Settings';

    protected static ?string $navigationLabel = 'Profile Settings';

    protected static ?string $slug = 'profile-settings';

    public ?string $email = null;
    public $student_photo = null;

    public function mount(): void
    {
        $user = Auth::user();
        if ($user) {
            $this->email = $user->email;
            
            $admission = $user->admission;
            if ($admission) {
                $this->student_photo = $admission->student_photo;
            }
            
            $this->form->fill([
                'email' => $this->email,
                'student_photo' => $this->student_photo,
            ]);
        }
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\FileUpload::make('student_photo')
                ->label('Profile Image')
                ->image()
                ->avatar()
                ->directory('student_photos')
                ->helperText('Upload a square profile photo (JPG, PNG, WebP)'),
            Forms\Components\TextInput::make('email')
                ->label('Email Address')
                ->email()
                ->required()
                ->maxLength(255)
                ->unique('users', 'email', ignorable: Auth::user()),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $user = Auth::user();
        
        if ($user) {
            // Update User email
            $user->update([
                'email' => $data['email'],
            ]);

            // Update Admission email and student_photo
            $admission = $user->admission;
            if ($admission) {
                $admission->update([
                    'email' => $data['email'],
                    'student_photo' => $data['student_photo'],
                ]);
            }

            Notification::make()
                ->title('Profile Updated')
                ->body('Your email and profile photo have been updated successfully.')
                ->success()
                ->send();
        }
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Save Changes')
                ->submit('save'),
        ];
    }
}
