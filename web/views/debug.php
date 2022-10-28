<main class="app">
    <div class="img-container img-page-title">
        <img src="<?php echo(public_base_url()); ?>/resources/img/title_debug.png">
    </div>
<h1>Log</h1>
<?php 
    echo('Config file = ' . CONFIG_FILE);br();echo_pre(getConfig()); 
    $dt = new DateTime(null);
    echo('DateTime = ' . $dt->format('d-m-Y H:i:s'));
?>
<h1>Session status</h1>
<?php global $auth; echo_pre(($_SESSION)); ?>
<form action="interfaces/clearDebug.php">
    <button type="submit">Clear debug</button>
</form>
</main>