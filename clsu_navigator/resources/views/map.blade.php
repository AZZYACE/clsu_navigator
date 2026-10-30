<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Campus Map | CLSU Navigator</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/home.css') }}?v={{ filemtime(public_path('css/home.css')) }}">
    <link rel="stylesheet" href="{{ asset('css/map.css') }}?v={{ filemtime(public_path('css/map.css')) }}">
</head>
<body class="map-page">

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
    <span class="style-caption">Filter style</span>
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
      <a class="solid" href="{{ route('login') }}">LOGIN</a>
    @endauth
  </div>
</header>

<div class="map-layout" id="mapLayout">

  <button type="button" class="side-toggle" id="sideToggle" aria-expanded="true" aria-controls="mapSide" aria-label="Collapse the menu">
    <span class="chev" aria-hidden="true">&lsaquo;</span><span class="side-toggle-text">Hide menu</span>
  </button>

  <aside class="side" id="mapSide" aria-label="Map menu">

    <section>
      <h2 class="side-h">Search</h2>
      <label class="sr-only" for="mapSearch">Search the campus</label>
      <input id="mapSearch" type="search" placeholder="Search the campus" autocomplete="off">
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
          <label><input type="checkbox" name="amenity[]" value="Academic Support"> Academic Support</label>
          <label><input type="checkbox" name="amenity[]" value="Sports & Recreation"> Sports &amp; Recreation</label>
          <label><input type="checkbox" name="amenity[]" value="Health & Wellbeing"> Health &amp; Wellbeing</label>
          <label><input type="checkbox" name="amenity[]" value="Daily Living & Dining"> Daily Living &amp; Dining</label>
        </div>
      </div>

      <div class="acc">
        <button type="button" class="acc-btn" aria-expanded="false" aria-controls="accFacility">Facilities <span class="acc-ico" aria-hidden="true">+</span></button>
        <div class="acc-panel" id="accFacility" hidden>
          <label><input type="checkbox" name="facility[]" value="Academic & Instructional Facilities"> Academic &amp; Instructional</label>
          <label><input type="checkbox" name="facility[]" value="Research & Innovation Facilities"> Research &amp; Innovation</label>
          <label><input type="checkbox" name="facility[]" value="Residential & Living Facilities"> Residential &amp; Living</label>
          <label><input type="checkbox" name="facility[]" value="Administrative & Student Support Facilities"> Administrative &amp; Student Support</label>
          <label><input type="checkbox" name="facility[]" value="Athletic & Recreational Facilities"> Athletic &amp; Recreational</label>
          <label><input type="checkbox" name="facility[]" value="Institutional Support & Infrastructure"> Institutional Support &amp; Infrastructure</label>
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

    <section id="filtersBubble" hidden>
      <h2 class="side-h">Filters</h2>
      <p class="empty-note">Bubble filters will be added here once the design is ready.</p>
    </section>

    <section>
      <h2 class="side-h">More</h2>
      <button type="button" class="menu-btn">Events</button>
      @auth
        <button type="button" class="menu-btn">Floor plans</button>
      @else
        <button type="button" class="menu-btn locked" aria-disabled="true">Floor plans <span class="chip">LOGIN REQUIRED</span></button>
      @endauth
    </section>

  </aside>

  <main class="map-area">
    {{-- Map goes here --}}
    <div id="campusMap" class="map-canvas" role="region" aria-label="Campus map"></div>
  </main>

</div>

<script src="{{ asset('js/map.js') }}?v={{ filemtime(public_path('js/map.js')) }}" defer></script>
</body>
</html>
