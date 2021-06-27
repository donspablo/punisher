<?php

        if (!is_writable(ADMIN_PUNISH_SETTINGS)) {
            $error->add('The settings file is not writable. You will not be able to save any changes. Please set permissions to allow PHP to write to <b>' . realpath(ADMIN_PUNISH_SETTINGS) . '</b>');
            $tpl->disabled = ' disabled="disabled"';
        }

        $options = simplexml_load_string('<?xml version="1.0" encoding="UTF-8"?><options><section name="Special Options" type="settings"><option key="license_key" type="string" input="text" styles="wide-input"><title>Punisher License key</title><default>\'\'</default><desc>If you have purchased a license, please enter your license key here. Leave blank if you don\'t have a license.</desc></option><option key="enable_blockscript" type="bool" input="radio"><title>Enable BlockScript</title><default>false</default><desc>BlockScript is security software which protects websites and empowers webmasters to stop unwanted traffic.</desc></option></section><section name="Installation Options" type="settings"><option key="asset" type="string" input="select"><title>Asset</title><default>\'default\'</default><desc>Theme/skin to use. This should be the name of the appropriate folder inside the /assets/ folder.</desc><generateOptions eval="true"><![CDATA[/* Check the dir exists */$assetDir = PUNISH_ROOT . \'/assets/\';if ( ! is_dir($assetDir) ) {return false;?>/* Load folders from /assets/ */$dirs = scandir($assetDir);/* Loop through to create options string */$options = \'\';foreach ( $dirs as $dir ) {/* Ignore dotfiles */if ( $dir[0] == \'.\' ) {continue;?>/* Add if this is valid asset */if ( file_exists($assetDir . $dir . \'/main.php\') ) {/* Make selected if this is our current asset */$selected =	( isset($currentValue) && $currentValue == $dir ) ? \' selected="selected"\' : \'\';/* Add option */$options .= "<option<?=$selected?>><?=$dir?></option>";}}return $options;]]></generateOptions></option><option key="plugins" type="string" input="text" styles="wide-input" readonly="readonly"><title>Register Plugins</title><default></default><desc>Run plugins on these websites</desc><toDisplay eval="true"><![CDATA[ if ($handle = opendir(PUNISH_ROOT."/plugins")) {while (($plugin=readdir($handle))!==false) {if (preg_match(\'#\.php$#\', $plugin)) <?=$plugin = preg_replace("#\.php$#", "", $plugin);$plugins[] = $plugin;}}closedir($handle);$plugin_list = implode(",", $plugins);} return $plugin_list; ]]></toDisplay><afterField>Auto-generated from plugins directory. Do not edit!</afterField></option><option key="tmp_dir" type="string" input="text" styles="wide-input"><title>Temporary directory</title><default>PUNISH_ROOT . \'/tmp/\'</default><desc>Temporary directory used by the script. Many features require write permission to the temporary directory. Ensure this directory exists and is writable for best performance.</desc><relative to="PUNISH_ROOT" desc="root proxy folder" /><isDir /></option><option key="gzip_return" type="bool" input="radio"><title>Use GZIP compression</title><default>false</default><desc>Use GZIP compression when sending pages back to the user. This reduces bandwidth usage but at the cost of increased CPU load.</desc></option><option key="ssl_warning" type="bool" input="radio"><title>SSL warning</title><default>true</default><desc>Warn users before browsing a secure site if on an insecure connection. This option has no effect if your proxy is on https.</desc></option><option key="override_javascript" type="bool" input="radio"><title>Override native javascript</title><default>false</default><desc>The fastest and most reliable method of ensuring javascript is properly proxied is to override the native javascript functions with our own. However, this may interfere with any other javascript added to the page, such as ad codes.</desc></option><option key="load_limit" type="float" input="text" styles="small-input"><title>Load limiter</title><default>0</default><desc>This option fetches the server load and stops the script serving pages whenever the server load goes over the limit specified. Set to 0 to disable this feature.</desc><afterField eval="true"><![CDATA[/* Attempt to find the load */$load = ( ($uptime = @shell_exec(\'uptime\')) && preg_match(\'#load average: ([0-9.]+),#\', $uptime, $tmp) ) ? (float) $tmp[1] : false;if ( $load === false ) {return \'<span class="error-color">Feature unavailable here</span>. Failed to find current server load.\';} else {return \'<span class="ok-color">Feature available here</span>. Current load: \' . $load;}]]></afterField></option><option key="footer_include" type="string" input="textarea" styles="wide-input"><title>Footer include</title><default>\'\'</default><desc>Anything specified here will be added to the bottom of all proxied pages just before the <![CDATA[</body>]]> tag.</desc><toDisplay eval="true"><![CDATA[ return htmlentities($currentValue); ]]></toDisplay></option></section><section name="URL Encoding Options" type="settings"><option key="path_info_urls" type="bool" input="radio"><title>Use path info</title><default>false</default><desc>Formats URLs as browse.php/aHR0... instead of browse.php?u=aHR0... Path info may not be available on all servers.</desc></option></section><section name="Hotlinking" type="settings"><option key="stop_hotlinking" type="bool" input="radio"><title>Prevent hotlinking</title><default>true</default><desc>This option prevents users &quot;hotlinking&quot; directly to a proxied page and forces all users to first visit the index page. Note: hotlinking is also prevented when the &quot;Encrypt URL&quot; option is enabled.</desc></option><option key="hotlink_domains" type="array" input="textarea" styles="wide-input"><title>Allow hotlinking from</title><default>array()</default><desc>If the above option is enabled, you can add individual referrers that are allowed to bypass the hotlinking protection. Note: hotlinking is also prevented when the &quot;Encrypt URL&quot; option is enabled.</desc><toDisplay eval="true"><![CDATA[ return implode("\r\n", $currentValue); ]]></toDisplay><toStore eval="true"><![CDATA[ $value = str_replace("\r", "\n", $value);$value=preg_replace("#\n+#", "\n", $value);return array_filter(explode("\n", $value));]]></toStore><afterField>Enter one domain per line</afterField></option></section><section name="Logging" type="settings"><comment><![CDATA[<p>You may be held responsible for requests from your proxy\'s IP address. You can use logs to record the decrypted URLs of pages visited by users in case of illegal activity undertaken through your proxy.</p>]]></comment><option key="enable_logging" type="bool" input="radio"><title>Enable logging</title><default>false</default><desc>Enable/disable the logging feature. If disabled, skip the rest of this section.</desc></option><option key="logging_destination" type="string" input="text" styles="wide-input"><title>Path to log folder</title><default>$SETTINGS[\'tmp_dir\']	. \'logs/\'</default><desc>Enter a destination for log files. A new log file will be created each day in the directory specified. The directory must be writable. To protect against unauthorized access, place the log folder above your webroot.</desc><relative to="$SETTINGS[\'tmp_dir\']" desc="temporary directory" /><isDir /></option><option key="log_all" type="bool" input="radio"><title>Log all requests</title><default>false</default><desc>You can avoid huge log files by only logging requests for .html pages, as per the default setting. If you want to log all requests (images, etc.) as well, enable this.</desc></option></section><section name="Website access control" type="settings"><comment><![CDATA[<p>You can restrict access to websites through your proxy with either a whitelist or a blacklist:</p><ul class="black"><li>Whitelist: any site that <strong>is not</strong> on the list will be blocked.</li><li>Blacklist: any site that <strong>is</strong> on the list will be blocked</li></ul>]]></comment><option key="whitelist" type="array" input="textarea" styles="wide-input"><title>Whitelist</title><default>array()</default><desc>Block everything except these websites</desc><toDisplay eval="true"><![CDATA[ return implode("\r\n", $currentValue); ]]></toDisplay><toStore eval="true"><![CDATA[ $value = str_replace("\r", "\n", $value);$value=preg_replace("#\n+#", "\n", $value);return array_filter(explode("\n", $value));]]></toStore><afterField>Enter one domain per line</afterField></option><option key="blacklist" type="array" input="textarea" styles="wide-input"><title>Blacklist</title><default>array()</default><desc>Block these websites</desc><toDisplay eval="true"><![CDATA[ return implode("\r\n", $currentValue); ]]></toDisplay><toStore eval="true"><![CDATA[ $value = str_replace("\r", "\n", $value);$value=preg_replace("#\n+#", "\n", $value);return array_filter(explode("\n", $value));]]></toStore><afterField>Enter one domain per line</afterField></option></section><section name="User access control" type="settings"><comment><![CDATA[<p>You can ban users from accessing your proxy by IP address. You can specify individual IP addresses or IP address ranges in the following formats:</p><ul class="black"><li>127.0.0.1</li><li>127.0.0.1-127.0.0.5</li><li>127.0.0.1/255.255.255.255</li><li>192.168.17.1/16</li><li>189.128/11</li></ul>]]></comment><option key="ip_bans" type="array" input="textarea" styles="wide-input"><title>IP bans</title><default>array()</default><toDisplay eval="true"><![CDATA[ return implode("\r\n", $currentValue); ]]></toDisplay><toStore eval="true"><![CDATA[ $value = str_replace("\r", "\n", $value);$value=preg_replace("#\n+#", "\n", $value);return array_filter(explode("\n", $value));]]></toStore><afterField>Enter one IP address or IP address range per line</afterField></option></section><section name="Transfer options" type="settings"><option key="connection_timeout" type="int" input="text" styles="small-input" unit="seconds"><title>Connection timeout</title><default>5</default><desc>Time to wait for while establishing a connection to the target server. If the connection takes longer, the transfer will be aborted.</desc><afterField>Use 0 for no limit</afterField></option><option key="transfer_timeout" type="int" input="text" styles="small-input" unit="seconds"><title>Transfer timeout</title><default>15</default><desc>Time to allow for the entire transfer. You will need a longer time limit to download larger files.</desc><afterField>Use 0 for no limit</afterField></option><option key="max_filesize" type="int" input="text" styles="small-input" unit="MB"><title>Filesize limit</title><default>0</default><desc>Preserve bandwidth by limiting the size of files that can be downloaded through your proxy.</desc><toDisplay>return $currentValue ? round($currentValue/(1024*1024), 2) : 0;</toDisplay><toStore>return $value*1024*1024;</toStore><afterField>Use 0 for no limit</afterField></option><option key="download_speed_limit" type="int" input="text" styles="small-input" unit="KB/s"><title>Download speed limit</title><default>0</default><desc>Preserve bandwidth by limiting the speed at which files are downloaded through your proxy. Note: if limiting download speed, you may need to increase the transfer timeout to compensate.</desc><toDisplay>return $currentValue ? round($currentValue/(1024), 2) : 0;</toDisplay><toStore>return $value*1024;</toStore><afterField>Use 0 for no limit</afterField></option><option key="resume_transfers" type="bool" input="radio"><title>Resume transfers</title><default>false</default><desc>This forwards any requested ranges from the client and this makes it possible to resume previous downloads. Depending on the &quot;Queue transfers&quot; option below, it may also allow users to download multiple segments of a file simultaneously.</desc></option><option key="queue_transfers" type="bool" input="radio"><title>Queue transfers</title><default>true</default><desc>You can limit use of your proxy to allow only one transfer at a time per user. Disable this for faster browsing.</desc></option></section><section name="Cookies" type="settings"><comment><![CDATA[<p>All cookies must be sent to the proxy script. The script can then choose the correct cookies to forward to the target server. However there are finite limits in both the client\'s storage space and the size of the request Cookie: header that the server will accept. For prolonged browsing, you may wish to store cookies server side to avoid this problem.</p><br><p>This has obvious privacy issues - if using this option, ensure your site clearly states how it handles cookies and protect the cookie data from unauthorized access.</p>]]></comment><option key="cookies_on_server" type="bool" input="radio"><title>Store cookies on server</title><default>false</default><desc>If enabled, cookies will be stored in the folder specified below.</desc></option><option key="cookies_folder" type="string" input="text" styles="wide-input"><title>Path to cookie folder</title><default>$SETTINGS[\'tmp_dir\']	 . \'cookies/\'</default><desc>If storing cookies on the server, specify a folder to save the cookie data in. To protect against unauthorized access, place the cookie folder above your webroot.</desc><relative to="$SETTINGS[\'tmp_dir\']" desc="temporary directory" /><isDir /></option><option key="encode_cookies" type="bool" input="radio"><title>Encode cookies</title><default>false</default><desc>You can encode cookie names, domains and values with this option for optimum privacy but at the cost of increased server load and larger cookie sizes. This option has no effect if storing cookies on server.</desc></option></section><section name="Maintenance" type="settings"><option key="tmp_cleanup_interval" type="float" input="text" styles="small-input" unit="hours"><title>Cleanup interval</title><default>48</default><desc>How often to clear the temporary files created by the script?</desc><afterField>Use 0 to disable</afterField></option><option key="tmp_cleanup_logs" type="float" input="text" styles="small-input" unit="days"><title>Keep logs for</title><default>30</default><desc>When should old log files be deleted? This option has no effect if the above option is disabled.</desc><afterField>Use 0 to never delete logs</afterField></option></section><section type="user" name="User Configurable Options"><option key="encodeURL" default="true" force="false"><title>Encrypt URL</title><desc>Encrypts the URL of the page you are viewing for increased privacy. Note: this option is intended to obscure URLs and does not provide security. Use SSL for actual security.</desc></option><option key="encodePage" default="false" force="false"><title>Encrypt Page</title><desc>Helps avoid filters by encrypting the page before sending it and decrypting it with javascript once received. Note: this option is intended to obscure HTML source code and does not provide security. Use SSL for actual security.</desc></option><option key="showForm" default="true" force="true"><title>Show Form</title><desc>This provides a mini-form at the top of each page that allows you to quickly jump to another site without returning to our homepage.</desc></option><option key="allowCookies" default="true" force="false"><title>Allow Cookies</title><desc>Cookies may be required on interactive websites (especially where you need to log in) but advertisers also use cookies to track your browsing habits.</desc></option><option key="tempCookies" default="true" force="true"><title>Force Temporary Cookies</title><desc>This option overrides the expiry date for all cookies and sets it to at the end of the session only - all cookies will be deleted when you shut your browser. (Recommended)</desc></option><option key="stripTitle" default="false" force="true"><title>Remove Page Titles</title><desc>Removes titles from proxied pages.</desc></option><option key="stripJS" default="true" force="false"><title>Remove Scripts</title><desc>Remove scripts to protect your anonymity and speed up page loads. However, not all sites will provide an HTML-only alternative. (Recommended)</desc></option><option key="stripObjects" default="false" force="false"><title>Remove Objects</title><desc>You can increase page load times by removing unnecessary Flash, Java and other objects. If not removed, these may also compromise your anonymity.</desc></option></section><section type="forced" hidden="true" name="Do not edit this section manually!"><option key="version" type="string"><default>\'' . ADMIN_VERSION . '\'</default><desc>Settings file version for determining compatibility with admin tool.</desc></option></section></options>');

        if ($input->pSubmit && !$error->hasMsg()) {

            function filter($value, $type)
            {

                switch ($type) {

                    case 'string':
                    default:
                        return quote($value);

                    case 'int':
                        return intval($value);

                    case 'float':
                        if (is_numeric($value)) {
                            return $value;
                        }
                        return quote($value);

                    case 'array':
                        $args = $value ? implode(', ', array_map('quote', (array)$value)) : '';
                        return 'array(' . $args . ')';

                    case 'bool':
                        if (bool($value) === NULL) {
                            global $option;
                            $value = $option->default;
                        }
                        return $value;

                }

            }

            function comment($text, $multi = false)
            {

                $char = $multi ? '*' : '#';

                $text = wordwrap($text, 65, "\r\n$char ");

                if ($multi) {
                    return '/*****************************************************************
* ' . $text . '
******************************************************************/';
                }

                return "# $text";

            }

            $toWrite = '<?php

';
            foreach ($options->section as $section) {

                $toWrite .= NL . NL . comment($section['name'], true) . NL;

                foreach ($section->option as $option) {

                    $key = (string)$option['key'];

                    $value = $input->{'p' . $key};

                    if ($section['type'] == 'user') {

                        $title = filter((isset($value['title']) ? $value['title'] : $option->title), 'string');
                        $desc = filter((isset($value['desc']) ? $value['desc'] : $option->desc), 'string');
                        $default = filter((isset($value['default']) ? $value['default'] : $option['default']), 'bool');
                        $force = isset($value['force']) ? 'true' : 'false';

                        $toWrite .=
                            "\r\n\$SETTINGS['options'][" . quote($key) . "] = array(
	'title'	 => $title,
	'desc'	 => $desc,
	'default' => $default,
	'force'	 => $force
);\r\n";

                        continue;
                    }

                    if ($value === NULL || $section['forced']) {

                        $value = $option->default;

                    } else {

                        if ($option->toStore && ($tmp = @eval($option->toStore))) {
                            $value = $tmp;
                        }

                        if ($option->isDir) {

                            $value = str_replace('\\', '/', $value);

                            if (substr($value, -1) && substr($value, -1) != '/') {
                                $value .= '/';
                            }
                        }

                        $value = filter($value, $option['type']);

                        if ($option->relative && $input->{'pRelative_' . $key}) {
                            $value = $option->relative['to'] . ' . ' . $value;
                        }

                    }

                    $toWrite .= NL . comment($option->desc) . NL;
                    if ($key == 'enable_blockscript' && $value == 'true') {
                        $value = 'false';
                        if (function_exists('ioncube_loader_version') && file_exists($_SERVER['DOCUMENT_ROOT'] . '/blockscript/detector.php')) {
                            $value = 'true';
                        }
                    }
                    $toWrite .= '$SETTINGS[' . quote($key) . '] = ' . $value . ';' . NL;

                }

            }

            $file = file_get_contents(ADMIN_PUNISH_SETTINGS);

            if ($tmp = strpos($file, '//---PRESERVE ME---')) {
                $toWrite .= NL . substr($file, $tmp);
            }

            if (file_put_contents(ADMIN_PUNISH_SETTINGS, $toWrite)) {
                $confirm->add('The settings file has been updated.');
            } else {
                $error->add('The settings file failed to write. The file was detected as writable but file_put_contents() returned false.');
            }

            $location->redirect('settings');

        }

        $output->title = 'edit settings';
        $output->bodyTitle = 'Edit settings';

