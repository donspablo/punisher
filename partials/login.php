<?php


if ($input->pLoginSubmit) {

    if (!($username = $input->pAdminUsername)) {
        $error->add('You did not enter your username. Please try again.');
    }

    if (!($password = $input->pAdminPassword)) {
        $error->add('You did not enter your password. Please try again.');
    }

    if (!$error->hasMsg()) {

        if (isset($adminDetails[$username]) && $adminDetails[$username] == md5($password)) {

            $user->login($username);

            $location->cleanRedirect();

        } else {

            $error->add('The login details you submitted were incorrect.');

        }

    }

}

if ($user->aborted) {
    $error->add($user->aborted);
}

$output->title = 'log in';
$output->bodyTitle = 'Log in';

$output->addDomReady("document.getElementById('username').focus();");

?>
<p>Enter your log in details below.</p>
<form action="<?= $self ?>?login" method="post">
    <table class="form_table" border="0" cellpadding="0" cellspacing="0">
        <tr>
            <td align="right">Username:</td>
            <td align="left"><input class="inputgri" id="username" name="adminUsername" type="text"></td>
        </tr>
        <tr>
            <td align="right">Password:</td>
            <td align="left"><input class="inputgri" name="adminPassword" type="password"></td>
        </tr>
    </table>
    <p><input class="button" value="Submit &raquo;" name="loginsubmit" type="submit"></p>
</form>