<?php

if(!isset($_SESSION)){
header('Location: Login Folder/login.php');
}


$servername = "localhost";
$username = "root";
$password = "";
$dbname = "lms";

try {
  $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
  // set the PDO error mode to exception
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
  echo "<strong>Connection failed:</strong> " . $e->getMessage();
}
?>