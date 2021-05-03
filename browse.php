<?php

require 'inc/init.php';

if (count($adminDetails) === 0) {
    header("HTTP/1.1 302 Found");
    header("Location: punisher.php");
    exit;
}


define('DEBUG_MODE', 0);
define('CURL_LOG', 0);


if (CURL_LOG && ($fh = @fopen('curl.txt', 'w'))) {
    $toSet[CURLOPT_STDERR] = $fh;
    $toSet[CURLOPT_VERBOSE] = true;
}

header('Content-Type:');

header('Cache-Control:');
header('Last-Modified:');


switch (true) {

    case !empty($_GET['u']) && ($toLoad = deproxyURL($_GET['u'], true)):
        break;

    case !empty($_SERVER['PATH_INFO']) && ($toLoad = deproxyURL($_SERVER['PATH_INFO'], true)):
        break;

    default:
        redirect();
}

if (!preg_match('#^((https?)://(?:([a-z0-9-.]+:[a-z0-9-.]+)@)?([a-z0-9-.]+)(?::([0-9]+))?)(?:/|$)((?:[^?/]*/)*)([^?]*)(?:\?([^\#]*))?(?:\#.*)?$#i', $toLoad, $tmp)) {

    error('invalid_url', htmlentities($toLoad));

}

$URL = array(
    'scheme_host' => $tmp[1],
    'scheme' => $tmp[2],
    'auth' => $tmp[3],
    'host' => strtolower($tmp[4]),
    'domain' => strtolower(preg_match('#(?:^|\.)([a-z0-9-]+\.(?:[a-z.]{5,6}|[a-z]{2,}))$#', $tmp[4], $domain) ? $domain[1] : $tmp[4]), # Attempt to split off the subdomain (if any)
    'port' => $tmp[5],
    'path' => '/' . $tmp[6],
    'filename' => $tmp[7],
    'extension' => pathinfo($tmp[7], PATHINFO_EXTENSION),
    'query' => isset($tmp[8]) ? $tmp[8] : ''
);


$URL['href'] = str_replace(' ', '%20', $toLoad);

if ($URL['auth']) {
    $_SESSION['authenticate'][$URL['scheme_host']] = $URL['auth'];
}


if (preg_match('#^localhost#i', $URL['host'])) {
    error('banned_site', $URL['host']);
}

$host = $URL['host'];


if (preg_match('#^\d+$#', $host)) { # decimal IPs
    $host = implode('.', array($host >> 24 & 255, $host >> 16 & 255, $host >> 8 & 255, $host & 255));
}
if (preg_match('#^(0|10|127|169\.254|192\.168|172\.(?:1[6-9]|2[0-9]|3[01])|2[2-5][0-9])\.#', $host)) { # special use netblocks
    error('banned_site', $host);
}

if ($SETTINGS['stop_hotlinking'] && empty($_SESSION['no_hotlink'])) {

    $tmp = true;

    if (!empty($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'http') === 0) {

        foreach (array_merge((array)PUNISH_URL, $SETTINGS['hotlink_domains']) as $domain) {

            if (stripos($_SERVER['HTTP_REFERER'], $domain) !== false) {

                $tmp = false;
                break;
            }
        }
    }

    if ($tmp) {
        error('no_hotlink');
    }

}

$_SESSION['no_hotlink'] = true;

if (!empty($SETTINGS['whitelist'])) {

    $tmp = false;

    foreach ($SETTINGS['whitelist'] as $domain) {

        if (strpos($URL['host'], $domain) !== false) {

            $tmp = true;

        }

    }

    if (!$tmp) {
        error('banned_site', $URL['host']);
    }

}

if (!empty($SETTINGS['blacklist'])) {

    foreach ($SETTINGS['blacklist'] as $domain) {

        if (strpos($URL['host'], $domain) !== false) {

            error('banned_site', $URL['host']);

        }

    }

}


if ($URL['scheme'] == 'https' && $SETTINGS['ssl_warning'] && empty($_SESSION['ssl_warned']) && !HTTPS) {

    $_SESSION['return'] = currentURL();

    sendNoCache();

    echo loadTemplate('sslwarning.page');

    exit;

}


