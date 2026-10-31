<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>Campus Map | CLSU Navigator</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/home.css') }}?v={{ filemtime(public_path('css/home.css')) }}">
    <link rel="stylesheet" href="{{ asset('css/map.css') }}?v={{ filemtime(public_path('css/map.css')) }}">
</head>
<body class="map-page">

@php
    // Filter options (value => label). Used by both the side menu and the bubble filters.
    $amenities = [
        'Academic Support' => 'Academic Support',
        'Sports & Recreation' => 'Sports & Recreation',
        'Health & Wellbeing' => 'Health & Wellbeing',
        'Daily Living & Dining' => 'Daily Living & Dining',
    ];
    $facilities = [
        'Academic & Instructional Facilities' => 'Academic & Instructional',
        'Research & Innovation Facilities' => 'Research & Innovation',
        'Residential & Living Facilities' => 'Residential & Living',
        'Administrative & Student Support Facilities' => 'Administrative & Student Support',
        'Athletic & Recreational Facilities' => 'Athletic & Recreational',
        'Institutional Support & Infrastructure' => 'Institutional Support & Infrastructure',
    ];
@endphp

<header class="mapbar">
  <a class="back" href="{{ route('home') }}" aria-label="Back to the homepage"><span aria-hidden="true">&larr;</span> Back</a>
  <div class="mapbar-title">
    <img class="seal-img mini" src="{{ asset('images/clsu-seal.png') }}" alt="CLSU seal">
    <div>
      <strong>CLSU NAVIGATOR</strong>
      <span>Campus Map</span>
    </div>
  </div>
  <div class="style-switch">
    <span class="opt on" id="optBoxed">Boxed</span>
    <button type="button" class="switch" id="filterStyle" role="switch" aria-checked="false" aria-label="Use bubble filters instead of boxed filters"></button>
    <span class="opt" id="optBubble">Bubble</span>
  </div>
  <div class="acts">
    @auth
      <span class="who">{{ Auth::user()->full_name }}</span>
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit">LOGOUT</button>
      </form>
    @else
      <a class="solid" href="{{ route('login') }}" data-login-open>LOGIN</a>
    @endauth
  </div>
</header>

