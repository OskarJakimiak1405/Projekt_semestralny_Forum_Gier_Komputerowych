<?php
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['avatar'])) {
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["avatar"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    $check = getimagesize($_FILES["avatar"]["tmp_name"]);
    if ($check !== false) {
        $uploadOk = 1;
    } else {
        echo "BŁĄD! Plik nie jest obrazem.";
        $uploadOk = 0;
    }

    if ($_FILES["avatar"]["size"] > 2000000) {
        echo "BŁĄD! Plik jest za duży, zmień wielkość na mniejszą.";
        $uploadOk = 0;
    }

    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        echo "BŁĄD! Tylko pliki JPG, JPEG, PNG i GIF są dozwolone, zmień format.";
        $uploadOk = 0;
    }

    if ($uploadOk == 0) {
        echo "BŁĄD! Nie udało się przesłać pliku, spróbuj ponownie.";

    } else {
        if (move_uploaded_file($_FILES["avatar"]["tmp_name"], $target_file)) {

            $stmt = $conn->prepare("UPDATE users SET avatar = ? WHERE id = ?");
            $stmt->execute([basename($_FILES["avatar"]["name"]), $_SESSION['user_id']]);
            header("Location: profile.php");
        } else {
            echo "BŁĄD! Wystąpił błąd podczas przesyłania pliku.";
        }
    }
}
?>
