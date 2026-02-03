{{-- Google Tag Manager (Body) - Place immediately after opening

<body> tag --}}
    @if(get_setting('google_tag_manager_id'))
        <noscript><iframe src="https://www.googletagmanager.com/ns.html?id={{ get_setting('google_tag_manager_id') }}"
                height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    @endif