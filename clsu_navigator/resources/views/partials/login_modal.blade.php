{{-- Login pop-up (guests only). Used on the homepage and the campus map. --}}
@guest
<div class="modal" id="loginModal" hidden data-open="{{ ($errors->any() || session('open_login')) ? '1' : '0' }}">
  <div class="modal-box" role="dialog" aria-modal="true" aria-labelledby="loginTitle">
    <button class="x" type="button" aria-label="Close login" data-close>&times;</button>
    <div class="m-head">
      <img class="seal-img sm" src="{{ asset('images/clsu-seal.png') }}" alt="CLSU seal">
      <div><h2 id="loginTitle">Log in</h2><p>CLSU Navigator</p></div>
    </div>
    <p class="m-lead">For CLSU students, faculty, staff, and admins.</p>

    <form method="POST" action="{{ route('login.submit') }}">
      @csrf
      @if ($errors->any())
        <div class="alert" role="alert">{{ $errors->first() }}</div>
      @endif

      <label for="loginEmail">Email address</label>
      <input id="loginEmail" name="email" type="email" value="{{ old('email') }}" placeholder="Enter your email address" autocomplete="username" required>

      <label for="loginPassword">Password</label>
      <div class="pw">
        <input id="loginPassword" name="password" type="password" placeholder="Enter your password" autocomplete="current-password" required>
        <button type="button" id="pwToggle" aria-controls="loginPassword">Show</button>
      </div>

      <button class="btn primary full" type="submit">Log in</button>
    </form>

    <p class="m-note">No account yet? Accounts are created by CLSU administrators. Forgot your password? Ask your administrator to reset it.</p>
    <div class="m-guest">Just browsing? <a href="{{ route('map') }}" data-close>Continue as guest</a></div>
  </div>
</div>
@endguest
