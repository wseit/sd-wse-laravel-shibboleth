<!DOCTYPE html>
<html>
<head>
    <title>Emulated IdP Login</title>
    <style type="text/css">
        body {
            font-family: sans-serif;
        }

        form {
            color: grey;
        }

        div.login-box {
            margin: 10px auto;
            width: 100%;
            border: 1px solid grey;
            border-radius: 5px;
            padding: 10px;
            max-width: 400px;
            min-width: 300px;
        }

        .title {
            text-align: center;
            font-weight: 200;
            color: grey;
        }

        input[type="text"],
        input[type="password"],
        select {
            width: 100%;
            padding: 5px;
            border-radius: 5px;
            border: 1px solid #cdcdcd;
        }

        input[type="submit"] {
            padding: 10px;
            border: 1px solid #cdcdcd;
            border-radius: 5px;
            background-color: #fff;
            min-width: 100%;
        }

        input[type="submit"]:hover {
            background-color: #cdcdcd;
            cursor: pointer;
        }
    </style>
</head>
<body>
<div class="login-box">
    <h2 class="title">Login to Continue</h2>
    <form action="" method="post">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <strong>{{ $error ?? 'Please login below.' }}</strong>
        <p>
            <select id="user-select">
                <option value="" disabled selected>Select One</option>
                @foreach(config('shibboleth.emulate_idp_users') as $username => $info)
                    <option value="{{ $username }}">{{ $username }} - {{ $info['displayName'] }}</option>
                @endforeach
            </select>
            <input type="hidden" name="username" id="username" />
            <input type="hidden" name="password" id="password" />
        </p>
        <p>
            <input type="submit" value="Login">
        </p>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const select = document.getElementById('user-select');
        const usernameInput = document.getElementById('username');
        const passwordInput = document.getElementById('password');

        select.addEventListener('change', function () {
            const selectedValue = select.value;
            usernameInput.value = selectedValue;
            passwordInput.value = selectedValue;
        });
    });
</script>
</body>
</html>
