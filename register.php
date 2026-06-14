<?php
include 'config.php';
$errors = [];

if(isset($_POST['reg'])){
    $u = mysqli_real_escape_string($conn, $_POST['u']);
    $p = $_POST['p'];
    $n = mysqli_real_escape_string($conn, $_POST['n']);
    $c = mysqli_real_escape_string($conn, $_POST['c']);

    if(strlen($u) < 3) $errors[] = "Username must be at least 3 characters";
    if(strlen($p) < 6) $errors[] = "Password must be at least 6 characters";
    if(strlen($n) < 3) $errors[] = "Full name required";
    if(strlen($c) < 10) $errors[] = "Valid contact number required";
    
    
    $check = mysqli_query($conn, "SELECT id FROM users WHERE username='$u'");
    if(mysqli_num_rows($check) > 0) $errors[] = "Username already taken";
  
    if(!isset($_FILES['f']) || $_FILES['f']['error'] != 0) $errors[] = "Face photo required";
    if(!isset($_FILES['i']) || $_FILES['i']['error'] != 0) $errors[] = "ID photo required";
    if(!isset($_FILES['v']) || $_FILES['v']['error'] != 0) $errors[] = "Video proof required";
    
    if(empty($errors)){
        
        if(!file_exists("uploads")) mkdir("uploads", 0777, true);
        
        
        $face = time() . "_face_" . basename($_FILES['f']['name']);
        $id_p = time() . "_id_" . basename($_FILES['i']['name']);
        $vid = time() . "_vid_" . basename($_FILES['v']['name']);
        
        move_uploaded_file($_FILES['f']['tmp_name'], "uploads/".$face);
        move_uploaded_file($_FILES['i']['tmp_name'], "uploads/".$id_p);
        move_uploaded_file($_FILES['v']['tmp_name'], "uploads/".$vid);
        
        $sql = "INSERT INTO users (username, password, full_name, role, contact_no, face_photo, id_photo, video_proof, account_status) 
                VALUES ('$u', '$p', '$n', 'lendee', '$c', '$face', '$id_p', '$vid', 'pending')";
        
        if(mysqli_query($conn, $sql)){
            
            $user_id = mysqli_insert_id($conn);
            mysqli_query($conn, "INSERT INTO notifications (user_id, message, type) 
                                VALUES (1, 'New borrower registration from $n', 'user_registration')");
            echo "<script>alert('Registration successful! Your account is pending lender approval.'); window.location='index.php';</script>";
        } else {
            $errors[] = "Registration failed. Please try again.";
        }
    }
    
    if(!empty($errors)){
        echo "<script>alert('Error: " . implode("\\n", $errors) . "');</script>";
    }
}
?>
<link rel="stylesheet" href="style.css">
<div class="card" style="max-width:500px; margin:20px auto;">
    <h2>Borrower Registration</h2>
    <form method="POST" enctype="multipart/form-data">
        <input type="text" name="u" placeholder="Username (min 3 chars)" required><br>
        <input type="password" name="p" placeholder="Password (min 6 chars)" required><br>
        <input type="text" name="n" placeholder="Full Name" required><br>
        <input type="text" name="c" placeholder="Contact Number" required><br>
        Face Photo: <input type="file" name="f" accept="image/*" required><br>
        ID Photo: <input type="file" name="i" accept="image/*" required><br>
        Video Proof: <input type="file" name="v" accept="video/*" required><br>
        <small>Upload a short video proving you'll pay on time</small><br><br>
        <button type="submit" name="reg" class="btn">Register</button>
    </form>
    <p>Already registered? <a href="index.php">Login Here</a></p>
</div>
