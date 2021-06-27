<?php
if (file_exists($bsc = $_SERVER['DOCUMENT_ROOT'] . '/blockscript/tmp/config.php')) {
    include($bsc);
}
$installed = isset($BS_VAL['license_agreement_accepted']) ? '<span class="ok-color">installed</span>' : '<span class="error-color">not installed</span>';
$enabled = (isset($BS_VAL['license_agreement_accepted']) && !empty($SETTINGS['enable_blockscript'])) ? '<span class="ok-color">enabled</span>' : '<span class="error-color">disabled</span>';

if (!($ok = function_exists('ioncube_loader_version'))) {
    $error->add('BlockScript requires IonCube.');
}
$IonCubeVersion = $ok && ($tmp = ioncube_loader_version()) ? $tmp : 'not available';
if ($ok && $tmp != 'not available') {

}

$output->title = 'BlockScript&reg;';
$output->bodyTitle = 'BlockScript&reg; Integration';

?>
    <form action="<?= $self ?>?blockscript" method="post">
        <table class="form_table" border="0" cellpadding="0" cellspacing="0">
            <tr>
                <td align="right">BlockScript status:</td>
                <td><b><?= $installed ?> and <?= $enabled ?></b></td>
            </tr>
        </table>
    </form>
    <div class="hr"></div>
    <h2>About</h2>
    <p><a href="https://www.blockscript.com/" target="_blank">BlockScript</a> is security software which protects
        websites and empowers webmasters to stop unwanted traffic. BlockScript detects and blocks requests from all
        types of proxy servers and anonymity networks, hosting networks, undesirable robots and spiders, and even entire
        countries.</p>

    <p>BlockScript can help proxy websites by blocking filtering company spiders and other bots. BlockScript detects and
        blocks: barracudanetworks.com, bluecoat.com, covenanteyes.com, emeraldshield.com, ironport.com,
        lightspeedsystems.com, mxlogic.com, n2h2.com, netsweeper.com, securecomputing.com, mcafee.com, sonicwall.com,
        stbernard.com, surfcontrol.com, symantec.com, thebarriergroup.com, websense.com, and much more.</p>

    <p>BlockScript provides free access to core features and <a href="https://www.blockscript.com/pricing.php"
                                                                target="_blank">purchasing a license key</a> unlocks all
        features. A one week free trial is provided so that you can fully evaluate all features of the software.</p>

    <div class="hr"></div>
    <h2>Installation Instructions</h2>
    <ol>
        <li><a href="https://www.blockscript.com/download.php" target="_blank">Download BlockScript</a> and extract the
            contents of the .zip file.
        </li>
        <li>Upload the &quot;blockscript&quot; directory and its contents.</li>
        <li>CHMOD 0777 (or 0755 if running under suPHP) the &quot;detector.php&quot; file and the &quot;/blockscript/tmp/&quot;
            directory.
        </li>
        <li>Visit <a href="http://<?= $_SERVER['HTTP_HOST'] ?>/blockscript/detector.php"
                     target="_blank">http://<?= $_SERVER['HTTP_HOST'] ?>/blockscript/detector.php</a> in your browser.
        </li>
        <li>Follow the on-screen prompts in your BlockScript control panel.</li>
    </ol>
    <br>

<?php
if ($bsc) {
    $admin_password = isset($BS_VAL['admin_password']) ? $BS_VAL['admin_password'] : '';
    echo '<div class="hr"></div><h2>Your BlockScript Installation</h2><p><a href="/blockscript/detector.php?blockscript=setup&bsap=' . $admin_password . '" target="_blank">Login To Your BlockScript Control Panel</a></p>';
}