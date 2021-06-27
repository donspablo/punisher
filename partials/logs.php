<?php

if ($input->pDestination !== NULL) {

    $path = realpath($input->pDestination);

    if ($path) {
        $confirm->add('Log folder updated.');
    } else {
        $error->add('Log folder not updated. <b>' . $input->pDestination . '</b> does not exist.');
    }

    $path = str_replace('\\', '/', $path);

    if (isset($path[strlen($path) - 1]) && $path[strlen($path) - 1] != '/') {
        $path .= '/';
    }

    $_SESSION['logging_destination'] = $path;

    $location->redirect('logs');

}

$enabled = empty($SETTINGS['enable_logging']) == false;
$status = $enabled ? '<span class="ok-color">enabled</span>' : '<span class="error-color">disabled</span>';
$destination = isset($SETTINGS['logging_destination']) ? $SETTINGS['logging_destination'] : '';

if (!empty($_SESSION['logging_destination'])) {
    $destination = $_SESSION['logging_destination'];
}

$output->title = 'log viewer';
$output->bodyTitle = 'Logging';

?>
<form action="<?= $self ?>?logs" method="post">
    <table class="form_table" border="0" cellpadding="0" cellspacing="0">
        <tr>
            <td align="right">Logging feature:</td>
            <td><b><?= $status ?></b></td>
        </tr>
        <tr>
            <td align="right"><span class="tooltip"
                                    onmouseover="tooltip('The value here is for viewing and analysing logs only - changing this has no effect on the proxy logging feature itself and will not change the folder in which new log files are created.')"
                                    onmouseout="exit()">Log folder</span>:
            </td>
            <td><input type="text" name="destination" class="inputgri wide-input" value="<?= $destination ?>">
                <input type="submit" class="button" value="Update &raquo;"></td>
        </tr>
    </table>
</form>
<div class="hr"></div>
<h2>Log files</h2>
<?php

if (!(file_exists($destination) && is_dir($destination) && ($logFiles = scandir($destination, 1)))) {

    echo '<p>No log files to analyze.</p>';
    break;

}

?>
<table class="table" cellpadding="0" cellspacing="0">
    <?php

    $currentYearMonth = false;
    $first = true;
    $totalSize = 0;

    foreach ($logFiles

    as $file) {

    if (!(strlen($file) == 14 && preg_match('#^([0-9]{4})-([0-9]{2})-([0-9]{2})\.log$#', $file, $matches))) {
        continue;
    }

    list(, $yearNumeric, $monthNumeric, $dayNumeric) = $matches;


    $timestamp = strtotime(str_replace('.log', ' 12:00 PM', $file));


    $month = date('F', $timestamp);
    $day = date('l', $timestamp);
    $display = date('jS', $timestamp) . ' (' . $day . ')';
    $yearMonth = $yearNumeric . '-' . $monthNumeric;


    if ($display == date('jS (l)')) {
        $display = '<b>' . $display . '</b>';
    }


    if ($yearMonth != $currentYearMonth) {


    if ($first == false) {
    ?>
</table>
<br>
<table class="table" cellpadding="0" cellspacing="0">
    <?php
    }


    ?>
    <tr class="table_header">
        <td colspan="2"><?= $month ?> <?= $yearNumeric ?></td>
        <td>[<a href="<?= $self ?>?logs-view&month=<?= $yearMonth ?>&show=popular-sites">popular sites</a>]</td>
    </tr>
    <?php


    $currentYearMonth = $yearMonth;
    $first = false;

    }


    $filesize = filesize($destination . $file);
    $totalSize += $filesize;

    $size = formatSize($filesize);


    $row = ($day == 'Saturday' || $day == 'Sunday') ? '3' : '1';


    ?>
    <tr class="row<?= $row ?>">
        <td width="150"><?= $display ?></td>
        <td width="100"><?= $size ?></td>
        <td>
            [<a href="<?= $self ?>?logs-view&file=<?= $file ?>&show=raw" target="_blank" title="Opens in a new window">raw
                log</a>]
            &nbsp;
            [<a href="<?= $self ?>?logs-view&file=<?= $file ?>&show=popular-sites">popular sites</a>]
        </td>
    </tr>
    <?php

    }


    $total = formatSize($totalSize);

    ?>
</table>
<p>Total space used by logs: <b><?= $total ?></b></p>
<p class="little">Note: Raw logs open in a new window.</p>
<p class="little">Note: You can set up your proxy to automatically delete old logs with the maintenance feature.</p>