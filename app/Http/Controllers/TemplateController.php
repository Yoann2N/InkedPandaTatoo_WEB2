<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;

class TemplateController extends Controller
{
    public function show($page = 'homepage-1')
    {
        // CORRIGER LE CHEMIN - resources/js/templates au lieu de resources/templates
        $templatePath = resource_path("js/templates/{$page}.json");
        
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
            $isInner = $el['isInner'] ?? false;

            // --- SECTION
            if ($elType === 'section') {
                $style = $this->buildSectionStyle($settings);
                $sectionClass = 'elementor-section' . ($isInner ? ' elementor-inner-section' : '');
                
                $html .= '<section class="'.$sectionClass.'" style="'.htmlspecialchars($style).'">';
                
                // Gestion du slideshow background
                if (($settings['background_background'] ?? '') === 'slideshow' && !empty($settings['background_slideshow_gallery'])) {
                    $html .= $this->renderSlideshow($settings['background_slideshow_gallery']);
                }
                
                // Rendu des éléments enfants
                if (!empty($el['elements'])) {
                    $html .= $this->renderContent($el['elements']);
                }
                
                $html .= '</section>';
            }

            // --- COLUMN
            if ($elType === 'column') {
                $colSize = $settings['_column_size'] ?? 100;
                $style = 'flex:0 0 '.(int)$colSize.'%;';
                
                // Ajouter le padding si présent
                if (!empty($settings['padding'])) {
                    $p = $settings['padding'];
                    $unit = $p['unit'] ?? 'px';
                    $style .= 'padding: ' . ($p['top'] ?? 0) . $unit . ' ' . ($p['right'] ?? 0) . $unit . ' ' . ($p['bottom'] ?? 0) . $unit . ' ' . ($p['left'] ?? 0) . $unit . ';';
                }
                
                $html .= '<div class="elementor-column" style="'.htmlspecialchars($style).'">';
                $html .= $this->renderContent($el['elements'] ?? []);
                $html .= '</div>';
            }

            // --- WIDGET
            if ($elType === 'widget') {
                $widgetType = $el['widgetType'] ?? 'widget';
                $widgetSettings = $el['settings'] ?? [];
                
                $html .= '<div class="elementor-widget elementor-widget-'.$widgetType.'">';
                $html .= $this->renderWidget($widgetType, $widgetSettings);
                $html .= '</div>';
            }
        }

        return $html;
    }
    private function buildSectionStyle($settings)
    {
        $style = '';

        // Padding
        if (!empty($settings['padding'])) {
            $p = $settings['padding'];
            $unit = $p['unit'] ?? 'px';
            $style .= 'padding: ' . ($p['top'] ?? 0) . $unit . ' ' . ($p['right'] ?? 0) . $unit . ' ' . ($p['bottom'] ?? 0) . $unit . ' ' . ($p['left'] ?? 0) . $unit . ';';
        }

        // Margin
        if (!empty($settings['margin'])) {
            $m = $settings['margin'];
            $unit = $m['unit'] ?? 'px';
            $style .= 'margin: ' . ($m['top'] ?? 0) . $unit . ' ' . ($m['right'] ?? 0) . $unit . ' ' . ($m['bottom'] ?? 0) . $unit . ' ' . ($m['left'] ?? 0) . $unit . ';';
        }

        // Background color
        if (!empty($settings['background_color'])) {
            $style .= 'background-color: '.$settings['background_color'].';';
        }

        // Background image
        if (!empty($settings['background_image']['url'])) {
            $style .= 'background-image: url("'.htmlspecialchars($settings['background_image']['url']).'");';
            $style .= 'background-size: cover; background-position: center;';
        }

        return $style;
    }
    private function renderSlideshow($gallery)
    {
        $html = '<div class="template-slideshow">';
        foreach ($gallery as $index => $slide) {
            $url = $slide['url'] ?? '';
            $active = $index === 0 ? 'active' : '';
            $html .= '<div class="slide '.$active.'"><img src="'.htmlspecialchars($url).'" alt=""></div>';
        }
        $html .= '</div>';
        return $html;
    }
    private function renderWidget($widgetType, $settings)
    {
        switch ($widgetType) {
            case 'heading':
                return $this->renderHeading($settings);
            case 'text-editor':
                return $this->renderTextEditor($settings);
            case 'image':
                return $this->renderImage($settings);
            case 'button':
                return $this->renderButton($settings);
            case 'icon-box':
                return $this->renderIconBox($settings);
            case 'spacer':
                return $this->renderSpacer($settings);
            case 'testimonial':
                return $this->renderTestimonial($settings);
            case 'image-carousel':
                return $this->renderImageCarousel($settings);
            case 'icon':
                return $this->renderIcon($settings);
            case 'video':
                return $this->renderVideo($settings);
            default:
                return '<!-- Widget non supporté: '.$widgetType.' -->';
        }
    }
    private function renderHeading($settings)
    {
        $size = $settings['header_size'] ?? 'h3';
        $title = $settings['title'] ?? '';
        $align = $settings['align'] ?? 'left';
        
        return '<'.$size.' class="elementor-heading-title" style="text-align: '.$align.'">'.($title).'</'.$size.'>';
    }

    private function renderTextEditor($settings)
    {
        $content = $settings['editor'] ?? $settings['text'] ?? '';
        $align = $settings['align'] ?? 'left';
        
        return '<div class="elementor-widget-container" style="text-align: '.$align.'">'.$content.'</div>';
    }

    private function renderButton($settings)
    {
        $text = $settings['text'] ?? '';
        $align = $settings['align'] ?? 'left';
        
        return '<div style="text-align: '.$align.'">
            <button class="elementor-button">'.$text.'</button>
        </div>';
    }

    private function renderIconBox($settings)
    {
        $title = $settings['title_text'] ?? '';
        $description = $settings['description_text'] ?? '';
        
        return '<div class="elementor-icon-box">
            <div class="elementor-icon-box-title">'.$title.'</div>
            <div class="elementor-icon-box-description">'.$description.'</div>
        </div>';
    }

    private function renderSpacer($settings)
    {
        $height = $settings['space']['size'] ?? 20;
        $unit = $settings['space']['unit'] ?? 'px';
        
        return '<div style="height: '.$height.$unit.';"></div>';
    }

    private function renderImage($settings)
    {
        $image = $settings['image'] ?? [];
        $url = is_array($image) ? ($image['url'] ?? '') : $settings['url'] ?? '';
        
        if ($url) {
            return '<div class="elementor-image"><img src="'.htmlspecialchars($url).'" alt=""></div>';
        }
        
        return '';
    }

    private function renderVideo($settings)
    {
        $url = $settings['youtube_url'] ?? $settings['vimeo_url'] ?? '';
        
        if (str_contains($url, 'youtube') || str_contains($url, 'youtu.be')) {
            if (preg_match("#(youtube\.com/watch\?v=|youtu\.be/)([A-Za-z0-9_\-]+)#", $url, $m)) {
                $id = $m[2];
                return '<div class="elementor-video"><iframe src="https://www.youtube.com/embed/'.htmlspecialchars($id).'" frameborder="0" allowfullscreen></iframe></div>';
            }
        }
        
        return '<div class="elementor-video"><iframe src="'.htmlspecialchars($url).'" frameborder="0" allowfullscreen></iframe></div>';
    }
}