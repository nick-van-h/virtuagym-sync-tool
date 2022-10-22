<?php
//Default include autoload
require_once __DIR__ . '/modules/autoload.php';

//Enable error logging for dev environment
set_error_reporting();

//Check if the user is logged in
$auth = new Auth;
if ($auth->userIsLoggedIn()) {
    redirectToUrl(public_base_url() . '/app.php');
}

//Build the head part of the document
get_vw_head_start();
get_vw_head_title('VirtuaGym Sync Tool');
get_vw_head_resources();
get_vw_head_end();

//Site content
?>
<main>
<div style="width: 100%; position:relative;">
    <div class="img-container" style="width: 100%; margin: 100px 0;">
        <img style="display: block; margin-left: auto; margin-right: auto; width: 100%; max-width: 640px; padding: 0 20px; " src="<?php echo(public_base_url()); ?>/resources/img/main_logo_big.png">
    </div>
    <h1 class="row">Login</h1>
    <div class="login-container" style="display: flex; justify-content: center; flex-direction: row; width: 100%;">
        <form class="login box-xl" action="interfaces/login.php" method="post">
            <fieldset>
                <div class="row row--align box-xs">
                    <label class="col-1-3" for="username">Username</label>
                    <input class="col-2-3" type="text" name="username" autofocus/>
                </div>
                <div class="row row--align box-xs">
                    <label class="col-1-3" for="password">Password</label>
                    <input class="col-2-3" type="password" name="password"/>
                </div>
                <div class="row row--justify-right .row--align-right box-xs">
                    <span class="login-message"><?php echo($auth->getLoginMessage()); ?></span><button type="submit">Submit</button>
                </div>
            </fieldset>
        </form>
    </div>
</div>
</main>
<?php

//Foot
get_vw_foot();
