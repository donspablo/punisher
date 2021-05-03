<html>
<head>
    <title>401 Authorization Required</title>
    <style type="text/css">
        html, body {
            background: #0b1933;
            text-align: center;
        }

        body {
            font: 80% Tahoma;
        }

        margin:

        100
        px auto

        ;
        width:

        500
        px

        ;
        text-align: left

        ;
        background: #fff

        ;
        padding:

        10
        px

        ;
        border:

        5
        px solid #ccc

        ;
        }

        form {
            margin: 5px;
            background: #eee;
            padding: 5px;
        }

        label {
            display: block;
        }
    </style>
    <base href="<?php echo PUNISH_URL; ?>/">
</head>
<body>
<div id="wrapper">
    <h1>Authorization Required</h1>
    <p>The site <strong><?php echo $site; ?></strong> is requesting a username and password to access the realm
        "<strong><?php echo $realm; ?></strong>".</p>
    <form action="inc/process.php?action=authenticate" method="post">
        <label for="user">Username:</label>
        <input type="text" name="user" id="user">
        <label for="pass">Password:</label>
        <input type="password" name="pass" id="pass">
        <input type="submit" value="Submit">

        <input type="hidden" name="site" value="<?php echo $site; ?>">
        <input type="hidden" name="return" value="<?php echo $return; ?>">
    </form>
</div>
</body>
</html>