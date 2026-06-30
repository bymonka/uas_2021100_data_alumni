<?php

require_once __DIR__ . '/AuthController.php';
AuthController::checkSession();

require_once __DIR__ . '/ReportController.php';

$type = $_GET['type'] ?? '';
$status = $_GET['status'] ?? '';

$report = new ReportController();

if ($type === 'pdf') {
    $report->exportPDF($status);
} elseif ($type === 'excel') {
    $report->exportExcel($status);
} else {
    header("Location: ../view/alumni/laporan.php");
    exit();
}
