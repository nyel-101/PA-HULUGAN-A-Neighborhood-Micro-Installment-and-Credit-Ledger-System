<?php
include '../config.php';
check_role('lender');

if(isset($_GET['app'])){
    $uid = mysqli_real_escape_string($conn, $_GET['app']);
    $user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT full_name FROM users WHERE id=$uid"));
    mysqli_query($conn, "UPDATE users SET account_status='approved' WHERE id=$uid");
    mysqli_query($conn, "INSERT INTO notifications (user_id, message, type) 
                        VALUES ($uid, 'Your account has been approved! You can now apply for loans.', 'account_approved')");
    echo "<script>alert('Account approved for " . $user['full_name'] . "'); window.location='user_approvals.php';</script>";
}

if(isset($_GET['rej'])){
    $uid = mysqli_real_escape_string($conn, $_GET['rej']);
    $user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT full_name FROM users WHERE id=$uid"));
    mysqli_query($conn, "UPDATE users SET account_status='rejected' WHERE id=$uid");
    mysqli_query($conn, "INSERT INTO notifications (user_id, message, type) 
                        VALUES ($uid, 'Your account registration was not approved.', 'account_rejected')");
    echo "<script>alert('Account rejected for " . $user['full_name'] . "'); window.location='user_approvals.php';</script>";
}

$res = mysqli_query($conn, "SELECT * FROM users WHERE account_status='pending' ORDER BY id DESC");
?>
<link rel="stylesheet" href="../style.css">
<div class="container">
    <h2>Review New Borrower Registrations</h2>
    <p>Verify the documents submitted by new borrowers before approving their accounts.</p>
    
    <?php if($res && mysqli_num_rows($res) > 0) { ?>
    <table>
        <tr>
            <th>Applicant Name</th>
            <th>Contact</th>
            <th>Face Photo</th>
            <th>ID Photo</th>
            <th>Video Proof</th>
            <th>Registration Date</th>
            <th>Action</th>
        </tr>
        <?php while($r = mysqli_fetch_assoc($res)){ ?>
        <tr>
            <td><strong><?php echo $r['full_name']; ?></strong><br><small>@<?php echo $r['username']; ?></small></td>
            <td><?php echo $r['contact_no']; ?></td>
            <td>
                <a href="../uploads/<?php echo $r['face_photo']; ?>" target="_blank" class="btn" style="padding:5px 10px; font-size:12px;">View</a>
            </td>
            <td>
                <a href="../uploads/<?php echo $r['id_photo']; ?>" target="_blank" class="btn" style="padding:5px 10px; font-size:12px;">View</a>
            </td>
            <td>
                <a href="../uploads/<?php echo $r['video_proof']; ?>" target="_blank" class="btn" style="padding:5px 10px; font-size:12px;">Watch</a>
            </td>
            <td><?php echo date('M d, Y', strtotime($r['created_at'])); ?></td>
            <td>
                <a href="user_approvals.php?app=<?php echo $r['id']; ?>" class="btn" style="background:#27ae60; padding:5px 10px; font-size:12px;">Approve</a>
                <a href="user_approvals.php?rej=<?php echo $r['id']; ?>" class="btn" style="background:#e74c3c; padding:5px 10px; font-size:12px;">Reject</a>
            </td>
        </tr>
        <?php } ?>
    </table>
    <?php } else { ?>
    <div class="card" style="text-align:center; padding:40px;">
        <h3>All caught up!</h3>
        <p>No pending registrations to review.</p>
    </div>
    <?php } ?>
    <br><a href="dashboard.php" class="btn">← Back to Dashboard</a>
</div>
