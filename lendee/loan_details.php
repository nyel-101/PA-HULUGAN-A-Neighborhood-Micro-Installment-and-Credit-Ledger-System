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
            <h3 style="margin-top:0;">Loan Information</h3>
            <p><strong>Item:</strong> <?php echo $loan['item_details']; ?></p>
            <p><strong>Loan Type:</strong> <?php echo ucfirst($loan['loan_type']); ?></p>
            <p><strong>Total Amount:</strong> ₱<?php echo number_format($loan['total_amount'], 2); ?></p>
            <p><strong>Applied Date:</strong> <?php echo date('M d, Y', strtotime($loan['created_at'])); ?></p>
        </div>
        
        
        <div class="card">
            <h3 style="margin-top:0;">Payment Status</h3>
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
            <p><strong>Due Date:</strong> <?php echo $loan['due_date'] ?? '% Pending Approval'; ?></p>
            <p><strong>Balance Due:</strong> ₱<?php echo number_format($loan['remaining_balance'], 2); ?></p>
            <p><strong>Penalty/Day:</strong> ₱<?php echo number_format($loan['penalty'], 2); ?></p>
            
            <?php if($loan['status'] == 'active' || $loan['status'] == 'overdue') { ?>
            <a href="make_payment.php?id=<?php echo $loan['id']; ?>" class="btn" style="width:100%; margin-top:10px; background:#27ae60;">💳 Make Payment</a>
            <?php } ?>
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
            </tr>
            <?php 
            $total_paid = 0;
            while($p = mysqli_fetch_assoc($payments)){ 
                $total_paid += $p['payment_amount'];
            ?>
            <tr>
                <td><?php echo date('M d, Y', strtotime($p['payment_date'])); ?></td>
                <td style="font-weight:bold; color:#27ae60;">₱<?php echo number_format($p['payment_amount'], 2); ?></td>
                <td><?php echo ucfirst(str_replace('_', ' ', $p['payment_method'])); ?></td>
            </tr>
            <?php } ?>
            <tr style="background:#f5f5f5; font-weight:bold;">
                <td>TOTAL PAID</td>
                <td style="color:#27ae60;">₱<?php echo number_format($total_paid, 2); ?></td>
                <td></td>
            </tr>
        </table>
        <?php } else { ?>
        <p style="text-align:center; padding:20px; color:#666;">No payments recorded yet.</p>
        <?php } ?>
    </div>
    
    <br><a href="dashboard.php" class="btn"><- Back to Dashboard</a>
</div>
