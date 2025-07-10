<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Google Registration</title>

    <!-- Load the Google Identity Services library -->
    <script src="https://accounts.google.com/gsi/client" async defer></script>
</head>
<body>
<h2>Register or Log in with Google</h2>

<!-- Google Sign-In Button -->
<div id="g_id_onload"
     data-client_id="{{ env('GOOGLE_CLIENT_ID') }}"
     data-callback="onSignIn"
     data-auto_prompt="false">
</div>
<div class="g_id_signin" data-type="standard" data-theme="dark"></div>

<script>
    // Callback function when the user signs in with Google
    function onSignIn(response) {
        const id_token = response.credential; // Get the ID token from the response

        // Send the Google ID token to your Laravel backend for verification and registration
        registerByGoogle(id_token);
    }

    function registerByGoogle(token) {
        fetch('{{ route("chef.register.by-google") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}' // Add CSRF token for Laravel security
            },
            body: JSON.stringify({ token: token })
        })
            .then(response => response.json())
            .then(data => {
                console.log(data);
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Something went wrong!');
            });
    }
</script>
</body>
</html>