<?php

/**
 * Redirects the user to the specified url
 * 
 * @param string $url
 */
function redirectToUrl($url)
{
    header('Location: ' . filter_var($url, FILTER_SANITIZE_URL));
}