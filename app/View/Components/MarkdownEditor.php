<?php

namespace App\View\Components;

use App\Models\User;
use Filament\Forms\Components\MarkdownEditor as BaseMarkdownEditor;

class MarkdownEditor extends BaseMarkdownEditor
{
    protected string $view = 'components.markdown-editor';
}
