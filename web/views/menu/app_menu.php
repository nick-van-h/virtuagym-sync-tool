<?php
//$auth = new Authenticator;
global $auth;
?>

<input type="checkbox" id="check">
<label for="check">
    <i class="fas fa-bars" id="btn"></i>
    <i class="fas fa-times" id="cancel"></i>
</label>

<nav class="app">
    <ul>
        <a href="<?php echo(public_base_url()); ?>/app.php">
            <li>
                <i class="fas fa-home"></i>
                <span>Home</span>
            </li>
        </a>
        <a href="<?php echo(public_base_url()); ?>/settings.php">
        <li>
            <i class="fas fa-wrench"></i>
            <span>Settings</span>
        </li>
        </a>
        <?php if ($auth->userIsAdmin()) : ?>
            <a href="<?php echo(public_base_url()); ?>/admin.php">
            <li>
                <i class="fas fa-lock"></i>
                <span>Admin</span>
            </li>
            </a>
            <a href="<?php echo(public_base_url()); ?>/tests">
            <li>
                <i class="fas fa-code"></i>
                <span>Tests</span>
            </li>
            </a>
        <?php endif; ?>
        <li>
            <i class="fas fa-sign-out-alt"></i>
            <a href="<?php echo(public_base_url()); ?>/interfaces/logout.php">Logout</a>
        </li>
    </ul>
</nav>