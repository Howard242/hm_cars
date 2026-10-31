
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Staff | HM CARS</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            padding: 30px 16px;
            font-family: Arial, sans-serif;
            background: #f3f5f9;
            color: #172033;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 14px;
            box-shadow: 0 8px 30px #17203312;
        }
        h1 { margin-top: 0; }
        .subtitle { color: #667085; margin-bottom: 25px; }
        label {
            display: block;
            margin: 18px 0 7px;
            font-weight: bold;
        }
        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #d0d5dd;
            border-radius: 7px;
            font-size: 15px;
        }
        button, .back {
            display: inline-block;
            border: 0;
            border-radius: 7px;
            padding: 12px 16px;
            font-size: 15px;
            text-decoration: none;
            cursor: pointer;
        }
        button { background: #10233f; color: white; margin-top: 24px; }
        .back { background: #eaecf0; color: #172033; margin-top: 24px; }
        .error { color: #b42318; font-size: 14px; margin-top: 6px; }
        .alert {
            background: #fef3f2;
            color: #b42318;
            padding: 12px;
            border-radius: 7px;
        }
        .hint { color: #667085; font-size: 13px; margin-top: 6px; }
    </style>
</head>
<body>
<div class="container">
    <h1>Add Staff Member</h1>
    <p class="subtitle">Create an authorized HM CARS staff account.</p>

    @if ($errors->any())
        <div class="alert">Please correct the errors below.</div>
    @endif

    <form method="POST" action="{{ route('admin.staff.store') }}">
        @csrf

        <label for="name">Full name</label>
        <input id="name" name="name" type="text"
               value="{{ old('name') }}" required autocomplete="name">
        @error('name') <div class="error">{{ $message }}</div> @enderror

        <label for="email">Email address</label>
        <input id="email" name="email" type="email"
               value="{{ old('email') }}" required autocomplete="email">
        @error('email') <div class="error">{{ $message }}</div> @enderror

        <label for="password">Temporary password</label>
        <input id="password" name="password" type="password"
               required autocomplete="new-password">
        <p class="hint">At least 12 characters, with uppercase and lowercase letters, a number and a symbol.</p>
        @error('password') <div class="error">{{ $message }}</div> @enderror

        <label for="password_confirmation">Confirm password</label>
        <input id="password_confirmation" name="password_confirmation"
               type="password" required autocomplete="new-password">

        <button type="submit">Create Staff Account</button>
        <a class="back" href="{{ route('admin.staff.index') }}">Cancel</a>
    </form>
</div>
</body>
</html>
