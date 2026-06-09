<?php

namespace App\Filament\Admin\Pages;

use App\Models\CompanySetting;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;

class CompanySettings extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static string | \UnitEnum | null $navigationGroup = 'System Management';
    protected static ?int $navigationSort = 10;
    protected static ?string $title = 'Company Settings';

    protected string $view = 'filament.admin.pages.company-settings';

    public ?array $data = [];

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('Super Admin') ?? false;
    }

    public function mount(): void
    {
        $setting = CompanySetting::first();
        if ($setting) {
            $this->form->fill($setting->toArray());
        } else {
            $this->form->fill();
        }
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make('General Information')
                    ->schema([
                        Forms\Components\TextInput::make('company_name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->rows(3)
                            ->maxLength(1000),
                    ]),
                Section::make('Contact & Web Information')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('support_email')
                                    ->email()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('mobile_no')
                                    ->maxLength(50),
                                Forms\Components\TextInput::make('website')
                                    ->maxLength(255),
                            ]),
                        Forms\Components\Textarea::make('address')
                            ->rows(2)
                            ->maxLength(500),
                    ]),
                Section::make('Branding')
                    ->schema([
                        Forms\Components\FileUpload::make('logo')
                            ->image()
                            ->directory('branding')
                            ->imageResizeMode('force')
                            ->imageResizeTargetWidth('200')
                            ->imageResizeTargetHeight('50'),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $setting = CompanySetting::first() ?? new CompanySetting();
        $setting->fill($data);
        $setting->save();

        Notification::make()
            ->title('Settings Saved')
            ->body('Company information has been updated successfully.')
            ->success()
            ->send();
    }
}
