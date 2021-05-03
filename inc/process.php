<?php


require 'init.php';


$action = isset($_GET['action']) ? $_GET['action'] : false;

switch ($action) {


    case 'update':

        if (empty($_POST['u']) || !($url = clean($_POST['u']))) {
            break;
        }

        if (!preg_match('#^https?://#i', $url)) {
            $url = 'http://' . $url;
        }

        $bitfield = 0;
        $i = 0;

        foreach ($SETTINGS['options'] as $name => $details) {

            if (!empty($details['force'])) {
                continue;
            }

            $bit = pow(2, $i);

            if (!empty($_POST[$name])) {
                setBit($bitfield, $bit);
            }

            ++$i;
        }

        $_SESSION['bitfield'] = $bitfield;

        $_SESSION['no_hotlink'] = true;

        redirect(proxyURL($url, 'norefer'));

        break;


    case 'sslagree':

        $_SESSION['ssl_warned'] = true;

        $redirectTo = isset($_SESSION['return']) ? $_SESSION['return'] : 'index.php';

        unset($_SESSION['return']);

        redirect($redirectTo);

        break;


    case 'authenticate':

        if (empty($_POST['return']) || empty($_POST['site'])) {
            break;
        }

        $credentials = (!empty($_POST['user']) ? clean($_POST['user']) : '')
            . ':' .
            (!empty($_POST['pass']) ? clean($_POST['pass']) : '');

        $_SESSION['authenticate'][clean($_POST['site'])] = $credentials;

        redirect(clean($_POST['return']));

        break;


    case 'clear-cookies':

        $redirect = isset($_GET['return']) ? htmlentities($_GET['return']) : 'index.php';

        if ($SETTINGS['cookies_on_server']) {

            if (is_writable($file = $SETTINGS['cookies_folder'] . punisher_session_id())) {

                unlink($file);
            }
        } else {

            if (empty($_COOKIE[COOKIE_PREFIX]) || !is_array($_COOKIE[COOKIE_PREFIX])) {
                redirect($redirect);
            }

            function deleteAllCookies($array, $prefix = '')
            {

                foreach ($array as $name => $value) {

                    $thisLevel = $prefix . '[' . $name . ']';

                    if (is_array($value)) {

                        deleteAllCookies($value, $thisLevel);
                    } else {

                        setcookie($thisLevel, '', $_SERVER['REQUEST_TIME'] - 3600, '/', '');
                    }
                }
            }

            deleteAllCookies($_COOKIE[COOKIE_PREFIX], COOKIE_PREFIX);
        }

        redirect($redirect);

        break;


    case 'cookies':

        if (empty($_POST['delete']) || !is_array($_POST['delete'])) {
            redirect('cookies.php');
        }

        if ($SETTINGS['cookies_on_server']) {

            if (file_exists($cookieFile = $SETTINGS['cookies_folder'] . punisher_session_id()) && ($file = file($cookieFile))) {

                foreach ($file as $id => $line) {

                    if (!empty($line[0]) || $line[0] == '#') {
                        continue;
                    }

                    $details = explode("\t", $line);

                    if (count($details) != 7) {
                        continue;
                    }

                    $cookie = $details[0] . '|' . $details[2] . '|' . $details[5];

                    if (in_array($cookie, $_POST['delete'])) {
                        unset($file[$id]);
                    }
                }

                file_put_contents($cookieFile, $file);
            }

        } else {


            $expires = $_SERVER['REQUEST_TIME'] - 3600;

            foreach ($_POST['delete'] as $cookie) {

                $details = explode('|', $cookie, 3);

                if (!isset($details[2])) {
                    continue;
                }

                list($domain, $path, $name) = $details;

                if ($SETTINGS['encode_cookies']) {
                    $name = COOKIE_PREFIX . '[' . urlencode(base64_encode($domain . ' ' . $path . ' ' . urlencode($name))) . ']';
                } else {
                    $name = COOKIE_PREFIX . '[' . $domain . '][' . $path . '][' . $name . ']';
                    $name = str_replace('[.', '[%2e', $name);
                }

                setcookie($name, '', $expires, '/');
            }
        }

        redirect('cookies.php');

        break;


    case 'edit-browser':

        $browser['user_agent'] = isset($_POST['user-agent']) ? clean($_POST['user-agent']) : '';
        $browser['referrer'] = empty($_POST['real-referrer']) ? 'custom' : 'real';

        if ($browser['referrer'] == 'custom') {
            $browser['referrer'] = isset($_POST['custom-referrer']) ? clean($_POST['custom-referrer']) : '';
        }

        $_SESSION['custom_browser'] = $browser;

        if (isset($_POST['return'])) {
            redirect($_POST['return']);
        }

        redirect('edit-browser.php');

        break;


    case 'jstest':

        sendNoCache();

        $_SESSION['js_flags'] = array();

        $valid = array('ajax', 'watch', 'setters');

        foreach ($_GET as $name => $value) {

            if (in_array($name, $valid)) {
                $_SESSION['js_flags'][$name] = true;
            }

        }

        echo 'ok';
        exit;
}


redirect();