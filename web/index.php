<?php
//Default include autoload
require_once __DIR__ . '/modules/autoload.php';

//Build the head part of the document
get_vw_head_start();
get_vw_head_title('Project Planner42 - The only planner you will ever need again');
get_vw_head_resources();
get_vw_head_end();

//Site content

//Foot
get_vw_foot();
