<?php
include 'koneksi.php';

if (isset($_GET['nota_no'])) {
    $nota_no = $_GET['nota_no'];

    // Prepare and bind
    $stmt = $conn->prepare("DELETE FROM transactions WHERE nota_no = ?");
    $stmt->bind_param("s", $nota_no);

    // Execute the statement
    if ($stmt->execute()) {
        echo "<script>alert('Record deleted successfully');window.location.href='transaksi.php';</script>";
    } else {
        echo "<script>alert('Error deleting record: " . $conn->error . "');window.location.href='transaksi.php';</script>";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "<script>alert('No Nota No specified');window.location.href='index.php';</script>";
}
?>
