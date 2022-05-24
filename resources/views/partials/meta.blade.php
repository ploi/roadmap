<meta name="description" content="@yield('description')"/>

{{-- Schema.org markup for Google+ --}}
<meta itemprop="name" content="@yield('title')">
<meta itemprop="description" content="@yield('description')">
<meta itemprop="image" content="@yield('image', asset('images/og.jpg'))">

{{-- Open Graph data --}}
<meta property="og:title" content="@yield('title')" />
<meta property="og:description" content="@yield('description')" />
<meta property="og:image" content="@yield('image', asset('images/og.jpg'))" />
<meta property="og:type" content="@yield('og_type', 'website')">

{{-- Twitter card data --}}
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="@yield('title') ">
<meta name="twitter:description" content="@yield('description')">
<meta name="twitter:image" content="@yield('image', asset('images/og.jpg'))">

<link rel="sitemap" type="application/xml" href="{{ asset('sitemap.xml') }}">

@yield('additional_meta')
