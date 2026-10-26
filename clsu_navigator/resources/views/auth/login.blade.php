<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | CLSU Navigator</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f3f5f3;
            font-family: Arial, sans-serif;
            color: #222;
        }

        .login-container {
            width: 100%;
            max-width: 400px;
            padding: 32px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        h1 {
            margin: 0 0 8px;
            font-size: 25px;
        }

        .description {
            margin-bottom: 24px;
            color: #666;
            font-size: 14px;
            line-height: 1.5;
        }

        label {
            display: block;
            margin: 16px 0 6px;
            font-size: 14px;
            font-weight: bold;
        }

        input {
            width: 100%;
            padding: 11px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
        }

        button {
            width: 100%;
            margin-top: 22px;
            padding: 12px;
            border: none;
            border-radius: 5px;
            background: #176b3a;
            color: white;
            font-size: 15px;
            cursor: pointer;
        }

        button:hover {
            background: #10552d;
        }

        .error {
            margin-top: 12px;
            color: #b42318;
            font-size: 13px;
        }
    </style>
</head>
<body>
    <main class="login-container">
        <h1>CLSU Navigator</h1>

        <p class="description">
            Sign in using your authorized university account.
        </p>

        <form method="POST" action="{{ route('login.submit') }}">
            @csrf

            <label for="email">University Email</label>
            <input
                type="email"
                id="email"
                name="email"
                value="{{ old('email') }}"
                required
                autocomplete="username"
            >

            <label for="password">Password</label>
            <input
                type="password"
                id="password"
                name="password"
                required
                autocomplete="current-password"
            >

            @if ($errors->any())
                <p class="error">{{ $errors->first() }}</p>
            @endif

            <button type="submit">Sign In</button>
        </form>
    </main>
</body>
</html>
