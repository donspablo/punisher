<?php

class SkinOutput extends Output
{

    protected function wrap()
    {

        $self = ADMIN_URI;

        $date = date('H:i, d F Y');

        $title = $this->title . ($this->title ? ' : ' : '') . 'Punisher control panel';

        ob_start();

        ?>
        <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
                "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
        <html xmlns="http://www.w3.org/1999/xhtml" style="background:#000 ;font-size:11px ">
        <head>
            <title><?= $title ?></title>
            <meta http-equiv="content-type" content="text/html; charset=UTF-8">
            <script type="text/javascript" src="./assets/admin.js"></script>
            <script type="text/javascript">
                <?php

                if ($this->domReady) {
                    echo 'window.addDomReadyFunc(function(){', $this->printAll('domReady'), '});';
                }

                if ($this->javascript) {
                    echo $this->printAll('javascript');
                }

                ?>
            </script>
            <link rel="icon" type="image/png" href="favicon.ico">
            <link href="./assets/admin.css" rel="stylesheet" type="text/css" media="all">
        </head>
        <body>
        <div id="wrap">

            <div id="top_content">

                <div id="header">

                    <div id="leftheader">
                        <p>control v1.0.1 [beta]</p>
                    </div>

                    <div id="rightheader">
                        <p>
                            <?= $date ?>
                            <br/>
                            <?php

                            if ($this->admin) {
                                echo "welcome, <i><?=$this->admin?></i> : <strong><a href=\"<?=$self?>?logout\">log out</a></strong>\r\n";
                            }

                            $http_host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
                            ?>
                        </p>
                    </div>

                    <div id="topheader">
                        <h1 id="title">
                            <a href="<?= $self ?>"><img alt="Punisher" src="./assets/banner.png" width="50%"
                                                        height="auto"/></a><br>
                        </h1>
                    </div>

                    <div id="navigation">
                        <ul>
                            <?php
                            if (is_array($this->navigation)) {

                                foreach ($this->navigation as $text => $href) {
                                    if (stripos($href, $self) !== false) {
                                        echo "<li><a href=\"" . $href . "\">" . $text . "</a></li>\r\n";
                                    } else {
                                        echo "<li><a href=\"" . $href . "\" target=\"_blank\">" . $text . "</a></li>\r\n";
                                    }
                                }

                            }

                            ?>
                        </ul>
                    </div>

                </div>

                <div id="content">

                    <h1><?= $this->bodyTitle ?></h1>
                    <?php

                    if ($this->error) {

                        foreach ($this->error as $id => $message) {
                            ?>
                            <div class="notice_error" id="notice_error_<?= $id ?>">
                                <a class="close" title="Dismiss"
                                   onclick="document.getElementById('notice_error_<?= $id ?>').style.display='none';">X</a>
                                <?= $message ?>
                            </div>
                            <?php
                        }

                    }

                    if ($this->confirm) {

                        foreach ($this->confirm as $id => $message) {
                            ?>
                            <div class="notice" id="notice_<?= $id ?>">
                                <a class="close" title="Dismiss"
                                   onclick="document.getElementById('notice_<?= $id ?>').style.display='none';">X</a>
                                <?= $message ?>
                            </div>
                            <?php
                        }

                    }

                    echo $this->content;

                    if (is_array($this->footerLinks)) {

                        echo '
				<br>
				<div class="other_links">
					<h2>See also</h2>
					<ul class="other">
					';

                        foreach ($this->footerLinks as $text => $href) {
                            echo "<li><a href=\"{$href}\">{$text}</a></li>\r\n";
                        }

                        echo '
					</ul>
				</div>
					';

                    }

                    ?>

                </div>

            </div>

            <div id="footer">

                <div id="footer_bg">
                    <p><a href="http://www.ţ.com/">Punisher</a>&reg; 1985 - 2021 All rights reserved.</p><br/>
                    <p style="font-size:5px;color:#444;">DON FEDERATION 👁 THE DON FEDERATION IS A WORLD LEADER IN
                        PRODUCT DEVELOPMENT AND SERIVCES. CONTENT OF THE PAGES OF THIS WEBSITE IS FOR YOUR GENERAL
                        INFORMATION AND USE ONLY. IT IS SUBJECT TO CHANGE WITHOUT NOTICE. THIS WEBSITE USES COOKIES TO
                        MONITOR BROWSING PREFERENCES. IF YOU DO ALLOW COOKIES TO BE USED, YOUR PERSONAL INFORMATION MAY
                        BE STORED BY US FOR USE BY THIRD PARTIES. NO WARRANTY OR GUARANTEE TO THE ACCURACY, OF THE
                        INFORMATION AND MATERIALS FOUND ON THIS WEBSITE. MATERIAL WHICH IS OWNED BY OR LICENSED TO US
                        NOT LIMITED TO, THIS WEBSITE, DESIGN, LAYOUT, LOOK, APPEARANCE AND GRAPHICS. REPRODUCTION IS
                        PROHIBITED OTHER THAN IN ACCORDANCE WITH THE COPYRIGHT NOTICE, WHICH FORMS PART OF THESE TERMS
                        AND CONDITIONS. UNAUTHORISED USE OF THIS WEBSITE MAY GIVE RISE TO A CRIMINAL OFFENCE. YOUR USE
                        OF THIS WEBSITE AND ANY DISPUTE ARISING OUT OF SUCH USE OF THE WEBSITE IS SUBJECT TO THE LAWS OF
                        ENGLAND, NORTHERN IRELAND, SCOTLAND AND WALES.</p>
                </div>

            </div>

        </div>

        <div id="preload">
            <span class="ajax-loading">&nbsp;</span>
        </div>

        </body>
        </html>
        <?php

        $this->output = ob_get_contents();

        ob_end_clean();

    }


    private function printAll($name)
    {
        $name = strtolower($name);
        if (isset($this->data[$name]) && is_array($this->data[$name])) {
            foreach ($this->data[$name] as $item) {
                echo $item;
            }
        }
    }

}