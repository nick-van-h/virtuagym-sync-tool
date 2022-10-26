<main class="app box-s">
<h1>Under construction</h1>
<?php
$vg = new VirtuaGym;
$vg->callActivities();
echo($vg->getResultCount() . ' activities found');br();
?>
</main>