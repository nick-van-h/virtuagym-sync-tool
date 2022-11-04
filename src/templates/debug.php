<main class="app">
    <div class="img-container img-page-title">
        <img src="<?php echo(public_base_url()); ?>/resources/img/title_debug.png">
    </div>
<h1>Log</h1>
<h1>Server status</h1>
<?php 
    echo('Config file location = ' . CONFIG_FILE);br();echo_pre(getConfig(), 'config'); 
?>
<?php global $auth; echo_pre(($_SESSION),'_SESSION'); ?>
<form action="interfaces/server/clearDebug.php">
    <button type="submit">Clear debug</button>
</form>
</main>