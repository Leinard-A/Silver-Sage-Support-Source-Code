<?php

$is_invalid = false;
$invalid_email = false;
$invalid_password = false;
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    $mysqli = require __DIR__ . "/database.php";
    //Retrieve the data from the database to validate user
    $sqlRequest_Password = "SELECT password FROM users
                            WHERE email = '{$_POST['email']}'
    ";
    
    $sqlRequest_Email =  "SELECT email FROM users
                            WHERE email = '{$_POST['email']}'
    ";
    $resultEmail = mysqli_query($mysqli, $sqlRequest_Email);
    //Verify the email
    if (mysqli_num_rows($resultEmail) == 0){
        $invalid_email = true;
    }
    else{//If email is valid verify password using email to get the hashed password.
        $resultPassword = mysqli_query($mysqli, $sqlRequest_Password); 
        $arrayPassword = mysqli_fetch_assoc($resultPassword);
        $hashedPassword = $arrayPassword["password"];
        if (!password_verify($_POST['password'],$hashedPassword)){
            $invalid_password = true;
        }
        mysqli_free_result($resultPassword);
    }
    //If all is fine then redirect the user to the user profile page.
    if (mysqli_num_fields($resultEmail) > 0 && 
        password_verify($_POST['password'],$hashedPassword)) {
        $sqlCheckSession = "SELECT * FROM userSessions 
                            WHERE timeOfSession >= DATE_SUB(NOW(), INTERVAL 1 DAY)
                            AND email = '{$_POST['email']}'";
        $sqlSession = mysqli_query($mysqli, $sqlCheckSession);
        if (mysqli_num_fields($sqlSession) > 0){
            $result = mysqli_fetch_assoc($sqlSession);
            $sessionHash = $result["sessionHash"];
            mysqli_free_result($sqlSession);
        }
        else{
            //Get rid of the old session entry
            $sqlCheckSession = "SELECT * FROM userSessions 
                                WHERE timeOfSession <= DATE_SUB(NOW(), INTERVAL 1 DAY)
                                AND email = '{$_POST['email']}'";
            $sqlSession = mysqli_query($mysqli, $sqlCheckSession);
            if (mysqli_num_rows($sqlSession) > 0){
                $removePrevSession = "DELETE FROM userSession
                                    WHERE timeOfSession <= DATE_SUB(NOW(), INTERVAL 1 DAY) 
                                     AND email = '{$_POST['email']}'";
                mysqli_query($mysqli, $removePrevSession);   
            }
            mysqli_free_result($sqlSession);
            //Create a new session entry
            $timeOfSession = date('Y-m-d H:i:s');
            $sessionValue = ($timeOfSession.$_POST['email']);
            $sessionHash = hash('sha256',$sessionValue);  
            $sqlStmt = "INSERT INTO userSessions(email, sessionHash, timeOfSession)
                        VALUES ('{$_POST['email']}', '{$sessionHash}', '{$timeOfSession}')
            ";
            $resultStmt = mysqli_query($mysqli, $sqlStmt);
        }
        header("Location: userPage.html?sessionID={$sessionHash}");  
    } 

    
    $is_invalid = true;
    mysqli_free_result($resultEmail);
    mysqli_close($mysqli);
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
                    <input type="email" id="email" name="email" placeholder="Enter your email" required>
                    <?php if ($invalid_email): ?>
                        <label>Invalid Email</label>
                     <?php endif; ?>
                </div>

                <div class="form-row">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                    <?php if ($invalid_password): ?>
                        <label>Invalid Password</label>
                    <?php endif; ?>
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