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
    include(BASE_PATH . '/views/document/head_start.php');
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
    include(BASE_PATH . '/views/document/head_resources.php');
}

function get_vw_head_end()
{
    include(BASE_PATH . '/views/document/head_end.php');
}

function get_vw_foot()
{
    include(BASE_PATH . '/views/document/foot.php');
}

/**
 * Menu
 */
function get_vw_test_nav()
{
    include(BASE_PATH . '/views/menu/test_menu.php');
}

function get_vw_app_nav()
{
    include(BASE_PATH . '/views/menu/app_menu.php');
}

/**
 * Content
 */
function get_vw_app_header()
{
    include(BASE_PATH . '/views/content/header.php');
}

/**
 * Index
 */
function get_vw_index()
{
    include(BASE_PATH . '/views/index.php');
}

/**
 * App
 */
function get_vw_app()
{
    include(BASE_PATH . '/views/app.php');
}

/**
 * Admin
 */
function get_vw_admin()
{
    include(BASE_PATH . '/views/admin.php');
}

/**
 * Settings
 */
function get_vw_settings()
{
    include(BASE_PATH . '/views/settings.php');
}