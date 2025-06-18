<?php
    require __DIR__ . "/db_config.php";

    $host = "localhost";
    $user = DB_USER;
    $pass = DB_PASS;
    $db = DB_NAME;

    $db_con = pg_connect("host=$host dbname=$db user=$user password=$pass");
?>