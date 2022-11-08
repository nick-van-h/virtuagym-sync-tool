<?php

/**
 * =========================
 * Interfaces to website component
 * =========================
 */

 /**
  * Document layout
  */

function get_vw_head_start()
{
    include(BASE_PATH . '/src/templates/document/head_start.php');
}

function get_vw_head_title($title = null)
{
    if (empty($title)) {
        $title = 'VirtuaGym Sync Tool';
    }
    echo ('<title>' . $title . '</title>');
}

function get_vw_head_resources()
{
    include(BASE_PATH . '/src/templates/document/head_resources.php');
}

function get_vw_head_end()
{
    include(BASE_PATH . '/src/templates/document/head_end.php');
}

function get_vw_foot()
{
    include(BASE_PATH . '/src/templates/document/foot.php');
}

/**
 * Menu
 */

function get_vw_nav()
{
    include(BASE_PATH . '/src/templates/parts/menu.php');
}

/**
 * Content
 */
function get_vw_app_header()
{
    include(BASE_PATH . '/src/templates/parts/app_header.php');
}

/**
 * Main
 */
function get_vw_index()
{
    include(BASE_PATH . '/src/templates/index.php');
}

function get_vw_app()
{
    include(BASE_PATH . '/src/templates/app.php');
}

function get_vw_admin()
{
    include(BASE_PATH . '/src/templates/admin.php');
}

function get_vw_settings()
{
    include(BASE_PATH . '/src/templates/settings.php');
}

function get_vw_settings_calendar()
{
    include (BASE_PATH . '/src/templates/parts/settings_calendar_google.php');
}

function get_vw_debug()
{
    include(BASE_PATH . '/src/templates/debug.php');
}

function get_vw_pw_reset() 
{
    include(BASE_PATH . '/src/templates/pwreset.php');
}

function get_vw_invalid_token()
{
    include(BASE_PATH . '/src/templates/invalid_token.php');
}