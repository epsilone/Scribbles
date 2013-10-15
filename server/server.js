// init db connection

var host = "ds049568.mongolab.com";
var port = "49568";
var user = "ubi_user";
var pwd = "Ubisoft123";

var db_url = "mongodb://"+user+":"+pwd+"@"+ host + ":" + port + "/ubi_test";


// db connection:
var express = require('express'),
  mongoskin = require('mongoskin'),
  app = express();

app.use(express.bodyParser());

var db = mongoskin.db(db_url, {safe:true});
var scribbleCollection = db.collection("scribble");
var userCollection = db.collection("user");
userCollection.ensureIndex( { "username": 1 }, { "unique": true }, function (e) {
} );


app.lastPost = new Date();

app.all('/*', function(req, res, next) {
  res.header("Access-Control-Allow-Origin", "*");
  res.header('Access-Control-Allow-Methods', 'GET,PUT,POST,DELETE');
  res.header('Access-Control-Allow-Headers', 'Content-Type');
  next();
});

app.get('/LastScribble', function(req, res, next){
  res.send(app.lastPost);
});

/**
 * get latest scribbles
 */
app.get('/scribble/latest', function(req, res, next){
  scribbleCollection.find({}, {limit:10, sort: [['date', -1]]}).toArray(function(e, results){
    if (e) return next(e);
    res.send(results);
  });
});

/**
 * listing scribbles by score
 */
app.get('/scribble', function(req, res, next) {
  scribbleCollection.find({}, {limit:10, sort: [['score',-1]]}).toArray(function(e, results){
    if (e) return next(e);
    res.send(results);
  });
});

/**
 * creating a new scribble
 */
app.post('/scribble', function(req, res, next) {
  var object;
  if (req.body.text && req.body.user)
  {
    object = {'user':req.body.user, 'text':req.body.text};
  } else {
    return next();
  }
  object.date = new Date().getTime();
  object.upvote = [];
  object.downvote = [];
  object.score = 0;

  app.lastPost = object.date;

  scribbleCollection.insert(object, {}, function(e, results){
    if (e) return next(e);
    res.send(results[0]);
  });
});

/**
 * voting on scribble
 */
app.post('/scribble/vote', function(req, res, next) {
  console.log("voting");
  if (req.body.id && req.body.vote && req.body.user)
  {
    scribbleCollection.findOne({"_id": scribbleCollection.id(req.body.id)}, function(e, result){
      if (e) return next(e);
      if (result && result.upvote.indexOf(req.body.user) == -1 && result.downvote.indexOf(req.body.user) == -1) {
          var update;
        if (req.body.vote > 0) {
          update = {"$inc": {"score" : 1}, "$addToSet": {"upvote" : req.body.user}};
        } else {
          update = {"$inc": {"score" : -1}, "$addToSet": {"downvote" : req.body.user}};
        }
        scribbleCollection.findAndModify({"_id": scribbleCollection.id(req.body.id)}, [], update, {new:true}, function(e, results) {
          if (e) return next(e);
          res.send(results, 200);
        });
      } else return next();
    });
  } else return next();
});


/**
 * Login part
 */
var crypto = require('crypto');

app.post('/attempt_register', function (req, res, next) {
  var user = {};
  user.username = req.body.username;
  user.password = req.body.password;
  user.email = req.body.email;

  userCollection.insert(user, {}, function(e, results){
    if (e) return res.send("fail");
    res.send("user created");
  });

});

app.post('/attempt_login', function (req, res, next) {
  userCollection.findOne({"user": req.body.username}, function(e, result) {
    if (result === null) return next();
    var hash = crypto.createHash('md5').update(result.password + req.body.timestamp).digest("hex");
    var clientHash = crypto.createHash('md5').update(req.body.password).digest("hex");
    if (hash == clientHash) {
      res.send(result._id);
    } else {
      res.send("fail");
    }
  });
});


app.listen(3001);
