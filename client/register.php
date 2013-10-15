<?php
error_reporting(E_ERROR | E_PARSE);

if ($_GET["op"] == "reg") {
    $bInputFlag = false;
    foreach($_POST as $field) {
        if ($field == "") {
            $bInputFlag = false;
        }
        else {
            $bInputFlag = true;
        }
    }
    // If we had problems with the input, exit with error
    if ($bInputFlag == false) {
        die("Problem with your registration info. "."Please go back and try again.");
    }

    // $url = 'https://ubi_test_nodejs-c9-ben_epsilone.c9.io/attempt_register';
    $url = 'http://127.0.0.1:3001/attempt_register';
    $salt = md5($_POST["password"]);

    $data = array('username' => $_POST["username"], 'password' => $salt, 'email' => $_POST["email"]);

    $options = array('http' => array('header' => "Content-type: application/x-www-form-urlencoded\r\n", 'method' => 'POST', 'content' => http_build_query($data), ), );

    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);

    if ($result == "fail") {
        die("Error: User not added to database.");
    }
    else {
        // Redirect to thank you page.
        Header("Location: register.php?op=thanks");
    }
}
elseif($_GET["op"] == "thanks") {
    echo "<h2>Thanks for registering!</h2>";
    echo "<META HTTP-EQUIV=\"refresh\" CONTENT=\"2;URL=index.php\">";
    echo "<!--";
}
?>

<html lang="en">
  <head>
    <title>Scribbles - register</title>
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
  <h1>Scribbles - register</h1>
        <div class="row controls span5">
            <form action="?op=reg" method="POST">
                Username: <input type="text" class="input-block-level" name="username"/>
                Email Address: <input type="text" name="email" class="input-block-level"> 
                Password: <input type="password" name="password" class="input-block-level">

                <input type="submit" class="span2 btn btn-primary" value="register">
                <a href="./register.php"> register</a>

            </form>
          </div>
    </div>
  </body>