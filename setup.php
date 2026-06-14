<?php


include 'config.php';

$message = '';
$error = '';


if(isset($_POST['create_lender'])){
    $username = 'lender1';
    $password = password_hash('password123', PASSWORD_DEFAULT);
    $name = 'Admin Lender';
    $contact = '09000000001';
    
    $check = mysqli_query($conn, "SELECT id FROM users WHERE username='$username'");
    if(mysqli_num_rows($check) > 0){
        $error = "Lender account already exists";
    } else {
        $result = mysqli_query($conn, "INSERT INTO users (username, password, full_name, role, contact_no, account_status) 
                                      VALUES ('$username', '$password', '$name', 'lender', '$contact', 'approved')");
        if($result){
            $message = "✓ Lender account created!<br>Username: <strong>lender1</strong><br>Password: <strong>password123</strong>";
        } else {
            $error = "Error creating account";
        }
    }
}


$db_test = false;
$db_tables = [];
if($conn){
    $db_test = true;
    $tables = mysqli_query($conn, "SHOW TABLES FROM lending_db");
    while($t = mysqli_fetch_array($tables)){
        $db_tables[] = $t[0];
    }
}


$user_count = 0;
$loan_count = 0;
$payment_count = 0;

if($db_test && in_array('users', $db_tables)){
    $user_result = mysqli_query($conn, "SELECT COUNT(*) as count FROM users");
    $user_count = mysqli_fetch_assoc($user_result)['count'];
    
    if(in_array('loans', $db_tables)){
        $loan_result = mysqli_query($conn, "SELECT COUNT(*) as count FROM loans");
        $loan_count = mysqli_fetch_assoc($loan_result)['count'];
    }
    
    if(in_array('payments', $db_tables)){
        $payment_result = mysqli_query($conn, "SELECT COUNT(*) as count FROM payments");
        $payment_count = mysqli_fetch_assoc($payment_result)['count'];
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Lending System - Setup & Admin</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .setup-container {
            max-width: 900px;
            margin: 50px auto;
        }
        .status-good { color: #27ae60; font-weight: bold; }
        .status-bad { color: #e74c3c; font-weight: bold; }
        .info-box {
            background: #ecf0f1;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
            border-left: 4px solid #3498db;
        }
        .db-table {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin: 10px 0;
        }
        .db-item {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            border-left: 3px solid #27ae60;
        }
        .credentials-box {
            background: #fff3cd;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #ffc107;
            font-family: monospace;
            margin: 10px 0;
        }
        .warning { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 15px 0; }
    </style>
</head>
<body>
<div class="setup-container">
    <div class="card">
        <h2>Lending System Setup & Administration</h2>
        <p style="color: #666; margin-bottom: 20px;">Use this page to initialize your lending system database and create test accounts.</p>
        
        <!-- Status Messages -->
        <?php if($message) { ?>
            <div class="success">[SUCCESS] <strong><?php echo $message; ?></strong></div>
        <?php } ?>
        <?php if($error) { ?>
            <div class="error">[ERROR] <strong><?php echo $error; ?></strong></div>
        <?php } ?>
        
        
        <h3>1. Database Connection</h3>
        <div class="info-box">
            <p><strong>Status:</strong> 
                <?php if($db_test) { ?>
                    <span class="status-good">[OK] Connected to 'lending_db'</span>
                <?php } else { ?>
                    <span class="status-bad">[FAILED] Connection Failed</span>
                <?php } ?>
            </p>
            
            <?php if($db_test && count($db_tables) > 0) { ?>
            <p><strong>Database Tables Found:</strong></p>
            <div class="db-table">
                <?php foreach($db_tables as $table) { ?>
                <div class="db-item"><?php echo $table; ?></div>
                <?php } ?>
            </div>
            
            <p style="margin-top: 15px;"><strong>Current Data:</strong></p>
            <ul style="margin-left: 20px;">
                <li>Users: <strong><?php echo $user_count; ?></strong></li>
                <li>Loans: <strong><?php echo $loan_count; ?></strong></li>
                <li>Payments: <strong><?php echo $payment_count; ?></strong></li>
            </ul>
            
            <?php } else if($db_test) { ?>
                <p style="color: #e74c3c;">[WARNING] No tables found. Please import database_schema.sql first.</p>
            <?php } ?>
        </div>
        
        <h3 style="margin-top: 30px;">2. Create Default Lender Account</h3>
        <div class="info-box">
            <p>Click the button below to create a lender account for testing.</p>
            <form method="POST" style="margin-top: 15px;">
                <button type="submit" name="create_lender" class="btn" style="background: #27ae60;"> Create Lender Account</button>
            </form>
            
            <?php if($user_count > 0 && mysqli_fetch_assoc(mysqli_query($conn, "SELECT id FROM users WHERE role='lender' LIMIT 1"))) { ?>
            <p style="margin-top: 15px; color: #27ae60;">[OK] Lender account(s) already exist</p>
            <?php } ?>
        </div>
        
       
        <h3 style="margin-top: 30px;">3. Quick Start Guide</h3>
        <div class="info-box">
            <h4>Step 1: Database Setup</h4>
            <ol style="margin-left: 20px;">
                <li>Open phpMyAdmin: http://localhost/phpmyadmin</li>
                <li>Create database named 'lending_db'</li>
                <li>Import SQL from <strong>database_schema.sql</strong></li>
                <li>Verify tables appear above</li>
            </ol>
            
            <h4 style="margin-top: 15px;">Step 2: Create Accounts</h4>
            <ol style="margin-left: 20px;">
                <li>Use button above to create lender account</li>
                <li>Go to <strong>register.php</strong> to create borrower account</li>
                <li>Approve borrower in Lender Dashboard</li>
            </ol>
            
            <h4 style="margin-top: 15px;">Step 3: Test the System</h4>
            <ol style="margin-left: 20px;">
                <li><a href="index.php" target="_blank">Login as Lender</a> (lender1 / password123)</li>
                <li><a href="register.php" target="_blank">Register as Borrower</a></li>
                <li>Lender approves borrower in User Approvals</li>
                <li>Borrower applies for loan</li>
                <li>Lender approves loan and sets terms</li>
                <li>Borrower makes payment</li>
            </ol>
        </div>
        
        
        <h3 style="margin-top: 30px;">Test Credentials</h3>
        <div class="credentials-box">
            <p><strong>Lender Account:</strong></p>
            Username: lender1<br>
            Password: password123<br>
            <a href="index.php" class="btn" style="margin-top: 10px;">Go to Login</a>
        </div>
        
       
        <h3 style="margin-top: 30px;">Important Notes</h3>
        <div class="warning">
            <strong>[WARNING] SECURITY:</strong><br>
            This setup page should be <strong>DELETED</strong> before deploying to production. 
            It exposes sensitive information and allows unauthorized database modifications.
            <br><br>
            Delete this file after setup: <strong>setup.php</strong>
        </div>
        
       
        <h3 style="margin-top: 30px;">✓ Implemented Features</h3>
        <div class="info-box">
            <ul style="margin-left: 20px;">
                <li>✓ User authentication with password hashing</li>
                <li>✓ Lender approval system for borrowers</li>
                <li>✓ Loan request management</li>
                <li>✓ Payment processing and tracking</li>
                <li>✓ Automatic penalty calculation for overdue loans</li>
                <li>✓ Real-time notification system</li>
                <li>✓ SQL injection prevention</li>
                <li>✓ Role-based access control</li>
                <li>✓ File upload validation</li>
                <li>✓ Responsive UI design</li>
            </ul>
        </div>
        
      
        <h3 style="margin-top: 30px;"> System Status</h3>
        <div class="info-box">
            <p><strong>PHP Version:</strong> <?php echo phpversion(); ?></p>
            <p><strong>MySQL Extension:</strong> <?php echo extension_loaded('mysqli') ? '✓ Available' : '✗ Not Available'; ?></p>
            <p><strong>File Uploads:</strong> <?php echo ini_get('file_uploads') ? '✓ Enabled' : '✗ Disabled'; ?></p>
            <p><strong>Max Upload Size:</strong> <?php echo ini_get('upload_max_filesize'); ?></p>
        </div>
        
        <hr style="margin: 30px 0;">
        <p style="text-align: center; color: #666;">
            <a href="README.md" target="_blank"> Read Full Documentation</a> | 
            <a href="index.php"> Go to Login</a>
        </p>
    </div>
</div>
</body>
</html>