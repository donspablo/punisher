<?php


$SETTINGS['license_key'] = '';


$SETTINGS['enable_blockscript'] = false;


$SETTINGS['asset'] = 'default';

$SETTINGS['plugins'] = 'facebook.com,google.com,hotmail.com,live.com,msn.com,myspace.com,twitter.com,yahoo.com,youtube.com,ytimg.com';

$SETTINGS['tmp_dir'] = PUNISH_ROOT . '/tmp/';


$SETTINGS['gzip_return'] = false;


$SETTINGS['ssl_warning'] = true;


$SETTINGS['override_javascript'] = false;


$SETTINGS['load_limit'] = 0;


$SETTINGS['footer_include'] = '';


$SETTINGS['path_info_urls'] = false;


$SETTINGS['unique_urls'] = false;

$SETTINGS['stop_hotlinking'] = true;


$SETTINGS['hotlink_domains'] = array();


$SETTINGS['enable_logging'] = false;


$SETTINGS['logging_destination'] = $SETTINGS['tmp_dir'] . 'logs/';


$SETTINGS['log_all'] = false;

$SETTINGS['whitelist'] = array();

$SETTINGS['blacklist'] = array();

$SETTINGS['ip_bans'] = array();


$SETTINGS['connection_timeout'] = 5;


$SETTINGS['transfer_timeout'] = 0;


$SETTINGS['max_filesize'] = 0;


$SETTINGS['download_speed_limit'] = 0;


$SETTINGS['resume_transfers'] = false;


$SETTINGS['queue_transfers'] = true;

$SETTINGS['cookies_on_server'] = false;


$SETTINGS['cookies_folder'] = $SETTINGS['tmp_dir'] . 'cookies/';


$SETTINGS['encode_cookies'] = false;

$SETTINGS['tmp_cleanup_interval'] = 0;


$SETTINGS['tmp_cleanup_logs'] = 0;


$SETTINGS['options']['encodeURL'] = array(
    'title' => 'Encrypt URL',
    'desc' => 'Encrypts the URL of the page you are viewing so that it does not contain the target site in plaintext.',
    'default' => true,
    'force' => false
);

$SETTINGS['options']['encodePage'] = array(
    'title' => 'Encrypt Page',
    'desc' => 'Helps avoid filters by encrypting the page before sending it and decrypting it with javascript once received.',
    'default' => false,
    'force' => false
);

$SETTINGS['options']['showForm'] = array(
    'title' => 'Show Form',
    'desc' => 'This provides a mini form at the top of each page to allow you to quickly jump to another site without returning to our homepage.',
    'default' => true,
    'force' => true
);

$SETTINGS['options']['allowCookies'] = array(
    'title' => 'Allow Cookies',
    'desc' => 'Cookies may be required on interactive websites (especially where you need to log in) but advertisers also use cookies to track your browsing habits.',
    'default' => true,
    'force' => false
);

$SETTINGS['options']['tempCookies'] = array(
    'title' => 'Force Temporary Cookies',
    'desc' => 'This option overrides the expiry date for all cookies and sets it to at the end of the session only - all cookies will be deleted when you shut your browser. (Recommended)',
    'default' => true,
    'force' => true
);

$SETTINGS['options']['stripTitle'] = array(
    'title' => 'Remove Page Titles',
    'desc' => 'Removes titles from proxied pages.',
    'default' => false,
    'force' => true
);

$SETTINGS['options']['stripJS'] = array(
    'title' => 'Remove Scripts',
    'desc' => 'Remove scripts to protect your anonymity and speed up page loads. However, not all sites will provide an HTML-only alternative. (Recommended)',
    'default' => true,
    'force' => false
);

$SETTINGS['options']['stripObjects'] = array(
    'title' => 'Remove Objects',
    'desc' => 'You can increase page load times by removing unnecessary Flash, Java and other objects. If not removed, these may also compromise your anonymity.',
    'default' => true,
    'force' => false
);


$SETTINGS['version'] = '1.0.1';
