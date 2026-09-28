<?php

namespace App\View\Components;

use App\Models\Item;
use Illuminate\Support\Js;
use Filament\Forms\Components\MarkdownEditor as BaseMarkdownEditor;

class MarkdownEditor extends BaseMarkdownEditor
{
    /**
     * Enable `@` autocomplete, suggesting the participants of the given item first.
     */
    public function mentions(?Item $item = null): static
    {
        return $this->extraAlpineAttributes([
            'x-mentions' => Js::from([
                'url' => route('mention-search'),
                'item' => $item?->id,
            ])->toHtml(),
        ]);
    }
}
