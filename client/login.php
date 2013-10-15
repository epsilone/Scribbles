<?php
error_reporting(E_ERROR | E_PARSE);
session_start();

  if ($_GET["op"] == "login")
  {
    if (!$_POST["username"] || !$_POST["password"])
    {
      die("You need to provide a username and password.");
    }

    // $url = 'https://ubi_test_nodejs-c9-ben_epsilone.c9.io/attempt_login';
    $url = 'http://127.0.0.1:3001/attempt_login';
    $timestamp = new DateTime();
    $timestamp = $timestamp->getTimestamp();
    $salt = md5($_POST["password"]) . $timestamp;
  
    $data = array('username' => $_POST["username"], 'password' => $salt, 'timestamp' => $timestamp);

    $options = array(
      'http' => array(
          'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
          'method'  => 'POST',
          'content' => http_build_query($data),
        ),
    );

    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);

    if ( $result != "fail")
    {
      // Login good, create session variables
      $_SESSION["valid_id"] = $result;
      $_SESSION["valid_user"] = $_POST["username"];
      $_SESSION["valid_time"] = time();

      // Redirect to member page
      Header("Location: index.php");
    }
    else
    {
      // Login not successful
      die("Sorry, could not log you in. Wrong login information.");
    }
  }
?>


<html lang="en">
  <head>
    <title>Scribbles - login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/bootstrap-responsive.min.css">
    <style>
      body {
        padding-top: 60px;
      }
    </style>
  </head>
  <body>
<div class="container">
  <h1>Scribbles - login</h1>
        <div class="row controls span5">
            <form action="?op=login" method="POST">
                Username: <input type="text" class="input-block-level" name="username"/>
              
                Password: <input type="password" name="password" class="input-block-level">

                <input type="submit" class="span1 btn btn-primary" value="login">
                <a href="./register.php"> register</a>

            </form>
          </div>
  </div>
  </body>