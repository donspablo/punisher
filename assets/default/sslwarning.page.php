<html>
<head>
    <title>Security Warning</title>
    <script type="text/javascript" src="../assets/default/main.js"></script>
    <link href="../assets/default/main.css" rel="stylesheet" type="text/css" media="all">
    <base href="<?php echo PUNISH_URL; ?>/">
</head>
<body>
<div id="wrapper">
    <h1>Warning!</h1>
    <p>The site you are attempting to browse is on a secure connection. This proxy is not on a secure
        connection.</p>
    <p>The target site may send sensitive data, which may be intercepted when the proxy sends it back to you.</p>
    <form action="inc/process.php" method="get">
        <input type="hidden" name="action" value="sslagree">
        <input type="submit" value="Continue anyway...">
        <input type="button" value="Return to index" onclick="window.location='.';">
    </form>
    <p><b>Note:</b> this warning will not appear again.</p>
</div>
</body>
</html>