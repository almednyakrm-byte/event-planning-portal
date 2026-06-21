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
    <title>Organizers</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body>
    <header class="bg-gray-900 py-4">
        <nav class="container mx-auto flex justify-between">
            <a href="index.php" class="text-lg text-white">Back to Index</a>
            <div class="flex items-center">
                <span class="text-lg text-white mr-4">Welcome, <?php echo $_SESSION['username']; ?></span>
                <a href="logout.php" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">Logout</a>
            </div>
        </nav>
    </header>
    <main class="container mx-auto p-4 pt-6 md:p-6 lg:p-12 xl:p-24">
        <h1 class="text-3xl text-gray-700 mb-4">Organizers</h1>
        <div class="flex justify-between mb-4">
            <button id="add-new-item" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">Add New Item</button>
            <input id="search-bar" type="text" placeholder="Search" class="py-2 pl-10 text-sm text-gray-700">
        </div>
        <table id="organizers-table" class="w-full table-auto">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2">Name</th>
                    <th class="px-4 py-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                <!-- Table content will be generated dynamically -->
            </tbody>
        </table>
    </main>

    <!-- Modal for adding new item -->
    <div id="add-new-item-modal" class="fixed top-0 left-0 w-full h-full bg-black bg-opacity-50 flex items-center justify-center hidden">
        <div class="bg-white p-4 rounded">
            <h2 class="text-lg text-gray-700 mb-4">Add New Organizer</h2>
            <form id="add-new-item-form">
                <input type="text" id="name" name="name" placeholder="Name" pattern="[A-Za-z\u0600-\u06FF0-9\s]+" required class="py-2 pl-10 text-sm text-gray-700 w-full">
                <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">Add</button>
            </form>
        </div>
    </div>

    <script>
        // Fetch organizers list from backend
        fetch('../backend/organizers.php')
            .then(response => response.json())
            .then(data => {
                const tableBody = document.getElementById('organizers-table').getElementsByTagName('tbody')[0];
                data.forEach(organizer => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td class="px-4 py-2">${organizer.name}</td>
                        <td class="px-4 py-2">
                            <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" onclick="editOrganizer(${organizer.id})">Edit</button>
                            <button class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded" onclick="deleteOrganizer(${organizer.id})">Delete</button>
                        </td>
                    `;
                    tableBody.appendChild(row);
                });
            });

        // Add new item
        document.getElementById('add-new-item').addEventListener('click', () => {
            document.getElementById('add-new-item-modal').classList.remove('hidden');
        });

        document.getElementById('add-new-item-form').addEventListener('submit', (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            fetch('../backend/organizers.php', {
                method: 'POST',
                body: formData,
            })
                .then(response => response.json())
                .then(data => {
                    const tableBody = document.getElementById('organizers-table').getElementsByTagName('tbody')[0];
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td class="px-4 py-2">${data.name}</td>
                        <td class="px-4 py-2">
                            <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" onclick="editOrganizer(${data.id})">Edit</button>
                            <button class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded" onclick="deleteOrganizer(${data.id})">Delete</button>
                        </td>
                    `;
                    tableBody.appendChild(row);
                    document.getElementById('add-new-item-modal').classList.add('hidden');
                    e.target.reset();
                });
        });

        // Edit organizer
        function editOrganizer(id) {
            fetch(`../backend/organizers.php?id=${id}`)
                .then(response => response.json())
                .then(data => {
                    const editModal = document.createElement('div');
                    editModal.innerHTML = `
                        <div class="fixed top-0 left-0 w-full h-full bg-black bg-opacity-50 flex items-center justify-center">
                            <div class="bg-white p-4 rounded">
                                <h2 class="text-lg text-gray-700 mb-4">Edit Organizer</h2>
                                <form id="edit-organizer-form">
                                    <input type="text" id="name" name="name" value="${data.name}" pattern="[A-Za-z\u0600-\u06FF0-9\s]+" required class="py-2 pl-10 text-sm text-gray-700 w-full">
                                    <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">Save</button>
                                </form>
                            </div>
                        </div>
                    `;
                    document.body.appendChild(editModal);
                    document.getElementById('edit-organizer-form').addEventListener('submit', (e) => {
                        e.preventDefault();
                        const formData = new FormData(e.target);
                        fetch(`../backend/organizers.php?id=${id}`, {
                            method: 'PUT',
                            body: formData,
                        })
                            .then(response => response.json())
                            .then(data => {
                                const tableBody = document.getElementById('organizers-table').getElementsByTagName('tbody')[0];
                                const rows = tableBody.getElementsByTagName('tr');
                                for (let i = 0; i < rows.length; i++) {
                                    if (rows[i].getElementsByTagName('button')[0].getAttribute('onclick') === `editOrganizer(${id})`) {
                                        rows[i].getElementsByTagName('td')[0].textContent = data.name;
                                        break;
                                    }
                                }
                                editModal.remove();
                                e.target.reset();
                            });
                    });
                });
        }

        // Delete organizer
        function deleteOrganizer(id) {
            fetch(`../backend/organizers.php?id=${id}`, {
                method: 'DELETE',
            })
                .then(response => response.json())
                .then(data => {
                    const tableBody = document.getElementById('organizers-table').getElementsByTagName('tbody')[0];
                    const rows = tableBody.getElementsByTagName('tr');
                    for (let i = 0; i < rows.length; i++) {
                        if (rows[i].getElementsByTagName('button')[0].getAttribute('onclick') === `editOrganizer(${id})`) {
                            rows[i].remove();
                            break;
                        }
                    }
                });
        }

        // Search bar
        document.getElementById('search-bar').addEventListener('input', () => {
            const searchValue = document.getElementById('search-bar').value.toLowerCase();
            const rows = document.getElementById('organizers-table').getElementsByTagName('tbody')[0].getElementsByTagName('tr');
            for (let i = 0; i < rows.length; i++) {
                const name = rows[i].getElementsByTagName('td')[0].textContent.toLowerCase();
                if (name.includes(searchValue)) {
                    rows[i].style.display = '';
                } else {
                    rows[i].style.display = 'none';
                }
            }
        });
    </script>
</body>
</html>