<?php
$errors = array();

//Validation of parameters
if (empty($_POST["fullname"]) OR empty($_POST["email"]) OR empty($_POST["password"]) OR empty($_POST["username"]) or empty ($_POST["nhsNumber"])) {
    array_push($errors,"All fields are required");
    $allFields = false;
   }

if (empty($_POST["fullname"])) {
    array_push($errors,"Enter your name");
    $missingName = false;
}
if (empty($_POST["nhsNumber"])) {
    array_push($errors,"Enter your NHS number");
    $nhsNumber = false;
}
if (!filter_var($_POST["email"],FILTER_VALIDATE_EMAIL)){
    array_push($errors, "Please enter vaild email.");
    $invalidEmail = true;
}
if (iconv_strlen($_POST["password"])<8)  {
    array_push($errors, "Password requires atleast 8 characters one of which must be a number.");
    $passwordLength = false;
}
if(!preg_match("/[A-Za-z]/",$_POST["password"])){
    array_push($errors, "Password must contain atleast 1 letter");
    $containLetter = false;

}
if(!preg_match("/[0-9]/",$_POST["password"])){
    array_push($errors, "must containt 1 number");
    $containsNumber = false;
}
if(!isset($_POST['dateOfBirth']) ){
    array_push($errors, "Enter your date of birth");
    $DOB = false;
}



//Not sure if this is neccessary.
if(isset($_POST['gender']) ){
    $verGender=$_POST['gender'];
        
}

//Hashing Password
$password_hash = password_hash($_POST["password"],PASSWORD_DEFAULT);

$mysqli = require __DIR__. "/database.php";
//Checks if password already exists in database
$sqlStmt = "SELECT * FROM users 
            WHERE password = '{$password_hash}' ";

$result = mysqli_query($mysqli,$sqlStmt);
if (mysqli_fetch_assoc($result) != null) {
    array_push($errors,"Password already exists!");
    $passwordExists = true;

}
//Checks if the email is already in use
$sqlStmt = "SELECT * FROM users
            WHERE email = '{$_POST['email']}'";

$result =  mysqli_query($mysqli,$sqlStmt);
if (mysqli_fetch_assoc($result) != null ){
    array_push($errors, "Emails already in use");
    $emailExists = true;
}
//Checks if the username is already in use
$sqlStmt = "SELECT * FROM users
            WHERE username = '{$_POST['username']}'";

$result = mysqli_query($mysqli,$sqlStmt);
if (mysqli_fetch_assoc($result) != null ){
    array_push($errors, "Username already in use");
    $usernameExists = true;
}


//Inputs data into the database. 
if (count($errors)>0){
    $registrationFail = true;
    mysqli_free_result($result);
    mysqli_close($mysqli);
}
else if (!empty($_POST["insuranceProvider"]) && 
    (!empty($_POST["insuranceNumber"])) ){ 
        $mysql="INSERT INTO users (fullname, email, username, 
                                    password, dateOfBirth, gender, 
                                    nhsNumber,  insuranceProvider, 
                                    insuranceNumber)
        VALUE('{$_POST["fullname"]}',  
        '{$_POST["email"]}',
        '{$_POST["username"]}',
        '{$password_hash}',
        '{$_POST["dateOfBirth"]}',
        '{$_POST["gender"]}',
        '{$_POST["nhsNumber"]}',
        '{$_POST["insuranceProvider"]}',
        '{$_POST["insuranceNumber"]}'   )";
    if (!mysqli_query($mysqli,$mysql)) {
        echo("<h4>SQL error description: " . mysqli_error($mysqli) . "</h4>");
    }
    $registrationFail = false;
}
else 
{
    $mysql="INSERT INTO users(fullname, email, username, 
                            password, dateOfBirth, 
                            gender, nhsNumber)
    VALUE('{$_POST["fullname"]}',  
    '{$_POST["email"]}',
    '{$_POST["username"]}',
    '{$password_hash}',
    '{$_POST["dateOfBirth"]}',
    '{$_POST["gender"]}',
    '{$_POST["nhsNumber"]}')";
    if (!mysqli_query($mysqli,$mysql)) {
        echo("<h4>SQL error description: " . mysqli_error($mysqli) . "</h4>");
    }
    $registrationFail = false;
}

