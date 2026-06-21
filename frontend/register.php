<?php
// Session start
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="h-screen bg-gray-100 flex justify-center items-center">
    <div class="bg-white p-10 rounded shadow-md w-80">
        <h2 class="text-2xl font-bold mb-4">Register</h2>
        <form id="register-form">
            <div class="mb-4">
                <label for="username" class="block text-gray-700 text-sm font-bold mb-2">Username</label>
                <input type="text" id="username" name="username" pattern="[A-Za-z\u0600-\u06FF0-9\s]+" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                <span class="text-red-500 text-xs" id="username-error"></span>
            </div>
            <div class="mb-4">
                <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                <input type="email" id="email" name="email" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                <span class="text-red-500 text-xs" id="email-error"></span>
            </div>
            <div class="mb-4">
                <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                <input type="password" id="password" name="password" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                <span class="text-red-500 text-xs" id="password-error"></span>
            </div>
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Register</button>
        </form>
        <div id="register-message" class="mt-4"></div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#register-form').submit(function(e) {
                e.preventDefault();
                var username = $('#username').val();
                var email = $('#email').val();
                var password = $('#password').val();

                if (username === '' || email === '' || password === '') {
                    $('#register-message').html('<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">Please fill in all fields.</div>');
                    return;
                }

                if (!username.match(/[A-Za-z\u0600-\u06FF0-9\s]+/)) {
                    $('#username-error').html('Invalid username. Only letters, numbers and spaces are allowed.');
                    return;
                } else {
                    $('#username-error').html('');
                }

                $.ajax({
                    type: 'POST',
                    url: '../backend/auth.php?action=register',
                    data: {username: username, email: email, password: password},
                    success: function(response) {
                        if (response === 'success') {
                            $('#register-message').html('<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">Registration successful. You will be redirected to the login page.</div>');
                            setTimeout(function() {
                                window.location.href = 'login.php';
                            }, 2000);
                        } else {
                            $('#register-message').html('<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">' + response + '</div>');
                        }
                    }
                });
            });
        });
    </script>
</body>
</html>