<?php
// Session check
session_start();
if (!isset($_SESSION['authenticated'])) {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Planning Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-indigo-900 text-white">
    <div class="container mx-auto p-4 pt-6 md:p-6 lg:p-12 xl:p-24">
        <div class="flex justify-between mb-4">
            <h1 class="text-3xl font-bold">Welcome, <?php echo $_SESSION['username']; ?></h1>
            <button class="bg-indigo-800 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded" onclick="logout()">Logout</button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white bg-opacity-10 rounded-lg p-4 shadow-md">
                <h2 class="text-lg font-bold mb-2">Total Events</h2>
                <p id="total-events" class="text-3xl font-bold"></p>
            </div>
            <div class="bg-white bg-opacity-10 rounded-lg p-4 shadow-md">
                <h2 class="text-lg font-bold mb-2">Total Venues</h2>
                <p id="total-venues" class="text-3xl font-bold"></p>
            </div>
            <div class="bg-white bg-opacity-10 rounded-lg p-4 shadow-md">
                <h2 class="text-lg font-bold mb-2">Total Users</h2>
                <p id="total-users" class="text-3xl font-bold"></p>
            </div>
            <div class="bg-white bg-opacity-10 rounded-lg p-4 shadow-md">
                <h2 class="text-lg font-bold mb-2">Total Bookings</h2>
                <p id="total-bookings" class="text-3xl font-bold"></p>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mt-4">
            <div class="bg-white bg-opacity-10 rounded-lg p-4 shadow-md">
                <h2 class="text-lg font-bold mb-2">Manage Events</h2>
                <a href="events.php" class="bg-indigo-800 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">View All</a>
            </div>
            <div class="bg-white bg-opacity-10 rounded-lg p-4 shadow-md">
                <h2 class="text-lg font-bold mb-2">Manage Venues</h2>
                <a href="venues.php" class="bg-indigo-800 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">View All</a>
            </div>
            <div class="bg-white bg-opacity-10 rounded-lg p-4 shadow-md">
                <h2 class="text-lg font-bold mb-2">Manage Users</h2>
                <a href="users.php" class="bg-indigo-800 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">View All</a>
            </div>
            <div class="bg-white bg-opacity-10 rounded-lg p-4 shadow-md">
                <h2 class="text-lg font-bold mb-2">Manage Bookings</h2>
                <a href="bookings.php" class="bg-indigo-800 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">View All</a>
            </div>
        </div>
    </div>

    <script>
        function logout() {
            window.location.href = 'logout.php';
        }

        // Fetch stats dynamically via Javascript API calls
        fetch('api/stats.php')
            .then(response => response.json())
            .then(data => {
                document.getElementById('total-events').innerText = data.totalEvents;
                document.getElementById('total-venues').innerText = data.totalVenues;
                document.getElementById('total-users').innerText = data.totalUsers;
                document.getElementById('total-bookings').innerText = data.totalBookings;
            });
    </script>
</body>
</html>