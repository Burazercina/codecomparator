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
            $total = count($_FILES['upload']['name']);
            for($i = 0; $i < $total; $i++)
            {
                $rand_string = substr(md5(rand()), 0, 7); // Generate random string
                $ext = strtolower(pathinfo($_FILES["upload"]["name"][$i], PATHINFO_EXTENSION));

                $target_dir;
                if ($ext == "in")
                    $target_dir = "inputs/";
                else if ($ext == "cpp")
                    $target_dir = "submissions/";
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
</body>
</html>