global $foundPlugin;
$plugins = explode(',', $SETTINGS['plugins']);
if ($foundPlugin = in_array($URL['domain'], $plugins)) {
    include(PUNISH_ROOT . '/plugins/' . $URL['domain'] . '.php');
}


if (!$SETTINGS['queue_transfers']) {

    session_write_close();

}


if (

    $SETTINGS['load_limit']


    && !in_array($URL['extension'], array('jpg', 'jpeg', 'png', 'gif', 'css', 'js', 'ico'))
) {


    if (!file_exists($file = $SETTINGS['tmp_dir'] . 'load.php') || !(include $file) || !isset($load, $lastChecked) || $lastChecked < $_SERVER['REQUEST_TIME'] - 60) {

        $load = (float)0;

        if (($uptime = @shell_exec('uptime')) && preg_match('#load average: ([0-9.]+),#', $uptime, $tmp)) {
            $load = (float)$tmp[1];

            file_put_contents($file, '<?php $load = ' . $load . '; $lastChecked = ' . $_SERVER['REQUEST_TIME'] . ';');
        }

    }

    if ($load > $SETTINGS['load_limit']) {
        error('server_busy'); # Show error
    }
}

$toSet[CURLOPT_CONNECTTIMEOUT] = $SETTINGS['connection_timeout'];

$toSet[CURLOPT_TIMEOUT] = $SETTINGS['transfer_timeout'];


$toSet[CURLOPT_SSL_VERIFYPEER] = false;
$toSet[CURLOPT_SSL_VERIFYHOST] = false;

$toSet[CURLOPT_HTTPHEADER][] = 'Expect:';

if (defined('CURLOPT_IPRESOLVE') && defined('CURL_IPRESOLVE_V4')) {
    $toSet[CURLOPT_IPRESOLVE][] = 'CURL_IPRESOLVE_V4';
}

if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {

    $toSet[CURLOPT_TIMECONDITION] = CURL_TIMECOND_IFMODSINCE;

    $toSet[CURLOPT_TIMEVALUE] = strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']);

}

if ($SETTINGS['resume_transfers'] && isset($_SERVER['HTTP_RANGE'])) {

    $toSet[CURLOPT_RANGE] = substr($_SERVER['HTTP_RANGE'], 6);

}

if ($SETTINGS['max_filesize'] && defined('CURLOPT_MAXFILESIZE')) {

    $toSet[CURLOPT_MAXFILESIZE] = $SETTINGS['max_filesize'];

}

$toSet[CURLOPT_DNS_CACHE_TIMEOUT] = 600;


if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
    $toSet[CURLOPT_HTTPHEADER][] = 'Accept-Language: ' . $_SERVER['HTTP_ACCEPT_LANGUAGE'];
}

if (isset($_SERVER['HTTP_ACCEPT'])) {
    $toSet[CURLOPT_HTTPHEADER][] = 'Accept: ' . $_SERVER['HTTP_ACCEPT'];
}

if (isset($_SERVER['HTTP_ACCEPT_CHARSET'])) {
    $toSet[CURLOPT_HTTPHEADER][] = 'Accept-Charset: ' . $_SERVER['HTTP_ACCEPT_CHARSET'];
}

if ($_SESSION['custom_browser']['user_agent']) {
    $toSet[CURLOPT_USERAGENT] = $_SESSION['custom_browser']['user_agent'];
}

if ($_SESSION['custom_browser']['referrer'] == 'real') {

    if (isset($_SERVER['HTTP_REFERER']) && $flag != 'norefer' && strpos($tmp = deproxyURL($_SERVER['HTTP_REFERER']), PUNISH_URL) === false) {
        $toSet[CURLOPT_REFERER] = $tmp;
    }

} else if ($_SESSION['custom_browser']['referrer']) {

    $toSet[CURLOPT_REFERER] = $_SESSION['custom_browser']['referrer'];

}

if ($flag == 'norefer') {
    $flag = '';
}

if (isset($_SESSION['authenticate'][$URL['scheme_host']])) {

    $toSet[CURLOPT_HTTPAUTH] = CURLAUTH_BASIC;
    $toSet[CURLOPT_USERPWD] = $_SESSION['authenticate'][$URL['scheme_host']];

}

