<?php
// Session validation
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
    <title>Users Management</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body>
    <header class="bg-blue-500 text-white p-4 flex justify-between">
        <a href="index.php" class="text-lg font-bold">Back to Index</a>
        <div class="flex items-center">
            <span class="mr-4">Welcome, <?php echo $_SESSION['username']; ?></span>
            <a href="logout.php" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">Logout</a>
        </div>
    </header>
    <main class="p-4">
        <h1 class="text-3xl font-bold mb-4">Users List</h1>
        <div class="flex justify-between mb-4">
            <button id="add-new-item" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">Add New Item</button>
            <input id="search-bar" type="text" placeholder="Search..." class="py-2 pl-10 text-sm text-gray-700">
        </div>
        <table id="users-table" class="w-full table-auto">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2">ID</th>
                    <th class="px-4 py-2">Name</th>
                    <th class="px-4 py-2">Email</th>
                    <th class="px-4 py-2">Actions</th>
                </tr>
            </thead>
            <tbody id="table-body">
                <!-- Table data will be populated here -->
            </tbody>
        </table>
    </main>

    <!-- Modal for adding new item -->
    <div id="modal" class="fixed top-0 left-0 w-full h-full bg-black bg-opacity-50 flex items-center justify-center hidden">
        <div class="bg-white p-4 rounded">
            <h2 class="text-lg font-bold mb-4">Add New User</h2>
            <form id="add-form">
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                    <input id="name" type="text" pattern="[A-Za-z\u0600-\u06FF0-9\s]+" required class="mt-1 block w-full py-2 pl-10 text-sm text-gray-700">
                </div>
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input id="email" type="email" required class="mt-1 block w-full py-2 pl-10 text-sm text-gray-700">
                </div>
                <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">Add</button>
            </form>
        </div>
    </div>

    <script>
        // Fetch API to get users list
        fetch('../backend/users.php')
            .then(response => response.json())
            .then(data => {
                const tableBody = document.getElementById('table-body');
                data.forEach(user => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${user.id}</td>
                        <td>${user.name}</td>
                        <td>${user.email}</td>
                        <td>
                            <button class="edit-btn" data-id="${user.id}">Edit</button>
                            <button class="delete-btn" data-id="${user.id}">Delete</button>
                        </td>
                    `;
                    tableBody.appendChild(row);
                });
            });

        // Add new item button click event
        document.getElementById('add-new-item').addEventListener('click', () => {
            document.getElementById('modal').classList.remove('hidden');
        });

        // Add new item form submit event
        document.getElementById('add-form').addEventListener('submit', (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            fetch('../backend/users.php', {
                method: 'POST',
                body: formData,
            })
                .then(response => response.json())
                .then(data => {
                    console.log(data);
                    document.getElementById('modal').classList.add('hidden');
                    // Refresh table data
                    fetch('../backend/users.php')
                        .then(response => response.json())
                        .then(data => {
                            const tableBody = document.getElementById('table-body');
                            tableBody.innerHTML = '';
                            data.forEach(user => {
                                const row = document.createElement('tr');
                                row.innerHTML = `
                                    <td>${user.id}</td>
                                    <td>${user.name}</td>
                                    <td>${user.email}</td>
                                    <td>
                                        <button class="edit-btn" data-id="${user.id}">Edit</button>
                                        <button class="delete-btn" data-id="${user.id}">Delete</button>
                                    </td>
                                `;
                                tableBody.appendChild(row);
                            });
                        });
                });
        });

        // Edit button click event
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('edit-btn')) {
                const id = e.target.dataset.id;
                fetch(`../backend/users.php?id=${id}`, {
                    method: 'GET',
                })
                    .then(response => response.json())
                    .then(data => {
                        const modal = document.getElementById('modal');
                        modal.classList.remove('hidden');
                        const form = document.getElementById('add-form');
                        form.innerHTML = `
                            <div class="mb-4">
                                <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                                <input id="name" type="text" pattern="[A-Za-z\u0600-\u06FF0-9\s]+" value="${data.name}" required class="mt-1 block w-full py-2 pl-10 text-sm text-gray-700">
                            </div>
                            <div class="mb-4">
                                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                                <input id="email" type="email" value="${data.email}" required class="mt-1 block w-full py-2 pl-10 text-sm text-gray-700">
                            </div>
                            <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">Update</button>
                        `;
                        form.addEventListener('submit', (e) => {
                            e.preventDefault();
                            const formData = new FormData(e.target);
                            fetch(`../backend/users.php?id=${id}`, {
                                method: 'PUT',
                                body: formData,
                            })
                                .then(response => response.json())
                                .then(data => {
                                    console.log(data);
                                    modal.classList.add('hidden');
                                    // Refresh table data
                                    fetch('../backend/users.php')
                                        .then(response => response.json())
                                        .then(data => {
                                            const tableBody = document.getElementById('table-body');
                                            tableBody.innerHTML = '';
                                            data.forEach(user => {
                                                const row = document.createElement('tr');
                                                row.innerHTML = `
                                                    <td>${user.id}</td>
                                                    <td>${user.name}</td>
                                                    <td>${user.email}</td>
                                                    <td>
                                                        <button class="edit-btn" data-id="${user.id}">Edit</button>
                                                        <button class="delete-btn" data-id="${user.id}">Delete</button>
                                                    </td>
                                                `;
                                                tableBody.appendChild(row);
                                            });
                                        });
                                });
                        });
                    });
            }
        });

        // Delete button click event
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('delete-btn')) {
                const id = e.target.dataset.id;
                fetch(`../backend/users.php?id=${id}`, {
                    method: 'DELETE',
                })
                    .then(response => response.json())
                    .then(data => {
                        console.log(data);
                        // Refresh table data
                        fetch('../backend/users.php')
                            .then(response => response.json())
                            .then(data => {
                                const tableBody = document.getElementById('table-body');
                                tableBody.innerHTML = '';
                                data.forEach(user => {
                                    const row = document.createElement('tr');
                                    row.innerHTML = `
                                        <td>${user.id}</td>
                                        <td>${user.name}</td>
                                        <td>${user.email}</td>
                                        <td>
                                            <button class="edit-btn" data-id="${user.id}">Edit</button>
                                            <button class="delete-btn" data-id="${user.id}">Delete</button>
                                        </td>
                                    `;
                                    tableBody.appendChild(row);
                                });
                            });
                    });
            }
        });

        // Search bar input event
        document.getElementById('search-bar').addEventListener('input', (e) => {
            const searchQuery = e.target.value.toLowerCase();
            const tableRows = document.querySelectorAll('#users-table tbody tr');
            tableRows.forEach((row) => {
                const rowText = row.textContent.toLowerCase();
                if (rowText.includes(searchQuery)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>