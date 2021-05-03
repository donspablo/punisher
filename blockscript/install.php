<?php


// Copyright 2010-2020 UpsideOut, Inc. All rights reserved.
// Use of this software is subject to the terms and conditions of the Software License Agreement
// https://www.blocked.com/license.php


error_reporting(0);
ini_set('display_errors', 0);

$blockscript_dir = dirname(__FILE__) . '/';
$detector_path = $blockscript_dir . 'detector.php';
$version_php = function_exists('phpversion') ? phpversion() : '1';
$version_ioncube = function_exists('ioncube_loader_version') ? ioncube_loader_version() : '1';
if ($_SERVER['HTTP_HOST'] && strpos($_SERVER['HTTP_HOST'], '.') !== false) {
    $hostname = $_SERVER['HTTP_HOST'];
} elseif ($_SERVER['SERVER_NAME'] && strpos($_SERVER['SERVER_NAME'], '.') !== false) {
    $hostname = $_SERVER['SERVER_NAME'];
} elseif ($_SERVER['SERVER_ADDR'] && strpos($_SERVER['SERVER_ADDR'], '.') !== false) {
    $hostname = $_SERVER['SERVER_ADDR'];
} else {
    $hostname = 'unknown';
}

if (!function_exists('stripos')) {
    function stripos($haystack, $needle, $offset = 0)
    {
        return strpos(strtolower($haystack), strtolower($needle), $offset);
    }
}

// check if ionCube is installed
if (!function_exists('ioncube_loader_version')) {
    print_message('<b>Warning:</b> ionCube is not installed. ionCube is required to run BlockScript. Please <a href="http://www.ioncube.com/loaders.php" target="_blank">download latest version of the ionCube Loaders</a> for your server. You can refer to the <a href="http://www.ioncube.com/loader_installation.php" target="_blank">ionCube Loader Installation Manual</a> for instructions and help.');
}

// check permissions on detector.php
if (!is_writable($detector_path)) {
    print_message('<b>Warning:</b> Permissions error: detector.php file is not writable. Please set permissions (CHMOD 0777 or 0755 if running under suPHP) to allow PHP to write to <i>' . $detector_path . '</i>');
}

// check permissions on tmp directory
if (!is_writable($blockscript_dir . 'tmp')) {
    print_message('<b>Warning:</b> Permissions error: tmp directory is not writable. Please set permissions (CHMOD 0777 or 0755 if running under suPHP) to allow PHP to write to <i>' . $blockscript_dir . 'tmp</i>');
}

// check if detector.php is already installed and up-to-date
if (filesize($detector_path) > 102400) {
    $checksum_o = md5_file($detector_path);
    $checksum_n = trim(get_url('https://www.blockscript.com/vh.php?d=1&hostname=' . urlencode($hostname) . '&php=' . urlencode($version_php) . '&ion=' . urlencode($version_ioncube)));
    if ($checksum_o == $checksum_n) {
        print_message('BlockScript is already installed and up-to-date. <a href="/blockscript/detector.php?blockscript=setup" target="_blank">Click here</a> to access the control panel.');
    }
}

$update_file = $blockscript_dir . 'tmp/detector_update.txt';
$update_code = get_url('https://www.blockscript.com/download.php?d=1&hostname=' . urlencode($hostname) . '&php=' . urlencode($version_php) . '&ion=' . urlencode($version_ioncube));
sleep(1);
if (strlen($update_code) > 102400) {
    write_to_file($update_file, 'wb', $update_code);
    if (!file_exists($update_file) || filesize($update_file) < 102400) {
        print_message('<b>Warning:</b> Install process failed. Please <a href="https://www.blocked.com/contact.php" target="_blank">contact BlockScript support</a> for assistance.');
    }

    $old_perms = octdec(octal_fileperms($detector_path));
    copy($update_file, $detector_path);
    chmod($detector_path, $old_perms);
    unlink($update_file);
}

redirect_to('/blockscript/detector.php?blockscript=setup');


