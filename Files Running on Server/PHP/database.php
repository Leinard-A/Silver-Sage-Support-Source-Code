<?php


$host="localhost";
$dbname= "db2201213";
$username = "2201213";
$password = "4jrat6";

$mysqli = mysqli_connect($host,$username,$password,$dbname);

if(!$mysqli){
    die("connection error".$mysqli->connect_error);
}
return $mysqli;