<?php

$gui = new Vst\View\GUI;
$settings = new Vst\Model\Database\Settings;

$lastVisited = $settings->getLastVisitedVersion();
$latestReleased = $gui->getLatestReleasedVersion();
$changelog = $gui->getChangelog();
if ($lastVisited < $latestReleased) : ?>
    <div class="modal-outer changelog">
        <div class="modal-inner">
            <h1>Changelog</h1>
            <div class="modal-close"><i class="fas fa-times"></i></div>
            <div class="changes">
                <?php echo ($changelog); ?>
            </div>
        </div>
    </div>
<?php
    $settings->setLastVisitedVersion($latestReleased);
endif; ?>