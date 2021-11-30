<?php
$config_json = file_get_contents("update.json");
$config = json_decode($config_json);

$jobs = $config -> jobs;

