<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Owner</title>
    <style>
        .popup {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: #fff;
            padding: 20px;
            border: 1px solid #ccc;
        }

        .popup-close {
            float: right;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <button id="addOwnerButton">Add New Owner</button>

    <div id="popup" class="popup">
        <span class="popup-close">&times;</span>
        <h2>Add New Owner</h2>
        <form id="addNewOwnerForm" action="process_owner.php" method="post">
            <label for="owner_name">Name:</label>
            <input type="text" id="owner_name" name="owner_name" required><br>
            <label for="owner_phone">Phone Number:</label>
            <input type="text" id="owner_phone" name="owner_phone" required><br>
            <label for="owner_email">Email:</label>
            <input type="email" id="owner_email" name="owner_email" required><br>
            <label for="owner_address">Address:</label>
            <textarea id="owner_address" name="owner_address" required></textarea><br>
            <label for="property_id">Property ID:</label>
            <input type="text" id="property_id" name="property_id" required><br>
            <input type="submit" value="Save Changes">
        </form>
    </div>

    <script>
        const addOwnerButton = document.getElementById('addOwnerButton');
        const popup = document.getElementById('popup');
        const closeButton = document.querySelector('.popup-close');

        addOwnerButton.addEventListener('click', () => {
            popup.style.display = 'block';
        });

        closeButton.addEventListener('click', () => {
            popup.style.display = 'none';
        });
    </script>
</body>
</html>