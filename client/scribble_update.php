<?php
session_start();

if (!$_SESSION["valid_user"]) {
    // User not logged in, redirect to login page
    Header("Location: login.php");
}

// remote
// $url = 'https://ubi_test_nodejs-c9-ben_epsilone.c9.io/collections/scribble';
$url = "http://127.0.0.1:3001/scribble";

if (isset($_GET["latest"])) {
    $url = $url."/latest";
}
$scribbles_str = file_get_contents($url);
$scribbles = json_decode($scribbles_str);
$scribbles_str = json_encode($scribbles);
echo($scribbles_str);
?> 