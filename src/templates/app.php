<?php
//init local variables
$sync = new Vst\Controller\Sync;
$dt = new \DateTime();
$ldt = new \DateTime();

$vgConnection = $sync->testVgConnection();
$calConnection = $sync->testCalendarConnection();

?>

<main class="app">
    <div class="img-container img-page-title">
        <img src="<?php echo (public_base_url()); ?>/resources/img/title_home_alt.png">
    </div>
    <h1>Status</h1>
    <div class="status-summary">
        <div class="row">
            <span class="col-1-3">Last update</span>
            <span class="col-2-3"><?php echo ($sync->getLastSyncDate()); ?></span>
        </div>
        <div class="row">
            <span class="col-1-3">Virtuagym connection</span>
            <span class="col-2-3 <?php echo ($vgConnection ? 'success' : 'warning'); ?>"><?php echo (($vgConnection ? 'OK' : 'NOK')); ?></span>
        </div>
        <div class="row">
            <span class="col-1-3">Calendar connection</span>
            <span class="col-2-3 <?php echo ($calConnection ? 'success' : 'warning'); ?>"><?php echo (($calConnection ? 'OK' : 'NOK')); ?></span>
        </div>
        <div class="row row--align row--justify-left">
            <?php if ($vgConnection && $calConnection) : ?>
                <div><button id="manual-sync">Sync now</button></div>
                <div class="dynamic-loader">
                    <?php get_vw_loader(); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <h1>Activity overview</h1>
    <input type="checkbox" id="show-cancelled" />
    <label for="show-cancelled">Show cancelled events</label><br><br>

    <h3>Upcoming activities</h3>
    <?php
    $activities = $sync->getAllStoredActivities(ORDER_ASC);
    foreach ($activities as $act) {
        $edt = new \DateTime(date("d-m-Y H:i", $act['event_start']));
        if ($edt >= $dt) {
            echo '<div class="training-entry' . (($edt < $dt) ? ' past' : '') . (($act['deleted'] || $act['cancelled']) ? ' cancelled' : '') . '">';
            echo '<div class="dow">' . date("D", $act['event_start']) . '</div>';
            echo '<div class="dt">';
            echo '<div class="date">' . date("d-m-Y", $act['event_start']) . '</div>';
            echo '<div class="time">' . date("H:i", $act['event_start']) . ' - ' . date("H:i", $act['event_end']) . '</div>';
            echo '</div>';
            echo '<div class="title">' . $act['name'] . '</div>';
            echo '</div>';
        }
    }
    ?>

    <h3>past activities</h3>
    <?php
    $activities = $sync->getAllStoredActivities(ORDER_DESC);
    foreach ($activities as $act) {
        $edt = new \DateTime(date("d-m-Y H:i", $act['event_start']));
        if ($edt < $dt) {
            echo '<div class="training-entry' . (($edt < $dt) ? ' past' : '') . ($act['cancelled'] ? ' cancelled' : '') . (!$act['joined'] ? ' not-joined' : '') . '">';
            echo '<div class="dow">' . date("D", $act['event_start']) . '</div>';
            echo '<div class="dt">';
            echo '<div class="date">' . date("d-m-Y", $act['event_start']) . '</div>';
            echo '<div class="time">' . date("H:i", $act['event_start']) . ' - ' . date("H:i", $act['event_end']) . '</div>';
            echo '</div>';
            echo '<div class="title">' . $act['name'] . '</div>';
            echo '</div>';
        }
    }
    ?>
</main>