if ($options['allowCookies']) {

    if ($SETTINGS['cookies_on_server']) {

        if ($s = checkTmpDir($SETTINGS['cookies_folder'], 'Deny from all')) {

            $toSet[CURLOPT_COOKIEFILE] = $toSet[CURLOPT_COOKIEJAR] = $SETTINGS['cookies_folder'] . punisher_session_id();

        }

    } else if (isset($_COOKIE[COOKIE_PREFIX])) {

        if ($SETTINGS['encode_cookies']) {

            foreach ($_COOKIE[COOKIE_PREFIX] as $attributes => $value) {

                $attributes = explode(' ', base64_decode($attributes));

                if (!isset($attributes[2])) {
                    continue;
                }

                list($domain, $path, $name) = $attributes;

                if (stripos($URL['host'], $domain) === false) {
                    continue;
                }

                if (stripos($URL['path'], $path) !== 0) {
                    continue;
                }


                $key = $path . $name;

                if (isset($toSend[$key]) && $toSend[$key]['path'] == $path && $toSend[$key]['domain'] > strlen($domain)) {

                    continue;

                }

                $value = base64_decode($value);


                $value = str_replace('!SEC', '', $value, $tmp);

                if ($tmp && $URL['scheme'] != 'https') {
                    continue;
                }


                # Everything checked and verified, add to $toSend for further processing later
                $toSend[$key] = array('path_size' => strlen($path), 'path' => $path, 'domain' => strlen($domain), 'send' => $name . '=' . $value);

            }

        } else {

            # Option (3): unencoded cookies stored client-side
            foreach ($_COOKIE[COOKIE_PREFIX] as $domain => $paths) {

                # $domain holds the domain (surprisingly) and $path is an array
                # of keys (paths) and more arrays (each child array of $path = one cookie)
                # e.g. Array('domain.com' => Array('/' => Array('cookie_name' => 'value')))

                # First check for domain match and skip to next domain if no match
                if (stripos($URL['host'], $domain) === false) {
                    continue;
                }

                # If conflicting cookies with same name and same path,
                # send the one with the more complete tail match. To do this we
                # need to know how long each match is/was so record domain length.
                $domainSize = strlen($domain);

                # Now look at all the available paths
                foreach ($paths as $path => $cookies) {

                    # Check for match and skip to next path if fail
                    if (stripos($URL['path'], $path) !== 0) {
                        continue;
                    }

                    # In final header, cookies are ordered with most specific path
                    # matches first so include the length of match in temp array
                    $pathSize = strlen($path);

                    # All cookies in $cookies array should be sent
                    foreach ($cookies as $name => $value) {

                        # Multiple cookies of the same name are permitted if different paths
                        # so use path AND name as the key in the temp array
                        $key = $path . $name;

                        # Check for existing cookie with same domain, same path and same name
                        if (isset($toSend[$key]) && $toSend[$key]['path'] == $path && $toSend[$key]['domain'] > $domainSize) {

                            # Conflicting cookies so ignore the one with the less complete tail match
                            # (i.e. the current one)
                            continue;

                        }

                        # Only send secure cookies on https connection - secure cookies marked by !SEC suffix
                        # so remove the suffix
                        $value = str_replace('!SEC', '', $value, $tmp);

                        # And if secure cookie but not https site, do not send
                        if ($tmp && $URL['scheme'] != 'https') {
                            continue;
                        }

                        # Add to $toSend for further processing later
                        $toSend[$key] = array('path_size' => $pathSize, 'path' => $path, 'domain' => $domainSize, 'send' => $name . '=' . $value);

                    }

                }

            }

        }

        # Ensure we have found cookies
        if (!empty($toSend)) {

            # Order by path specificity (as per Netscape spec)
            function compareArrays($a, $b)
            {
                return ($a['path_size'] > $b['path_size']) ? -1 : 1;
            }

            # Apply the sort to order by path_size descending
            uasort($toSend, 'compareArrays');

            # Go through the ordered array and generate the Cookie: header
            $tmp = '';

            foreach ($toSend as $cookie) {
                $tmp .= $cookie['send'] . '; ';
            }

            # Give the string to cURL
            $toSet[CURLOPT_COOKIE] = $tmp;

        }

        # And clear the toSend array
        unset($toSend);

    }

}


