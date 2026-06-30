<?php

require_once __DIR__ . '/../library/SimplePDF.php';
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../model/Alumni.php';

class ReportController
{
    private $alumniModel;

    public function __construct()
    {
        $database = new Database();
        $db = $database->getConnection();
        $this->alumniModel = new Alumni($db);
    }

    private function getReportData($status = '')
    {
        if (!empty($status)) {
            return $this->alumniModel->search('', $status);
        }
        return $this->alumniModel->getAll();
    }

    private function tableHeader()
    {
        return [
            'columns' => ['NIM', 'Nama Lengkap', 'Jurusan', 'Thn Lulus', 'Email', 'No. Telp', 'Pekerjaan', 'Status'],
            'widths'  => [60, 110, 100, 55, 130, 75, 110, 70],
        ];
    }

    public function exportPDF($status = '')
    {
        $data = $this->getReportData($status);
        $header = $this->tableHeader();
        $startX = 30;

        $pdf = new SimplePDF('Laporan Data Alumni');

        $drawTitle = function (SimplePDF $p) use ($header, $startX) {
            $p->text(30, 16, 'LAPORAN DATA ALUMNI', true);
            $p->moveDown(20);
            $p->text(30, 9, 'Dicetak pada: ' . date('d-m-Y H:i'));
            $p->moveDown(16);
            $p->tableRow($header['columns'], $header['widths'], $startX, 16, true);
        };

        $pdf->setHeaderCallback($drawTitle);
        $pdf->setFooterCallback(function (SimplePDF $p, $pageNo) {
            $p->setY(20);
            $p->text(($p->getPageWidth() / 2) - 30, 8, "Halaman {$pageNo}");
        });

        $pdf->addPage();

        foreach ($data as $row) {
            $pdf->checkPageBreak(16, function (SimplePDF $p) use ($header, $startX) {
                $p->tableRow($header['columns'], $header['widths'], $startX, 16, true);
            });

            $pdf->tableRow([
                $row['nim'],
                $row['nama_lengkap'],
                $row['jurusan'],
                (string) $row['tahun_lulus'],
                $row['email'],
                $row['no_telepon'],
                $row['pekerjaan_saat_ini'],
                $row['status'],
            ], $header['widths'], $startX, 16, false);
        }

        $pdf->output('Laporan_Data_Alumni_' . date('Ymd_His') . '.pdf');
        exit();
    }

    public function exportExcel($status = '')
    {
        $data = $this->getReportData($status);

        $filename = 'Laporan_Data_Alumni_' . date('Ymd_His') . '.xls';
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Pragma: no-cache");
        header("Expires: 0");

        echo "\xEF\xBB\xBF"; 

        echo "<table border='1'>";
        echo "<tr style='background:#cccccc;font-weight:bold;'>
                <th>NIM</th><th>Nama Lengkap</th><th>Jurusan</th><th>Tahun Lulus</th>
                <th>Email</th><th>No. Telepon</th><th>Pekerjaan Saat Ini</th>
                <th>Alamat</th><th>Status</th><th>Tanggal Daftar</th>
              </tr>";

        foreach ($data as $row) {
            echo "<tr>
                    <td>{$row['nim']}</td>
                    <td>{$row['nama_lengkap']}</td>
                    <td>{$row['jurusan']}</td>
                    <td>{$row['tahun_lulus']}</td>
                    <td>{$row['email']}</td>
                    <td>{$row['no_telepon']}</td>
                    <td>{$row['pekerjaan_saat_ini']}</td>
                    <td>{$row['alamat']}</td>
                    <td>{$row['status']}</td>
                    <td>{$row['created_at']}</td>
                  </tr>";
        }
        echo "</table>";
        exit();
    }
}
