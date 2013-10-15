<?php
session_start();

if (!$_SESSION["valid_user"]) {
    // User not logged in, redirect to login page
    Header("Location: login.php");
}

// remote
// $url = 'https://ubi_test_nodejs-c9-ben_epsilone.c9.io/attempt_register';
$url = 'http://127.0.0.1:3001/scribble';

$data = array('user' => $_SESSION["valid_user"], 'text' => $_GET["text"]);

$options = array(
      'http' => array(
          'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
          'method'  => 'POST',
          'content' => http_build_query($data),
      ),
  );

$context = stream_context_create($options);
$result = file_get_contents($url, false, $context);

echo $result;

?> 