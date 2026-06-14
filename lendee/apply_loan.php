<?php
include '../config.php';
check_role('lendee');

if(isset($_POST['apply'])){
    $type = mysqli_real_escape_string($conn, $_POST['type']);
    $det = mysqli_real_escape_string($conn, $_POST['det']);
    $amt = mysqli_real_escape_string($conn, $_POST['amt']);
    $uid = $_SESSION['user_id'];
    
    
    if($amt <= 0) {
        echo "<script>alert('Invalid amount');</script>";
    } else {
        $result = mysqli_query($conn, "INSERT INTO loans (lendee_id, loan_type, item_details, total_amount, remaining_balance, status) 
                            VALUES ($uid, '$type', '$det', $amt, $amt, 'pending')");
        
        if($result){
           
            $user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT full_name FROM users WHERE id=$uid"));
            mysqli_query($conn, "INSERT INTO notifications (user_id, message, type) 
                                VALUES (1, '" . $user['full_name'] . " requested a " . $type . " loan for ₱" . number_format($amt, 2) . "', 'loan_request')");
            
            echo "<script>alert('Loan request submitted! The lender will review it shortly.'); window.location='dashboard.php';</script>";
        } else {
            echo "<script>alert('Error submitting request. Please try again.');</script>";
        }
    }
}
?>
<link rel="stylesheet" href="../style.css">
<div class="card" style="max-width:500px; margin:auto;">
    <h2>Request a Loan</h2>
    <p style="color:#666; margin-bottom:20px;">Fill out the form below to apply for a loan. The lender will review your request and notify you of approval.</p>
    
    <form method="POST">
        <label><strong>Loan Type:</strong></label>
        <select name="type" required style="padding:8px; margin-bottom:15px; width:100%;">
            <option value="">-- Select Loan Type --</option>
            <option value="cash">Cash Loan</option>
            <option value="gadget">Gadget/Electronics</option>
            <option value="appliance">Home Appliance</option>
        </select>
        
        <label><strong>Item Description/Details:</strong></label>
        <input type="text" name="det" placeholder="e.g., iPhone 13, Samsung Washing Machine, etc." required style="padding:8px; margin-bottom:15px; width:100%;">
        
        <label><strong>Loan Amount (₱):</strong></label>
        <input type="number" name="amt" placeholder="Enter amount" min="1" step="0.01" required style="padding:8px; margin-bottom:15px; width:100%;">
        
        <button type="submit" name="apply" class="btn" style="width:100%; padding:10px;">Submit Loan Request</button>
    </form>
    
    <hr style="margin:20px 0;">
    <p style="color:#999; font-size:12px;">
        <strong>Note:</strong> Your request will be reviewed by the lender. You will receive a notification once approved with the payment schedule and terms.
    </p>
</div>
<br>
<div style="text-align:center;">
    <a href="dashboard.php" class="btn">← Back to Dashboard</a>
</div>
