@props(['options' => []])

@php
    $images = $options['images'] ?? [];
    $totalSlides = count($images);
@endphp

{{-- Hero Slider Component --}}
<section class="hero">
    <div class="container">
        <div class="hero-slider">
            <div class="hero-track" style="--total-slides: {{ $totalSlides > 0 ? $totalSlides : 1 }}">
                @if ($totalSlides > 0)
                    @foreach ($images as $image)
                        <div class="hero-slide">
                            <a href="{{ $image['link'] ?? '#' }}">
                                <img src="{{ $image['image'] }}" alt="{{ $image['title'] ?? 'Banner' }}">
                            </a>
                        </div>
                    @endforeach
                @else
                    {{-- Fallback or empty state --}}
                    <div class="hero-slide">
                        <img src="{{ asset('themes/ta-vpp-theme/images/slide.png') }}" alt="VPP Shop Banner Default">
                    </div>
                @endif
            </div>
            
            @if ($totalSlides > 1)
                <div class="slider-dots" aria-hidden="true">
                    @foreach ($images as $index => $image)
                        <span class="slider-dot {{ $index === 0 ? 'active' : '' }}" data-index="{{ $index }}"></span>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</section>

@pushOnce('styles')
<style>
    .hero-track {
        display: flex;
        width: calc(var(--total-slides) * 100%);
        transition: transform 0.5s ease-in-out;
    }

    .hero-slide {
        flex: 0 0 calc(100% / var(--total-slides));
    }

    /* Simple auto-slide animation for dynamic number of slides */
    @keyframes dynamic-hero-slide {
        @php
            if ($totalSlides > 1) {
                $step = 100 / $totalSlides;
                for ($i = 0; $i <= $totalSlides; $i++) {
                    $percentage = ($i * $step);
                    $translateX = -($i * (100 / $totalSlides));
                    if ($i == $totalSlides) {
                         // Reset to first slide at the end
                         echo "100% { transform: translateX(0%); }\n";
                    } else {
                        $stayEnd = $percentage + ($step * 0.8);
                        echo "{$percentage}% { transform: translateX({$translateX}%); }\n";
                        if ($stayEnd < 100) {
                            echo "{$stayEnd}% { transform: translateX({$translateX}%); }\n";
                        }
                    }
                }
            }
        @endphp
    }

    .hero-track.animated {
        animation: dynamic-hero-slide {{ $totalSlides * 3 }}s infinite;
    }
</style>
@endpushOnce

@pushOnce('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tracks = document.querySelectorAll('.hero-track');
        tracks.forEach(track => {
            if (parseInt(track.style.getPropertyValue('--total-slides')) > 1) {
                track.classList.add('animated');
            }
        });
    });
</script>
@endpushOnce
