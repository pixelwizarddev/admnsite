<!DOCTYPE html>
<html>
<head>
    <title>Admin Usernames</title>
    <style>
        textarea {
            min-height: 100px; /* Set the minimum height for the input box */
            resize: vertical; /* Allow vertical resizing of the input box */
        }
    </style>
</head>
<body>
    <h1>Admin Usernames</h1>

    <form method="post" action="get_admins.php">
        <label for="channel_username">Channel Usernames (One per line):</label>
        <br>
        <textarea id="channel_username" name="channel_username" required></textarea>
        <br>
        <input type="submit" value="Get Admins">
    </form>
</body>
</html>
