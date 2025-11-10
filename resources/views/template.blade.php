<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">

  <title>{{ $slug ?? 'Template' }}</title>

  {{-- Ton CSS template (place files dans public/template/css/) --}}
  <link rel="stylesheet" href="{{ asset('template/css/style.css') }}">

  {{-- CSS léger pour simuler les classes elementor --}}
  <style>
    /* layout de base */
    body { margin:0; font-family: Arial, Helvetica, sans-serif; color:#111; background:#fff; }
    .elementor-section { position: relative; overflow: hidden; }
    .elementor-column { display:flex; flex-direction:column; padding:1rem; box-sizing:border-box; }
    .elementor-widget { margin-bottom:1rem; }
    .elementor-heading-title { margin:0 0 1rem 0; font-family: 'Oswald', sans-serif; text-align:center; }

    /* slideshow */
    .template-slideshow { position: absolute; inset:0; z-index:0; }
    .template-slideshow .slide { position:absolute; inset:0; opacity:0; transition:opacity .8s ease; }
    .template-slideshow .slide img { width:100%; height:100%; object-fit:cover; display:block; }
    .template-slideshow .slide.active { opacity:1; z-index:0; }

    /* overlay to put content above slideshow */
    .elementor-section > .elementor-column,
    .elementor-section > .elementor-widget,
    .elementor-section .elementor-column {
      position: relative; z-index:2;
    }

    /* gallery */
    .elementor-gallery { display:flex; flex-wrap:wrap; gap:8px; }
    .elementor-gallery .gallery-item img { width:200px; height:120px; object-fit:cover; }

    /* simple list */
    .elementor-list { list-style:none; padding:0; margin:0; display:flex; flex-wrap:wrap; gap:8px; }
    .elementor-list li { background:#f5f5f5; padding:6px 10px; border-radius:6px; }

    /* responsive */
    @media (max-width: 768px) {
      .elementor-column { padding: .5rem; }
    }
    /* Styles généraux */
.elementor-button {
    background: #AE8959;
    color: white;
    padding: 12px 30px;
    border: none;
    text-transform: uppercase;
    font-weight: bold;
    cursor: pointer;
    display: inline-block;
    text-decoration: none;
}

.elementor-button:hover {
    background: #8c6d47;
}

.elementor-icon-box {
    text-align: center;
    padding: 20px;
}

.elementor-icon-box-title {
    font-size: 1.2em;
    font-weight: bold;
    margin-bottom: 10px;
    color: #FFFFFF;
}

.elementor-icon-box-description {
    color: #CCCCCC;
    line-height: 1.5;
}

/* Témoignages */
.elementor-testimonial {
    background: rgba(255,255,255,0.1);
    padding: 20px;
    border-radius: 5px;
    margin: 10px;
}

.testimonial-image img {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    object-fit: cover;
    margin-bottom: 10px;
}

.testimonial-content {
    font-style: italic;
    margin-bottom: 10px;
    line-height: 1.5;
}

.testimonial-name {
    font-weight: bold;
    color: #AE8959;
}

.testimonial-job {
    font-size: 0.9em;
    color: #CCCCCC;
}

/* Icônes */
.elementor-icon {
    font-size: 2em;
    color: #AE8959;
    display: inline-block;
}

/* Carrousel d'images */
.elementor-image-carousel {
    margin: 20px 0;
}

.carousel-item {
    overflow: hidden;
    border-radius: 5px;
}

/* Slideshow */
.template-slideshow {
    position: absolute;
    inset: 0;
    z-index: 0;
}

.template-slideshow .slide {
    position: absolute;
    inset: 0;
    opacity: 0;
    transition: opacity 0.8s ease;
}

.template-slideshow .slide img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

.template-slideshow .slide.active {
    opacity: 1;
    z-index: 0;
}

/* Layout */
.elementor-section {
    position: relative;
    overflow: hidden;
}

.elementor-column {
    display: flex;
    flex-direction: column;
    padding: 1rem;
    box-sizing: border-box;
}

.elementor-widget {
    margin-bottom: 1rem;
}

.elementor-heading-title {
    margin: 0 0 1rem 0;
    font-family: 'Oswald', sans-serif;
}

.elementor-image img {
    max-width: 100%;
    height: auto;
}

/* Overlay pour le contenu au-dessus du slideshow */
.elementor-section > .elementor-column,
.elementor-section > .elementor-widget,
.elementor-section .elementor-column {
    position: relative;
    z-index: 2;
}

/* Responsive */
@media (max-width: 768px) {
    .elementor-column {
        padding: 0.5rem;
    }
    
    .elementor-image-carousel {
        grid-template-columns: repeat(2, 1fr) !important;
    }
}
  </style>
</head>
<body>

  <div id="template-root">
    {!! $html !!}
  </div>

  {{-- JS minimal pour le slideshow --}}
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const slideshows = document.querySelectorAll('.template-slideshow');
      slideshows.forEach(function(slideshow) {
        const slides = Array.from(slideshow.querySelectorAll('.slide'));
        if (slides.length === 0) return;
        let idx = 0;
        slides.forEach((s,i) => { if (i===0) s.classList.add('active'); });
        setInterval(() => {
          slides[idx].classList.remove('active');
          idx = (idx + 1) % slides.length;
          slides[idx].classList.add('active');
        }, 4000);
      });
    });
  </script>

  {{-- ton JS template si nécessaire --}}
  <script src="{{ asset('template/js/main.js') }}"></script>
</body>
</html>
