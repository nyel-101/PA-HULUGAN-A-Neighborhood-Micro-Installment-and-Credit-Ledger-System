<?php
include '../config.php';
check_role('lender');

if(!isset($_GET['id'])){
    header("Location: dashboard.php");
    exit();
}

$loan_id = mysqli_real_escape_string($conn, $_GET['id']);
$loan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT l.*, u.full_name, u.contact_no FROM loans l 
                                                  JOIN users u ON l.lendee_id = u.id 
                                                  WHERE l.id=$loan_id"));

if(!$loan){
    header("Location: dashboard.php");
    exit();
}


$payments = mysqli_query($conn, "SELECT * FROM payments WHERE loan_id=$loan_id ORDER BY payment_date DESC");
?>
<link rel="stylesheet" href="../style.css">
<div class="container">
    <h2>Loan Details</h2>
    
    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:20px; margin:20px 0;">
        
        <div class="card">
            <h3>Borrower Information</h3>
            <p><strong>Name:</strong> <?php echo $loan['full_name']; ?></p>
            <p><strong>Contact:</strong> <?php echo $loan['contact_no']; ?></p>
            <p><strong>Loan ID:</strong> #<?php echo $loan['id']; ?></p>
            <p><strong>Loan Type:</strong> <?php echo ucfirst($loan['loan_type']); ?></p>
            <p><strong>Item/Details:</strong> <?php echo $loan['item_details']; ?></p>
        </div>
        
       
        <div class="card">
            <h3>Loan Status</h3>
            <p><strong>Status:</strong> 
                <span style="padding:5px 10px; border-radius:5px;
                    <?php 
                    if($loan['status'] == 'active') echo 'background:#27ae60; color:white;';
                    elseif($loan['status'] == 'paid') echo 'background:#2ecc71; color:white;';
                    elseif($loan['status'] == 'overdue') echo 'background:#e74c3c; color:white;';
                    elseif($loan['status'] == 'pending') echo 'background:#f39c12; color:white;';
                    ?>">
                    <?php echo strtoupper($loan['status']); ?>
                </span>
            </p>
            <p><strong>Total Amount:</strong> ₱<?php echo number_format($loan['total_amount'], 2); ?></p>
            <p><strong>Remaining Balance:</strong> ₱<?php echo number_format($loan['remaining_balance'], 2); ?></p>
            <p><strong>Due Date:</strong> <?php echo $loan['due_date'] ?? 'Not Set'; ?></p>
            <p><strong>Penalty per Day:</strong> ₱<?php echo number_format($loan['penalty'], 2); ?></p>
        </div>
    </div>
    
 
    <div class="card">
        <h3>Payment History</h3>
        <?php if($payments && mysqli_num_rows($payments) > 0) { ?>
        <table>
            <tr>
                <th>Payment Date</th>
                <th>Amount Paid</th>
                <th>Payment Method</th>
                <th>Status</th>
            </tr>
            <?php 
            $total_paid = 0;
            while($p = mysqli_fetch_assoc($payments)){ 
                $total_paid += $p['payment_amount'];
            ?>
            <tr>
                <td><?php echo date('M d, Y', strtotime($p['payment_date'])); ?></td>
                <td>₱<?php echo number_format($p['payment_amount'], 2); ?></td>
                <td><?php echo ucfirst($p['payment_method'] ?? 'Not specified'); ?></td>
                <td><span style="color:green; font-weight:bold;">✓ <?php echo ucfirst($p['status']); ?></span></td>
            </tr>
            <?php } ?>
            <tr style="background:#f5f5f5; font-weight:bold;">
                <td>TOTAL PAID</td>
                <td style="color:green;">₱<?php echo number_format($total_paid, 2); ?></td>
                <td colspan="2"></td>
            </tr>
        </table>
        <?php } else { ?>
        <p style="text-align:center; padding:20px; color:#666;">No payments recorded yet.</p>
        <?php } ?>
    </div>
    
    <br><a href="dashboard.php" class="btn">← Back to Dashboard</a>
</div>
