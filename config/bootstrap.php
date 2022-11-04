<?php

//Start the session if it is not yet started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}