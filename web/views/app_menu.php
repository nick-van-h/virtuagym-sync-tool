<?php
$auth = new Auth;
?>
<nav style="background-color: lightsteelblue; padding: 10px;">
    <a href="<?php echo(public_base_url()); ?>/app.php">Home</a>
    <span style="display:inline-block; width: 20px;"></span>
    <a href="<?php echo(public_base_url()); ?>/settings.php">Settings</a>
    <?php if ($auth->userIsAdmin()) : ?>
        <span style="display:inline-block; width: 20px;"></span>
        <a href="<?php echo(public_base_url()); ?>/admin.php">Admin</a>
        <span style="display:inline-block; width: 20px;"></span>
        <a href="<?php echo(public_base_url()); ?>/tests">Tests</a>
    <?php endif; ?>
    <span style="display:inline-block; width: 20px;"></span>
    <a href="<?php echo(public_base_url()); ?>/interfaces/logout.php">Logout</a>
</nav>