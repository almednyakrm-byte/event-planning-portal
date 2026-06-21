**list_bookings.php**

<?php
// Session validation
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bookings</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0,0,0);
            background-color: rgba(0,0,0,0.4);
        }
        
        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
        }
        
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <header class="bg-gray-800 py-4">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center">
                <a href="index.php" class="text-white hover:text-gray-300">Back to Index</a>
                <div class="flex items-center">
                    <p class="text-white mr-4">Welcome, <?= $_SESSION['username'] ?></p>
                    <button class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded" onclick="document.location='logout.php'">Logout</button>
                </div>
            </div>
        </div>
    </header>
    <main class="container mx-auto px-4 py-8">
        <h2 class="text-2xl font-bold mb-4">Bookings</h2>
        <div class="flex items-center mb-4">
            <input type="search" id="search" class="w-full py-2 pl-10 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-600" placeholder="Search...">
            <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" onclick="searchBookings()">Search</button>
        </div>
        <button class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded" onclick="openModal()">Add New Item</button>
        <table class="w-full mt-4">
            <thead>
                <tr>
                    <th class="px-4 py-2">ID</th>
                    <th class="px-4 py-2">Name</th>
                    <th class="px-4 py-2">Actions</th>
                </tr>
            </thead>
            <tbody id="bookings-table">
                <!-- Table rows will be populated here -->
            </tbody>
        </table>
    </main>
    <div id="modal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2 class="text-2xl font-bold mb-4">Add New Item</h2>
            <form id="add-item-form">
                <div class="mb-4">
                    <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Name:</label>
                    <input type="text" id="name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Name" pattern="[A-Za-z\u0600-\u06FF0-9\s]+">
                    <p id="name-error" class="text-red-500 text-xs italic"></p>
                </div>
                <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" type="submit">Add Item</button>
            </form>
        </div>
    </div>

    <script>
        // Get modal and form elements
        const modal = document.getElementById("modal");
        const closeModal = document.querySelector(".close");
        const addItemForm = document.getElementById("add-item-form");
        const nameInput = document.getElementById("name");
        const nameError = document.getElementById("name-error");

        // Open modal
        function openModal() {
            modal.style.display = "block";
        }

        // Close modal
        closeModal.addEventListener("click", function() {
            modal.style.display = "none";
        });

        // Add event listener to form submit
        addItemForm.addEventListener("submit", function(event) {
            event.preventDefault();
            const name = nameInput.value.trim();
            if (name === "") {
                nameError.textContent = "Name is required";
                return;
            }
            if (!name.match(pattern)) {
                nameError.textContent = "Invalid name format";
                return;
            }
            // Send AJAX request to add new item
            fetch('../backend/bookings.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ name })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Add new item to table
                    const newRow = document.createElement("tr");
                    newRow.innerHTML = `
                        <td>${data.id}</td>
                        <td>${data.name}</td>
                        <td>
                            <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" onclick="editItem(${data.id})">Edit</button>
                            <button class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded" onclick="deleteItem(${data.id})">Delete</button>
                        </td>
                    `;
                    document.getElementById("bookings-table").appendChild(newRow);
                    // Close modal
                    modal.style.display = "none";
                } else {
                    alert("Error adding new item");
                }
            })
            .catch(error => console.error(error));
        });

        // Search bookings
        function searchBookings() {
            const searchQuery = document.getElementById("search").value.trim();
            fetch('../backend/bookings.php', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json'
                },
                params: {
                    search: searchQuery
                }
            })
            .then(response => response.json())
            .then(data => {
                // Clear table
                document.getElementById("bookings-table").innerHTML = "";
                // Add new items to table
                data.forEach(item => {
                    const newRow = document.createElement("tr");
                    newRow.innerHTML = `
                        <td>${item.id}</td>
                        <td>${item.name}</td>
                        <td>
                            <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" onclick="editItem(${item.id})">Edit</button>
                            <button class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded" onclick="deleteItem(${item.id})">Delete</button>
                        </td>
                    `;
                    document.getElementById("bookings-table").appendChild(newRow);
                });
            })
            .catch(error => console.error(error));
        }

        // Edit item
        function editItem(id) {
            // Send AJAX request to get item details
            fetch('../backend/bookings.php', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json'
                },
                params: {
                    id
                }
            })
            .then(response => response.json())
            .then(data => {
                // Update item details in modal
                const nameInput = document.getElementById("name");
                nameInput.value = data.name;
                // Send AJAX request to update item
                fetch('../backend/bookings.php', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ id, name: nameInput.value })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update item in table
                        const rows = document.getElementById("bookings-table").rows;
                        for (let i = 0; i < rows.length; i++) {
                            if (rows[i].cells[0].textContent === id.toString()) {
                                rows[i].cells[1].textContent = data.name;
                                break;
                            }
                        }
                    } else {
                        alert("Error updating item");
                    }
                })
                .catch(error => console.error(error));
            })
            .catch(error => console.error(error));
        }

        // Delete item
        function deleteItem(id) {
            // Send AJAX request to delete item
            fetch('../backend/bookings.php', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ id })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove item