<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;

class TemplateController extends Controller
{
    public function show(?string $page = null)
    {
        $base = resource_path('js/templates');

        $manifestPath = $base . '/manifest.json';
        if (!File::exists($manifestPath)) abort(500, 'manifest.json introuvable');

        $manifest = json_decode(File::get($manifestPath), true);
        $defaultSlug = $manifest['default'] ?? 'homepage-1';

        $slug = $page ?: $defaultSlug;
        $pagePath = $base . '/' . $slug . '.json';
        if (!File::exists($pagePath)) abort(404, "Page $slug introuvable");

        $pageData = json_decode(File::get($pagePath), true);

        $globalPath = $base . '/global.json';
        $global = File::exists($globalPath)
            ? json_decode(File::get($globalPath), true)
            : [];

       return view('studio', [
            'slug'   => $slug,
            'page'   => $pageData,
            'global' => $global,
            'assets' => '/template',
        ]);
    }
}
