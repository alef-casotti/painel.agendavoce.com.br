<div class="carrossel-slide modelo-{{ $modelo }} slide-preview" data-slide-index="{{ $index }}">
    <div class="slide-glow slide-glow-a"></div>
    <div class="slide-glow slide-glow-b"></div>
    <div class="slide-ring"></div>

    <div class="slide-top">
        @if(!empty($slide['destaque']))
            <span class="badge"><span class="badge-label">{{ $slide['destaque'] }}</span></span>
        @endif
        <div class="accent-bar"></div>
    </div>

    <div class="slide-body">
        <h2 class="titulo">{{ $slide['titulo'] ?? '' }}</h2>
        <p class="texto">{{ $slide['texto'] ?? '' }}</p>
    </div>

    <div class="footer">
        <div class="brand-wrap">
            <span class="brand-dot"></span>
            <span class="brand"><span class="brand-label">Agenda Você</span></span>
        </div>
        <span class="page">{{ $index + 1 }} <span class="page-sep">/</span> {{ $total }}</span>
    </div>
</div>
