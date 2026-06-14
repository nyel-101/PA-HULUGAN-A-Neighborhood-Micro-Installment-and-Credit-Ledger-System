<?php
include '../config.php';
check_role('lender');


$overdue_check = mysqli_query($conn, "SELECT l.* FROM loans l 
                               WHERE l.status = 'active' AND l.due_date < CURDATE()");
if($overdue_check){
    while($loan = mysqli_fetch_assoc($overdue_check)){
        if($loan['status'] != 'overdue'){
            mysqli_query($conn, "UPDATE loans SET status='overdue' WHERE id=" . $loan['id']);
            $lendee = mysqli_fetch_assoc(mysqli_query($conn, "SELECT full_name FROM users WHERE id=" . $loan['lendee_id']));
            mysqli_query($conn, "INSERT INTO notifications (user_id, message, type) 
                                VALUES (" . $loan['lendee_id'] . ", 'Your loan is overdue. Penalty of " . $loan['penalty'] . " applied.', 'penalty_applied')");
        }
    }
}


$res = mysqli_query($conn, "SELECT l.*, u.full_name FROM loans l 
                            JOIN users u ON l.lendee_id = u.id 
                            WHERE l.status != 'pending' 
                            ORDER BY l.status ASC, l.due_date ASC");
if(!$res) $res = false;


$pending_users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM users WHERE account_status='pending'"));
$pending_loans = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM loans WHERE status='pending'"));
$notifications_query = mysqli_query($conn, "SELECT * FROM notifications WHERE user_id=1 AND is_read=0 ORDER BY created_at DESC");
$notif_count = ($notifications_query) ? mysqli_num_rows($notifications_query) : 0;


$total_stats = mysqli_fetch_assoc(mysqli_query($conn, "SELECT 
    COUNT(DISTINCT lendee_id) as total_borrowers,
    SUM(total_amount) as total_loaned,
    SUM(remaining_balance) as total_outstanding
    FROM loans WHERE status IN ('active', 'overdue')"));
?>
<link rel="stylesheet" href="../style.css">
<div class="container">
    <div style="display:flex; justify-content:space-between; align-items:center;">
        <h2>Lender Dashboard - Loan Management System</h2>
        <span style="font-size:18px;"><?php echo $_SESSION['name']; ?> | <a href="../logout.php" class="btn" style="background:red; padding:5px 10px;">Logout</a></span>
    </div>
    
   
    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin: 20px 0;">
        <div class="card" style="text-align:center;">
            <h3><?php echo $total_stats['total_borrowers'] ?? 0; ?></h3>
            <p>Active Borrowers</p>
        </div>
        <div class="card" style="text-align:center;">
            <h3>₱<?php echo number_format($total_stats['total_loaned'] ?? 0, 2); ?></h3>
            <p>Total Loaned</p>
        </div>
        <div class="card" style="text-align:center;">
            <h3>₱<?php echo number_format($total_stats['total_outstanding'] ?? 0, 2); ?></h3>
            <p>Outstanding Balance</p>
        </div>
        <div class="card" style="text-align:center; background:#fff3cd;">
            <h3><?php echo $pending_loans['count']; ?></h3>
            <p>Pending Requests</p>
        </div>
    </div>
    
    
    <div class="card" style="display:flex; gap:10px; flex-wrap:wrap;">
        <a href="user_approvals.php" class="btn" style="background:#27ae60;">
            User Approvals (<?php echo $pending_users['count']; ?>)
        </a> 
        <a href="loan_requests.php" class="btn" style="background:#e67e22;">
            Loan Requests (<?php echo $pending_loans['count']; ?>)
        </a>
        <a href="notifications.php" class="btn" style="background:#9b59b6;">
            Notifications (<?php echo $notif_count; ?>)
        </a>
    </div>
    
    
    <h3 style="margin-top:30px;">Loan Masterlist</h3>
    <table>
        <tr>
            <th>Borrower</th>
            <th>Item/Details</th>
            <th>Loan Type</th>
            <th>Total Amount</th>
            <th>Balance</th>
            <th>Due Date</th>
            <th>Penalty</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        <?php if($res && mysqli_num_rows($res) > 0) {
            while($r = mysqli_fetch_assoc($res)){ 
                $status_color = '';
                if($r['status'] == 'overdue') $status_color = 'style="color:red; font-weight:bold;"';
                elseif($r['status'] == 'paid') $status_color = 'style="color:green; font-weight:bold;"';
        ?>
        <tr>
            <td><?php echo $r['full_name']; ?></td>
            <td><?php echo $r['item_details']; ?></td>
            <td><?php echo ucfirst($r['loan_type']); ?></td>
            <td>₱<?php echo number_format($r['total_amount'], 2); ?></td>
            <td>₱<?php echo number_format($r['remaining_balance'], 2); ?></td>
            <td><?php echo $r['due_date'] ?? 'Pending'; ?></td>
            <td>₱<?php echo number_format($r['penalty'], 2); ?></td>
            <td <?php echo $status_color; ?>><?php echo strtoupper($r['status']); ?></td>
            <td>
                <a href="loan_details.php?id=<?php echo $r['id']; ?>" class="btn" style="padding:5px 10px; font-size:12px;">Details</a>
            </td>
        </tr>
        <?php } } else { ?>
        <tr><td colspan="9" style="text-align:center; padding:20px;">No active loans yet</td></tr>
        <?php } ?>
    </table>
</div>

<style>
.container { max-width: 1200px; }
</style>
