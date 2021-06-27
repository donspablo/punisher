<?php

require 'inc/init.php';


sendNoCache();


ob_start();


?>
    <h2 class="first">Manage Cookies</h2>
    <p>You can view and delete cookies set on your computer by sites accessed through our service. Your cookies are
        listed below:</p>
    <form action="inc/process.php?action=cookies" method="post">
        <table cellpadding="2" cellspacing="0" align="center">
            <tr>
                <th width="33%">Website</th>
                <th width="33%">Name</th>
                <th width="33%">Value</th>
                <th>&nbsp;</th>
            </tr>

            <?php


            if ($SETTINGS['cookies_on_server']) {


            if (file_exists($cookieFile = $SETTINGS['cookies_folder'] . punisher_session_id())) {


            if ($cookieLine = file($cookieFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES)) {


                foreach ($cookieLine as $line) {


                    if (!isset($line[0]) || $line[0] == '
                                continue;
                            }

                            
                            $line = rtrim($line);

                            
                            $details = explode("\t", $line);

                            
                            if (count($details) != 7) {
                                continue;
                            }

                            
                            $showCookies[] = array($details[0], $details[2], $details[5], $details[6]);

                        }
                    }
                }

            } else if (isset($_COOKIE[COOKIE_PREFIX])) {

                

                
                if ($SETTINGS['encode_cookies']) {

                    
                    foreach ($_COOKIE[COOKIE_PREFIX] as $attributes => $value) {

                        
                        $attributes = explode(' ', base64_decode($attributes));

                        
                        if (!isset($attributes[2])) {
                            continue;
                        }

                        
                        list($domain, $path, $name) = $attributes;

                        
                        $value = base64_decode($value);

                        
                        $value = str_replace('!SEC', '', $value);

                        
                        $showCookies[] = array($domain, $path, $name, $value);
                    }

                } else {

                    
                    foreach ($_COOKIE[COOKIE_PREFIX] as $domain => $paths) {

                        
                        
                        

                        foreach ($paths as $path => $cookies) {

                            foreach ($cookies as $name => $value) {

                                
                                $value = str_replace('!SEC', '', $value);

                                
                                $showCookies[] = array($domain, $path, $name, $value);

                            }
                        }
                    }
                }
            }


            
            if (empty($showCookies)) {

                ?>
                <tr>
                    <td colspan="4" align="center">No cookies found</td>
                </tr>

                <?php

            } else {

                
                foreach ($showCookies as $id => $cookie) {

                    
                    $website = $cookie[0] . ($cookie[1] == ' / ' ? '' : $cookie[1]);

                    
                    $name = htmlentities($cookie[2]);

                    
                    $value = $cookie[3];

                    
                    if (strlen($value) > 35) {

                        
                        $rowID = 'cookieRow' . $id;

                        
                        $wrapped = str_replace("'", "\'", wordwrap($cookie[3], 30, ' ', true));

                        
                        $truncated = substr($value, 0, 30);

                        
                        $value = <<<OUT
			<span id="<?=$rowID?>"><?=$truncated?><a style="cursor:pointer;" onclick="document.getElementById(' <?=$rowID?>').innerHTML='<?=$wrapped?>';">...</a></span>
                    OUT;
                    }

                    ?>
                    <tr>
                        <td><?= $website ?></td>
                        <td><?= $name ?></td>
                        <td><?= $value ?></td>
                        <td><input type="checkbox" name="delete[]"
                                   value="<?= $cookie[0] ?>|<?= $cookie[1] ?>|<?= $name ?>"></td>
                    </tr>

                    <?php
                }

            }


            ?>
            <tr>
                <th colspan="3" align="right"><input type="submit" value="Delete"></th>
                <th><input type="checkbox" name="checkall" onclick="selectAll(this)"></th>
            </tr>
        </table>
    </form>
    <script type="text/javascript">
        function selectAll(checkbox) {
            var theForm = checkbox.form;
            for (var z = 0; z < theForm.length; z++) {
                if (theForm[z].type == 'checkbox' && theForm[z].name != 'checkall') {
                    theForm[z].checked = checkbox.checked;
                }
            }
        }
    </script>
    <?php


    $content = ob_get_contents();


    ob_end_clean();


    echo replaceContent($content);
