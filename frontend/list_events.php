<?php
// Session validation
session_start();
if (!isset($_SESSION['authenticated'])) {
    header('Location: login.php');
    exit;
}

// Current user info
$current_user = $_SESSION['user'];

// Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events Module</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body>
    <header class="bg-gray-900 py-4">
        <nav class="container mx-auto flex justify-between">
            <a href="index.php" class="text-lg text-white">Back to Index</a>
            <span class="text-lg text-white">Welcome, <?= $current_user ?></span>
            <a href="?logout" class="text-lg text-white">Logout</a>
        </nav>
    </header>
    <main class="container mx-auto p-4 pt-6 mt-10">
        <h1 class="text-3xl text-gray-700 mb-4">Events List</h1>
        <div class="flex justify-between mb-4">
            <button id="add-new-item" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">Add New Item</button>
            <input id="search-bar" type="text" placeholder="Search..." class="py-2 pl-10 text-sm text-gray-700">
        </div>
        <table id="events-table" class="w-full table-auto">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2">ID</th>
                    <th class="px-4 py-2">Name</th>
                    <th class="px-4 py-2">Actions</th>
                </tr>
            </thead>
            <tbody id="events-tbody">
                <!-- Table data will be populated here -->
            </tbody>
        </table>
    </main>

    <!-- Modal for adding new item -->
    <div id="add-new-item-modal" class="fixed top-0 left-0 w-full h-full bg-black bg-opacity-50 flex items-center justify-center hidden">
        <div class="bg-white p-4 rounded shadow-md">
            <h2 class="text-lg text-gray-700 mb-4">Add New Item</h2>
            <form id="add-new-item-form">
                <input type="text" id="name" name="name" placeholder="Name" pattern="[A-Za-z\u0600-\u06FF0-9\s]+" required class="py-2 pl-10 text-sm text-gray-700 w-full">
                <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">Add</button>
            </form>
        </div>
    </div>

    <script>
        // Fetch events data from backend
        fetch('../backend/events.php')
            .then(response => response.json())
            .then(data => {
                const eventsTableBody = document.getElementById('events-tbody');
                data.forEach(event => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td class="px-4 py-2">${event.id}</td>
                        <td class="px-4 py-2">${event.name}</td>
                        <td class="px-4 py-2">
                            <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" onclick="editEvent(${event.id})">Edit</button>
                            <button class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded" onclick="deleteEvent(${event.id})">Delete</button>
                        </td>
                    `;
                    eventsTableBody.appendChild(row);
                });
            });

        // Add new item
        document.getElementById('add-new-item').addEventListener('click', () => {
            document.getElementById('add-new-item-modal').classList.remove('hidden');
        });

        document.getElementById('add-new-item-form').addEventListener('submit', (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            fetch('../backend/events.php', {
                method: 'POST',
                body: formData,
            })
                .then(response => response.json())
                .then(data => {
                    console.log(data);
                    location.reload();
                });
        });

        // Edit event
        function editEvent(id) {
            fetch(`../backend/events.php?id=${id}`)
                .then(response => response.json())
                .then(data => {
                    const editModal = document.createElement('div');
                    editModal.innerHTML = `
                        <div class="fixed top-0 left-0 w-full h-full bg-black bg-opacity-50 flex items-center justify-center">
                            <div class="bg-white p-4 rounded shadow-md">
                                <h2 class="text-lg text-gray-700 mb-4">Edit Event</h2>
                                <form id="edit-event-form">
                                    <input type="text" id="name" name="name" value="${data.name}" pattern="[A-Za-z\u0600-\u06FF0-9\s]+" required class="py-2 pl-10 text-sm text-gray-700 w-full">
                                    <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">Save</button>
                                </form>
                            </div>
                        </div>
                    `;
                    document.body.appendChild(editModal);

                    document.getElementById('edit-event-form').addEventListener('submit', (e) => {
                        e.preventDefault();
                        const formData = new FormData(e.target);
                        fetch(`../backend/events.php?id=${id}`, {
                            method: 'PUT',
                            body: formData,
                        })
                            .then(response => response.json())
                            .then(data => {
                                console.log(data);
                                location.reload();
                            });
                    });
                });
        }

        // Delete event
        function deleteEvent(id) {
            fetch(`../backend/events.php?id=${id}`, {
                method: 'DELETE',
            })
                .then(response => response.json())
                .then(data => {
                    console.log(data);
                    location.reload();
                });
        }

        // Search bar
        document.getElementById('search-bar').addEventListener('input', () => {
            const searchQuery = document.getElementById('search-bar').value.toLowerCase();
            const rows = document.getElementById('events-tbody').rows;
            for (let i = 0; i < rows.length; i++) {
                const row = rows[i];
                const nameCell = row.cells[1].textContent.toLowerCase();
                if (nameCell.includes(searchQuery)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            }
        });
    </script>
</body>
</html>