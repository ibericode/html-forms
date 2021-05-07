<?php

/** @var wpdb */
global $wpdb;
$table = $wpdb->prefix . 'hf_submissions';
$wpdb->query("ALTER TABLE {$table} CHANGE COLUMN referer_url referer_url TEXT NULL;");
