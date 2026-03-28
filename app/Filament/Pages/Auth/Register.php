<?php

namespace App\Filament\Pages\Auth;

use App\Models\User;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Auth\Pages\Register as BaseRegister;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class Register extends BaseRegister
{
    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        $this->getUsernameFormComponent(),
                        $this->getEmailFormComponent(),
                        $this->getPasswordFormComponent(),
                        $this->getPasswordConfirmationFormComponent(),
                        $this->getTermsFormComponent(),
                    ])
                    ->statePath('data'),
            ),
        ];
    }

    protected function getUsernameFormComponent(): Component
    {
        return TextInput::make('username')
            ->label('Username')
            ->required()
            ->maxLength(255)
            ->unique(User::class)
            ->alphaDash()
            ->autocomplete('username')
            ->autofocus()
            ->extraInputAttributes(['tabindex' => 1]);
    }

    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('email')
            ->label('Email address')
            ->email()
            ->required()
            ->maxLength(255)
            ->unique(User::class)
            ->autocomplete('email')
            ->extraInputAttributes(['tabindex' => 2]);
    }

    protected function getPasswordFormComponent(): Component
    {
        return TextInput::make('password')
            ->label('Password')
            ->password()
            ->revealable()
            ->required()
            ->rule(Password::default())
            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
            ->same('passwordConfirmation')
            ->validationAttribute('password')
            ->extraInputAttributes(['tabindex' => 3]);
    }

    protected function getUsernameFormComponent(): Component
    {
        return TextInput::make('name')
            ->label('Username')
            ->required()
            ->maxLength(255)
            ->unique(User::class, 'name')
            ->alphaDash()
            ->autocomplete('username')
            ->autofocus()
            ->extraInputAttributes(['tabindex' => 1]);
    }

    protected function getTermsFormComponent(): Component
    {
        return Checkbox::make('terms')
            ->label(fn () => new \Illuminate\Support\HtmlString(
                'I agree to the <a href="/terms" target="_blank" class="text-primary-500 hover:underline">Terms of Service</a> and <a href="/privacy" target="_blank" class="text-primary-500 hover:underline">Privacy Policy</a>'
            ))
            ->required()
            ->validationMessages([
                'required' => 'You must accept the terms of service to create an account.',
            ])
            ->dehydrated(false)
            ->extraInputAttributes(['tabindex' => 5]);
    }

    protected function mutateFormDataBeforeRegister(array $data): array
    {
        unset($data['passwordConfirmation'], $data['terms']);
        return $data;
    }
}