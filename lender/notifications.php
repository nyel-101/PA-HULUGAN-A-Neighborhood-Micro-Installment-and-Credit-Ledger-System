<?php
include '../config.php';
check_role('lender');


if(isset($_GET['mark_read'])){
    $notif_id = mysqli_real_escape_string($conn, $_GET['mark_read']);
    mysqli_query($conn, "UPDATE notifications SET is_read=1 WHERE id=$notif_id AND user_id=1");
    header("Location: notifications.php");
    exit();
}

$notifications = mysqli_query($conn, "SELECT * FROM notifications WHERE user_id=1 ORDER BY created_at DESC LIMIT 50");
?>
<link rel="stylesheet" href="../style.css">
<div class="container">
    <h2>Notifications</h2>
    
    <?php if($notifications && mysqli_num_rows($notifications) > 0) { ?>
    <table>
        <tr>
            <th>Message</th>
            <th>Type</th>
            <th>Date</th>
            <th>Status</th>
        </tr>
        <?php while($n = mysqli_fetch_assoc($notifications)){ 
            $bg = $n['is_read'] ? '' : 'style="background:#fff9e6;"';
        ?>
        <tr <?php echo $bg; ?>>
            <td><?php echo $n['message']; ?></td>
            <td><span class="badge"><?php echo str_replace('_', ' ', ucfirst($n['type'])); ?></span></td>
            <td><?php echo date('M d, Y H:i', strtotime($n['created_at'])); ?></td>
            <td>
                <?php if(!$n['is_read']) { ?>
                    <a href="notifications.php?mark_read=<?php echo $n['id']; ?>" class="btn" style="padding:5px 10px; font-size:12px;">Mark Read</a>
                <?php } else { ?>
                    <span style="color:green;">✅ Read</span>
                <?php } ?>
            </td>
        </tr>
        <?php } ?>
    </table>
    <?php } else { ?>
    <div class="card" style="text-align:center; padding:40px;">
        <h3>✓ All caught up!</h3>
        <p>No notifications yet.</p>
    </div>
    <?php } ?>
    
    <br><a href="dashboard.php" class="btn"> 🔙Back  to Dashboard</a>
</div>

<style>
.badge {
    display: inline-block;
    background: #3498db;
    color: white;
    padding: 3px 8px;
    border-radius: 3px;
    font-size: 12px;
}
</style>
