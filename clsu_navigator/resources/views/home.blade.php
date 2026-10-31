<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>CLSU Navigator | Interactive Campus Map</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/home.css') }}?v={{ filemtime(public_path('css/home.css')) }}">
</head>
<body>

<div class="site-top-wrap" id="topWrap"><div class="site-top" id="siteTop">

<div class="strip"><div class="in"><div><span><a href="mailto:op@clsu.edu.ph">op@clsu.edu.ph</a></span><span><a href="tel:+63449408785">(044) 940 8785</a></span></div><div>Official CLSU website: <a href="https://clsu.edu.ph/" target="_blank" rel="noopener noreferrer">clsu.edu.ph ↗</a></div></div></div>

<header class="head"><div class="in">
  <img class="seal-img" src="{{ asset('images/clsu-seal.png') }}" alt="CLSU seal">
  <div>
    <h2>CLSU NAVIGATOR</h2>
    <p>An Interactive Campus Map and Information System · Central Luzon State University, Science City of Muñoz</p>
  </div>
</div></header>

<nav class="navbar"><div class="in">
  <ul>
    <li class="on"><a href="#home">HOME</a></li><li><a href="#about">ABOUT</a></li><li><a href="#features">FEATURES</a></li><li><a href="#how">HOW TO USE</a></li><li><a href="#access">ACCESS &amp; ACCESSIBILITY</a></li>{{-- <li><a href="#help">HELP</a></li> --}}
  </ul>
  <a class="nav-box" href="{{ route('map') }}">CAMPUS MAP</a>
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
</div></nav>

</div></div>

<section class="hero" id="home"><div class="in">
  <div>
    <div class="lbl">Welcome to</div>
    <h1>CLSU Navigator</h1>
    <p>Find your way around the CLSU campus. Look up buildings, departments, amenities, rooms, and events in one place.</p>
    <div class="ctas">
      <a class="btn primary" href="{{ route('map') }}">Explore Campus Map</a>
      @guest
        <a class="btn ghost" href="{{ route('login') }}" data-login-open>Login</a>
      @endguest
    </div>
    <small>No account needed to browse. Login is for CLSU students, faculty, staff, and admins.</small>
  </div>
  <div class="ph"><span>CAMPUS PHOTO / ILLUSTRATION · dark green overlay · carousel dots</span></div>
</div></section>

<section class="sec green" id="about"><div class="in">
  <div class="lbl">About</div>
  <h2 class="h2">About CLSU Navigator</h2>
  <div class="about">
    <div>
      <p>CLSU Navigator is a web-based interactive campus map and information system. It gives students, faculty, staff, and visitors one place to look up CLSU locations and the information attached to them.</p>
      <p>Information is maintained by authorized CLSU personnel.</p>
    </div>
    <div class="purpose">
      <div class="lbl">Purpose</div>
      <ul>
        <li>Make unfamiliar places easier to find</li>
        <li>Keep campus information in one place</li>
        <li>Show accessibility features and, for logged-in users, indoor floor plans</li>
        <li>Keep data accurate through role-based content management</li>
      </ul>
    </div>
  </div>
</div></section>

<section class="sec" id="features"><div class="in">
  <div class="lbl">Features</div>
  <h2 class="h2">What you can find</h2>
  <div class="grid3">
    <div class="card"><div class="ico"></div><h3>Buildings &amp; offices</h3><p>Locate colleges, offices, and campus buildings on the map.</p></div>
    <div class="card"><div class="ico"></div><h3>Facilities &amp; amenities</h3><p>Filter the map by amenities such as dining, health, and sports, and by facilities such as academic, research, and residential.</p></div>
    <div class="card"><div class="ico"></div><h3>Rooms &amp; floor plans</h3><p>Find floors and rooms inside campus buildings. Interior floor plans are shown to logged-in users only.</p><span class="chip">FLOOR PLANS: LOGIN REQUIRED</span></div>
    <div class="card"><div class="ico"></div><h3>Departments &amp; colleges</h3><p>See which department or college a building belongs to.</p></div>
    <div class="card"><div class="ico"></div><h3>Campus events</h3><p>See published public events, when they happen, and where they are held.</p></div>
    <div class="card"><div class="ico"></div><h3>Three map views</h3><p>Switch between the interactive map, a detailed SVG map, and a simple black-and-white map.</p></div>
  </div>
