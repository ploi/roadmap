<?php

namespace App\View\Components;

use App\Models\User;
use Filament\Forms\Components\MarkdownEditor as BaseMarkdownEditor;

class MarkdownEditor extends BaseMarkdownEditor
{
    public $mentionables;

    protected string $view = 'components.markdown-editor';

    public function setUp(): void
    {
        $this->mentionables = User::all()
            ->map(function ($user) {
                return [
                    'key' => $user->name,
                    'value' => $user->username,
                ];
            });
    }
}