if (!empty($_POST)) {

    # enable backward compatibility with cURL's @ option for uploading files in PHP 5.5 and 5.6
    if (version_compare(PHP_VERSION, '5.5') >= 0) {
        $toSet[CURLOPT_SAFE_UPLOAD] = false;
    }

    # Attempt to get raw POST from the input wrapper
    if (!($tmp = file_get_contents('php://input'))) {

        # Raw data not available (probably multipart/form-data).
        # cURL will do a multipart post if we pass an array as the
        # POSTFIELDS value but this array can only be one deep.

        # Recursively flatten array to one level deep and rename keys
        # as firstLayer[second][etc]. Also apply the input decode to all
        # array keys.
        function flattenArray($array, $prefix = '')
        {

            # Start with empty array
            $stack = array();

            # Loop through the array to flatten
            foreach ($array as $key => $value) {

                # Decode the input name
                $key = inputDecode($key);

                # Determine what the new key should be - add the current key to
                # the prefix and surround in []
                $newKey = $prefix ? $prefix . '[' . $key . ']' : $key;

                if (is_array($value)) {

                    # If it's an array, recurse and merge the returned array
                    $stack = array_merge($stack, flattenArray($value, $newKey));

                } else {

                    # Otherwise just add it to the current stack
                    $stack[$newKey] = clean($value);

                }

            }

            # Return flattened
            return $stack;

        }

        $tmp = flattenArray($_POST);

        # Add any file uploads?
        if (!empty($_FILES)) {

            # Loop through and add the files
            foreach ($_FILES as $name => $file) {

                # Is this an array?
                if (is_array($file['tmp_name'])) {

                    # Flatten it - file arrays are in the slightly odd format of
                    # $_FILES['layer1']['tmp_name']['layer2']['layer3,etc.'] so add
                    # layer1 onto the start.
                    $flattened = flattenArray(array($name => $file['tmp_name']));

                    # And add all files to the post
                    foreach ($flattened as $key => $value) {
                        $tmp[$key] = '@' . $value;
                    }

                } else {

                    # Not another array. Check if the file uploaded successfully?
                    if (!empty($file['error']) || empty($file['tmp_name'])) {
                        continue;
                    }

                    # Add to array with @ - tells cURL to upload this file
                    $tmp[$name] = '@' . $file['tmp_name'];

                }

                # To do: rename the temp file to it's real name before
                # uploading it to the target? Otherwise, the target receives
                # the temp name instead of the original desired name
                # but doing this may be a security risk.

            }

        }

    }

    # Convert back to GET if required
    if (isset($_POST['convertGET'])) {

        # Remove convertGET from POST array and update our location
        $URL['href'] .= (empty($URL['query']) ? '?' : '&') . str_replace('convertGET=1', '', $tmp);

    } else {

        # Genuine POST so set the cURL post value
        $toSet[CURLOPT_POST] = 1;
        $toSet[CURLOPT_POSTFIELDS] = $tmp;

    }

}


if ($foundPlugin && function_exists('preRequest')) {
    preRequest();
}


class Request
{

    public $status = 0;

    public $headers = array();

    public $return;

    public $abort;

    public $error;

    public $parseType;

    public $sniff = false;

    private $forwardCookies = false;

    private $limitFilesize = 0;

    private $speedLimit = 0;

    private $URL;

    private $browsingOptions;

    private $curlOptions;

    public function __construct($curlOptions)
    {

        global $options, $SETTINGS;

        $curlOptions[CURLOPT_HEADERFUNCTION] = array(&$this, 'readHeader');
        $curlOptions[CURLOPT_WRITEFUNCTION] = array(&$this, 'readBody');

        if ($options['allowCookies'] && !$SETTINGS['cookies_on_server']) {
            $this->forwardCookies = $SETTINGS['encode_cookies'] ? 'encode' : 'normal';
        }

        if ($SETTINGS['max_filesize']) {
            $this->limitFilesize = $SETTINGS['max_filesize'];
        }

        if ($SETTINGS['download_speed_limit']) {
            $this->speedLimit = $SETTINGS['download_speed_limit'];
        }

        $this->browsingOptions = $options;
        $this->curlOptions = $curlOptions;

        set_time_limit($SETTINGS['transfer_timeout']);

        if (DEBUG_MODE) {
            $this->cookiesSent = isset($curlOptions[CURLOPT_COOKIE]) ? $curlOptions[CURLOPT_COOKIE] : (isset($curlOptions[CURLOPT_COOKIEFILE]) ? 'using cookie jar' : 'none');
            $this->postSent = isset($curlOptions[CURLOPT_POSTFIELDS]) ? $curlOptions[CURLOPT_POSTFIELDS] : '';
        }

    }

