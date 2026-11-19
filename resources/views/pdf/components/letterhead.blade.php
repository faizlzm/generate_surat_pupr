<div class="letterhead">
    @if(!empty($s['logo_path']))
        <img class="kop-image" src="{{ $s['logo_path'] }}" alt="Kop surat">
    @else
        <div class="fallback-kop">
            <div class="logo-box">LOGO</div>
            <div class="letterhead-text">
                <div class="ministry">{{ $s['ministry'] ?? '' }}</div>
                <div class="directorate">{{ $s['directorate'] ?? '' }}</div>
                <div class="address">{{ $s['address'] ?? '' }}</div>
            </div>
        </div>
    @endif
</div>
