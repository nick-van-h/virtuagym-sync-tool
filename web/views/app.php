<main class="app box-s">
<h1>Under construction</h1>
<?php
$vg = new VirtuaGym;
$vg->callActivities();
echo($vg->getResultCount() . ' activities found');br();
$vg->callClubIds();
$vg->callActivityDefinitions();
$vg->callEventDefinitions();
$activities = $vg->getEnrichedActivities();
foreach ($activities as $act) {
    echo '<div class="training-entry' . ($act['cancelled'] ? ' cancelled' : '') . (!$act['joined'] ? ' not-joined' : '') . '">';
        echo '<div class="dt">';
            echo '<div class="date">' . date("d-m-Y", $act['event_start']) . '</div>';
            echo '<div class="time">' . date("H:i", $act['event_start']) . ' - ' . date("H:i", $act['event_end']) . '</div>';
        echo '</div>';
        echo '<div class="title">' . $act['name'] . '</div>';
    echo '</div>';
}
?>
</main>