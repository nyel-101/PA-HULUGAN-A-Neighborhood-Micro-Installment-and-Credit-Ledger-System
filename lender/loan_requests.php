<?php
include '../config.php';


if(!isset($_SESSION['role']) || $_SESSION['role'] !== 'lender'){
    header("Location: ../index.php");
    exit();
}


if(isset($_POST['approve_loan'])){
    $id = $_POST['loan_id']; 
    $due = $_POST['due']; 
    $penalty = $_POST['penalty'];
    
  
    $update = "UPDATE loans SET due_date='$due', penalty='$penalty', status='active' WHERE id=$id";
    mysqli_query($conn, $update);
    header("Location: dashboard.php");
    exit();
}


$query = "SELECT l.*, u.full_name 
          FROM loans l 
          LEFT JOIN users u ON l.lendee_id = u.id 
          WHERE l.status = 'pending'";
$res = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Loan Requests</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <div class="container">
        <div class="card">
            <h2>Pending Loan Requests (<?php echo mysqli_num_rows($res); ?>)</h2>
            <table>
                <tr>
                    <th>Borrower</th>
                    <th>Type</th>
                    <th>Details</th>
                    <th>Amount</th>
                    <th>Action</th>
                </tr>
                <?php if(mysqli_num_rows($res) > 0) { ?>
                    <?php while($r = mysqli_fetch_assoc($res)){ ?>
                    <tr>
                        <td><?php echo $r['full_name'] ?? 'Unknown User'; ?></td>
                        <td><?php echo ucfirst($r['loan_type']); ?></td>
                        <td><?php echo $r['item_details']; ?></td>
                        <td>₱<?php echo number_format($r['total_amount'], 2); ?></td>
                        <td>
                            <form method="POST">
                                <input type="hidden" name="loan_id" value="<?php echo $r['id']; ?>">
                                Due: <input type="date" name="due" required>
                                Penalty: <input type="number" name="penalty" placeholder="₱" required>
                                <button type="submit" name="approve_loan" class="btn btn-green">Approve & Set Schedule</button>
                            </form>
                        </td>
                    </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="5" style="text-align:center;">No pending requests found in database.</td>
                    </tr>
                <?php } ?>
            </table>
            <br>
            <a href="dashboard.php" class="btn btn-blue">Back to Masterlist</a>
        </div>
    </div>
</body>
</html>
