<?php
//Declare global variables to be used in the rest of the document
global $auth;
?>

<main class="login">
    <div class="img-container">
        <img src="<?php echo(public_base_url()); ?>/resources/img/main_logo_alt.png">
    </div>
    <div class="login-container">
        <input type="radio" name="login-tabs" id="rad-login" class="tabs-selector" checked="checked" />
        <input type="radio" name="login-tabs" id="rad-signup" class="tabs-selector" />
        <div class="tabs">
            <div class="item login">
                <label for="rad-login" class="tabs-selector"></label>
                <span>Login</span>
            </div>
            <div class="item signup">
                <label for="rad-signup" class="tabs-selector"></label>
                <span>Sign up</span>
            </div>
        </div>
        <div class="form-container">
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
                        <button type="submit">Submit</button>
                    </div>
                    <div class="login-message"><?php echo($auth->getLoginMessage()); ?></div>
                </fieldset>
            </form>
            <form class="signup box-xl">
                Under construction
            </form>
        </div>
    </div>
</main>