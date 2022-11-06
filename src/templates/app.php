<main class="app">
    <div class="img-container img-page-title">
        <img src="<?php echo(public_base_url()); ?>/resources/img/title_home_alt.png">
    </div>
    Last update: Way too long ago<br>
    <h1>Overview of current activities</h1>
    <input type="checkbox" id="show-cancelled" />
    <label for="show-cancelled">Show cancelled events</label><br><br>

<?php

$sync = new Vst\Model\Sync;
$activities = $sync->getAllStoredActivities();
$dt = new \DateTime();
$ldt = new \DateTime();

foreach ($activities as $act) {
    $edt = new \DateTime(date("d-m-Y H:i", $act['event_start']));
    echo '<div class="training-entry' . (($edt < $dt) ? ' past' : '') . ($act['cancelled'] ? ' cancelled' : '') . (!$act['joined'] ? ' not-joined' : '') . '">';
        echo '<div class="dow">' . date("D", $act['event_start']) . '</div>';
        echo '<div class="dt">';
            echo '<div class="date">' . date("d-m-Y", $act['event_start']) . '</div>';
            echo '<div class="time">' . date("H:i", $act['event_start']) . ' - ' . date("H:i", $act['event_end']) . '</div>';
        echo '</div>';
        echo '<div class="title">' . $act['name'] . '</div>';
    echo '</div>';
}

?>
</main>