    public function go($URL)
    {

        $this->URL = $URL;

        $ch = curl_init($this->URL['href']);

        curl_setopt_array($ch, $this->curlOptions);

        curl_exec($ch);

        if (!$this->abort) {
            $this->error = curl_error($ch);
        }

        curl_close($ch);

        return $this->return;

    }


    public function readHeader($handle, $header)
    {

        if ($this->status == 0 || ($this->status == 100 && !strpos($header, ':'))) {
            $this->status = substr($header, 9, 3);
        }

        $parts = explode(':', $header, 2);

        if (isset($parts[1])) {

            $headerType = strtolower($parts[0]);

            $headerValue = trim($parts[1]);

            if ($headerType == 'set-cookie' && $this->forwardCookies) {

                $this->setCookie($headerValue);

            }

            $this->headers[$headerType] = $headerValue;

            $toForward = array('last-modified',
                'content-disposition',
                'content-type',
                'content-range',
                'content-language',
                'expires',
                'cache-control',
                'pragma');

            if (in_array($headerType, $toForward)) {
                header($header);
            }

        } else {

            if (($this->headers[] = trim($header)) == false) {

                $this->processHeaders();

                if ($this->abort) {
                    return -1;
                }

            }

        }

        return strlen($header);

    }

    private function setCookie($cookieString)
    {

        $cookieParts = explode(';', $cookieString);

        foreach ($cookieParts as $part) {

            $pair = explode('=', $part, 2);

            $pair[1] = isset($pair[1]) ? $pair[1] : '';

            if (!isset($cookieName)) {

                $cookieName = $pair[0];
                $cookieValue = $pair[1];

                continue;

            }

            $pair[0] = strtolower($pair[0]);

            if ($pair[1]) {

                $attr[ltrim($pair[0])] = $pair[1];

            } else {

                $attr[] = $pair[0];

            }

        }

        if (isset($attr['expires'])) {

            $expires = strtotime($attr['expires']);

        } else if (isset($attr['max-age'])) {

            $expires = $_SERVER['REQUEST_TIME'] + $attr['max-age'];

        } else {

            $expires = 0;

        }

        if ($this->browsingOptions['tempCookies'] && $expires > $_SERVER['REQUEST_TIME']) {
            $expires = 0;
        }

        if (!isset($attr['path'])) {
            $attr['path'] = '/';
        }

        if (isset($attr['domain'])) {

            if (stripos($attr['domain'], $this->URL['domain']) === false) {

                return;

            }

            if ($attr['domain'][0] == '.') {
                $attr['domain'] = substr($attr['domain'], 1);
            }

        } else {

            $attr['domain'] = $this->URL['domain'];

        }

        $sentSecure = in_array('secure', $attr);

        if ($sentSecure) {
            $cookieValue .= '!SEC';
        }

        $secure = HTTPS && $sentSecure;

        $httponly = in_array('httponly', $attr) && version_compare(PHP_VERSION, '5.2.0', '>=') ? true : false;

        $name = COOKIE_PREFIX . '[' . $attr['domain'] . '][' . $attr['path'] . '][' . inputEncode($cookieName) . ']';
        $value = $cookieValue;

        if ($this->forwardCookies == 'encode') {

            $name = COOKIE_PREFIX . '[' . urlencode(base64_encode($attr['domain'] . ' ' . $attr['path'] . ' ' . urlencode($cookieName))) . ']';
            $value = base64_encode($value);

        }

        if ($httponly) {

            setcookie($name, $value, $expires, '/', '', $secure, true);

        } else {

            setcookie($name, $value, $expires, '/', '', $secure);

        }

        if (DEBUG_MODE) {

            $this->cookiesReceived[] = array('name' => $cookieName,
                'value' => $cookieValue,
                'attributes' => $attr);

        }

    }

