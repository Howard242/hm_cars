
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Login | HM CARS</title>
    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 20px;
            background: #101827;
            color: #172033;
            font-family: Arial, sans-serif;
        }

        .login-card {
            width: 100%;
            max-width: 420px;
            padding: 35px;
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 15px 45px rgba(0,0,0,.25);
        }

        .brand {
            margin: 0;
            color: #12213a;
            font-size: 30px;
            font-weight: 800;
            text-align: center;
        }

        .subtitle {
            margin: 8px 0 28px;
            color: #687386;
            text-align: center;
        }

        label {
            display: block;
            margin: 17px 0 7px;
            font-weight: 700;
        }

        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccd3de;
            border-radius: 7px;
            font-size: 16px;
        }

        input:focus {
            outline: 2px solid #93baff;
            border-color: #2457c5;
        }

        button {
            width: 100%;
            margin-top: 24px;
            padding: 13px;
            border: 0;
            border-radius: 7px;
            background: #2457c5;
            color: white;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
        }

        button:hover { background: #19439d; }

        .error {
            margin-top: 15px;
            padding: 10px;
            border-radius: 6px;
            background: #fff0f0;
            color: #a52222;
            font-size: 14px;
        }

        .footer {
            margin-top: 22px;
            color: #687386;
            font-size: 12px;
            text-align: center;
        }
    </style>
</head>
<body>
    <main class="login-card">
        <h1 class="brand">HM CARS</h1>
        <p class="subtitle">Staff Portal · The Right Car. The Right Connection.</p>

        <form method="POST" action="{{ route('admin.login.submit') }}">
            @csrf

            <label for="email">Staff email</label>
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                autocomplete="username"
                required
                autofocus
            >

            <label for="password">Password</label>
            <input
                id="password"
                type="password"
                name="password"
                autocomplete="current-password"
                required
            >

            @if ($errors->any())
                <div class="error" role="alert">
                    {{ $errors->first() }}
                </div>
            @endif

            <button type="submit">Sign In</button>
        </form>

        <p class="footer">Authorised HM CARS staff only.</p>
    </main>
</body>
</html>
