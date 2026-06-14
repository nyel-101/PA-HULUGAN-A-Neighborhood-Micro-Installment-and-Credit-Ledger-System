<?php
include 'config.php';
if(isset($_POST['login'])){
    $u = mysqli_real_escape_string($conn, $_POST['u']);
    $p = mysqli_real_escape_string($conn, $_POST['p']);
    
    $res = mysqli_query($conn, "SELECT * FROM users WHERE username='$u' AND password='$p'");
    $user = mysqli_fetch_assoc($res);
    
    if($user){
        if($user['account_status'] != 'approved') { 
            echo "<script>alert('Account Pending Approval by Lender');</script>"; 
        }
        else {
            $_SESSION['user_id'] = $user['id']; 
            $_SESSION['role'] = $user['role'];
            $_SESSION['name'] = $user['full_name'];
            header("Location: " . ($user['role'] == 'lender' ? "lender/dashboard.php" : "lendee/dashboard.php"));
            exit();
        }
    } else { 
        echo "<script>alert('Invalid Username or Password');</script>"; 
    }
}
?>
<link rel="stylesheet" href="style.css">
<style>
    body {
      
        background-image: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('assets/background.jpg'); 
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        background-repeat: no-repeat;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        height: 100vh;
        margin: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .card {
        background: rgba(255, 255, 255, 0.15); 
        backdrop-filter: blur(10px); 
        padding: 40px;
        border-radius: 15px;
        box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37);
        border: 1px solid rgba(255, 255, 255, 0.18);
        color: white;
        text-align: center;
        width: 100%;
        max-width: 350px;
    }

    h2 { margin-bottom: 25px; }

    input {
        width: 100%;
        padding: 12px;
        margin-bottom: 15px;
        border-radius: 5px;
        border: none;
        box-sizing: border-box; 
    }

    .btn {
        width: 100%;
        padding: 12px;
        background-color: #27ae60;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-weight: bold;
    }

    a { color: #5dade2; text-decoration: none; }
</style>

<div class="card">
    <h2>PA-HULUGAN: Neighborhood Micro-Installment System</h2>
    <form method="POST">
        <input type="text" name="u" placeholder="Username" required>
        <input type="password" name="p" placeholder="Password" required>
        <button type="submit" name="login" class="btn">Login</button>
    </form>
    <p>No account? <a href="register.php">Register Here</a></p>
</div>