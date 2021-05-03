<style type="text/css">

    html body {
        margin-top: 60px;
    }


    #include * {
        text-align: left;
        border: 0;
        padding: 0;
        margin: 0;
        font: 11px Verdana, Arial, Tahoma;
        color: #333;
        font-weight: normal;
        background: transparent;
        text-decoration: none;
        display: inline;
    }


    #include p {
        margin: 4px 0 0 10px;
        display: block;
        padding: 1px;
    }

    #include span {
        display: block;
        padding: 3px;
        margin-bottom: 5px;
    }


    #include script {
        display: none;
    }


    #include {
        background: #000;
        position: fixed;
        bottom: 0px;
        left: 0;
        width: 100%;
        z-index: 999999999999999999999999999999999;
        padding: 0px;
        text-align: center;
        overflow: hidden;
        margin: 0px;
        border-top: 1px #eee solid;
    }


    #include a {
        color: #333;
    }

    #include input[type="checkbox"] {
        height: 8px;
    }

    #include a:hover {
        color: #ccc;
    }

    #include .url-input {
        padding: 10px;
        background: #111;
        color: #444;
        margin: 0px;
        margin-bottom: 5px;
        border: none !important;
    }

    #include .url-input:focus {
        background: #fff;
    }

    #include .url-button {
        font-weight: bold;
        border-style: outset;
        border: none !important;
        background: #222 !important;
        color: #555;
        padding: 10px 30px;
        position: absolute;
        right: 0px;
    }

    #include .url-button:hover {
        background: #ff0000 !important;
        color: #000;
        cursor: pointer;
    }

    #include label:hover, #include a:hover {
        color: #ff0000;
    }

    punisher {
        color: #444;
        position: absolute;
        right: 10px;
        bottom: 8px;
    }
</style>
<div id="include">

    <?php
    // Print form using variables (saves repeatedly opening/closing PHP tags)
    // Edit as if normal HTML but escape any dollar signs
    ?>
    <form action="<?= $proxy ?>/inc/process.php?action=update" target="_top" method="post"
          onsubmit="return updateLocation(this);">
        <?php

        ?>

        <input type="text" name="u" size="40" value="<?= $url ?>" class="url-input" style="width:100%;"/>
        <input type="submit" value="Go" class="url-input url-button"/>
        </br>
        <span>
         [<a href="<?= $proxy ?>/index.php" target="_top">home</a>]
         [<a href="<?= $proxy ?>/inc/process.php?action=clear-cookies&return=<?= $return ?>"
             target="_top">clear cookies</a>]
         

<?php

// Loop through the options and print with appropriate checkedness
foreach ($toShow as $details) {
    ?>
    <input type="checkbox" name="<?= $details['name'] ?>" id="<?= $details['name'] ?>"<?= $details['checked'] ?> />
    <label for="<?= $details['name'] ?>"><?= $details['title'] ?></label>
    <?php

}
?>
    </form>
    </span>
    <punisher>punish'em</punisher>
</div>

<!--[proxied]-->