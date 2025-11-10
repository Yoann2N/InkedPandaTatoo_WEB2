<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;

class TemplateController extends Controller
{
    public function show($page = 'homepage-1')
    {
        $templatePath = resource_path("js/templates/{$page}.json");
        
        \Log::info("Looking for template: " . $templatePath);
        
        if (!File::exists($templatePath)) {
            \Log::error("Template not found: " . $templatePath);
            abort(404, "Template not found: {$page}");
        }
        
        try {
            $templateData = json_decode(File::get($templatePath), true);
            
            // Vérifier si le JSON est valide
            if (json_last_error() !== JSON_ERROR_NONE) {
                abort(500, "Invalid JSON in template: " . json_last_error_msg());
            }
            
            // RENDER LE CONTENU - CORRECTION ICI
            $html = $this->renderContent($templateData['content'] ?? []);
            
            return view('template', [
                'content' => $html,  // CORRECTION : 'content' au lieu de 'html'
                'page' => $page
            ]);
            
        } catch (\Exception $e) {
            \Log::error("Template rendering error: " . $e->getMessage());
            abort(500, "Template rendering error: " . $e->getMessage());
        }
    }

    private function renderContent($elements)
    {
        $html = '';

        foreach ($elements as $el) {
            $elType = $el['elType'] ?? 'section';
            $settings = $el['settings'] ?? [];
            $isInner = $el['isInner'] ?? false;

            try {
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
                elseif ($elType === 'column') {
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
                elseif ($elType === 'widget') {
                    $widgetType = $el['widgetType'] ?? 'widget';
                    $widgetSettings = $el['settings'] ?? [];
                    
                    $html .= '<div class="elementor-widget elementor-widget-'.$widgetType.'">';
                    $html .= $this->renderWidget($widgetType, $widgetSettings);
                    $html .= '</div>';
                }
            } catch (\Exception $e) {
                // Log l'erreur mais continue le rendu
                \Log::error("Error rendering element: " . $e->getMessage());
                $html .= '<!-- Error rendering element: ' . $e->getMessage() . ' -->';
            }
        }

        \Log::info("Rendered HTML length: " . strlen($html)); // Debug
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
            case 'social-icons':
                return $this->renderSocialIcons($settings);
            case 'counter':
                return $this->renderCounter($settings);
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
    private function renderSocialIcons($settings)
    {
        $icons = $settings['social_icon_list'] ?? [];
        $html = '<div class="elementor-social-icons">';
        foreach ($icons as $iconItem) {
            $iconClass = $iconItem['social_icon']['value'] ?? '';
            if ($iconClass) {
                $html .= '<a href="#" class="elementor-social-icon"><i class="'.$iconClass.'"></i></a>';
            }
        }
        $html .= '</div>';
        return $html;
    }

    private function renderCounter($settings)
    {
        $end = $settings['ending_number'] ?? 0;
        $suffix = $settings['suffix'] ?? '';
        $title = $settings['title'] ?? '';
        return '
            <div class="elementor-counter">
                <div class="counter-number">'.$end.$suffix.'</div>
                <div class="counter-title">'.$title.'</div>
            </div>
        ';
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
    private function renderIcon($settings)
    {
        $icon = $settings['selected_icon']['value'] ?? '';
        $align = $settings['align'] ?? 'left';
        
        if ($icon) {
            return '<div style="text-align: '.$align.'">
                <span class="elementor-icon">'.$icon.'</span>
            </div>';
        }
        
        return '';
    }

    private function renderTestimonial($settings)
    {
        $content = $settings['testimonial_content'] ?? '';
        $name = $settings['testimonial_name'] ?? '';
        $job = $settings['testimonial_job'] ?? '';
        $image = $settings['testimonial_image']['url'] ?? '';
        $align = $settings['testimonial_alignment'] ?? 'left';
        
        $html = '<div class="elementor-testimonial" style="text-align: '.$align.'">';
        
        if ($image) {
            $html .= '<div class="testimonial-image"><img src="'.htmlspecialchars($image).'" alt="'.$name.'"></div>';
        }
        
        if ($content) {
            $html .= '<div class="testimonial-content">'.$content.'</div>';
        }
        
        if ($name) {
            $html .= '<div class="testimonial-name">'.$name.'</div>';
        }
        
        if ($job) {
            $html .= '<div class="testimonial-job">'.$job.'</div>';
        }
        
        $html .= '</div>';
        
        return $html;
    }

    private function renderImageCarousel($settings)
    {
        $images = $settings['carousel'] ?? [];
        $slidesToShow = $settings['slides_to_show'] ?? 4;
        
        if (empty($images)) {
            return '';
        }
        
        $html = '<div class="elementor-image-carousel" style="display: grid; grid-template-columns: repeat('.$slidesToShow.', 1fr); gap: 15px;">';
        
        foreach ($images as $image) {
            $url = $image['url'] ?? '';
            if ($url) {
                $html .= '<div class="carousel-item">
                    <img src="'.htmlspecialchars($url).'" alt="" style="width: 100%; height: 200px; object-fit: cover;">
                </div>';
            }
        }
        
        $html .= '</div>';
        
        return $html;
    }
}