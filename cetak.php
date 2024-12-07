<?php
// Menghubungkan ke database
include 'koneksi.php';

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
        WHERE DATE(tanggal_transaksi) BETWEEN ? AND ?
        GROUP BY jenis_kendaraan";

// Menyiapkan statement
$stmt = $conn->prepare($sql);

// Bind parameter tanggal awal dan tanggal akhir
$stmt->bind_param("ss", $tanggal_awal, $tanggal_akhir);

// Menjalankan query
$stmt->execute();

// Mengambil hasil query
$result = $stmt->get_result();

// Inisialisasi total transaksi dan pendapatan keseluruhan
$total_transaksi_keseluruhan = 0;
$total_pendapatan_keseluruhan = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Laporan Transaksi</title>
    <style>
        /* CSS untuk bentuk tabel */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            border: 1px solid #ddd;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h2>Laporan Transaksi</h2>
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
            // Memeriksa apakah hasil query mengandung data
            if ($result->num_rows > 0) {
                // Melakukan iterasi untuk menampilkan setiap baris hasil query
                while ($row = $result->fetch_assoc()) {
                    // Menampilkan data transaksi dalam bentuk baris tabel
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
                // Menampilkan pesan jika tidak ada data transaksi
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

    <script>
        // Otomatis memicu dialog cetak saat halaman dimuat
        window.onload = function() {
            window.print();
        };

            // Fungsi untuk melakukan redirect setelah pencetakan selesai atau dibatalkan
            window.onafterprint = function() {
            window.location.href = "laporan_bulan_ini.php"; // Redirect ke halaman cetak_laporan.php
        };
    </script>
</body>
</html>

<?php
// Menutup statement
$stmt->close();

// Menutup koneksi
$conn->close();
?>
