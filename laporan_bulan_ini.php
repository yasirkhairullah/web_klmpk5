<?php
include 'koneksi.php';
include 'sidebar.php';

// Inisialisasi variabel untuk filter tanggal awal dan akhir bulan
$tanggal_awal = isset($_GET['tanggal_awal']) ? $_GET['tanggal_awal'] : date('Y-m-01');
$tanggal_akhir = isset($_GET['tanggal_akhir']) ? $_GET['tanggal_akhir'] : date('Y-m-d');
$bulan_ini = isset($_GET['bulan_ini']) ? $_GET['bulan_ini'] : false;

// Jika laporan bulan ini dipilih, atur tanggal akhir menjadi tanggal hari ini
if ($bulan_ini) {
    $tanggal_akhir = date('Y-m-d');
}

// Query untuk menghitung total transaksi dan total pendapatan per jenis kendaraan berdasarkan rentang waktu
$sql = "SELECT jenis_kendaraan, COUNT(*) AS total_transaksi, SUM(biaya) AS total_pendapatan
        FROM transaksi
        WHERE DATE(tanggal_transaksi) BETWEEN '$tanggal_awal' AND '$tanggal_akhir'
        GROUP BY jenis_kendaraan";
$result = $conn->query($sql);

// Menghitung total keseluruhan
$total_transaksi_keseluruhan = 0;
$total_pendapatan_keseluruhan = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Transaksi</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* CSS for container */
        h2{
            font-size: 24px;
            margin-bottom: 20px;
            color: black;
        }
        .container {
            margin: 0 auto;
            padding: 20px;
            max-width: 175vh;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-left: 270px;
            margin-top: 50px;
        }

        /* CSS for table */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            border: 1px solid #ddd;
        }

        /* Styling for table header */
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        /* Styling for form */
        .filter-form {
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }

        .filter-form label {
            margin-right: 10px;
            color: #555;
        }

        .filter-form input {
            margin-right: 10px;
            padding: 8px;
            border: 1px solid #ced4da;
            border-radius: 4px;
        }

        .filter-form button {
            margin-right: 10px;
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            background-color: #007bff;
            color: #fff;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .filter-form button:hover {
            background-color: #0056b3;
        }

        /* Styling for custom buttons */
        .custom-btn {
    background-color: green;
    color: #fff;
    border: none;
    border-radius: 4px;
    padding: 8px 16px;
    cursor: pointer;
    transition: background-color 0.3s;
    text-decoration: none;
    margin-left: auto; /* Menggeser tombol ke ujung kanan */
    margin-right: 5px; /* Mengatur jarak kanan yang lebih rapat */
}
.custom-btn1 {
    background-color: #212529;
    color: #fff;
    border: none;
    border-radius: 4px;
    padding: 8px 16px;
    cursor: pointer;
    transition: background-color 0.3s;
    text-decoration: none;
   
}


    

        .custom-btn:hover,
        .custom-btn1:hover {
            background-color: #5a6268;
            color: #fff;
        }
        .btn-light {
    background-color: red;
    color: #212529;
    border: none;
    border-radius: 4px;
    padding: 8px 16px;
    cursor: pointer;
    transition: background-color 0.3s;
    text-decoration: none;
}

.btn-light:hover {
    background-color: #e2e6ea;
    color: #212529;
}


        /* Styling for icon */
        .icon {
            margin-right: 8px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Laporan Transaksi</h2>
        
        <!-- Form untuk filter rentang tanggal -->
        <form class="filter-form" method="GET" action="">
            <label for="tanggal_awal">Tanggal Awal:</label>
            <input type="date" id="tanggal_awal" name="tanggal_awal" value="<?php echo $tanggal_awal; ?>" required>
            <label for="tanggal_akhir">Tanggal Akhir:</label>
            <input type="date" id="tanggal_akhir" name="tanggal_akhir" value="<?php echo $tanggal_akhir; ?>" required>
            <button type="submit">Filter</button>
            <a href="laporan_bulan_ini.php" class="btn btn-light"><i class="fas fa-sync-alt"></i></a>
            <a href="laporan.php" class="custom-btn"><i class="fas fa-calendar-alt icon"></i> Laporan Bulan Ini</a>
            <a href="cetak.php"class="custom-btn1"><i class="fas fa-print icon"></i> Cetak</a>
            </form>
        </form>

        <div id="report">
            <table>
                <thead>
                    <tr>
                        <th>Jenis Kendaraan</th>
                        <th>Total Transaksi</th>
                        <th>Pendapatan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>
                                    <td>" . $row["jenis_kendaraan"] . "</td>
                                    <td>" . $row["total_transaksi"] . "</td>
                                    <td>Rp " . number_format($row["total_pendapatan"]) . "</td>
                                  </tr>";
                            // Menambahkan total transaksi dan pendapatan untuk total keseluruhan
                            $total_transaksi_keseluruhan += $row["total_transaksi"];
                            $total_pendapatan_keseluruhan += $row["total_pendapatan"];
                        }
                    } else {
                        echo "<tr><td colspan='3'>Tidak ada data transaksi.</td></tr>";
                    }
                    ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td><strong>Total Keseluruhan</strong></td>
                        <td><?php echo $total_transaksi_keseluruhan; ?></td>
                        <td>Rp <?php echo number_format($total_pendapatan_keseluruhan); ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</body>
</html>
