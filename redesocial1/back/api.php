<?php

header("Content-Type: application/json;charset=uft-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-methods: GET, POST, PUT, DELETE");

require __DIR__  . "/config.php";
require __DIR__ . "/routes/rota.php";