//Frees memory on the server and redirects user to the login page. 
if ($registrationFail == false){
    mysqli_free_result($result);
    mysqli_close($mysqli);
    header("Location: login.html");
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Silver Sage Support - Register</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Additional Styles */
        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 145vh; /* Change height to min-height */
        }

        .content {
            padding: 25px 20px 20px; /* Increase top padding to raise the content higher */
        }

        .form-row {
            margin-bottom: 20px; /* Reduce margin between form rows */
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
    </nav>

    <div class="container">
        <div class="content">
            <h2>Register</h2>
            <?php if (!$allFields):?>
                <label>Missing Fields</label>
            <?php endif; ?>
            <form action="register.php" method="POST" id="register" novalidate>
                <div class="form-row">
                    <label for="fullname">Full Name:</label>
                    <input type="text" id="fullname" name="fullname" required>
                    <?php if (empty($_POST["fullname"])):?>
                        <label>Missing Name</label>
                    <?php endif; ?>
                </div>
                <div class="form-row">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                    <?php if ($emailExists): ?>
                        <label>Email already in use</label>
                    <?php endif; ?>
                    <?php if (empty($_POST["email"])):?>
                        <label>Missing Email</label>
                    <?php endif; ?>
                    <?php if ($invalidEmail):?>
                        <label>Please enter vaild email.</label>
                    <?php endif; ?>
                </div>
                <div class="form-row">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required>
                    <?php if ($usernameExists): ?>
                        <label>Username already in use</label>
                    <?php endif; ?>
                </div>
                <div class="form-row">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                    <?php if ($passwordExists): ?>
                        <div>
                        <label>Password already in use</label>
                        </div>
                    <?php endif; ?>
                    <?php if (!$passwordLength): ?>
                        <div>
                        <label>Password length is not at least 8 characters</label>
                        </div>
                    <?php endif; ?>
                    <?php if (!$containLetter): ?>
                        <div>
                        <label>Password must contain atleast 1 letter</label>
                        </div>
                    <?php endif; ?>
                    <?php if (!$containsNumber): ?>
                        <div>
                        <label>Password must contain atleast 1 number</label>
                        </div>
                    <?php endif; ?>
                </div>
                <div>
                    <label>Password must contain:</label>
                    <ul>
                        <li>At least 8 characters long</li>
                        <li>Contains at least 1 number</li>
                        <li>Contains at least 1 letter</li>
                    </ul>

                </div>
                <div class="form-row">
                    <label for="dateOfBirth">Date of Birth:</label>
                    <input type="date" id="dateOfBirth" name="dateOfBirth" required>
                    <?php if (empty($_POST["nhsNumber"])): ?>
                        <label>Missing Date of Birth</label>
                    <?php endif; ?>
                </div>

                <div class="form-row">
                    <label for="gender">Gender:</label>
                    <select id="gender" name="gender" required>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="trans">Trans</option>
                        <option value="non-binary">Non-binary</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <div class="form-row">
                    <label for="nhsNumber">NHS Number:</label>
                    <input type="text" id="nhsNumber" name="nhsNumber" required>
                    <?php if (!$nhsNumber): ?>
                        <label>Missing NHS Number</label>
                    <?php endif; ?>
                </div>

                <div class="form-row">
                    <label for="insuranceProvider">Name of Insurance Provider(Optional):</label>
                    <input type="text" id="insuranceProvider" name="insuranceProvider">
                </div>

                <div class="form-row">
                    <label for="insuranceNumber">Insurance Number(Optional):</label>
                    <input type="text" id="insuranceNumber" name="insuranceNumber">
                </div>

                <div class="form-row">
                    <input type="submit" value="Register" onclick="validateUnique()">
                </div>
            </form>
        </div>
    </div>
    <footer>
        <p>&copy; 2024 Silver Sage Support</p>
    </footer>
</body>
</html>

        