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
        <div class = "container">
            <form action="index.php" method="post">
                <div class = "row">
                    <div class="input-field col s4 offset-s4">
                        <input placeholder="Username" name="username_textbox" type="text" class="validate">
                    </div>
                </div>
                <div class = "row">
                    <div class="input-field col s4 offset-s4">
                        <input placeholder="Password" name="password_textbox" type="text" class="validate">
                    </div>
                </div>
                <div class="row">
                    <button class="btn waves-effect waves-light col s2 offset-s5" type="submit" name="login_button" value="Login">Submit</button>
                </div>
            </form>

        </div>
        <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST")
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
                
                $username = $_POST["username_textbox"];
                $password = $_POST["password_textbox"];

                $sql = mysqli_query($connection,
                "SELECT password FROM users WHERE username='$username'");

                $row = mysqli_fetch_array($sql);
                if ($row != NULL && $row["password"] == $password)
                {
                    echo "Login succesful!";
                    header("Location: upload.php");
                }
                else
                    echo "Incorrect username or password";
            }
        ?>
        <!--JavaScript at end of body for optimized loading-->
        <script type="text/javascript" src="js/materialize.min.js"></script>
    </body>
</html>