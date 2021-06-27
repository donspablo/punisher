<?php

$fail = false;

if (!($dir = $input->gDir)) {

    $fail = 'no directory given';

} else if (!file_exists($dir) || !is_dir($dir)) {

    $fail = 'directory does not exist';

    if (!bool($input->gTmp) && is_writable(dirname($dir)) && !mkdir($dir, 0755, true) && !is_dir($dir)) {

        $fail = false;
        $ok = 'directory does not exist but can be created';
        rmdir($dir);

    }

} else if (!is_writable($dir)) {

    $fail = 'directory not writable - permission denied';

} else {

    $ok = 'directory exists and is writable';

}

if ($fail) {
    echo '<span class="error-color">Error:</span> ', $fail;
} else {
    echo '<span class="ok-color">OK:</span> ', $ok;
}