function get_url($url)
{
    $transports = array();
    $transports_ssl = array();
    $ssl_request = preg_match('#^https://#i', $url) ? true : false;
    $curl_version = array();

    if (function_exists('curl_exec')) {
        $transports[] = 'curl';
        if ($ssl_request && function_exists('curl_version')) {
            $curl_version = curl_version();
            if (is_array($curl_version)) {
                if (in_array('https', $curl_version['protocols'])) {
                    $transports_ssl[] = 'curl';
                }
            } else {
                if (stripos($curl_version, 'OpenSSL') !== false) {
                    $transports_ssl[] = 'curl';
                }
            }
        }
    }

    if (function_exists('fopen')) {
        if (ini_get('allow_url_fopen') != false) {
            $transports[] = 'fopen';
            if ($ssl_request) {
                if (extension_loaded('openssl') && function_exists('openssl_sign')) {
                    $transports_ssl[] = 'fopen';
                }
            }
        }
    }

    if (function_exists('fsockopen')) {
        $transports[] = 'fsockopen';
        if ($ssl_request) {
            if (extension_loaded('openssl') && function_exists('openssl_sign')) {
                $transports_ssl[] = 'fsockopen';
            }
        }
    }

    if (defined('OPENSSL_VERSION_NUMBER')) {
        if (OPENSSL_VERSION_NUMBER < 0x1000100) {
            unset($transports_ssl);
        }
    }

    if ($ssl_request && mycount($transports_ssl) > 0) {
        $method = $transports_ssl[0];
    } elseif ($ssl_request) {
        $ssl_request = false;
        $url = preg_replace("#^https://#i", "http://", $url);
        $method = $transports[0];
    } else {
        $method = $transports[0];
    }
    if (!$method) {
        return false;
    }

    switch ($method) {
        case 'curl':
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            if (isset($curl_version['features'])) {
                if ($curl_version['features'] & constant('CURLOPT_USERAGENT')) {
                    curl_setopt($ch, CURLOPT_USERAGENT, 'BlockScript installer');
                }
                if ($curl_version['features'] & constant('CURLOPT_BINARYTRANSFER')) {
                    curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
                }
            }
            $header[] = "Accept: text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5";
            $header[] = "Cache-Control: max-age=0";
            $header[] = "Connection: keep-alive";
            $header[] = "Keep-Alive: 300";
            $header[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
            $header[] = "Accept-Language: en-us,en;q=0.5";
            $header[] = "Pragma: ";
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 15);
            curl_setopt($ch, CURLOPT_MAXREDIRS, 7);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_FAILONERROR, true);
            if ($ssl_request) {
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            }
            $data = curl_exec($ch);
            if ($data === false) {
                $data = curl_error($ch);
            }
            curl_close($ch);
            return $data;
            break;

        case 'fsockopen':
            if (preg_match_all('#^[a-z]+://([^/]+)(.*?)$#i', $url, $matches, PREG_SET_ORDER)) {
                $host = $matches[0][1];
                $path = strlen($matches[0][2]) > 1 ? $matches[0][2] : '/';
                if ($ssl_request) {
                    $fshost = 'ssl://' . $host;
                    $port = 443;
                } else {
                    $fshost = $host;
                    $port = 80;
                }
            }
            $fp = fsockopen($fshost, $port, $errno, $errstr, 15);
            if (!$fp) {
                fclose($fp);
                return false;
            } else {
                fwrite($fp, "GET $path HTTP/1.0\r\nHost: $host\r\nConnection: Close\r\n\r\n");
                $data = '';
                $got_header = 0;
                while (!feof($fp)) {
                    $line = fgets($fp, 1024);
                    if ($line == "\r\n" && $got_header == 0) {
                        $got_header = 1;
                        continue;
                    }
                    if ($got_header) {
                        $data .= $line;
                    }
                }
                fclose($fp);
                return $data;
            }
            break;

        default:
        case 'fopen':
            if ($fh = @fopen($url, 'r')) {
                $data = '';
                while (!feof($fh)) {
                    $data .= fread($fh, 1024);
                }
                fclose($fh);
                return $data;
            } else {
                fclose($fh);
                return false;
            }
            break;
    }
    return false;
}

function mycount($x)
{
    return is_array($x) ? count($x) : 0;
}

function write_to_file($file, $mode, $data)
{
    $old_perms = file_exists($file) ? octdec(octal_fileperms($file)) : octdec(octal_fileperms(dirname($file)));
    $fh = fopen($file, $mode);
    if ($fh === false) {
        return;
    }
    fwrite($fh, $data);
    fclose($fh);
    chmod($file, $old_perms);
}

function octal_fileperms($file)
{
    return substr(sprintf('%o', fileperms($file)), -4);
}

function redirect_to($url)
{
    header("Expires: Mon, 23 Jul 1993 05:00:00 GMT");
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
    header("Cache-Control: no-store, no-cache, must-revalidate");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
    header('Location: ' . $url);
    exit;
}

function print_message($message)
{
    $logo = preg_match('#\b(Opera|Safari|AppleWebKit|Camino|KHTML|MSIE 8|Trident/4)\b#i', $_SERVER['HTTP_USER_AGENT']) ? 'data:image/png;base64,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' : 'https://www.blocked.com/images/logo.png';
    $this_year = date('Y');

    header("Expires: Mon, 23 Jul 1993 05:00:00 GMT");
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
    header("Cache-Control: no-store, no-cache, must-revalidate");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
    echo <<<EOF
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
<head>
<title>BlockScript Install Error</title>
<meta name="robots" content="noindex,follow,noarchive">

<style>
	.sep { border-bottom: 1px black dotted; }
</style>

</head>
<body bgcolor="#EEEEEE" text="#000000" link="#CC0000" vlink="#CC0000" alink="#CC0000">

<table border="0" cellspacing="0" cellpadding="0" width="70%" align="center">
	<tr>
		<td valign="top">
			<div align="center"><a href="https://www.blocked.com/" target="_blank"><img src="$logo" border="0" alt="" width="467" height="60"></a></div>
			<br><br>

			<table border="0" cellspacing="1" cellpadding="1" width="100%" bgcolor="#CC0000">
				<tr>
					<td bgcolor="#FFFFFF" style="padding:2px;">
						$message
					</td>
				</tr>
			</table>
			<br><br>

		</td>
	</tr>
	<tr>
		<td valign="top">
			Copyright &copy; 2010-$this_year <a href="http://upsideout.com/" target="_blank">UpsideOut, Inc.</a> All Rights Reserved. <a href="https://www.blocked.com/license.php" target="_blank">Software License Agreement</a>.
		</td>
	</tr>
</table>

</body>
</html>
EOF;

    exit;
}
