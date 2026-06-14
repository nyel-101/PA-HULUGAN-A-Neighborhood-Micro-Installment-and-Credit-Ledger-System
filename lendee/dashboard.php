<?php
include '../config.php';
check_role('lendee');


$uid = intval($_SESSION['user_id']); 




$notifications = mysqli_query($conn, "SELECT * FROM notifications WHERE user_id=$uid ORDER BY created_at DESC LIMIT 3");


$summary_query = "SELECT 
    COUNT(*) as total_loans,
    SUM(CASE WHEN status != 'pending' THEN total_amount ELSE 0 END) as total_borrowed,
    SUM(CASE WHEN status IN ('active', 'overdue') THEN remaining_balance ELSE 0 END) as total_outstanding
    FROM loans WHERE lendee_id=$uid";
$summary_res = mysqli_query($conn, $summary_query);
$summary = mysqli_fetch_assoc($summary_res);

$res = mysqli_query($conn, "SELECT * FROM loans WHERE lendee_id = $uid");
?>

<link rel="stylesheet" href="../style.css">
<div class="container">
    <div style="display:flex; justify-content:space-between; align-items:center;">
        <h2>Lendee Dashboard</h2>
        <span><strong><?php echo $_SESSION['name']; ?></strong> | <a href="../logout.php" style="color:red;">Logout</a></span>
    </div>

    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin: 20px 0;">
        <div class="card">
            <h3><?php echo $summary['total_loans'] ?? 0; ?></h3>
            <p>Total Applications</p>
        </div>
        <div class="card">
            <h3>₱<?php echo number_format($summary['total_borrowed'] ?? 0, 2); ?></h3>
            <p>Approved Credit</p>
        </div>
        <div class="card" style="background:#fff3f3; border-left: 5px solid #e74c3c;">
            <h3 style="color:#e74c3c;">₱<?php echo number_format($summary['total_outstanding'] ?? 0, 2); ?></h3>
            <p>Amount Due</p>
        </div>
    </div>

    <div style="margin-bottom:20px;">
        <a href="apply_loan.php" class="btn" style="background:#27ae60; color:white; text-decoration:none; padding:10px 20px; border-radius:5px;">+ Apply for New Loan</a>
    </div>

    <h3>My Loan Details</h3>
    
    <?php if($res && mysqli_num_rows($res) > 0): ?>
    <table style="width:100%; border-collapse: collapse; margin-top:10px;">
        <thead>
            <tr style="background:#f4f4f4; text-align:left;">
                <th style="padding:10px;">Item</th>
                <th style="padding:10px;">Type</th>
                <th style="padding:10px;">Balance</th>
                <th style="padding:10px;">Due Date</th>
                <th style="padding:10px;">Status</th>
                <th style="padding:10px;">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while($r = mysqli_fetch_assoc($res)): 
                $status = strtolower($r['status']);
                $color = ($status == 'active') ? 'green' : (($status == 'overdue') ? 'red' : 'orange');
            ?>
            <tr style="border-bottom:1px solid #ddd;">
                <td style="padding:10px;"><?php echo htmlspecialchars($r['item_details']); ?></td>
                <td style="padding:10px;"><?php echo ucfirst($r['loan_type']); ?></td>
                <td style="padding:10px; font-weight:bold;">₱<?php echo number_format($r['remaining_balance'], 2); ?></td>
                <td style="padding:10px;"><?php echo $r['due_date'] ?? 'Waiting...'; ?></td>
                <td style="padding:10px; color:<?php echo $color; ?>; font-weight:bold;"><?php echo strtoupper($status); ?></td>
                <td style="padding:10px;">
                    <?php if($status == 'active' || $status == 'overdue'): ?>
                        <a href="make_payment.php?id=<?php echo $r['id']; ?>" class="btn" style="background:#27ae60; color:white; padding:5px 10px; font-size:12px; text-decoration:none; border-radius:3px;">Pay Now</a>
                    <?php elseif($status == 'paid'): ?>
                        <span style="color:blue;">✓ Completed</span>
                    <?php else: ?>
                        <span style="color:gray;">Pending Approval</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <?php else: ?>
        <div class="card" style="padding:20px; text-align:center; color:#666;">
            <p>No loan records found for your account (ID: <?php echo $uid; ?>).</p>
        </div>
    <?php endif; ?>
</div>

