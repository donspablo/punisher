<?php
$output->title = 'view log';
$output->bodyTitle = 'View log file';


$logFolder = isset($_SESSION['logging_destination']) ? $_SESSION['logging_destination'] : $SETTINGS['logging_destination'];


if (!file_exists($logFolder) || !is_dir($logFolder)) {

    $error->add('The log folder specified does not exist.');
    break;

}


$file = $input->gFile ? realpath($logFolder . '/' . str_replace('..', '', $input->gFile)) : false;


switch ($input->gShow) {


    case 'raw':


        if ($file == false || file_exists($file) == false) {

            $error->add('The file specified does not exist.');
            break;

        }


        $output = new RawOutput;


        readfile($file);

        break;


    case 'popular-sites':


        $scan = array();


        if ($file) {


            $scan[] = $file;


            $date = ($fileTime = strtotime(basename($input->gFile, '.log'))) ? date('l jS F, Y', $fileTime) : '[unknown]';

        } else if ($input->gMonth && strlen($input->gMonth) > 5 && ($logFiles = scandir($logFolder))) {


            foreach ($logFiles as $file) {


                if (strpos($file, $input->gMonth) === 0) {
                    $scan[] = realpath($logFolder . '/' . $file);
                }

            }


            $date = date('F Y', strtotime($input->gMonth . '-01'));
        }


        if (empty($scan)) {
            $error->add('No files to analyze.');
            break;
        }


        $visited = array();


        foreach ($scan as $file) {


            @set_time_limit(30);


            if (($handle = fopen($file, 'rb')) === false) {
                continue;
            }


            while (($data = fgetcsv($handle, 2000)) !== false) {


                if (isset($data[2]) && preg_match('#(?:^|\.)([a-z0-9-]+\.(?:[a-z]{2,}|[a-z.]{5,6}))$#i', strtolower(parse_url(trim($data[2]), PHP_URL_HOST)), $tmp)) {


                    if (isset($visited[$tmp[1]])) {


                        ++$visited[$tmp[1]];

                    } else {


                        $visited[$tmp[1]] = 1;
                    }
                }
            }


            fclose($handle);
        }


        arsort($visited);


        $others = array_splice($visited, ADMIN_STATS_LIMIT);


        $others = array_sum($others);


        ?>
        <h2>Most visited sites for <?= $date ?></h2>
        <table class="form_table" cellpadding="0" cellspacing="0" width="100%">
            <?php


            $max = max($visited);


            foreach ($visited as $site => $count) {

                $rowWidth = round(($count / $max) * 100);


                ?>
                <tr>
                    <td width="200" align="right"><?= $site ?></td>
                    <td>
                        <div class="bar" style="width: <?= $rowWidth ?>%;"><?= $count ?></div>
                    </td>
                </tr>
                <?php

            }


            ?>
            <tr>
                <td align="right"><i>Others</i></td>
                <td><?= $others ?></td>
            </tr>
        </table>
        <p class="align-center">&laquo; <a href="<?= $self ?>?logs">Back</a></p>
        <?php

        break;


    default:

        $error->add('Missing input. No log view specified.');

}
