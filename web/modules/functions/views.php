<?php

/**
 * =========================
 * Interfaces to website component
 * =========================
 */

function get_vw_head_start()
{
    include(BASE_PATH . '/views/head_start.php');
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
    include(BASE_PATH . '/views/head_resources.php');
}

function get_vw_head_end()
{
    include(BASE_PATH . '/views/head_end.php');
}

function get_vw_foot()
{
    include(BASE_PATH . '/views/foot.php');
}

function get_vw_test_nav()
{
    include(BASE_PATH . '/views/test_menu.php');
}

function get_vw_app_nav()
{
    include(BASE_PATH . '/views/app_menu.php');
}