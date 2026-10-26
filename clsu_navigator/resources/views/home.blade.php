<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CLSU Navigator | Interactive Campus Map</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow:wght@400;500;600;700&family=Barlow+Condensed:wght@600;700&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
</head>
<body>
</head><body>
<title>CLSU Navigator Homepage Wireframe</title>
<meta name="claude:viewport-fit-sides" content="cover">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Barlow:wght@400;500;600;700&family=Barlow+Condensed:wght@600;700&family=IBM+Plex+Mono:wght@400;500&display=swap">
<style>
:root{
  --bg:#eef0ec; --surface:#ffffff; --ink:#1f2a22; --muted:#5d6a60; --line:#c4cbc3;
  --ph:#dfe4de; --ph-line:#aab3a9; --green:#1f7a2e; --green-dark:#0b3d15; --lime:#8cc63f;
  --font-body:"Barlow",system-ui,sans-serif; --font-head:"Barlow Condensed","Barlow",sans-serif; --font-mono:"IBM Plex Mono",ui-monospace,monospace;
  --gut:clamp(16px,4vw,64px);
}
@media (prefers-color-scheme:dark){:root:not([data-theme="light"]){
  --bg:#121713; --surface:#1b221d; --ink:#e6ece7; --muted:#9aa89d; --line:#34403a;
  --ph:#252e28; --ph-line:#4a5850; --green:#3fa352; --green-dark:#08210d; color-scheme:dark}}
:root[data-theme="dark"]{
  --bg:#121713; --surface:#1b221d; --ink:#e6ece7; --muted:#9aa89d; --line:#34403a;
  --ph:#252e28; --ph-line:#4a5850; --green:#3fa352; --green-dark:#08210d; color-scheme:dark}

*{box-sizing:border-box}
body{background:var(--surface);color:var(--ink);font-family:var(--font-body);font-size:16px;line-height:1.5;margin:0}
.in{max-width:1440px;margin:0 auto;padding-left:max(var(--gut),env(safe-area-inset-left,0px));padding-right:max(var(--gut),env(safe-area-inset-right,0px))}
a{color:inherit;text-decoration:none}

.ph{background:var(--ph);border:1.5px dashed var(--ph-line);color:var(--muted);display:flex;align-items:center;justify-content:center;text-align:center;font-family:var(--font-mono);font-size:12px;
  background-image:linear-gradient(to top right,transparent calc(50% - .75px),var(--ph-line) 50%,transparent calc(50% + .75px)),linear-gradient(to bottom right,transparent calc(50% - .75px),var(--ph-line) 50%,transparent calc(50% + .75px))}
.ph span{background:var(--ph);padding:2px 10px}
.btn{display:inline-block;font:600 15px var(--font-body);letter-spacing:.05em;text-transform:uppercase;padding:13px 26px;border:2px solid var(--green);border-radius:3px}
.btn.primary{background:var(--green);color:#fff}
.btn.ghost{color:var(--green)}
.lbl{font-family:var(--font-mono);font-size:12px;letter-spacing:.08em;text-transform:uppercase;color:var(--muted)}
.h2{font-family:var(--font-head);font-size:38px;line-height:1.1;margin:6px 0 0;text-wrap:balance}
.h2::after{content:"";display:block;width:56px;height:4px;background:var(--lime);margin-top:12px}
.lead{margin:14px 0 0;color:var(--muted);max-width:62ch;font-size:17px}
.sec{padding-block:72px}
.alt{background:var(--bg)}

.strip{background:var(--green-dark);color:#fff;font-size:13px}
.strip .in{display:flex;justify-content:space-between;gap:16px;padding-block:8px;flex-wrap:wrap}
.strip span+span{margin-left:20px}
.head .in{display:flex;align-items:center;gap:20px;padding-block:18px}
.seal{width:72px;height:72px;border-radius:50%;border:2px dashed var(--ph-line);background:var(--ph);display:grid;place-items:center;font:500 11px var(--font-mono);color:var(--muted);flex:none}
.head h2{font-family:var(--font-head);font-size:36px;line-height:1;margin:0;color:var(--green);letter-spacing:.01em}
.head p{margin:4px 0 0;color:var(--muted);font-size:14px}
.navbar{background:linear-gradient(90deg,var(--green),var(--lime))}
.navbar .in{display:flex;align-items:center;justify-content:space-between;gap:16px}
.navbar ul{display:flex;list-style:none;margin:0;padding:0;flex-wrap:wrap}
.navbar li a{display:block;color:#fff;font:600 14px var(--font-body);letter-spacing:.07em;padding:18px 20px;border-right:1px solid rgba(255,255,255,.35)}
.navbar li:first-child a{border-left:1px solid rgba(255,255,255,.35)}
.navbar li.on a{background:rgba(0,0,0,.18)}
.acts{display:flex;gap:10px}
.acts a{font:600 13px var(--font-body);letter-spacing:.07em;padding:9px 18px;border-radius:3px;border:2px solid #fff;color:#fff}
.acts a.solid{background:#fff;color:var(--green-dark)}

.hero{background:var(--bg)}
.hero .in{display:grid;grid-template-columns:1fr 1.2fr;gap:64px;align-items:center;min-height:min(640px,calc(100vh - 190px));padding-block:56px}
.hero h1{font-family:var(--font-head);font-size:76px;line-height:.98;margin:8px 0 20px}
.hero p{margin:0 0 30px;font-size:20px;max-width:44ch}
.ctas{display:flex;gap:14px;flex-wrap:wrap}
.hero small{display:block;margin-top:18px;color:var(--muted);font-size:14px}
.hero .ph{height:480px;border-radius:4px}

.about{display:grid;grid-template-columns:1.2fr 1fr;gap:64px;margin-top:32px}
.about p{margin:0 0 14px;font-size:18px;max-width:60ch}
.purpose{border:1.5px solid var(--line);border-radius:4px;padding:28px 32px;background:var(--surface)}
.purpose ul{margin:12px 0 0;padding-left:20px;font-size:17px}
.purpose li{margin-bottom:8px}

.grid3{display:grid;grid-template-columns:repeat(3,1fr);gap:28px;margin-top:36px}
.card{border:1.5px solid var(--line);border-radius:4px;background:var(--surface);padding:28px;display:flex;flex-direction:column;gap:10px}
.card .ico{width:52px;height:52px;border:1.5px dashed var(--ph-line);background:var(--ph);border-radius:4px}
.card h3{font-family:var(--font-head);font-size:26px;margin:6px 0 0;line-height:1.1}
.card p{margin:0;color:var(--muted)}
.chip{align-self:flex-start;font:500 11px var(--font-mono);letter-spacing:.06em;border:1.5px solid var(--muted);color:var(--muted);border-radius:99px;padding:3px 10px;margin-top:auto}
.num{font:700 48px var(--font-head);color:var(--green);line-height:1}
.role{font:700 13px var(--font-mono);letter-spacing:.08em;text-transform:uppercase;color:var(--green)}
.center{text-align:center;margin-top:40px}
.fine-line{margin:24px 0 0;color:var(--muted)}

.a11y{display:grid;grid-template-columns:1fr auto;gap:40px;align-items:center}
.icons{display:flex;gap:28px;margin-top:22px;flex-wrap:wrap}
.icons div{display:flex;align-items:center;gap:10px}
.icons i{width:32px;height:32px;border:1.5px dashed var(--ph-line);background:var(--ph);border-radius:50%}

.notice{margin:72px 0;border:2px solid var(--ink);border-radius:4px;padding:22px 28px;display:flex;gap:20px;align-items:flex-start}
.notice b{font-family:var(--font-head);font-size:22px;white-space:nowrap}
.notice p{margin:0}

.foot{background:var(--green-dark);color:#dfe8e0;padding-block:48px 24px}
.foot .cols{display:grid;grid-template-columns:1.4fr 1fr 1fr;gap:40px}
.foot h4{font:600 12px var(--font-mono);letter-spacing:.1em;text-transform:uppercase;margin:0 0 10px;color:#fff}
.foot p{margin:0;font-size:15px;line-height:1.8}
.foot .fine{border-top:1px solid rgba(255,255,255,.2);margin-top:32px;padding-top:16px;font-size:13px;opacity:.8}

@media (max-width:900px){
  .hero .in,.about,.grid3,.foot .cols,.a11y{grid-template-columns:1fr}
  .hero h1{font-size:52px}.hero .ph{height:260px}.hero .in{gap:32px;min-height:0}
  .navbar .in{flex-direction:column;align-items:stretch}.acts{padding-bottom:12px}
  .sec{padding-block:48px}.notice{flex-direction:column;gap:6px}
}
</style>

<div class="strip"><div class="in"><div><span>op@clsu.edu.ph</span><span>(044) 940 8785</span></div><div>Official CLSU website: clsu.edu.ph ↗</div></div></div>

<header class="head"><div class="in">
  <div class="seal">SEAL</div>
  <div>
    <h2>CLSU NAVIGATOR</h2>
    <p>An Interactive Campus Map and Information System · Central Luzon State University, Science City of Muñoz</p>
  </div>
</div></header>

<nav class="navbar"><div class="in">
  <ul>
    <li class="on"><a href="#home">HOME</a></li><li><a href="#about">ABOUT</a></li><li><a href="#features">FEATURES</a></li><li><a href="#how">HOW TO USE</a></li><li><a href="#accessibility">ACCESSIBILITY</a></li><li><a href="#help">HELP</a></li>
  </ul>
  <div class="acts"><a href="#map-coming-soon">CAMPUS MAP</a><a class="solid" href="{{ route('login') }}">LOGIN</a></div>
</div></nav>

<section class="hero" id="home"><div class="in">
  <div>
    <div class="lbl">Welcome to</div>
    <h1>CLSU Navigator</h1>
    <p>Find your way around the CLSU campus. Look up buildings, offices, facilities, rooms, and services in one place.</p>
    <div class="ctas"><a class="btn primary" href="#map-coming-soon">Explore Campus Map</a><a class="btn ghost" href="{{ route('login') }}">Login</a></div>
    <small>No account needed to browse. Login is for CLSU students, faculty, staff, and admins.</small>
  </div>
  <div class="ph"><span>CAMPUS PHOTO / ILLUSTRATION · dark green overlay · carousel dots</span></div>
</div></section>

<section class="sec" id="about"><div class="in">
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
        <li>Show accessibility features and indoor floor plans</li>
        <li>Keep data accurate through role-based content management</li>
      </ul>
    </div>
  </div>
</div></section>

<section class="sec alt" id="features"><div class="in">
  <div class="lbl">Features</div>
  <h2 class="h2">What you can find</h2>
  <div class="grid3">
    <div class="card"><div class="ico"></div><h3>Buildings &amp; offices</h3><p>Locate colleges, offices, and campus buildings on the map.</p></div>
    <div class="card"><div class="ico"></div><h3>Facilities &amp; amenities</h3><p>Libraries, labs, canteens, restrooms, and other campus amenities.</p></div>
    <div class="card"><div class="ico"></div><h3>Rooms &amp; floor plans</h3><p>View digital floor plans and find rooms inside buildings.</p></div>
    <div class="card"><div class="ico"></div><h3>Services</h3><p>See what each office offers and where to go for it.</p></div>
    <div class="card"><div class="ico"></div><h3>Event venues</h3><p>Find halls, gyms, and open spaces used for campus events.</p></div>
    <div class="card"><div class="ico"></div><h3>Personnel information</h3><p>Faculty and staff details and schedules, as posted by authorized personnel.</p><span class="chip">LOGIN REQUIRED</span></div>
  </div>
</div></section>

<section class="sec" id="how"><div class="in">
  <div class="lbl">How to use</div>
  <h2 class="h2">How to use CLSU Navigator</h2>
  <div class="grid3">
    <div class="card"><div class="num">1</div><h3>Open the map</h3><p>Select Explore Campus Map. Guests do not need to log in.</p></div>
    <div class="card"><div class="num">2</div><h3>Search or browse</h3><p>Type a building, office, or facility, or pick a category filter.</p></div>
    <div class="card"><div class="num">3</div><h3>View details</h3><p>Select a marker to see its information, photos, and floor plan.</p></div>
  </div>
  <div class="center"><a class="btn primary" href="#map-coming-soon">Get Started</a></div>
</div></section>

<section class="sec alt" id="access"><div class="in">
  <div class="lbl">Access</div>
  <h2 class="h2">Who can use it</h2>
  <div class="grid3">
    <div class="card"><div class="role">Guests / visitors</div><p>Browse the public map and campus information. No login needed.</p></div>
    <div class="card"><div class="role">Students, faculty, staff</div><p>Log in to see information available to your role.</p><a class="btn ghost" style="align-self:flex-start;margin-top:auto" href="{{ route('login') }}">Login</a></div>
    <div class="card"><div class="role">Admins</div><p>Log in to manage campus data and documents.</p><a class="btn ghost" style="align-self:flex-start;margin-top:auto" href="{{ route('login') }}">Login</a></div>
  </div>
  <p class="fine-line">Accounts are created by CLSU administrators only. There is no public sign-up.</p>
</div></section>

<section class="sec" id="accessibility"><div class="in">
  <div class="a11y">
    <div>
      <div class="lbl">Accessibility</div>
      <h2 class="h2">Accessibility information</h2>
      <p class="lead">Find accessibility features on campus, where information has been provided.</p>
      <div class="icons"><div><i></i>Ramps</div><div><i></i>Elevators</div><div><i></i>Accessible entrances</div><div><i></i>Accessible restrooms</div></div>
    </div>
    <a class="btn ghost" href="#accessibility">Learn more</a>
  </div>
  <div class="notice" id="help">
    <b>Please note</b>
    <p>CLSU Navigator shows campus locations and information only. It has no GPS navigation, real-time tracking, or turn-by-turn directions. Personnel schedules do not guarantee availability. Please verify important details with the appropriate CLSU office.</p>
  </div>
</div></section>

<footer class="foot"><div class="in">
  <div class="cols">
    <div><h4>CLSU Navigator</h4><p>An Interactive Campus Map and Information System<br>Central Luzon State University</p></div>
    <div><h4>Quick links</h4><p>Home · About · Features<br>How to Use · Accessibility<br>Help · Login</p></div>
    <div><h4>Contact</h4><p>op@clsu.edu.ph<br>(044) 940 8785<br>Science City of Muñoz, Nueva Ecija</p></div>
  </div>
  <div class="fine">A CLSU Information Technology capstone project. It complements, and does not replace, official CLSU sites.</div>
</div></footer>
    <script src="{{ asset('js/home.js') }}" defer></script>
</body>
</html>
