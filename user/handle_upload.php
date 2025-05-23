<?php
    session_start();
    $server = @mysqli_connect("localhost:3307", "root", "", "system_user") or die ("Không thể kết nối đến database");
    $user = $_SESSION['username'];
    $stmt = $server->prepare("select id from user where user = ?");
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $stmt->bind_result($user_id);
    $stmt->fetch();
    $stmt->close();
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $tittle = $_POST['title'];
        $description = $_POST['description'];
        if(isset($_FILES['image']) && $_FILES['image']['error'] == 0){
            $file_name = $_FILES['image']['name'];
            $file_tmp = $_FILES['image']['tmp_name'];
            
            $upload_dir = "uploads/";
            if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true); 
            }
            $file_path = $upload_dir . basename($file_name);
            if (move_uploaded_file($file_tmp, $file_path)) {
                // Lưu đường dẫn file + title vào CSDL
                $table = $server->prepare("insert into upload_picture (TenPic, filePic, idUser, MoTa) VALUES (?, ?, ?, ?)");
                $table->bind_param("ssss", $tittle, $file_path, $user_id, $description);
                if($table->execute()){
                    header ("Location: uploadpic.php");
                    $_SESSION['access'] = "Upload thành công!";
                    exit();
                }
                else {
                    header ("Location: uploadpic.php");
                    $_SESSION['error'] = "Lỗi khi lưu vào CSDL.";
                }
                $table->close();
            } else {
                $_SESSION['error'] = "Lỗi khi lưu file.";
                exit();
            }
        }
        else {
            echo "Không có file được tải lên.";
        }
    }
?>