    private function processHeaders()
    {

        static $runOnce;

        if (isset($runOnce)) {
            return;
        }

        $runOnce = true;

        header(' ', true, $this->status);

        switch (true) {

            case isset($this->headers['location']):

                $this->abort = 'redirect';

                return;

            case $this->status == 304:

                $this->abort = 'not_modified';

                return;

            case $this->status == 401:

                $this->abort = 'auth_required';

                return;

            case $this->status >= 400:

                $this->abort = 'http_status_error';

                return;

            case isset($this->headers['content-length']) && $this->limitFilesize && $this->headers['content-length'] > $this->limitFilesize:

                $this->abort = 'filesize_limit';

                return;

        }

        if (isset($this->headers['content-type'])) {

            $types = array(
                'text/javascript' => 'javascript',
                'text/ecmascript' => 'javascript',
                'application/javascript' => 'javascript',
                'application/x-javascript' => 'javascript',
                'application/ecmascript' => 'javascript',
                'application/x-ecmascript' => 'javascript',
                'text/livescript' => 'javascript',
                'text/jscript' => 'javascript',
                'application/xhtml+xml' => 'html',
                'text/html' => 'html',
                'text/css' => 'css',

            );

            global $charset;
            $content_type = explode(';', $this->headers['content-type'], 2);
            $mime = isset($content_type[0]) ? trim($content_type[0]) : '';
            if (isset($content_type[1])) {
                $charset = preg_match('#charset\s*=\s*([^"\'\s]*)#is', $content_type[1], $tmp, PREG_OFFSET_CAPTURE) ? $tmp[1][0] : null;
            }

            if (isset($types[$mime])) {
                $this->parseType = $types[$mime];
            }

            if (!preg_match('#^(application|audio|image|text|video)/#i', $mime)) {
                header('Content-Type: text/plain');
            }

        } else {

            $this->sniff = true;

        }

        if (!isset($this->headers['content-disposition']) && $this->URL['filename']) {
            header('Content-Disposition: filename="' . $this->URL['filename'] . '"');
        }

        if ($this->limitFilesize && isset($this->headers['content-length'])) {
            $this->limitFilesize = 0;
        }

    }

    public function readBody($handle, $data)
    {

        static $first;

        if (!isset($first)) {

            $this->firstBody($data);

            $first = false;

        }

        $length = strlen($data);

        if ($this->speedLimit) {

            $time = $length / $this->speedLimit; # [s]

            usleep(round($time * 1000000));
        }

        if ($this->limitFilesize) {

            static $downloadedBytes;

            if (!isset($downloadedBytes)) {
                $downloadedBytes = 0;
            }

            $downloadedBytes += $length;

            if ($downloadedBytes > $this->limitFilesize) {

                $this->abort = 'filesize_limit';
                return -1;

            }

        }

        if ($this->parseType) {

            $this->return .= $data;

        } else {
            echo $data; # No parsing so print immediately
        }

        return $length;

    }

    private function firstBody($data)
    {

        if ($this->sniff) {
            if (stripos($data, '<html') !== false && stripos($data, '<head') !== false) {
                header('Content-Type: text/html');
                $this->parseType = 'html';
            } else {
                header('Content-Type: text/plain');
            }
        }

        if (!$this->parseType && isset($this->headers['content-length'])) {
            header('Content-Length: ' . $this->headers['content-length']);
        }

    }

}

$fetch = new Request($toSet);

$document = $fetch->go($URL);


if ($fetch->abort) {

    switch ($fetch->abort) {

        case 'redirect':

            $location = proxyURL($fetch->headers['location'], $flag);

            if (DEBUG_MODE) {
                $fetch->redirected = '<a href="' . $location . '">' . $fetch->headers['location'] . '</a>';
                break;
            }

            header('Location: ' . $location, true, $fetch->status);
            exit;

        case 'not_modified':
            header("HTTP/1.1 304 Not Modified", true, 304);
            exit;

        case 'auth_required':

            if (!isset($fetch->headers['www-authenticate'])) {
                break;
            }

            $realm = preg_match('#\brealm="([^"]*)"#i', $fetch->headers['www-authenticate'], $tmp) ? $tmp[1] : '';

            sendNoCache();

            $tmp = array('site' => $URL['scheme_host'],
                'realm' => $realm,
                'return' => currentURL());

            echo loadTemplate('authenticate.page', $tmp);
            exit;

        case 'filesize_limit':

            if (!$fetch->parseType) {
                exit;
            }

            error('file_too_large', round($SETTINGS['max_filesize'] / 1024 / 1024, 3));
            exit;

        case 'http_status_error':

            $explain = isset($httpErrors[$fetch->status]) ? $httpErrors[$fetch->status] : '';

            error('http_error', $fetch->status, trim(substr($fetch->headers[0], 12)), $explain);
            exit;

        default:
            error('cURL::$abort (' . $fetch->abort . ')');
    }

}

