<?php
//Declare global variables to be used in the rest of the document
global $auth;
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