?>
		<p>This page allows you to edit your configuration to customize and tweak your proxy. If an option is unclear, hover over the option name for a more detailed description. <a href="#notes">More...</a></p>

		<form action="{$self}?settings" method="post">
<?php

function forCompare($val)
{
    return str_replace(' ', '', $val);
}

if (empty($SETTINGS['version']) || version_compare(forCompare(ADMIN_VERSION), forCompare($SETTINGS['version']), '>')) {
    echo '<p class="error-color">Your settings file needs updating. <input class="button" type="submit" name="submit" value="Update &raquo;"></p>';
}

$javascript = <<<OUT

		// Toggle "relative from root" option
		function toggleRelative(checkbox, textId) {

			var textField = document.getElementById(textId);
			var relative  = checkbox.value;

			// Are we adding or taking away?
			if ( ! checkbox.checked ) {

				// Does the field already contain the relative path?
				if ( textField.value.indexOf(relative) != 0 ) {
					textField.value = relative += textField.value;
				}

			} else {
				textField.value = textField.value.replace(relative, '');
			}

		}

		// Check if a given directory exists / is_writable
		var testDir = function(fieldName) {

			// Save vars in object
			this.input		 = document.getElementById(fieldName);
			this.result		 = document.getElementById('dircheck_' + fieldName);
			this.relative	 = document.getElementById('relative_' + fieldName);
			this.isTmp		 = fieldName == 'tmp_dir';

			// Update status
			this.updateDirStatus = function(response) {
				this.result.innerHTML = response;
			}

			// Run when value is changed
			this.changed = function() {

				this.isRelative = this.relative ? this.relative.checked : false;

				// Attempt to get path from the value
				var dirPath = this.input.value;

				// Is it relative?
				if ( this.isRelative ) {
					dirPath = this.relative.value + dirPath;
				}

				// Update with the loading .gif
				this.result.innerHTML = loadingImage;

				// Make the request
				runAjax('$self?fetch=test-dir&dir=' + encodeURIComponent(dirPath) + '&tmp=' + this.isTmp, null, this.updateDirStatus, this);

			}

		}
