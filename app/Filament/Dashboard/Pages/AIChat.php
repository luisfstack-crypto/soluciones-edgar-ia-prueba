<?php

namespace App\Filament\Dashboard\Pages;

use Filament\Pages\Page;

class AIChat extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Chat IA';
    protected static ?string $title = 'Chat IA';

    protected static string $view = 'filament.pages.a-i-chat';
}
