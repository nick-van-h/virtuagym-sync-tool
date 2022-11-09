<?php
//Get user variables
$user = new \Vst\Controller\User;
$credentials = $user->getCalendarCredentials();
$calSelected = $user->getTargetAgendaName();
//Get calendar variables
$cal = \Vst\Controller\CalendarFactory::getProvider(PROVIDER_GOOGLE,$credentials);
$account = $cal->getAccount();

$settings = new Vst\Model\Settings;
$session = new Vst\Controller\Session;
$session->setRedirectUrl(public_base_url() . '/settings.php');
//TODO NEXT: Assign click handlers to login & save calendar buttons
?>

<div class="row row--align box-xs">
    <div class="col-2-3 row row--justify-left row--align">
        <span>Connected account:</span><span class="box-xs" id="settings-calendar-account"><?php echo($account ?? '(none)'); ?></span>
    </div>
    <div class="col-1-3 row row--justify-right">
        <button id="settings-google-connect" name="google-connect"><?php echo($account ? 'Switch' : 'Login'); ?></button>
    </div>
</div>
<?php if(!empty($account)): ?>
    <div class="row row--align box-xs">
        <div class="col-1-3">Select agenda</div>
        <div class="col-2-3 select"><select name="calendar-agendas" id="calendar-agendas">
            <option value="" disabled selected>-- Select --</option>
            <?php
            foreach($cal->getAgendas() as $idx=>$val) {

                echo('<option value="' . $val['id'] . '|' . $val['name'] . '|' . $val['timezone'] . '"' . (($calSelected == $val['name']) ? ' selected' : '') . '>' . $val['name'] . '</option>');
            }
            ?>
        </select></div>
    </div>
    <div class="row row--align row--justify-right box-xs">
            <button name="calendar-save">Save</button>
    </div>
<?php endif; ?>
<div class="status-message">
    <span><?php echo($session->getAndClearStatus('Google-login')); ?></span>
</div>