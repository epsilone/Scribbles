<?php
error_reporting(E_ERROR | E_PARSE);
session_start();
if (!$_SESSION["valid_user"]) {
    // User not logged in, redirect to login page
    Header("Location: login.php");
}
?>

<!doctype html>
<html lang="en" ng-app>
  <head>
    <title>Scribbles</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/bootstrap-responsive.min.css">
    <style>
      body {
        padding-top: 60px;
      }
    </style>
    <script>

    <?php
    // remote
    //$url = 'https://ubi_test_nodejs-c9-ben_epsilone.c9.io/collections/scribble';
     $url = "http://127.0.0.1:3001/scribble";
    $update_url = "scribble_update.php";
    $send_url = "scribble_post.php";
    $vote_url = "scribble_vote.php";

    if (isset($_GET["latest"])) {
        $url = $url."/latest";
        $update_url = $update_url."?latest";
        $subtitle = "latest";
        $sorting_text = "sort by score";
        $sorting_url = "index.php";
    }
    else {
        $subtitle = "highest score";
        $sorting_text = "see latest post";
        $sorting_url = "index.php?latest";
    }

    $scribbles_str = file_get_contents($url);
    $scribbles = json_decode($scribbles_str);
    $scribbles_str = json_encode($scribbles);
    ?>

    function ScribbleController($scope) {
        $scope.update = function update() {
            jQuery.get("<?= $update_url ?>").error(function() {}).success(function(data) {
                $scope.$apply(function() {
                    $scope.scribbles = jQuery.parseJSON(data);
                });
            });
        };

        $scope.update();
        setInterval($scope.update, 5000);

        $scope.name = '';
        $scope.text = '';

        $scope.send = function send() {

            jQuery.get("<?= $send_url ?>", {
                'text': $scope.text
            }).error(function(e) {
                console.log(e);
            }).success(function(data) {
                console.log("post done");
                $scope.$apply(function() {
                    $scope.scribbles.push(jQuery.parseJSON(data));
                });
            });

            $scope.text = '';
        };

        $scope.vote = function vote(scble, vote) {
            var id = scble._id;
            jQuery.get("<?= $vote_url ?>", {
                'vote': vote,
                'id': id
            }).error(function(e) {
                console.log(e);
            }).success(function(data) {
                console.log("vote done");
                $scope.$apply(function() {
                    scble.score = jQuery.parseJSON(data).score;
                });
            });
        };
    }

    </script>
  </head>
  <body>
    <div class="container" ng-controller="ScribbleController">

      <div class="page-header">
        <h1>Scribbles</h1>
        <h2><?= $subtitle ?></h2>
      </div>

        <div class="span11">
          <table class="table table-striped table-bordered">
            <thead>
              <tr>
                <th class="span2">Name</th>
                <th class="span7">Text</th>
                <th class="span1">Score</th>
                <th class="span1">Up</th>
                <th class="span1">Down</th>
              </tr>
            </thead>
            <tbody>
              <tr ng-repeat="scble in scribbles">
                <td class="span2" ng-bind="scble.user"></td>
                <td class="span9" ng-bind="scble.text" ></td>
                <td class="span1" ng-bind="scble.score"></td>
                <td class="span1" ><form ng-submit="vote(scble, 1)"><input type="submit" class="span1 btn btn-primary" value="Up" /></form></td>
                <td class="span1" ><form ng-submit="vote(scble, -1)"><input type="submit" class="span1 btn btn-primary" value="Down" /></form></td>
              </tr>
            </tbody>
          </table>
          <div class="row controls">
            logged as
            <form ng-submit="send()">

              <div class="span2"><input type="text" class="input-block-level" ng-model="user" placeholder="<?= $_SESSION["valid_user"]?>" disabled></div>
              <div class="input-append span7">
                <input type="text" class="span6" ng-model="text" placeholder="Message">
                <input type="submit" class="span1 btn btn-primary" value="Send" ng-disabled="!text">
              </div>
            </form>
          </div>
        </div>
        <a class="span2" href="<?= $sorting_url ?>"><?= $sorting_text ?></a>
        <a class="span1" href="logout.php">logout</a>
      </div>

    </div>
    <!--script src="./socket.io/socket.io.js"></script-->
    <script type="text/javascript" src="./js/jquery.min.js"></script>
    <script type="text/javascript" src="./js/bootstrap.min.js"></script>
    <script type="text/javascript" src="./js/angular.min.js"></script>
  </body>
</html>
