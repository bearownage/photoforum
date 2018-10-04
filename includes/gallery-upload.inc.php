<?php
  //if the user hit the submit button
  if (isset($_POST['submit'])) {

    $newFileName = $_POST['filename'];
    //check spaces
    if (empty($newFileName)) {
      $newFileName = "gallery";
    } else {
      //if the user added spaces to the file name
      $newFileName = strtolower(str_replace(" ", "-", $newFileName ));
    }
    //for sql server
    $imageTitle = $_POST['filetitle'];
    $imageDesc = $_POST['filedesc'];

    $file = $_FILES["file"];

    //getting data from the file
    $fileName = $file["name"];
    $fileType = $file["type"];
    $fileTempName = $file["tmp_name"];
    $fileError = $file["error"];
    $fileSize = $file["size"];

    $fileExt = explode(".", $fileName);
    $fileActualExt = strtolower(end($fileExt));

    $allowed = array("jpg", "jpeg", "png");

    if (in_array($fileActualExt, $allowed))
    {
      if ($fileError === 0 )
      {
        if ($fileSize < 2000000)
        {
            $imageFullName = $newFileName . "." . uniqid("", true) . "." . $fileActualExt;
            $fileDestination = "../img/gallery/" . $imageFullName;

            include_once "dbh.inc.php";

            if (empty($imageTitle) || empty($imageDesc)) {
              header("Location: ../gallery.php?upload=empty");
              exit();
            } else {
              $sql = "SELECT * FROM gallery;";
              $stmt = mysqli_stmt_init($conn);
              //if this doesnt work
              if (!mysqli_stmt_prepare($stmt, $sql)) {
                echo "SQL statement failed 1";
            } else {
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $rowCount = mysqli_num_rows($result);
                $setImageOrder = $rowCount + 1;
                //placeholders
                $sql = "INSERT INTO gallery (titleGallery, descGallery, imgFullNameGallery, orderGallery) VALUES (?, ?, ?, ?);";
                if (!mysqli_stmt_prepare($stmt, $sql)) {
                  echo "SQL statement failed 2";
                }
                else
                {
                  mysqli_stmt_bind_param($stmt, "ssss", $imageTitle, $imageDesc, $imageFullName, $setImageOrder );
                  mysqli_stmt_execute($stmt);

                  move_uploaded_file($fileTempName, $fileDestination);

                  echo "Why is this not work";

                  header("Location: ../gallery.php?upload=success");
                }
            }
          }
        }
        else
        {
          echo "File size is too big!";
          exit();
        }
      }
      else
      {
        echo "You had an error!";
        exit();
      }
    }
    else
    {
      echo "You need to upload a proper file type!";
      exit();
    }
  }

 ?>
