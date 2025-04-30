<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Collaborative Assignment</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h1>Welcome to Collaborative Assignment</h1>
        <?php
        require_once "config/db_config.php";

        // Test database connection
        if($conn) {
            echo "<p class='success'>Successfully connected to the database!</p>";
        }
        ?>
    </div>
</body>
</html>