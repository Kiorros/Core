<?php
if(!defined("PHORUM_ADMIN")) return;

// Create new table for newflag min_id's
$upgrade_queries[] =
    "CREATE TABLE {$PHORUM['user_min_id_table']} (
           user_id               INT UNSIGNED NOT NULL ,
           forum_id              INT UNSIGNED NOT NULL ,
           min_id                INT UNSIGNED NOT NULL ,
           PRIMARY KEY ( user_id , forum_id )
        ) $charset";

?>