OUT;
$output->addJavascript($javascript);

foreach ($options->section as $section) {

    if ($section['hidden'] === NULL) {

        echo '
					<br>
					<div class="hr"></div>
					<h2>' . $section['name'] . '</h2>';

    }

    switch ($section['type']) {

        case 'settings':

            if ($section->comment) {
                echo '<div class="comment">', $section->comment, '</div>';
            }

            echo '<table class="form_table" border="0" cellpadding="0" cellspacing="0">';

            foreach ($section->option as $option) {

                $field = '';

                $key = (string)$option['key'];

                $currentValue = isset($SETTINGS[$key]) ? $SETTINGS[$key] : @eval('return ' . $option->default . ';');

                if ($option->relative) {

                    $relativeTo = @eval('return ' . $option->relative['to'] . ';');

                    $currentValue = str_replace($relativeTo, '', $currentValue, $relativeChecked);

                }

                if ($option->toDisplay && ($newValue = @eval($option->toDisplay)) !== false) {
                    $currentValue = $newValue;
                }

                $attr = 'type="'.$option['input'].'" name="'.$option['key'].'" id="'.$option['key'].'" value="'.$currentValue.'" class="inputgri '.$option['styles'];

                switch ($option['input']) {

                    case 'text':

                        if ($option->isDir) {
                            $attr .= " onchange=\"test{$option['key']}.changed()\"";
                        }

                        $field = '<input' . $attr . '>';

                        if ($option->relative) {

                            $checked = empty($relativeChecked) ? '' : ' checked="checked"';

                            $relativeToEscaped = str_replace('\\', '\\\\', $relativeTo);

                            $field .= '<input type="checkbox" onclick="toggleRelative(this,\''.$option['key'].'\')" value="'.$relativeTo.'" name="relative_'.$option['key'].'" id="relative_'.$option['key'].'"'.$checked.'><label class="tooltip" for="relative_'.$option['key'].'" onmouseover="tooltip(\'You can specify the value as relative to the '.$option->relative['desc'].':<br><b>'.$relativeToEscaped.'</b>\')" onmouseout="exit();">Relative to '.$option->relative['desc'].'</label>';
                        }
                        break;

                    case 'select':
                        $field = '<select' . $attr . '>' . @eval($option->generateOptions) . '</select>';
                        break;

                    case 'radio':
                        $onChecked = $currentValue ? ' checked="checked"' : '';
                        $offChecked = !$currentValue ? ' checked="checked"' : '';
                        $field = '<input type="radio" name="'.$option['key'].'" id="'.$option['key'].'_on" value="true" class="inputgri '.$option['styles'].'"'.$onChecked.'><label for="'.$option['key'].'_on">Yes</label>&nbsp; / &nbsp;<input type="radio" name="'.$option['key'].'" id="'.$option['key'].'_off" value="false" class="inputgri '.$option['styles'].'"'.$offChecked.'><label for="'.$option['key'].'_off">No</label>';
                        break;

                    case 'textarea':
                        $field = '<textarea ' . $attr . '>' . $currentValue . '</textarea><br>';
                        break;

                }

                $tooltip = $option->desc ? 'class="tooltip" onmouseover="tooltip(\'' . htmlentities(addslashes($option->desc), ENT_QUOTES) . '\')" onmouseout="exit()"' : '';

                if ($option['unit']) {
                    $field .= ' ' . $option['unit'];
                }

                if ($option->afterField) {

                    $add = $option->afterField['eval'] ? @eval($option->afterField) : $option->afterField;

                    if ($add) {
                        $field .= ' (<span class="little">' . $add . '</span>)';
                    }

                }

                ?>
                <tr>
                    <td width="160" align="right">
                        <label for="<?= $option['key'] ?>" <?= $tooltip ?>><?= $option->title ?>:</label>
                    </td>
                    <td><?= $field ?></td>
                </tr>

                <?php

                if ($option->isDir) {

                    $write = jsWrite('(<a style="cursor:pointer;" onclick="test' . $option['key'] . '.changed()">try again</a>)');

                    ?>
                    <tr>
                        <td>&nbsp;</td>
                        <td>
                            &nbsp;&nbsp;
                            <span id="dircheck_<?= $option['key'] ?>"></span>
                            $write
                        </td>
                    </tr>

                    <?php
                    $output->addDomReady("window.test{$option['key']} = new testDir('{$option['key']}');test{$option['key']}.changed();");
                }

            }

            echo '</table>';

            break;

        case 'user':

            ?>
            <table class="table" cellpadding="0" cellspacing="0">
            <tr class="table_header">
                <td width="200">Title</td>
                <td width="50">Default</td>
                <td>Description</td>
                <td width="50">Force <span class="tooltip"
                                           onmouseover="tooltip('Forced options do not appear on the proxy form and will always use the default value')"
                                           onmouseout="exit()">?</span></td>
            </tr>
            <?php
            $currentOptions = isset($SETTINGS['options']) ? $SETTINGS['options'] : array();

            foreach ($section->option as $option) {

                $key = (string)$option['key'];

                $title = isset($currentOptions[$key]['title']) ? $currentOptions[$key]['title'] : $option->title;
                $default = isset($currentOptions[$key]['default']) ? $currentOptions[$key]['default'] : bool($option['default']);
                $desc = isset($currentOptions[$key]['desc']) ? $currentOptions[$key]['desc'] : $option->desc;
                $force = isset($currentOptions[$key]['force']) ? $currentOptions[$key]['force'] : bool($option['force']);

                $on = $default == true ? ' checked="checked"' : '';
                $off = $default == false ? ' checked="checked"' : '';
                $force = $force ? ' checked="checked"' : '';

                $row = isset($row) && $row == 'row1' ? 'row2' : 'row1';

                ?>
                <tr class="<?= $row ?>">
                    <td><input type="text" class="inputgri" style="width:95%;" name="<?= $key ?>[title]"
                               value="<?= $title ?>"></td>
                    <td>
                        <input type="radio" name="<?= $key ?>[default]" value="true"
                               id="options_<?= $key ?>_on"<?= $on ?>> <label for="options_<?= $key ?>_on">On</label>
                        <br>
                        <input type="radio" name="<?= $key ?>[default]" value="false"
                               id="options_<?= $key ?>_off"<?= $off ?>> <label for="options_<?= $key ?>_off">Off</label>
                    </td>
                    <td><textarea class="inputgri wide-input" name="<?= $key ?>[desc]"><?= $desc ?></textarea></td>
                    <td><input type="checkbox" name="<?= $key ?>[force]"<?= $force ?> value="true"></td>
                </tr>

                <?php
            }

            ?>
            </table>';
            <?php
            break;

    }

}