<div class="map-layout" id="mapLayout">

  {{-- Show or hide the side menu (boxed style only) --}}
  <button type="button" class="side-toggle" id="sideToggle" aria-expanded="true" aria-controls="mapSide" aria-label="Collapse the menu">
    <span class="burger" aria-hidden="true"></span><span class="side-toggle-text">Hide menu</span>
  </button>

  <aside class="side" id="mapSide" aria-label="Map menu">

    <section>
      <h2 class="side-h">Search</h2>
      <label class="sr-only" for="mapSearch">Search the campus</label>
      <input id="mapSearch" class="f-search" type="search" placeholder="Search the campus" autocomplete="off">
    </section>

    <section>
      <h2 class="side-h">Map view</h2>
      <div class="seg" role="group" aria-label="Map view">
        <button type="button" aria-pressed="true" data-view="Leaflet">Interactive</button>
        <button type="button" aria-pressed="false" data-view="SVG">Detailed</button>
        <button type="button" aria-pressed="false" data-view="Flat">Black &amp; white</button>
      </div>
    </section>

    <section id="filtersBoxed">
      <h2 class="side-h">Filters</h2>

      <div class="acc">
        <button type="button" class="acc-btn" aria-expanded="false" aria-controls="accAmenity">Amenities <span class="acc-ico" aria-hidden="true">+</span></button>
        <div class="acc-panel" id="accAmenity" hidden>
          @foreach ($amenities as $value => $label)
            <label><input class="f-box" type="checkbox" name="amenity[]" value="{{ $value }}"> {{ $label }}</label>
          @endforeach
        </div>
      </div>

      <div class="acc">
        <button type="button" class="acc-btn" aria-expanded="false" aria-controls="accFacility">Facilities <span class="acc-ico" aria-hidden="true">+</span></button>
        <div class="acc-panel" id="accFacility" hidden>
          @foreach ($facilities as $value => $label)
            <label><input class="f-box" type="checkbox" name="facility[]" value="{{ $value }}"> {{ $label }}</label>
          @endforeach
        </div>
      </div>

      <div class="acc">
        <button type="button" class="acc-btn" aria-expanded="false" aria-controls="accCollege">College buildings <span class="acc-ico" aria-hidden="true">+</span></button>
        <div class="acc-panel" id="accCollege" hidden>
          <p class="empty-note">No colleges added yet. They will appear here once the official list is confirmed.</p>
        </div>
      </div>

      <button type="button" class="link-btn" id="clearFilters">Clear filters</button>
    </section>

    <section>
      <h2 class="side-h">More</h2>
      <button type="button" class="menu-btn">Events</button>
      @auth
        <button type="button" class="menu-btn">Floor plans</button>
      @else
        <button type="button" class="menu-btn locked" aria-disabled="true" title="Log in to view floor plans">Floor plans <svg class="lock-ico" viewBox="0 0 24 24" width="16" height="16" aria-hidden="true"><rect x="5" y="11" width="14" height="10" rx="2" fill="currentColor"/><path d="M8 11V8a4 4 0 018 0v3" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/></svg><span class="sr-only">(login required)</span></button>
      @endauth
    </section>

  </aside>

  <main class="map-area">
    {{-- Map goes here --}}
    <div id="campusMap" class="map-canvas" role="region" aria-label="Campus map"></div>

    {{-- Zoom and full screen (both filter styles) --}}
    <div class="map-tools">
      <button type="button" class="tool" id="fsBtn" aria-label="Enter full screen" title="Full screen" hidden>
        <svg class="ico-enter" viewBox="0 0 24 24" width="20" height="20" aria-hidden="true"><path d="M4 9V4h5M20 9V4h-5M4 15v5h5M20 15v5h-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        <svg class="ico-exit" viewBox="0 0 24 24" width="20" height="20" aria-hidden="true"><path d="M9 4v5H4M15 4v5h5M9 20v-5H4M15 20v-5h5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
      </button>
      <div class="zoom-group" role="group" aria-label="Zoom">
        <button type="button" class="tool" data-zoom="in" aria-label="Zoom in" title="Zoom in">+</button>
        <button type="button" class="tool" data-zoom="out" aria-label="Zoom out" title="Zoom out">&minus;</button>
      </div>
    </div>

    {{-- Bubble filter style: floats over the map. Same filters, search, and map views as the side menu. --}}
    <div class="bubbles" id="bubbleUI" hidden>
      <div class="bb-top">
        <div class="bb-search">
          <label class="sr-only" for="bubbleSearch">Search the campus</label>
          <input id="bubbleSearch" class="f-search" type="search" placeholder="Search the campus" autocomplete="off">
          <svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true"><circle cx="10" cy="10" r="6.5" fill="none" stroke="currentColor" stroke-width="2"/><path d="M15 15l6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
        </div>

        <div class="bb-chips" role="group" aria-label="Filters">
          <div class="bb-wrap">
            <button type="button" class="bb-chip bb-toggle" aria-expanded="false" aria-controls="bbAmenity">Amenities <span class="bb-count" data-count-for="bbAmenity" hidden></span></button>
            <div class="bb-pop" id="bbAmenity" role="group" aria-label="Amenities" hidden>
              @foreach ($amenities as $value => $label)
                <label><input class="f-box" type="checkbox" name="amenity[]" value="{{ $value }}"> {{ $label }}</label>
              @endforeach
            </div>
          </div>

          <div class="bb-wrap">
            <button type="button" class="bb-chip bb-toggle" aria-expanded="false" aria-controls="bbFacility">Facilities <span class="bb-count" data-count-for="bbFacility" hidden></span></button>
            <div class="bb-pop" id="bbFacility" role="group" aria-label="Facilities" hidden>
              @foreach ($facilities as $value => $label)
                <label><input class="f-box" type="checkbox" name="facility[]" value="{{ $value }}"> {{ $label }}</label>
              @endforeach
            </div>
          </div>

          <div class="bb-wrap">
            <button type="button" class="bb-chip bb-toggle" aria-expanded="false" aria-controls="bbCollege">College buildings <span class="bb-count" data-count-for="bbCollege" hidden></span></button>
            <div class="bb-pop" id="bbCollege" role="group" aria-label="College buildings" hidden>
              <p class="empty-note">No colleges added yet. They will appear here once the official list is confirmed.</p>
            </div>
          </div>

          <button type="button" class="bb-chip">Events</button>
          @auth
            <button type="button" class="bb-chip">Floor plans</button>
          @else
            <button type="button" class="bb-chip locked" aria-disabled="true" title="Log in to view floor plans">Floor plans <svg class="lock-ico" viewBox="0 0 24 24" width="16" height="16" aria-hidden="true"><rect x="5" y="11" width="14" height="10" rx="2" fill="currentColor"/><path d="M8 11V8a4 4 0 018 0v3" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/></svg><span class="sr-only">(login required)</span></button>
          @endauth

          <button type="button" class="bb-chip bb-clear" id="bbClear" hidden>Clear filters</button>
        </div>
      </div>

      <div class="bb-layers bb-wrap">
        <button type="button" class="bb-chip bb-toggle" aria-expanded="false" aria-controls="bbLayers">Map view</button>
        <div class="bb-pop" id="bbLayers" role="group" aria-label="Map view" hidden>
          <button type="button" class="bb-opt" aria-pressed="true" data-view="Leaflet">Interactive</button>
          <button type="button" class="bb-opt" aria-pressed="false" data-view="SVG">Detailed</button>
          <button type="button" class="bb-opt" aria-pressed="false" data-view="Flat">Black &amp; white</button>
        </div>
      </div>
    </div>
  </main>

</div>

@include('partials.login_modal')

<script src="{{ asset('js/login_modal.js') }}?v={{ filemtime(public_path('js/login_modal.js')) }}" defer></script>
<script src="{{ asset('js/map.js') }}?v={{ filemtime(public_path('js/map.js')) }}" defer></script>
</body>
</html>
