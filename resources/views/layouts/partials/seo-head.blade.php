{{-- SEO Meta Tags Partial --}}
{{-- Include this in your layout head section --}}

@php
    $seoTitle = $title ?? get_setting('og_title', get_setting('app_name', 'SPMB Disdikpora Cianjur'));
    $seoDescription = get_setting('meta_description', 'Sistem Penerimaan Murid Baru Disdikpora Kabupaten Cianjur');
    $seoKeywords = get_setting('meta_keywords', 'SPMB, Cianjur, Pendaftaran, Sekolah, SMP, Disdikpora');
    $ogTitle = get_setting('og_title', $seoTitle);
    $ogDescription = get_setting('og_description', $seoDescription);
    $ogImage = get_setting('og_image') ? asset('storage/' . get_setting('og_image')) : null;
    $ogType = get_setting('og_type', 'website');
    $ogLocale = get_setting('og_locale', 'id_ID');
    $canonicalUrl = get_setting('canonical_url', config('app.url'));
    $twitterCard = get_setting('twitter_card', 'summary_large_image');
    $twitterSite = get_setting('twitter_site');
@endphp

{{-- Favicon --}}
@if(get_setting('favicon_16'))
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('storage/' . get_setting('favicon_16')) }}">
@endif
@if(get_setting('favicon_32'))
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('storage/' . get_setting('favicon_32')) }}">
@endif
@if(get_setting('favicon_180'))
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('storage/' . get_setting('favicon_180')) }}">
@endif
@if(get_setting('favicon_192'))
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('storage/' . get_setting('favicon_192')) }}">
@endif
@if(get_setting('favicon_512'))
    <link rel="icon" type="image/png" sizes="512x512" href="{{ asset('storage/' . get_setting('favicon_512')) }}">
@endif

{{-- Default Favicon Fallback --}}
@if(!get_setting('favicon_16') && !get_setting('favicon_32'))
    <link rel="icon" type="image/png" href="{{ asset('templates/assets/images/favicon.png') }}">
@endif

{{-- Basic SEO Meta --}}
<meta name="description" content="{{ $seoDescription }}">
@if($seoKeywords)
    <meta name="keywords" content="{{ $seoKeywords }}">
@endif
<link rel="canonical" href="{{ $canonicalUrl }}">

{{-- OpenGraph Meta Tags --}}
<meta property="og:type" content="{{ $ogType }}">
<meta property="og:title" content="{{ $ogTitle }}">
<meta property="og:description" content="{{ $ogDescription }}">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:site_name" content="{{ get_setting('app_name', 'SPMB Disdikpora Cianjur') }}">
<meta property="og:locale" content="{{ $ogLocale }}">
@if($ogImage)
    <meta property="og:image" content="{{ $ogImage }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
@endif

{{-- Twitter Card Meta Tags --}}
<meta name="twitter:card" content="{{ $twitterCard }}">
<meta name="twitter:title" content="{{ $ogTitle }}">
<meta name="twitter:description" content="{{ $ogDescription }}">
@if($ogImage)
    <meta name="twitter:image" content="{{ $ogImage }}">
@endif
@if($twitterSite)
    <meta name="twitter:site" content="@{{ $twitterSite }}">
@endif

{{-- Google Site Verification --}}
@if(get_setting('google_site_verification'))
    <meta name="google-site-verification" content="{{ get_setting('google_site_verification') }}">
@endif

{{-- Google Tag Manager (Head) --}}
@if(get_setting('google_tag_manager_id'))
    <script>(function (w, d, s, l, i) {
            w[l] = w[l] || []; w[l].push({
                'gtm.start':
                    new Date().getTime(), event: 'gtm.js'
            }); var f = d.getElementsByTagName(s)[0],
                j = d.createElement(s), dl = l != 'dataLayer' ? '&l=' + l : ''; j.async = true; j.src =
                    'https://www.googletagmanager.com/gtm.js?id=' + i + dl; f.parentNode.insertBefore(j, f);
        })(window, document, 'script', 'dataLayer', '{{ get_setting('google_tag_manager_id') }}');</script>
@endif

{{-- Google Analytics (GA4) --}}
@if(get_setting('google_analytics_id') && !get_setting('google_tag_manager_id'))
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ get_setting('google_analytics_id') }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag() { dataLayer.push(arguments); }
        gtag('js', new Date());
        gtag('config', '{{ get_setting('google_analytics_id') }}');
    </script>
@endif