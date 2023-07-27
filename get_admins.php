<!DOCTYPE html>
<html>
<head>
    <title>Admin Usernames</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }

        th, td {
            text-align: left;
            padding: 8px;
        }

        th {
            background-color: #f2f2f2;
        }

        .button-container {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .copy-button {
            padding: 6px 12px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
    </style>
<script>
    function copyUsernames(usernames) {
        const tempElement = document.createElement('textarea');
        const modifiedUsernames = usernames.replace(/@@/g, "@").replace(/(Admin|Creator)[^,@]+/g, "").replace(/,+/g, "\n").trim();
        tempElement.value = modifiedUsernames;
        document.body.appendChild(tempElement);
        tempElement.select();
        document.execCommand('copy');
        document.body.removeChild(tempElement);
    }
</script>
</head>
<body>
    <h1>Admin Usernames</h1>

    <?php
    // Get the channel usernames from the form data
    $channel_usernames = explode("\n", $_POST['channel_username']);
    $channel_usernames = array_map('trim', $channel_usernames);

    // Remove empty lines and any duplicated channel usernames
    $channel_usernames = array_values(array_unique(array_filter($channel_usernames)));

    // Execute the Python script for each channel username and retrieve the output
    $adminOutput = "";
    $ownerData = array(); // Array to store owner data
    $errorOutput = "";

    // Process each channel username
    foreach ($channel_usernames as $channel_username) {
        $command = "python3 script.py" . escapeshellarg($channel_username);
        $admins = shell_exec($command);

        // If admin data is not empty
        if (!empty($admins)) {
            $adminsArray = explode("\n", $admins);

            foreach ($adminsArray as $admin) {
                $adminData = explode(",", $admin);
                if (count($adminData) >= 3) {
                    $username = "@" . $adminData[0];
                    $status = $adminData[1];
                    $groupLink = $adminData[2];

                    if ($status === "Creator") {
                        // Store owner data in the array
                        $ownerData[] = array(
                            'username' => $username,
                            'groupLink' => $groupLink
                        );
                    }

                    $adminOutput .= "<tr><td>$username</td><td>$status</td><td><a href='$groupLink'>$groupLink</a></td></tr>";
                }
            }
        } else {
            $errorOutput .= "<tr><td>$channel_username</td><td colspan='2'>Cannot fetch members. Invalid channel username or error occurred.</td></tr>";
        }
    }

    // Display the owner table
    echo "<div class='button-container'>";
    if (!empty($ownerData)) {
        $ownerUsernames = implode(",", array_column($ownerData, 'username'));
        echo "<button class='copy-button' onclick=\"copyUsernames('$ownerUsernames')\">Copy All Owner Usernames</button>";
    }
    
    if (!empty($adminOutput)) {
        $adminUsernames = [];
        $adminRows = explode("<tr>", $adminOutput);
        foreach ($adminRows as $adminRow) {
            if (strpos($adminRow, "<td>") !== false) {
                $username = "@" . strip_tags(strstr($adminRow, "<td>"));
                $adminUsernames[] = $username;
            }
        }
        $adminUsernames = implode(",", $adminUsernames);
        echo "<button class='copy-button' onclick=\"copyUsernames('$adminUsernames')\">Copy All Admin Usernames</button>";
    }
    echo "</div>";

    echo "<h2>All Owners</h2>";
    if (!empty($ownerData)) {
        echo "<table>";
        echo "<tr><th>Owner Username</th><th>Group Link</th></tr>";

        foreach ($ownerData as $owner) {
            $ownerUsername = $owner['username'];
            $ownerGroupLink = $owner['groupLink'];

            echo "<tr><td>$ownerUsername</td><td><a href='$ownerGroupLink'>$ownerGroupLink</a></td></tr>";
        }

        echo "</table>";
    } else {
        echo "<p>No owners found.</p>";
    }

    echo "<h2>All Admins</h2>";
    if (!empty($adminOutput)) {
        echo "<table>";
        echo "<tr><th>Username</th><th>Status</th><th>Group Link</th></tr>";
        echo $adminOutput;
        echo "</table>";
    } else {
        echo "<p>No admin usernames found.</p>";
    }

    echo "<h2>Errored Channels</h2>";
    if (!empty($errorOutput)) {
        echo "<table>";
        echo "<tr><th>Channel Username</th><th>Reason</th></tr>";
        echo $errorOutput;
        echo "</table>";
    } else {
        echo "<p>No errored channels.</p>";
    }
    ?>
</body>
</html>
