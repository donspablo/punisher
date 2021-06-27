<?php

ini_set('display_errors', 0);

define('ADMIN_PUNISH_SETTINGS', 'inc/settings.php');

define('ADMIN_TIMEOUT', 60 * 60);

define('ADMIN_STATS_LIMIT', 50);

define('ADMIN_URI', $_SERVER['PHP_SELF']);

define('ADMIN_VERSION', '1.0.1');

ob_start();

define('PUNISH_URL', pathToURL(dirname(ADMIN_PUNISH_SETTINGS) . '/..'));
define('PUNISH_ROOT', str_replace('\\', '/', dirname(dirname(realpath(ADMIN_PUNISH_SETTINGS)))));

foreach (glob("classes/*.php") as $filename) include $filename;
foreach (glob("abstract/*.php") as $filename) include $filename;

function findURL()
{
    return PUNISH_URL;
}

define('LCNSE_KEY', '');
define('proxyPATH', PUNISH_ROOT . '/');

$settingsLoaded = file_exists(ADMIN_PUNISH_SETTINGS) && (@include ADMIN_PUNISH_SETTINGS);

$action = isset($_SERVER['QUERY_STRING']) && preg_match('#^([a-z-]+)#', $_SERVER['QUERY_STRING'], $tmp) ? $tmp[1] : '';

$cache_bust = filemtime(__FILE__) + filemtime(ADMIN_PUNISH_SETTINGS);

define('NL', "\r\n");

if (isset($_GET['image'])) {

    function sendImage($str)
    {
        header('Content-Type: image/gif');
        header('Last-Modified: ' . gmdate("D, d M Y H:i:s", filemtime(__FILE__)) . 'GMT');
        header('Expires: ' . gmdate("D, d M Y H:i:s", filemtime(__FILE__) + (60 * 60)) . 'GMT');
        echo base64_decode($str);
        exit;
    }

}

function quote($str)
{
    $str = str_replace('\\', '\\\\', $str);
    $str = str_replace("'", "\'", $str);
    return "'$str'";
}

function formatSize($bytes)
{
    $types = array('B', 'KB', 'MB', 'GB', 'TB');

    for ($i = 0, $l = count($types) - 1; $bytes >= 1024 && $i < $l; $bytes /= 1024, $i++) {
        return (round($bytes, 2) . ' ' . $types[$i]);
    }

}

function pathToURL($filePath)
{

    $realPath = realpath($filePath);

    if (is_file($realPath)) {

        $dir = dirname($realPath);

    } elseif (is_dir($realPath)) {

        $dir = $realPath;

    } else {
        return false;
    }

    $_SERVER['DOCUMENT_ROOT'] = realpath($_SERVER['DOCUMENT_ROOT']);

    if (strlen($dir) < strlen($_SERVER['DOCUMENT_ROOT'])) {
        return false;
    }

    $rootPos = strlen($_SERVER['DOCUMENT_ROOT']);

    if (($tmp = substr($_SERVER['DOCUMENT_ROOT'], -1)) && ($tmp == '/' || $tmp == '\\')) {
        --$rootPos;
    }

    $pathFromRoot = substr($realPath, $rootPos);

    $path = 'http' . (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) != 'off' ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . $pathFromRoot;

    if (DIRECTORY_SEPARATOR == '\\') {
        $path = str_replace('\\', '/', $path);
    }

    return $path;
}

function jsWrite($str)
{
    return '<script type="text/javascript">document.write(' . quote($str) . ')</script>';
}

function bool($str)
{
    if ($str == 'false') {
        return false;
    }
    if ($str == 'true') {
        return true;
    }
    return NULL;
}


$output = new SkinOutput;

$tpl = new Overloader;

$location = new Location;

$user = new User();

$confirm = new Notice('confirm');
$error = new Notice('error');

$input = new Input;

$output->addObserver($confirm);
$output->addObserver($error);

$location->addObserver($confirm);
$location->addObserver($error);

$output->admin = $user->name;


if ($input->gFetch && $user->isAdmin()) {

    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
    header('Cache-Control: no-cache, must-revalidate');
    header('Pragma: no-cache');

    switch ($input->gFetch) {
        case 'news':
            include 'partials/news.php';
            break;
        case 'test-dir':
            include 'partials/news.php';
            break;
    }

    exit;
}


if (!$settingsLoaded) {

    $error->add('The settings file for Punisher could not be found. Attempted to load: <b>' . ADMIN_PUNISH_SETTINGS . '</b>');
    $output->out();

}


if (!$user->isAdmin()) {
    $action = 'login';
}

if (!isset($adminDetails)) {
    $action = 'install';
}


$self = ADMIN_URI;

if ($user->isAdmin()) {
    $output->addNavigation('Home', $self);
    $output->addNavigation('Edit Settings', $self . '?settings');
    $output->addNavigation('View Logs', $self . '?logs');
    $output->addNavigation('BlockScript&reg;', $self . '?blockscript');
}


switch ($action) {

    case 'install':
        include 'install/install.php';
        break;

    case 'login':
        include 'partials/login.php';
        break;

    case 'logout':
        include 'partials/logout.php';
        break;

    case '':
        include 'partials/default.php';
        break;

    case 'settings':
        include 'partials/settings.php';
        break;

    case 'blockscript':
        include 'partials/blockscript.php';
        break;

    case 'logs':
        include 'partials/logs.php';
        break;
    case 'logs-view':
        include 'partials/logs-view.php';
        break;
    default:
        include 'partials/not-found.php';
}

$content = ob_get_contents();
ob_end_clean();
$output->addContent($content);
$output->out();
