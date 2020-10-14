<?php
    function sqlconnect($query)
    {
        $server = "localhost";
        $db_username = "root";
        $db_password = "";
        $db_name = "miloje";

        $connection = mysqli_connect($server, $db_username, $db_password, $db_name);
        if (!$connection)
        {
            die("Connection failed: " . mysqli_connect_error());
        }

        return mysqli_query($connection, $query);
    }
?>