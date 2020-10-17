<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload</title>
</head>
<body>
    <form action="upload.php" method="post" enctype="multipart/form-data">
        <label for="upload[]"></label>
        <input type="file" name="upload[]" multiple="multiple">
        <input type="submit" name="upload_button">
        <input list="problems" name="problem_list">
        <datalist id="problems">
            <?php
                include 'connect_to_db.php';
                $query = "SELECT * FROM problems";
                $result = sqlconnect($query);
                while(mysqli_num_rows($result) > 0 && $row = mysqli_fetch_assoc($result))
                {
                    echo "<option value=\"" . $row["name"] . "\">";
                }
            ?>
        </datalist>
    </form>

    <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST")
        {
            $selected_problem = $_POST["problem_list"];
            $query = "SELECT id FROM problems WHERE name=\"$selected_problem\"";
            $result = sqlconnect($query);
            if (mysqli_num_rows($result) == 0)
            {
                $query_insert = "INSERT INTO problems VALUES (NULL, \"$selected_problem\")";
                sqlconnect($query_insert);
            }
            
            $result = sqlconnect($query);
            $problem_id = mysqli_fetch_assoc($result)["id"];

            $total = count($_FILES['upload']['name']);
            for($i = 0; $i < $total; $i++)
            {
                $rand_string = substr(md5(rand()), 0, 7); // Generate random string
                $ext = strtolower(pathinfo($_FILES["upload"]["name"][$i], PATHINFO_EXTENSION));

                $target_dir;
                if ($ext == "in")
                {
                    $target_dir = "inputs/$problem_id/";
                    $query_insert = "INSERT INTO inputs VALUES (NULL, \"$problem_id\")";
                    if (!file_exists($target_dir))
                        mkdir($target_dir);
                    sqlconnect($query_insert);
                }
                else if ($ext == "cpp")
                {
                    $user_id = $_SESSION["user_id"];
                    $target_dir = "submissions/$user_id/$problem_id/";
                    if (!file_exists("submissions/$user_id/")) 
                        mkdir("submissions/$user_id/");
                    if (!file_exists($target_dir))
                        mkdir($target_dir);
                    $query_insert = "INSERT INTO submissions VALUES (NULL, \"$user_id\", \"$rand_string\", \"$problem_id\")";
                    sqlconnect($query_insert);
                }
                else
                {
                    echo "Smrt";
                    die();
                }

                $target_file = $target_dir . $rand_string . '.' . $ext; // Example: submissions/iqwejvo.cpp
                if (move_uploaded_file($_FILES["upload"]["tmp_name"][$i], $target_file)) {
                    echo "The file has been uploaded.";
                } else {
                    echo "Sorry, there was an error uploading your file.";
                }
            }
        }
    ?>

<table>
        <tr>
            <td>Inputs</td>
            <?php
                if ($_SERVER["REQUEST_METHOD"] == "POST")
                {
                    # print inputs
                    $inputs_dir = "inputs/$problem_id/";
                    $files = scandir($inputs_dir);
                    foreach($files as $file)
                    {
                        if($file == '.' || $file == '..') continue;
                        $dir = $inputs_dir . $file;
                        $myfile = fopen($dir, "r") or die("Unable to open file!");
                        echo "<td>";
                        echo fread($myfile, filesize($dir));
                        echo "</td>";
                        fclose($myfile);
                    }
                }
            ?>
        </tr>
        <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST")
            {
                # print output for each user
                $submissions_dir = "submissions/";
                $user_folders = scandir($submissions_dir);
                foreach($user_folders as $folder)
                {
                    if($folder == '.' || $folder == '..') continue;
                    echo "<tr>";
                    echo "<td>";
                    $query = "SELECT username FROM users WHERE id=\"$folder\"";
                    $result = sqlconnect($query);
                    $username = mysqli_fetch_assoc($result)["username"];
                    echo $username;
                    echo "</td>";
                    $outputs_dir = $submissions_dir . $folder . "/" . $problem_id . "/";
                    $outputs = scandir($outputs_dir);
                    foreach($outputs as $output)
                    {
                        if($output == '.' || $output == '..') continue;
                        $dir = $outputs_dir . $output;
                        if (pathinfo($dir)["extension"] == "out")
                        {
                            $myfile = fopen($dir, "r") or die("Unable to open file!");
                            echo "<td>";
                            echo fread($myfile, filesize($dir));
                            echo "</td>";
                            fclose($myfile);
                        }
                    }
                    echo "</tr>";
                }
            } 
        ?>
    </table>
</body>
</html>