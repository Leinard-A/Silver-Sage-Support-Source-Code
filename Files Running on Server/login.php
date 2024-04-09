<?php

$is_invalid = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    $mysqli = require __DIR__ . "/database.php";
    
    $sql = sprintf("SELECT * FROM user
                    WHERE email = '%s'",
                   $mysqli->real_escape_string($_POST["email"]));
    
    $result = $mysqli->query($sql);
    
    $user = $result->fetch_assoc();
    
    if ($user) {
        
        if (password_verify($_POST["password"], $user["password_hash"])) {
            
            session_start();
            
            session_regenerate_id();
            
            $_SESSION["user_id"] = $user["id"];
            
            header("Location: index.php");
            exit;
        }
    }
    
    $is_invalid = true;
}

?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Silver Sage Support - Login</title>
    <link rel="stylesheet" href="styles.css">
	    <style>
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 16px;
        }
    </style>
</head>
<body>
    <header>
        <h1>Silver Sage Support</h1>
    </header>

    <nav>
            <a href="index.html">Home</a>
            <a href="about.html">About</a>
            <a href="contact.html">Contact</a>
            <a href="login.html">Login</a>
            <a href="register.html">Register</a>
            <a href="virtual_chat_bot.html">Virtual Chat Bot</a>
    </nav>

    <div class="container2">
        <div class="content">
            <h2>Login</h2>
            <?php if ($is_invalid): ?>  
        <em>Invalid login</em>
    <?php endif; ?>
            <form id="loginForm" action="login.php" method="post">
                <div class="form-row">
                    <label for="email">Email:</label>
                    <input type="text" id="email" name="email" placeholder="Enter your email" required>
                    value="<?= htmlspecialchars($_POST["email"] ?? "") ?>">
                </div>

                <div class="form-row">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                </div>

                <div class="form-row">
                    <input type="submit" value="Login">
                </div>
            </form>
        </div>
    </div>

    <footer>
        <p>&copy; 2024 Silver Sage Support</p>
    </footer>
</body>
</html>

