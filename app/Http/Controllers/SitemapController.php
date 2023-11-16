<?php

namespace App\Http\Controllers;

use App\Models\Board;
use App\Models\Item;
use App\Models\Project;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\SitemapIndex;
use Spatie\Sitemap\Tags\Url;
use Spatie\Sitemap\Tags\Sitemap as TagSitemap;

class SitemapController extends Controller
{
    public function index()
    {
        $sitemapIndex = SitemapIndex::create();

        $sitemapIndex->add(TagSitemap::create(route('sitemap.projects')));
        $sitemapIndex->add(TagSitemap::create(route('sitemap.items')));

        return $sitemapIndex;
    }

    public function projects()
    {
        $sitemap = Sitemap::create();

        $projects = Project::query()
            ->with([
                'boards'
            ])
            ->visibleForCurrentUser()
            ->get();

        /** @var Project $project */
        foreach ($projects as $project) {
            $sitemap->add(
                Url::create(route('projects.show', $project))
                    ->setLastModificationDate($project->updated_at)
            );

            /** @var Board $board */
            foreach ($project->boards as $board) {
                $sitemap->add(
                    Url::create(route('projects.boards.show', [$project, $board]))
                        ->setLastModificationDate($board->updated_at)
                );
            }
        }

        return $sitemap;
    }

    public function items()
    {
        $sitemap = Sitemap::create();

        $items = Item::query()
            ->with([
                'project'
            ])
            ->visibleForCurrentUser()
            ->get();

        /** @var Item $item */
        foreach ($items as $item) {
            $sitemap->add(
                Url::create($item->view_url)
                    ->setLastModificationDate($item->updated_at)
            );
        }

        return $sitemap;
    }
}
