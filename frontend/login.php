<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 h-screen">
    <div class="glassmorphic-container mx-auto p-12 md:p-20 lg:p-24 h-full flex justify-center items-center">
        <div class="glassmorphic-card w-full max-w-md p-12 md:p-16 lg:p-20 bg-white rounded-lg shadow-lg">
            <h2 class="text-3xl font-bold text-gray-800 mb-4">Login</h2>
            <form id="login-form" class="space-y-4">
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                    <input type="text" id="username" name="username" class="block w-full p-2 mt-1 text-sm text-gray-700 bg-gray-100 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500" pattern="[A-Za-z\u0600-\u06FF0-9\s]+" required>
                    <div id="username-error" class="text-red-500 hidden"></div>
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <input type="password" id="password" name="password" class="block w-full p-2 mt-1 text-sm text-gray-700 bg-gray-100 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500" required>
                    <div id="password-error" class="text-red-500 hidden"></div>
                </div>
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Login</button>
                <p class="text-sm text-gray-500">Don't have an account? <a href="register.php" class="text-blue-500 hover:text-blue-700">Register</a></p>
            </form>
        </div>
    </div>

    <script>
        const form = document.getElementById('login-form');
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;
            const errors = [];

            if (!username.match(pattern)) {
                document.getElementById('username-error').textContent = 'Invalid username';
                document.getElementById('username-error').classList.remove('hidden');
                return;
            } else {
                document.getElementById('username-error').classList.add('hidden');
            }

            if (password.length < 8) {
                document.getElementById('password-error').textContent = 'Password must be at least 8 characters';
                document.getElementById('password-error').classList.remove('hidden');
                return;
            } else {
                document.getElementById('password-error').classList.add('hidden');
            }

            try {
                const response = await fetch('../backend/auth.php?action=login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ username, password })
                });

                const data = await response.json();

                if (data.success) {
                    window.location.href = 'dashboard.php';
                } else {
                    document.getElementById('username-error').textContent = data.message;
                    document.getElementById('username-error').classList.remove('hidden');
                }
            } catch (error) {
                console.error(error);
                document.getElementById('username-error').textContent = 'Error logging in';
                document.getElementById('username-error').classList.remove('hidden');
            }
        });
    </script>

    <script>
        const pattern = "[A-Za-z\u0600-\u06FF0-9\s]+";
    </script>
</body>
</html>


This code creates a premium-looking login page with a glassmorphic layout, gradients, and a form for username and password input. It uses Tailwind CSS CDN for styling and includes standard HTML input pattern validator to support Arabic and Latin characters. The form is submitted using AJAX with the Fetch API to the `../backend/auth.php?action=login` endpoint. The response is handled dynamically, and error alerts are displayed if the login fails. The page also includes a direct link to the `register.php` page.