if ($fetch->error) {

    error('curl_error', $fetch->error);

}

if ($flag == 'ajax' || ($fetch->parseType && strlen($document) < 10)) {

    if ($fetch->parseType) {
        echo $document;
    }

    exit;
}

if ($fetch->parseType) {

    if (isset($fetch->headers['content-encoding']) && $fetch->headers['content-encoding'] == 'gzip') {
        if (function_exists('gzinflate')) {
            unset($fetch->headers['content-encoding']);
            $document = gzinflate(substr($document, 10, -8));
        }
    }

    if ($foundPlugin && function_exists('preParse')) {
        $document = preParse($document, $fetch->parseType);
    }

    require PUNISH_ROOT . '/inc/parser.php';

    $parser = new parser($options, $jsFlags);

    switch ($fetch->parseType) {

        case 'html':

            $inject =
            $footer =
            $insert = false;

            if ($flag != 'frame' && $fetch->sniff == false) {

                if ($options['showForm']) {
                    $toShow = array();

                    foreach ($SETTINGS['options'] as $name => $details) {

                        if (!empty($details['force'])) {
                            continue;
                        }

                        $toShow[] = array(
                            'name' => $name,
                            'title' => $details['title'],
                            'checked' => $options[$name] ? ' checked="checked" ' : ''
                        );

                    }

                    if ($options['encodePage']) {
                        $vars['url'] = ''; # Currently visited URL
                    } else {
                        $vars['url'] = $URL['href']; # Currently visited URL
                    }
                    $vars['toShow'] = $toShow; # Options
                    $vars['return'] = rawurlencode(currentURL()); # Return URL (for clearcookies) (i.e. current URL proxied)
                    $vars['proxy'] = PUNISH_URL; # Base URL for proxy directory

                    $insert = loadTemplate('framedForm.inc', $vars);

                    if ($SETTINGS['override_javascript']) {
                        $insert = '<script type="text/javascript">disableOverride();</script>'
                            . $insert
                            . '<script type="text/javascript">enableOverride();</script>';
                    }
                }

                $footer = $SETTINGS['footer_include'];

            }

            if ($fetch->sniff == false) {
                $inject = true;
            }

            $document = $parser->HTMLDocument($document, $insert, $inject, $footer);

            break;

        case 'css':

            $document = $parser->CSS($document);

            break;

        case 'javascript':

            $document = $parser->JS($document);

            break;

    }

    if ($foundPlugin && function_exists('postParse')) {
        $document = postParse($document, $fetch->parseType);
    }

    if (!DEBUG_MODE) {

        if ($SETTINGS['gzip_return'] && isset($_SERVER['HTTP_ACCEPT_ENCODING']) && strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false && extension_loaded('zlib') && !ini_get('zlib.output_compression')) {

            header('Content-Encoding: gzip');
            echo gzencode($document, 3);

        } else {

            echo $document;

        }

    }

}

if (DEBUG_MODE) {

    $fetch->return = $document;
    echo '<pre>', print_r($fetch, 1), '</pre>';
}

if ($SETTINGS['enable_logging'] && ($SETTINGS['log_all'] || $fetch->parseType == 'html')) {

    if (checkTmpDir($SETTINGS['logging_destination'], 'Deny from all')) {

        $file = $SETTINGS['logging_destination'] . '/' . date('Y-m-d') . '.log';

        $write = str_pad($_SERVER['REMOTE_ADDR'] . ', ', 17) . date('d/M/Y:H:i:s O') . ', ' . $URL['href'] . "\r\n";

        file_put_contents($file, $write, FILE_APPEND);

    }

}
