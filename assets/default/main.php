<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html style="background:#000 ;font-size:11px ">
<head>
    <title><!--[site_name]--></title>
    <meta name="description" content="<!--[meta_description]-->">
    <meta name="keywords" content="<!--[meta_keywords]-->">
    <style type="text/css">

    </style>
    <?= injectionJS(); ?>
    <script type="text/javascript" src="./assets/default/main.js"></script>
    <link href="./assets/default/main.css" rel="stylesheet" type="text/css" media="all">
</head>
<body>
<div id="wrapper">
    <div id="header">
        <h1><a href="index.php"><?php
                # Just a bit of PHP to auto-color a multiple word name
                global $themeReplace;
                if (isset($themeReplace['site_name'])) {
                    $wc = 0;
                    $words = explode(' ', $themeReplace['site_name']);
                    foreach ($words as $word) {
                        $wc++;
                        if ($wc % 2 == 1) {
                            echo $word . ' ';
                        } else {
                            echo '<span>' . $word . '</span> ';
                        }
                    }
                }
                ?></a></h1>
    </div>
    <center><img src="../assets/banner.png"/></center>
    <div id="content">

        <!-- CONTENT START -->

        <!--[error]-->

        <h2>where to begin<span class="blink_me">_</span></h2>

        <!--[index_above_form]-->

        <form action="inc/process.php?action=update" method="post" onsubmit="return updateLocation(this);"
              class="form">
            <input type="text" name="u" id="input" size="40" class="textbox">
            <input type="submit" value="Go" class="button"> &nbsp; [<a style="cursor:pointer;"
                                                                       onclick="document.getElementById('options').style.display=(document.getElementById('options').style.display=='none'?'':'none')">options</a>]
            <ul id="options">
                <?php foreach ($toShow as $option) echo '<li><input type="checkbox" name="' . $option['name'] . '" id="' . $option['name'] . '"' . $option['checked'] . '><label for="' . $option['name'] . '" class="tooltip" onmouseover="tooltip(\'' . $option['escaped_desc'] . '\')" onmouseout="exit();">' . $option['title'] . '</label></li>'; ?>
            </ul>
            <br style="clear: both;">
        </form>

        <!--[index_below_form]-->

        <!-- CONTENT END -->

        <ul id="nav">
            <li class="left"><a href="index.php">Home</a></li>
            <li class="left"><a href="edit-browser.php">Edit Browser</a></li>
            <li class="left"><a href="cookies.php">Manage Cookies</a></li>
            <li><a href="disclaimer.php">Disclaimer</a></li>
        </ul>
    </div>
    <div id="footer">
        <p><a href="http://www.ţ.com/">Punisher</a>&reg;  <!--[version]--> 1985 - 2021</p>
        <p style="font-size:5px;color:#444;">DON FEDERATION 👁 THE DON FEDERATION IS A WORLD LEADER IN PRODUCT
            DEVELOPMENT AND SERIVCES. CONTENT OF THE PAGES OF THIS WEBSITE IS FOR YOUR GENERAL INFORMATION AND USE ONLY.
            IT IS SUBJECT TO CHANGE WITHOUT NOTICE. THIS WEBSITE USES COOKIES TO MONITOR BROWSING PREFERENCES. IF YOU DO
            ALLOW COOKIES TO BE USED, YOUR PERSONAL INFORMATION MAY BE STORED BY US FOR USE BY THIRD PARTIES. NO
            WARRANTY OR GUARANTEE TO THE ACCURACY, OF THE INFORMATION AND MATERIALS FOUND ON THIS WEBSITE. MATERIAL
            WHICH IS OWNED BY OR LICENSED TO US NOT LIMITED TO, THIS WEBSITE, DESIGN, LAYOUT, LOOK, APPEARANCE AND
            GRAPHICS. REPRODUCTION IS PROHIBITED OTHER THAN IN ACCORDANCE WITH THE COPYRIGHT NOTICE, WHICH FORMS PART OF
            THESE TERMS AND CONDITIONS. UNAUTHORISED USE OF THIS WEBSITE MAY GIVE RISE TO A CRIMINAL OFFENCE. YOUR USE
            OF THIS WEBSITE AND ANY DISPUTE ARISING OUT OF SUCH USE OF THE WEBSITE IS SUBJECT TO THE LAWS OF ENGLAND,
            NORTHERN IRELAND, SCOTLAND AND WALES.</p>
    </div>
</div>
</body>
</html>