<?php

namespace App\Http\Controllers;

use App\Models\Changelog;
use App\Settings\GeneralSettings;

class ChangelogController extends Controller
{
    public function index()
    {
        abort_unless(app(GeneralSettings::class)->enable_changelog, 404);

        return view('changelog', [
            'changelogs' => Changelog::query()->published()->get(),
        ]);
    }

    public function show(Changelog $changelog)
    {
        abort_unless(app(GeneralSettings::class)->enable_changelog, 404);

        abort_if($changelog->published_at > now(), 404);

        return view('changelog', [
            'changelogs' => collect([$changelog]),
        ]);
    }
}
