<?php
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "project"; // Replace "db_name" with the actual name of your database.
// Create connection
$mysqli = new mysqli($servername, $username, $password, $dbname);

if ($mysqli->connect_errno) {
    Echo $mysqli->connect_errno.": ".$mysqli->connect_error;
    Echo nl2br("\n");
}else{
    Echo "Connect was successful.";
    Echo nl2br("\n");
}

echo "Connected successfully to MySQL database 'project'";
?>