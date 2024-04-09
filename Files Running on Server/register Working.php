<?php
$errors = array();
//Validation of parameters
if (empty($_POST["fullName"]) OR empty($_POST["email"]) OR empty($_POST["password"]) OR empty($_POST["username"]) or empty ($_POST["nhsNumber"])) {
    array_push($errors,"All fields are required");
   }

if (empty($_POST["fullname"])) {
    die("Please enter  vaild name");
}
if (empty($_POST["nhsNumber"])) {
    die("vaild nhs number");
}
if (!filter_var($_POST["email"],FILTER_VALIDATE_EMAIL)){
    die("Please enter vaild email.");
}
if (strlen($_POST["password"])<8)  {
    die("Password requires atleast 8 characters one of which must be a number.");
}
if(preg_match("/^[a-zA-Z]+$/",$_POST["password"])){
    die("password must contain atleast 1 letter");

}
if(preg_match("/^[0-9]+$/",$_POST["password"])){
    die("must containt 1 number");

}

//Not sure if this is neccessary.
if(isset($_POST['gender']) ){
    $verGender=$_POST['gender'];
        
}
if(isset($_POST['dateOfBirth']) ){
    $verGender=$_POST['dateOfBirth'];
}

//Hashing Password
$password_hash = password_hash($_POST ["password"],PASSWORD_DEFAULT);

$mysqli= require __DIR__. "/database.php";
//Checks if password already exists in database
$sqlStmt = "SELECT * FROM users WHERE password = '{$password_hash}' ";

$result = mysqli_query($mysqli,$sqlStmt);
$row = mysqli_num_rows($result);
if ($row > 0) {
    array_push($errors,"Password already exists!");
}



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

header("Location: login.php");



        