echo <<<OUT
<div class="hr"></div>
<p class="center"><input class="button" type="submit" name="submit" value="Save Changes"{$tpl->disabled}></p>
</form>

<div class="hr"></div>
<h2><a name="notes"></a>Notes:</h2>
<ul class="black">
    <li><b>Temporary directory:</b> many features require write access to the temporary directory. Ensure you set up the permissions accordingly if you use any of these features: logging, server-side cookies, maintenance/cleanup and the server load limiter.</li>
    <li><b>Sensitive data:</b> some temporary files may contain personal data that should be kept private - that is, log files and server-side cookies. If using these features, protect against unauthorized access by choosing a suitable location above the webroot or using .htaccess files to deny access.</li>
    <li><b>Relative paths:</b> you can specify some paths as relative to other paths. For example, if logs are created in /[tmp_dir]/logs/ (as per the default setting), you can edit the value for tmp_dir and the logs path will automatically update.</li>
    <li><b>Quick start:</b> by default, all temporary files are created in the /tmp/ directory. Subfolders for features are created as needed. Private files are protected with .htaccess files. If running Apache, you can simply set writable permissions on the /tmp/ directory (0755 or 0777) and all features will work without further changes to the filesystem or permissions.</li>
</ul>
<p class="right"><a href="#">^ top</a></p>
OUT;
