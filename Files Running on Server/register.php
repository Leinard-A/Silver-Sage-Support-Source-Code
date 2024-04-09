<?php
$errors = array();

//Validation of parameters
if (empty($_POST["fullName"]) OR empty($_POST["email"]) OR empty($_POST["password"]) OR empty($_POST["username"]) or empty ($_POST["nhsNumber"])) {
    array_push($errors,"All fields are required");
   }

if (empty($_POST["fullname"])) {
    array_push($errors,"Enter your name");
}
if (empty($_POST["nhsNumber"])) {
    array_push($errors,"Enter your NHS number");
}
if (!filter_var($_POST["email"],FILTER_VALIDATE_EMAIL)){
    array_push($errors, "Please enter vaild email.");
}
if (strlen($_POST["password"])<8)  {
    array_push($errors, "Password requires atleast 8 characters one of which must be a number.");
}
if(!preg_match("/^[a-zA-Z]+$/",$_POST["password"])){
    array_push($errors, "Password must contain atleast 1 letter");

}
if(!preg_match("/^[0-9]+$/",$_POST["password"])){
    array_push($errors, "must containt 1 number");
}
if(!isset($_POST['dateOfBirth']) ){
    array_push($errors, "Enter your date of birth");
}



//Not sure if this is neccessary.
if(isset($_POST['gender']) ){
    $verGender=$_POST['gender'];
        
}

//Hashing Password
$password_hash = password_hash($_POST ["password"],PASSWORD_DEFAULT);

$mysqli = require __DIR__. "/database.php";
//Checks if password already exists in database
$sqlStmt = "SELECT * FROM users WHERE password = '{$password_hash}' ";

$result = mysqli_query($mysqli,$sqlStmt);
if (mysqli_num_fields($result) > 0) {
    array_push($errors,"Password already exists!");
}

if (count($errors)>0) {
    foreach ($errors as $error) {
        echo "<div class='alert alert-danger'>$error</div>";
        header("Location: register.html");

    }
}

//Checks if the email is already in use
$sqlStmt = "SELECT * FROM users
            WHERE email = '{$_GET['Email']}'";

$result =  mysqli_query($mysqli,$sqlStmt);

if (mysqli_num_fields($result) > 0 ){
    array_push($errors, "Emails already in use");
}


//Inputs data into the database. 
if (!empty($_POST["insuranceProvider"]) && !empty($_POST["insuranceNumber"])){ 
    $mysql="INSERT INTO users (fullname, email, username, password, dateOfBirth, gender, nhsNumber,  insuranceProvider, insuranceNumber)
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
        echo("<h4>SQL error description: " . $mysqli -> error . "</h4>");
    }
}
else 
{
    $mysql="INSERT INTO users(fullname, email, username, password, dateOfBirth, gender, nhsNumber)
    VALUE('{$_POST["fullname"]}',  
    '{$_POST["email"]}',
    '{$_POST["username"]}',
    '{$password_hash}',
    '{$_POST["dateOfBirth"]}',
    '{$_POST["gender"]}',
    '{$_POST["nhsNumber"]}')";
    if (!mysqli_query($mysqli,$mysql)) {
        echo("<h4>SQL error description: " . $mysqli -> error . "</h4>");
    }
}

//Frees memory on the server and redirects user to the login page. 
mysqli_free_result($result);
mysqli_close($mysqli);
header("Location: login.php");



        