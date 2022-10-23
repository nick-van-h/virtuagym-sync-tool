<?php
$settings = new Settings;
$user = new Model\Users;
$crypt = new Crypt;

?>

<main class="app box-s">
<h1>Settings</h1>
<h2>Virtuagym credentials</h2>
<form class="settings" action="interfaces/updateVirtuagymCredentials.php" method="post">
    <fieldset>
        <div class="row row--align box-xs">
            <label class="col-1-3" for="username">Username</label>
            <input class="col-2-3" type="text" name="username" value="<?php echo($crypt->getDecryptedMessage($user->getVirtuagymUsernameEnc())); ?>"  required/>
        </div>
        <div class="row row--align box-xs">
            <label class="col-1-3" for="password">Password</label>
            <input class="col-2-3" type="password" name="password" value="<?php echo($crypt->getDecryptedMessage($user->getVirtuagymPasswordEnc())); ?>" required/>
        </div>
        <div class="row row--justify-right .row--align-right box-xs">
            <button type="submit" name="test">Test</button>
            <button type="submit" name="save">Save</button>
        </div>
        <div class="status-message">
            <span><?php echo($settings->getVirtuagymMessage('virtuagym')); ?></span>
        </div>
    </fieldset>
</form>
</main>