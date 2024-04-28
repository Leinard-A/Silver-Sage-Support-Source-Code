<?php


$host="localhost";
$dbname= "";
$username = "";
$password = "";

$mysqli = mysqli_connect($host,$username,$password,$dbname);

if(!$mysqli){
    die("connection error".$mysqli->connect_error);
}
return $mysqli;