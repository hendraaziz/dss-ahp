<?php
date_default_timezone_set('asia/jakarta');

// Get Heroku ClearDB connection information
$cleardb_url = getenv("CLEARDB_DATABASE_URL");
if ($cleardb_url) {
    $cleardb = parse_url($cleardb_url);
    $config["server"] = $cleardb["host"];
    $config["username"] = $cleardb["user"];
    $config["password"] = $cleardb["pass"];
    $config["database_name"] = substr($cleardb["path"], 1);
} else {
    // Fallback to default configuration
    $config["server"] = 'www.db4free.net';
    $config["username"] = 'dss_ahp';
    $config["password"] = 'tugasdss';
    $config["database_name"] = 'dss_ahp';
}

$config_lokal["server"] = 'localhost';
$config_lokal["username"] = 'root';
$config_lokal["password"] = '';
$config_lokal["database_name"] = 'dss_dua';