</div></section>

<section class="sec green" id="how"><div class="in">
  <div class="lbl">How to use</div>
  <h2 class="h2">How to use CLSU Navigator</h2>
  <div class="grid3">
    <div class="card"><div class="num">1</div><h3>Open the map</h3><p>Select Explore Campus Map. Guests do not need to log in.</p></div>
    <div class="card"><div class="num">2</div><h3>Search or browse</h3><p>Type a building, office, or facility, or pick a category filter.</p></div>
    <div class="card"><div class="num">3</div><h3>View details</h3><p>Select a marker to see its information, photos, and floor plan.</p></div>
  </div>
  <div class="notice notice-how" id="help">
    <b>Please note</b>
    <p>CLSU Navigator shows campus locations and information only. It has no GPS navigation, real-time tracking, or turn-by-turn directions. Personnel schedules do not guarantee availability. Please verify important details with the appropriate CLSU office.</p>
  </div>
  <div class="center"><a class="btn primary" href="{{ route('map') }}">Get Started</a></div>
</div></section>

<section class="sec" id="access"><div class="in">
  <div class="lbl">Access</div>
  <h2 class="h2">Who can use it</h2>
  <div class="grid3 two">
    <div class="card"><div class="role">Guests / visitors</div><p>Browse the public map, buildings, amenities, and published public events. No login needed. Interior floor plans are not shown.</p></div>
    <div class="card"><div class="role">Students, faculty, staff</div><p>Log in to view the floor plans you are permitted to see and to request access to restricted ones.</p>@guest<a class="btn ghost" style="align-self:flex-start;margin-top:auto" href="{{ route('login') }}" data-login-open>Login</a>@endguest</div>
  </div>
  <p class="fine-line">Accounts are created by CLSU administrators only. There is no public sign-up.</p>
</div></section>

<section class="sec green" id="accessibility"><div class="in">
  <div class="lbl">Accessibility</div>
  <h2 class="h2">Accessibility information</h2>
  <div class="a11y">
    <div>
      <p class="lead">Find accessibility features on campus, where information has been provided. Features shown on interior floor plans need a login.</p>
      <div class="icons"><div><i></i>Ramps</div><div><i></i>Elevators</div><div><i></i>Accessible entrances</div><div><i></i>Accessible restrooms</div></div>
    </div>
    <a class="btn ghost" href="#accessibility">Learn more</a>
  </div>
</div></section>

<footer class="foot"><div class="in">
  <div class="cols">
    <div><h4>CLSU Navigator</h4><p>An Interactive Campus Map and Information System<br>Central Luzon State University</p></div>
    <div><h4>Quick links</h4><p>Home · About · Features<br>How to Use · Access &amp; Accessibility<br>Login<br><a href="https://clsu.edu.ph/" target="_blank" rel="noopener noreferrer">Official CLSU website ↗</a></p></div>
    <div><h4>Contact</h4><p><a href="mailto:op@clsu.edu.ph">op@clsu.edu.ph</a><br><a href="tel:+63449408785">(044) 940 8785</a><br>Science City of Muñoz, Nueva Ecija</p></div>
  </div>
</div></footer>

@include('partials.login_modal')

<script src="{{ asset('js/home.js') }}?v={{ filemtime(public_path('js/home.js')) }}" defer></script>
<script src="{{ asset('js/login_modal.js') }}?v={{ filemtime(public_path('js/login_modal.js')) }}" defer></script>
</body>
</html>
