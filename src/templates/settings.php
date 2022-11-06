<?php
$settings = new Vst\Model\Settings;
$user = new Vst\Controller\Users;
$crypt = new Vst\Model\Crypt;

?>

<main class="app">
    <div class="img-container img-page-title">
        <img src="<?php echo(public_base_url()); ?>/resources/img/title_settings.png">
    </div>
<h1>Virtuagym credentials</h1>

<form class="settings" id="settings-virtuagym" method="post">
    <fieldset>
        <div class="row row--align box-xs">
            <label class="col-1-3" for="username">Username</label>
            <input class="col-2-3" type="text" name="username" value="<?php echo($settings->getVirtuagymUsername()); ?>"  required/>
        </div>
        <div class="row row--align box-xs">
            <label class="col-1-3" for="password">Password</label>
            <input class="col-2-3" type="password" name="password" value="<?php echo($settings->getVirtuagymPassword()); ?>" required/>
        </div>
        <div class="row row--justify-right .row--align-right box-xs">
            <button type="submit" name="test">Test</button>
            <button type="submit" name="save">Save</button>
        </div>
    </fieldset>
    <div class="status-message status-message--hidden">
        <span><?php echo($settings->getVirtuagymMessage('virtuagym')); ?></span>
    </div>
</form>

<h1>Calendar credentials</h1>
<form class="settings" id="settings-calendar" method="post">
    <fieldset>
        <div class="row row--align box-xs">
            <label class="col-1-3" for="calendar-provider">Calendar provider</label>
            <div class="col-2-3 select"><select name="calendar-provider">
                <option value="1">Google</option>
            </select></div>
        </div>
        <div id="calendar-settings">
            <!-- Calendar specific content goes here -->
            <?php include __DIR__ . '/parts/setting_calendar_google.php'; ?>
        </div>
    </fieldset>
</form>
<br>
&lt;Under construction>
<h1>Manage account</h1>
    <div class="img-container">
        <img src="<?php echo(public_base_url()); ?>/resources/img/here_be_dragons.png">
    </div>
</main>
