<?php
//$auth = new Vst\Model\Authenticator;
global $auth;

$curPage = substr($_SERVER["SCRIPT_NAME"],strrpos($_SERVER["SCRIPT_NAME"],"/")+1);  
?>

<input type="checkbox" id="check">
<label for="check">
    <i class="fas fa-bars" id="btn"></i>
    <i class="fas fa-times" id="cancel"></i>
</label>

<nav class="app">
    <div class="img-container">
        <img src="<?php echo(public_base_url()); ?>/resources/img/main_logo_alt.png">
    </div>
    <ul>
        <a href="<?php echo(public_base_url()); ?>/app.php">
            <li class="<?php echo($curPage == 'app.php' ? 'selected' : ''); ?>">
                <i class="fas fa-home"></i>
                <span>Home</span>
            </li>
        </a>
        <a href="<?php echo(public_base_url()); ?>/settings.php">
            <li class="<?php echo($curPage == 'settings.php' ? 'selected' : ''); ?>">
                <i class="fas fa-wrench"></i>
                <span>Settings</span>
            </li>
        </a>
        <?php if ($auth->userIsAdmin() || $auth->userIsDev()) : ?>
            <a href="<?php echo(public_base_url()); ?>/debug.php">
                <li class="<?php echo($curPage == 'debug.php' ? 'selected' : ''); ?>">
                    <i class="fas fa-bug"></i>
                    <span>Debug</span>
                </li>
            </a>
        <?php endif; ?>
        <?php if ($auth->userIsAdmin()) : ?>
            <a href="<?php echo(public_base_url()); ?>/admin.php">
                <li class="<?php echo($curPage == 'admin.php' ? 'selected' : ''); ?>">
                    <i class="fas fa-lock"></i>
                    <span>Admin</span>
                </li>
            </a>
            <a href="<?php echo(public_base_url()); ?>/tests.php">
                <li class="<?php echo($curPage == 'tests.php' ? 'selected' : ''); ?>">
                    <i class="fas fa-code"></i>
                    <span>Tests</span>
                </li>
            </a>
        <?php endif; ?>
        <a href="<?php echo(public_base_url()); ?>/interfaces/server/logout.php">
            <li>
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </li>
        </a>
    </ul>
</nav>