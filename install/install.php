<?php

if (isset($adminDetails)) {

    $error->add('An administrator account already exists. For security reasons, you must manually create additional administrator accounts.');

    $location->redirect();

}

if ($input->pSubmit) {

    if (!($username = $input->pAdminUsername)) {
        $error->add('You must enter a username to protect access to your control panel!');
    }

    if (!($password = $input->pAdminPassword)) {
        $error->add('You must enter a password to protect access to your control panel!');
    }

    $tpl->username = $username;

    if (!$error->hasMsg() && is_writable(ADMIN_PUNISH_SETTINGS)) {

        $file = file_get_contents(ADMIN_PUNISH_SETTINGS);

        if (substr(trim($file), -2) == '?>') {
            $file = substr(trim($file), 0, -2);
        }

        if (strpos($file, '//---PRESERVE ME---') === false) {

            $file .= "\r\n//---PRESERVE ME---
# Anything below this line will be preserved when the admin control panel rewrites
# the settings. Useful for storing settings that don't/can't be changed from the control panel\r\n";

        }

        $password = md5($password);

        $file .= "\r\n\$adminDetails[" . quote($username) . "] = " . quote($password) . ";\r\n";

        if (file_put_contents(ADMIN_PUNISH_SETTINGS, $file)) {

            $confirm->add('Installation successful. You have added <b>' . $username . '</b> as an administrator and are now logged in.');

            $user->login($username);

        } else {

            $error->add('Installation failed. The settings file appears writable but file_put_contents() failed.');

        }

        $location->redirect();

    }

}

$output->title = 'install';
$output->bodyTitle = 'First time use installation';

$output->addDomReady("document.getElementById('username').focus();");

if (!($writable = is_writable(ADMIN_PUNISH_SETTINGS))) {

    $error->add('The settings file was found at <b>' . ADMIN_PUNISH_SETTINGS . '</b> but is not writable. Please set the appropriate permissions to make the settings file writable.');

    $tpl->disabled = ' disabled="disabled"';

} else {

    $confirm->add('Settings file was found and is writable. Installation can proceed. <b>Do not leave the script at this stage!</b>');

}

?>
<p>Enter a username and password below to continue.</p>

<form action="<?= $self ?>?install" method="post">
    <table class="form_table" border="0" cellpadding="0" cellspacing="0">
        <tr>
            <td align="right">Username:</td>
            <td align="left"><input class="inputgri" id="username" name="adminUsername" type="text"
                                    value="<?= $tpl->username ?>"></td>
        </tr>
        <tr>
            <td align="right">Password:</td>
            <td align="left"><input class="inputgri" name="adminPassword" type="password"></td>
        </tr>
    </table>
    <p><input class="button" value="Submit &raquo;" name="submit" type="submit"<?= $tpl->disabled ?>></p>
</form>
