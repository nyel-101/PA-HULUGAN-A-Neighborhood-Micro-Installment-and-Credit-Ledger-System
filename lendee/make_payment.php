<?php
include '../config.php';
check_role('lendee');

$uid = $_SESSION['user_id'];

if(!isset($_GET['id'])){
    header("Location: dashboard.php");
    exit();
}

$loan_id = mysqli_real_escape_string($conn, $_GET['id']);
$loan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM loans WHERE id=$loan_id AND lendee_id=$uid"));

if(!$loan || $loan['status'] == 'paid' || $loan['status'] == 'pending'){
    header("Location: dashboard.php");
    exit();
}

$error = '';
$success = '';

if(isset($_POST['make_payment'])){
    $amount = floatval($_POST['amount']);
    $method = mysqli_real_escape_string($conn, $_POST['method']);
    
    if($amount <= 0) {
        $error = "Please enter a valid amount";
    } elseif($amount > $loan['remaining_balance']) {
        $error = "Payment cannot exceed remaining balance (₱" . number_format($loan['remaining_balance'], 2) . ")";
    } else {
        $new_balance = $loan['remaining_balance'] - $amount;
        $new_status = ($new_balance <= 0) ? 'paid' : $loan['status']; 
        
        
        $payment_query = "INSERT INTO payments (loan_id, payment_amount, payment_date, payment_method) 
                          VALUES ($loan_id, $amount, CURDATE(), '$method')";
        
        $payment_result = mysqli_query($conn, $payment_query);
        
        if($payment_result){
            
            $update_loan = mysqli_query($conn, "UPDATE loans SET remaining_balance=$new_balance, status='$new_status' WHERE id=$loan_id");
            
            if($update_loan){
                
                $user_name = mysqli_real_escape_string($conn, $_SESSION['name']);
                $msg = "$user_name paid ₱" . number_format($amount, 2) . " for " . $loan['item_details'];
                mysqli_query($conn, "INSERT INTO notifications (user_id, message, type) VALUES (1, '$msg', 'payment_received')");
                
                $success = "Payment of ₱" . number_format($amount, 2) . " recorded successfully!";
                
                $loan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM loans WHERE id=$loan_id"));
            } else {
                $error = "Payment recorded, but failed to update loan balance: " . mysqli_error($conn);
            }
        } else {
            
            $error = "Error recording payment: " . mysqli_error($conn);
        }
    }
}
?>

<link rel="stylesheet" href="../style.css">
<div class="container" style="max-width:600px;">
    <h2>Confirm Payment</h2>
    
    <div class="card" style="background:#f8f9fa; margin-bottom:20px; border-left: 5px solid #27ae60;">
        <h3 style="margin-top:0;">Loan Summary</h3>
        <p><strong>Item:</strong> <?php echo htmlspecialchars($loan['item_details']); ?></p>
        <div style="display:flex; justify-content:space-between; font-weight:bold;">
            <span style="color:#e74c3c;">Balance: ₱<?php echo number_format($loan['remaining_balance'], 2); ?></span>
            <span>Due: <?php echo $loan['due_date']; ?></span>
        </div>
    </div>

    <?php if($success): ?>
        <div style="background:#d4edda; color:#155724; padding:15px; margin-bottom:20px; border-radius:5px; border: 1px solid #c3e6cb;">
            <strong>✓ Success!</strong> <?php echo $success; ?>
        </div>
    <?php endif; ?>

    <?php if($error): ?>
        <div style="background:#f8d7da; color:#721c24; padding:15px; margin-bottom:20px; border-radius:5px; border: 1px solid #f5c6cb;">
            <strong>✗ Error:</strong> <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <?php if($loan['remaining_balance'] > 0 && !$success): ?>
    <form method="POST" class="card">
        <label><strong>Amount to Pay (₱):</strong></label>
        <input type="number" name="amount" step="0.01" max="<?php echo $loan['remaining_balance']; ?>" 
               value="<?php echo $loan['remaining_balance']; ?>" required 
               style="width:100%; padding:12px; margin:10px 0; font-size:18px; border: 1px solid #ccc; border-radius: 4px;">
        
        <label><strong>Payment Method:</strong></label>
        <select name="method" required style="width:100%; padding:12px; margin:10px 0; border: 1px solid #ccc; border-radius: 4px;">
            <option value="cash"> Cash</option>
            <option value="e_wallet"> E-Wallet / GCash</option>
            <option value="bank_transfer"> Bank Transfer</option>
        </select>
        
        <button type="submit" name="make_payment" class="btn" style="width:100%; padding:12px; background:#27ae60; color:white; border:none; cursor:pointer; font-size:16px; border-radius:4px; font-weight:bold;">Submit Payment</button>
    </form>
    <?php endif; ?>
    
    <br><a href="dashboard.php" style="text-decoration:none; color:#3498db;">← Back to Dashboard</a>
</div>