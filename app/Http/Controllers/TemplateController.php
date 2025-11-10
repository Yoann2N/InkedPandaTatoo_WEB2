<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;

class TemplateController extends Controller
{
    public function show($page = 'homepage-1')
    {
        $templatePath = resource_path("templates/{$page}.json");
        
        if (!File::exists($templatePath)) {
            abort(404, "Template not found: {$page}");
        }
        
        $templateData = json_decode(File::get($templatePath), true);
        $content = $this->renderContent($templateData['content'] ?? []);
        
        return view('template', [
            'content' => $content,
            'page' => $page
        ]);
    }

    private function renderContent($elements)
    {
        $html = '';

        foreach ($elements as $el) {
            $elType = $el['elType'] ?? 'section';
            $settings = $el['settings'] ?? [];

            // --- SECTION
            if ($elType === 'section') {
                // build inline style for padding/background
                $style = '';

                if (!empty($settings['padding'])) {
                    $p = $settings['padding'];
                    $unit = $p['unit'] ?? 'px';
                    $style .= 'padding: ' . ($p['top'] ?? 0) . $unit . ' ' . ($p['right'] ?? 0) . $unit . ' ' . ($p['bottom'] ?? 0) . $unit . ' ' . ($p['left'] ?? 0) . $unit . ';';
                }

                $sectionClass = 'elementor-section';
                $inner = '';

                // slideshow background
                if (($settings['background_background'] ?? '') === 'slideshow' && !empty($settings['background_slideshow_gallery'])) {
                    $slides = $settings['background_slideshow_gallery'];
                    $inner .= '<div class="template-slideshow">';
                    foreach ($slides as $s) {
                        $url = $s['url'] ?? '';
                        $inner .= '<div class="slide"><img src="'.htmlspecialchars($url).'" alt=""></div>';
                    }
                    $inner .= '</div>';
                }

                // overlay / other background handling (basic)
                if (!empty($settings['background_overlay_color'])) {
                    $style .= 'position:relative;';
                }

                $html .= '<section class="'.$sectionClass.'" style="'.htmlspecialchars($style).'">';
                // render child elements (columns / widgets)
                if (!empty($el['elements'])) {
                    $html .= $this->renderContent($el['elements']);
                }
                // append slideshow inner if present (we appended before children to be background-like)
                if ($inner) {
                    // place as first child so images are background-ish (CSS will absolutely position)
                    $html = str_replace('<section', '<section', $html); // noop, kept for clarity
                }
                // add slideshow markup at top of section
                if (!empty($settings['background_slideshow_gallery'])) {
                    // we already built slides above; inject them right after opening tag:
                    // naive injection: re-build last added section by replacing last occurrence
                    $lastSectionPos = strrpos($html, '</section>');
                    // easier: just append slideshow before content end
                    // but to keep it simple: add slideshow wrapper now
                    // (we already rendered slideshow in $inner)
                    // ensure slideshow is top-level inside section
                    $html = rtrim($html, '</section>') . $inner . '</section>';
                }
                $html .= '</section>';
            }

            // --- COLUMN
            if ($elType === 'column') {
                $colSize = $settings['_column_size'] ?? 100;
                $html .= '<div class="elementor-column" style="flex:0 0 '.(int)$colSize.'%">';
                $html .= $this->renderContent($el['elements'] ?? []);
                $html .= '</div>';
            }

            // --- WIDGET
            if ($elType === 'widget') {
                $widgetType = $el['widgetType'] ?? 'widget';
                $widgetSettings = $el['settings'] ?? [];

                // COMMON wrapper
                $html .= '<div class="elementor-widget elementor-widget-'.$widgetType.'">';

                // Heading widget
                if ($widgetType === 'heading' || !empty($widgetSettings['header_size'])) {
                    $size = $widgetSettings['header_size'] ?? 'h3';
                    $title = $widgetSettings['title'] ?? '';
                    $html .= '<'.$size.' class="elementor-heading-title">'.($title).'</'.$size.'>';
                }

                // Text editor / WYSIWYG
                if (!empty($widgetSettings['editor']) || !empty($widgetSettings['text'])) {
                    $content = $widgetSettings['editor'] ?? $widgetSettings['text'] ?? '';
                    $html .= '<div class="elementor-widget-container">'.$content.'</div>';
                }

                // Image widget
                if (!empty($widgetSettings['image'] ) || !empty($widgetSettings['url'])) {
                    // Elementor sometimes nests image as ['id'=>..., 'url'=>...]
                    $img = $widgetSettings['image'] ?? $widgetSettings;
                    $imgUrl = '';
                    if (is_array($img)) {
                        $imgUrl = $img['url'] ?? ($img['0']['url'] ?? '');
                    } else {
                        $imgUrl = $widgetSettings['url'] ?? '';
                    }
                    if ($imgUrl) {
                        $html .= '<div class="elementor-image"><img src="'.htmlspecialchars($imgUrl).'" alt=""></div>';
                    }
                }

                // Gallery widget (basic)
                if (!empty($widgetSettings['gallery'])) {
                    $html .= '<div class="elementor-gallery">';
                    foreach ($widgetSettings['gallery'] as $g) {
                        $url = $g['url'] ?? '';
                        $html .= '<div class="gallery-item"><img src="'.htmlspecialchars($url).'" alt=""></div>';
                    }
                    $html .= '</div>';
                }

                // Video widget (basic: supports youtube / iframe url)
                if (!empty($widgetSettings['video_url']) || !empty($widgetSettings['link'])) {
                    $v = $widgetSettings['video_url'] ?? $widgetSettings['link'] ?? '';
                    // convert to iframe if youtube
                    if (str_contains($v, 'youtube') || str_contains($v, 'youtu.be')) {
                        // naive youtube id extraction
                        if (preg_match("#(youtube\.com/watch\?v=|youtu\.be/)([A-Za-z0-9_\-]+)#", $v, $m)) {
                            $id = $m[2];
                            $html .= '<div class="elementor-video"><iframe src="https://www.youtube.com/embed/'.htmlspecialchars($id).'" frameborder="0" allowfullscreen></iframe></div>';
                        } else {
                            $html .= '<div class="elementor-video"><iframe src="'.htmlspecialchars($v).'" frameborder="0" allowfullscreen></iframe></div>';
                        }
                    } else {
                        $html .= '<div class="elementor-video"><iframe src="'.htmlspecialchars($v).'" frameborder="0" allowfullscreen></iframe></div>';
                    }
                }

                // Repeater / lists (ex: artistes) - fallback: iterate settings arrays and print
                foreach ($widgetSettings as $k => $v) {
                    if (is_array($v) && !empty($v) && array_key_exists('0', $v) && is_array($v[0])) {
                        // print simple list
                        if (str_contains($k, 'list') || str_contains($k, 'items') || str_contains($k, 'gallery')) {
                            $html .= '<ul class="elementor-list">';
                            foreach ($v as $item) {
                                $label = $item['label'] ?? ($item['name'] ?? json_encode($item));
                                $sub = $item['sub'] ?? ($item['meta'] ?? '');
                                $html .= '<li>'.($label).' '.($sub ? ' - '.$sub : '').'</li>';
                            }
                            $html .= '</ul>';
                        }
                    }
                }

                // Fallback: dump specific simple settings like 'title' handled above; otherwise show nothing.

                $html .= '</div>'; // end widget
            }
        }

        return $html;
    }
}