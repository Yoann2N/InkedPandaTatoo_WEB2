<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;

class TemplateController extends Controller
{
    public function show(?string $page = null)
    {
        $base = resource_path('js/templates');

        // 1. Charger le manifest
        $manifestPath = $base . '/manifest.json';
        if (!File::exists($manifestPath)) {
            abort(500, 'manifest.json introuvable');
        }

        $manifest = json_decode(File::get($manifestPath), true);
        if (!$manifest) abort(500, "manifest.json invalide");

        $defaultSlug = $manifest['default'] ?? 'homepage-1';

        // 2. Choisir le template
        $slug = $page ?: $defaultSlug;
        $pagePath = $base . '/templates/' . $slug . '.json';

        if (!File::exists($pagePath)) {
            abort(404, "Template '$slug' introuvable");
        }

        // 3. Charger la page JSON Elementor
        $pageData = json_decode(File::get($pagePath), true);
        if (!$pageData) abort(500, "Le JSON '$slug.json' est invalide");

        $content = $pageData['content'] ?? [];

        // 4. Convertir JSON -> HTML
        $html = $this->renderContent($content);

        // 5. Renvoyer vers une vue propre (sans layout Laravel)
        return view('template', [
            'html' => $html
        ]);
    }


    private function renderContent($elements)
    {
        $html = '';

        foreach ($elements as $el) {

            // SECTION
            if (($el['elType'] ?? '') === 'section') {
                $html .= '<section class="elementor-section">';

                if (!empty($el['elements'])) {
                    $html .= $this->renderContent($el['elements']);
                }

                $html .= '</section>';
            }

            // COLUMN
            if (($el['elType'] ?? '') === 'column') {
                $html .= '<div class="elementor-column">';
                $html .= $this->renderContent($el['elements'] ?? []);
                $html .= '</div>';
            }

            // WIDGET
            if (($el['elType'] ?? '') === 'widget') {

                $widget = $el['widgetType'] ?? 'widget';

                $html .= '<div class="elementor-widget '.$widget.'">';

                // Exemple : extraire le titre s'il existe
                if (!empty($el['settings']['title'])) {
                    $html .= '<h3>'.$el['settings']['title'].'</h3>';
                }

                $html .= '</div>';
            }
        }

        return $html;
    }
}
