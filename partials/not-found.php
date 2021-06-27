<?php
$output->sendStatus(404);
$output->title = 'page not found';
$output->bodyTitle = 'Page Not Found (404)';
?>
<p>The requested page <b><?= $_SERVER['REQUEST_URI'] ?></b> was not found.</p>