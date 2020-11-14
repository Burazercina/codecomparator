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
            $time_limit = 1; # Currently always 1
            $memory_limit = 256; # Currently always 256 
            $query = "SELECT id FROM problems WHERE name=\"$selected_problem\"";
            $result = sqlconnect($query);
            if (mysqli_num_rows($result) == 0)
            {
                $query_insert = "INSERT INTO problems VALUES (NULL, \"$selected_problem\", \"$time_limit\", \"$memory_limit\")";
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
                
                    # Reevaluate exisiting submissions for new input file
                    
                    $target_file = $target_dir . $rand_string . '.' . $ext; // Example: submissions/iqwejvo.cpp
                    if (move_uploaded_file($_FILES["upload"]["tmp_name"][$i], $target_file)) {
                        echo "The file has been uploaded.";
                    } else {
                        echo "Sorry, there was an error uploading your file.";
                    }

                    $query = "SELECT id FROM users";
                    $result = sqlconnect($query);
                    while(mysqli_num_rows($result) > 0 && $row = mysqli_fetch_assoc($result))
                    {
                        $old_user_id = $row['id'];
                        $old_submission = "submissions/$old_user_id/$problem_id/";
                    
                        $last_submission_query = "SELECT hash FROM submissions where id=(SELECT max(id) FROM submissions where user = '$old_user_id' and problem = '$problem_id')";
                        $last_submission_result = sqlconnect($last_submission_query);
						if (mysqli_num_rows($last_submission_result) == 0)
						{
							continue;
						}
                        $source_hash = mysqli_fetch_assoc($last_submission_result)["hash"];
                        $source_path = $old_submission . $source_hash . ".cpp";
                        $inputs_folder = "inputs/$problem_id/";
                        $output_path = $old_submission;
                        $comments_path = $old_submission;
                        $time_limit = 1; # Currently always 1 second, should fetch from database

                        # Create batch file that runs command
                        $batch = fopen("compile.bat", "w");
                        $batch_command = "compile_and_run.py \"" . $source_path . "\" \"" . $inputs_folder . "\" \"" . $output_path . "\" \"" . $comments_path . "\" " . $time_limit;
                        fwrite($batch, $batch_command);
                        fclose($batch);
                        # Execute batch
                        if (file_exists("compile.bat"))
                        {
                            exec("compile.bat");
                            unlink("compile.bat"); # Delete batch when finished running
                        }
                    }
                }
                else if ($ext == "cpp")
                {
                    # Create directories for cpp submissions
                    $user_id = $_SESSION["user_id"];
                    $target_dir = "submissions/$user_id/$problem_id/";
                    if (!file_exists("submissions/$user_id/")) 
                        mkdir("submissions/$user_id/");
                    if (!file_exists($target_dir))
                        mkdir($target_dir);

                    # Arguments for compilation script
                    $source_path = $target_dir . $rand_string . "." . $ext;
                    $inputs_folder = "inputs/$problem_id/";
                    $output_path = $target_dir;
                    $comments_path = $target_dir;
                    $time_limit = 1; # Currently always 1 second, should fetch from database

                    # Create batch file that runs command
                    $batch = fopen("compile.bat", "w");
                    $batch_command = "compile_and_run.py \"" . $source_path . "\" \"" . $inputs_folder . "\" \"" . $output_path . "\" \"" . $comments_path . "\" " . $time_limit;
                    fwrite($batch, $batch_command);
                    fclose($batch);

                    $query_insert = "INSERT INTO submissions VALUES (NULL, \"$user_id\", \"$rand_string\", \"$problem_id\")";
                    sqlconnect($query_insert);
                
                    $target_file = $target_dir . $rand_string . '.' . $ext; // Example: submissions/iqwejvo.cpp
                    if (move_uploaded_file($_FILES["upload"]["tmp_name"][$i], $target_file)) {
                        echo "The file has been uploaded.";
                    } else {
                        echo "Sorry, there was an error uploading your file.";
                    }
    
                    # Execute batch
                    if (file_exists("compile.bat"))
                    {
                        exec("compile.bat");
                        unlink("compile.bat"); # Delete batch when finished running
                    }
                }
                else
                {
                    echo "Smrt";
                    die();
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
                        echo "<td><a href=\" $dir \">";
                        $text = fread($myfile, filesize($dir));
                        echo md5($text);
                        echo "</a></td>";
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
                $outputs_matrix = array();
                $color_matrix = array();

                foreach($user_folders as $folder)
                {
                    if($folder == '.' || $folder == '..') continue;
                    $query = "SELECT username FROM users WHERE id=\"$folder\"";
                    $result = sqlconnect($query);
                    $username = mysqli_fetch_assoc($result)["username"];
                    $user_array = array($username);

                    $outputs_dir = $submissions_dir . $folder . "/" . $problem_id . "/";
                    if (file_exists($outputs_dir))
                    {
                        $outputs = scandir($outputs_dir);
                        foreach($outputs as $output)
                        {
                            if($output == '.' || $output == '..') continue;
                            $dir = $outputs_dir . $output;
                            if (pathinfo($dir)["extension"] == "out")
                            {
                                $myfile = fopen($dir, "r") or die("Unable to open file!");
                                $text = fread($myfile, filesize($dir));
                                array_push($user_array,$text);
                                fclose($myfile);
                            }
                        }
                    }
                    array_push($outputs_matrix,$user_array);
                    # Add the user_array to the color matrix (just to create a row of the same size)
                    array_push($color_matrix,$user_array); 
                }

                # Count number of different values in each column and determine colors
                include 'color_generator.php';
                for($col = 1; $col < count($outputs_matrix[0]); $col++)
                {
                    # Count number of different hashes in each column
                    $hashes_in_column = array();
                    for($row = 0; $row < count($outputs_matrix);$row++)
                    {
                        $output_hash = md5($outputs_matrix[$row][$col]);
                        array_push($hashes_in_column,$output_hash);
                    }
                    $unique_hashes = array_unique($hashes_in_column);
                    # Generate colors and map hash to color
                    $colors = generate_colors(count($unique_hashes));
                    $hash_color_map = array();
                    $color_index = 0;
                    foreach($unique_hashes as $hash)
                    {
                        $hash_color_map[$hash] = $colors[$color_index];
                        $color_index++;
                    }

                    # Fill a column in color matrix
                    for($row = 0; $row < count($outputs_matrix); $row++)
                    {
                        $output_hash = md5($outputs_matrix[$row][$col]);
                        $color_matrix[$row][$col] = $hash_color_map[$output_hash];
                    }
                }
                
                for($row = 0; $row < count($outputs_matrix); $row++)
                {
                    echo "<tr>";
                        echo "<td>{$outputs_matrix[$row][0]}</td>";
                    for($col = 1; $col < count($outputs_matrix[0]); $col++)
                    {
                        $color = $color_matrix[$row][$col];
                        echo "<td style=\"background-color:rgb({$color[0]},{$color[1]},{$color[2]})\">";
                        echo $outputs_matrix[$row][$col];
                        echo "</td>";
                    }
                    echo "</tr>";
                }
            } 
        ?>
    </table>
</body>
</html>