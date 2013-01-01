<?php
require_once("config.php");
require_once("lib/classes/Database.class.php");

$db = new Database($config);

if(isset($_POST['inputName']) && isset($_POST['inputGoal']))
{
    if(is_int($_POST['inputGoal']))
    {
        $hash = md5(rand(0, 500) . $_POST['inputGoal'] . time());
        $db->write("INSERT INTO `users` (`hash`,`nickname`,`count`,`goal`) VALUES ('%s','%s',0,%d)", $hash, ((empty($_POST['inputName']) || strlen($_POST['inputName']) == 0) ? null : $_POST['inputName']), $_POST['inputGoal']);
        header('Location: index.php?code=' . $hash);
    }
}

if(isset($_GET['code']) && strlen($_GET['code']) == 32)
{
    $user = $db->read_single("SELECT * FROM `users` WHERE `hash` = '%s' LIMIT 1", $_GET['code']);

    if(!isset($user['goal']))
        unset($user);
    elseif(isset($_POST['inputAchieved']) && ctype_digit($_POST['inputAchieved']))
    {
        $user['count'] += intval($_POST['inputAchieved']);
        $db->write("UPDATE `users` SET `count` = %d WHERE `hash` = '%s' LIMIT 1", $user['count'], $user['hash']);
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>PushUps - The Push Up Counter</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="apple-mobile-web-app-capable" content="yes" />

    <link href="//netdna.bootstrapcdn.com/twitter-bootstrap/2.2.2/css/bootstrap-combined.min.css" rel="stylesheet">

    <!-- <script src="//netdna.bootstrapcdn.com/twitter-bootstrap/2.2.2/js/bootstrap.min.js"></script> -->

    <script type="text/javascript">
    function hideAddressBar()
    {
      if(!window.location.hash)
      {
          if(document.height <= window.outerHeight)
          {
              document.body.style.height = (window.outerHeight + 30) + 'px';
          }
     
          setTimeout( function(){ window.scrollTo(0, 1); }, 50 );
      }
    }
     
    window.addEventListener("load", function(){ if(!window.pageYOffset){ hideAddressBar(); } } );
    window.addEventListener("orientationchange", hideAddressBar );
    </script>
</head>

<body>
    <div class="container-fluid" style="margin-top: 30px;">
        <div class="row-fluid">
            <div class="span4">
                <div class="well">
                    <?php if(isset($user)) { ?>
                        <p>Hey<?php if(!empty($user['nickname'])) echo " " . $user['nickname']; ?>, you're doing great! Only...</p>
                        <h1 style="text-align: center;"><?=($user['goal'] - $user['count'])?></h1>
                        <p class="pull-right">to go!</p>
                        <div class="clearfix"></div>

                        <p>Add to your counter:</p>
                        <form class="form-inline" method="post">
                            <div class="input-append">
                                <input type="text" class="span6" name="inputAchieved" placeholder="Push-Ups">
                                <button type="submit" class="btn btn-info">Add</button>
                            </div>
                        </form>
                    <?php } else { ?>
                        <p>Want to make a PushUp counter? Enter your goal and a name (optional) below.</p>
                        <form class="form-horizontal" method="post">
                            <div class="control-group">
                                <label class="control-label" for="inputName">Name</label>
                                <div class="controls">
                                    <input type="text" id="inputName" placeholder="Name" name="inputName">
                                </div>
                            </div>

                            <div class="control-group">
                                <label class="control-label" for="inputGoal">Goal</label>
                                <div class="controls">
                                    <input type="text" id="inputGoal" placeholder="Goal" name="inputGoal">
                                </div>
                            </div>

                            <div class="control-group">
                                <div class="control">
                                    <button type="submit" class="btn btn-success">Go!</button>
                                </div>
                            </div>
                        </form>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
