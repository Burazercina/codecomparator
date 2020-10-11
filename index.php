<!DOCTYPE html>
<html lang="en">
    <head>
        <!--Import Google Icon Font-->
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <!--Import materialize.css-->
        <link type="text/css" rel="stylesheet" href="css/materialize.min.css"  media="screen,projection"/>

        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Testirac</title>
    </head>
    <body>
        <form action="index.php" method="post">
            <label for="username_textbox">Username</label>
            <input name="username_textbox">
            <br>
            
            <label for="password_textbox">Password</label>
            <input name="password_textbox">
            <br>

            <input type="submit" name="login_button" value="Login">
            <br>
        </form>

        <form action="index.php" method="post">
            <label for="file_input"></label>
            <input type="file" name="file_input">
            <input type="submit" name="upload_button">Upload file</button>
            <br>
        </form>
        
        <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST")
            {
                $server = "localhost";
                $db_username = "root";
                $db_password = "";
                $db_name = "miloje"

                $connection = mysqli_connect($server, $db_username, $db_password, $db_name);
                if (!$connection)
                {
                    die("Connection failed: " . mysqli_connect_error());
                }
                
                $username = $_POST["username_textbox"];
                $password = $_POST["password_textbox"];

                $sql = mysqli_query($connection,
                "SELECT password FROM users WHERE username='$username'");

                $get_password = mysqli_fetch_array($sql)["password"];

                if($get_password == $password)
                    echo "Login succesful!";
                else
                    echo "Incorrect username or password";
            }
        ?>
        <!--JavaScript at end of body for optimized loading-->
        <script type="text/javascript" src="js/materialize.min.js"></script>
    </body>
</html>