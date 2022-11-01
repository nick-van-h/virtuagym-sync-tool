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
    include(BASE_PATH . '/template/document/head_start.php');
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
    include(BASE_PATH . '/template/document/head_resources.php');
}

function get_vw_head_end()
{
    include(BASE_PATH . '/template/document/head_end.php');
}

function get_vw_foot()
{
    include(BASE_PATH . '/template/document/foot.php');
}

/**
 * Menu
 */

function get_vw_nav()
{
    include(BASE_PATH . '/template/parts/menu.php');
}

/**
 * Content
 */
function get_vw_app_header()
{
    include(BASE_PATH . '/template/parts/app_header.php');
}

/**
 * Main
 */
function get_vw_index()
{
    include(BASE_PATH . '/template/index.php');
}

function get_vw_app()
{
    include(BASE_PATH . '/template/app.php');
}

function get_vw_admin()
{
    include(BASE_PATH . '/template/admin.php');
}

function get_vw_settings()
{
    include(BASE_PATH . '/template/settings.php');
}

function get_vw_debug()
{
    include(BASE_PATH . '/template/debug.php');
}

function get_vw_pw_reset() 
{
    include(BASE_PATH . '/template/pwreset.php');
}

function get_vw_invalid_token()
{
    include(BASE_PATH . '/template/invalid_token.php');
}