<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Excel as ExcelFormat;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class DummyExcelController extends Controller
{
    public function downloadFormatif()
    {
        // Dummy data
        $tps = [
            ['nomor' => 1, 'deskripsi' => 'membaca Al-Qur’an dengan meyakini bahwa kontrol diri (Mujahadah An-Nafs) adalah perintah agama'],
            ['nomor' => 2, 'deskripsi' => 'menunjukan perilaku control diri (Mujahadah An-Nafs), sebagai implementasi dari perintah Q.S. Al-Anfal /8:72 serta Hadits terkait.'],
            ['nomor' => 3, 'deskripsi' => 'menganalisis Q.S. Al-Anfal/8:72, serta hadits tentang control diri (Mujahadah An-Nafs).'],
            ['nomor' => 4, 'deskripsi' => 'membaca Q.S. Al-Anfal/8:72, sesuai dengan kaidah tajwid dan Makharijul Huruf.'],
            ['nomor' => 5, 'deskripsi' => 'menghafal Q.S. Al-Anfal/8:72, dengan fasih dan lancar.'],
            ['nomor' => 5, 'deskripsi' => 'menyajikan hubungan antara kualitas keimanan dengan control diri (Mujahadah An-Nafs), sesuai dengan pesan Q.S. Al-Anfal /8:72, serta hadits.'],
        ];
        $siswa = [
            ['nama' => 'ADITYA RIZKI ARIFIN'],
            ['nama' => 'ALYA NUR ZAHRA'],
            ['nama' => 'ARSYAD FATHI MAWARDI'],
            ['nama' => 'BABY CANTIKA CAHAYA PERMATA'],
        ];

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header baris 1
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Nama Siswa');
        $sheet->setCellValue('C1', 'Tujuan Pembelajaran');
        $tpCount = count($tps);
        $lastTPCol = chr(67 + ($tpCount * 2) - 1); // C = 67
        $sheet->mergeCells("C1:{$lastTPCol}1");
        $sheet->setCellValue(chr(ord($lastTPCol)+1).'1', 'Deskripsi Capaian Tertinggi dalam Rapor');
        $sheet->setCellValue(chr(ord($lastTPCol)+2).'1', 'Deskripsi Capaian Terendah dalam Rapor');

        // Header baris 2
        $sheet->setCellValue('A2', '');
        $sheet->setCellValue('B2', '');
        for ($i = 0; $i < $tpCount; $i++) {
            $col = chr(67 + ($i * 2));
            $sheet->setCellValue($col.'2', 'TP '.($i+1));
            $sheet->mergeCells($col.'2:'.chr(ord($col)+1).'2');
        }

        // Baris 3: Deskripsi TP
        $sheet->setCellValue('A3', '');
        $sheet->setCellValue('B3', '');
        for ($i = 0; $i < $tpCount; $i++) {
            $col = chr(67 + ($i * 2));
            $sheet->setCellValue($col.'3', $tps[$i]['deskripsi']);
            $sheet->mergeCells($col.'3:'.chr(ord($col)+1).'3');
        }

        // // Header baris 3
        // $sheet->setCellValue('A3', '');
        // $sheet->setCellValue('B3', '');
        // for ($i = 0; $i < $tpCount; $i++) {
        //     $col = chr(67 + ($i * 2));
        //     $sheet->setCellValue($col.'3', 'KKTP');
        //     $sheet->setCellValue(chr(ord($col)+1).'3', 'Tampil/Tidak');
        // }

        $sheet->setCellValue('A4', '');
        $sheet->setCellValue('B4', '');
        for ($i = 0; $i < $tpCount; $i++) {
            $col = chr(67 + ($i * 2));
            $sheet->setCellValue($col.'4', 'KKTP');
            $sheet->setCellValue(chr(ord($col)+1).'4', 'Tampil/Tidak');
        }

        // // Header baris 4 (deskripsi TP)
        // $sheet->setCellValue('A4', '');
        // $sheet->setCellValue('B4', '');
        // for ($i = 0; $i < $tpCount; $i++) {
        //     $col = chr(67 + ($i * 2));
        //     $sheet->setCellValue($col.'4', $tps[$i]['deskripsi']);
        //     $sheet->mergeCells($col.'4:'.chr(ord($col)+1).'4');
        // }
        // $sheet->setCellValue(chr(ord($lastTPCol)+1).'4', 'Deskripsi Capaian Tertinggi dalam Rapor');
        // $sheet->setCellValue(chr(ord($lastTPCol)+2).'4', 'Deskripsi Capaian Terendah dalam Rapor');

        // Data siswa
        $row = 5;
        foreach ($siswa as $idx => $s) {
            $sheet->setCellValue('A'.$row, $idx+1);
            $sheet->setCellValue('B'.$row, $s['nama']);
            for ($i = 0; $i < $tpCount; $i++) {
                $col = chr(67 + ($i * 2));
                $sheet->setCellValue($col.$row, ''); // KKTP
                $sheet->setCellValue(chr(ord($col)+1).$row, ''); // Tampil/Tidak
            }
            $sheet->setCellValue(chr(ord($lastTPCol)+1).$row, '');
            $sheet->setCellValue(chr(ord($lastTPCol)+2).$row, '');
            $row++;
        }

        // Download
        $filename = 'template_asesmen_formatif.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function downloadSumatif()
    {
        // Dummy data
        $sumatifs = [
            ['nomor' => 1, 'deskripsi' => 'hadist tentang pentingnya mengendalikan diri'],
            ['nomor' => 2, 'deskripsi' => 'Hujurat / 49:12 dan Hadist tentang prasangka baik'],
            ['nomor' => 3, 'deskripsi' => 'Hadist tentang Indahnya Persaudaraan'],
            ['nomor' => 4, 'deskripsi' => 'Hadist tentang Menjaga Diri dari Pergaulan Buruk'],
            ['nomor' => 5, 'deskripsi' => 'Meneladani ALLAH SWT melalui Asmaul Husna'],
            ['nomor' => 6, 'deskripsi' => 'Menghadirkan malaikat dalam kehidupan'],
            ['nomor' => 7, 'deskripsi' => ''],
            ['nomor' => 8, 'deskripsi' => ''],
            ['nomor' => 9, 'deskripsi' => ''],
            ['nomor' => 10, 'deskripsi' => ''],
        ];
        $siswa = [
            ['nama' => 'ADITYA RIZKI ARIFIN'],
            ['nama' => 'ALYA NUR ZAHRA'],
            ['nama' => 'ARSYAD FATHI MAWARDI'],
            ['nama' => 'BABY CANTIKA CAHAYA PERMATA'],
        ];

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header baris 1
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Nama Siswa');
        $sumatifCount = count($sumatifs);
        $firstSumatifCol = 'C';
        $lastSumatifCol = chr(ord($firstSumatifCol) + $sumatifCount - 1); // C + (10-1) = L
        $naCol = chr(ord($lastSumatifCol) + 1); // M
        $sheet->setCellValue($firstSumatifCol.'1', 'Sumatif Akhir Lingkup Materi (Wajib)');
        $sheet->mergeCells($firstSumatifCol.'1:'.$naCol.'1');

        // Kolom setelah NA: N = Non Tes, O = Tes, P = NA Sumatif Akhir Semester, Q = Nilai Rapor
        $sheet->setCellValue('N1', 'Sumatif Akhir Semester (Tidak Wajib)');
        $sheet->mergeCells('N1:P1');
        $sheet->setCellValue('Q1', 'Nilai Rapor');

        // Header baris 2
        $sheet->setCellValue('A2', '');
        $sheet->setCellValue('B2', '');
        for ($i = 0; $i < $sumatifCount; $i++) {
            $col = chr(ord($firstSumatifCol) + $i);
            $sheet->setCellValue($col.'2', 'Sumatif '.($i+1));
        }
        $sheet->setCellValue($naCol.'2', 'NA Sumatif Lingkup Materi');
        $sheet->setCellValue('N2', 'Non Tes');
        $sheet->setCellValue('O2', 'Tes');
        $sheet->setCellValue('P2', 'NA Sumatif Akhir Semester');
        $sheet->setCellValue('Q2', '');

        // Header baris 3: deskripsi sumatif
        $sheet->setCellValue('A3', '');
        $sheet->setCellValue('B3', '');
        for ($i = 0; $i < $sumatifCount; $i++) {
            $col = chr(ord($firstSumatifCol) + $i);
            $sheet->setCellValue($col.'3', $sumatifs[$i]['deskripsi']);
        }
        $sheet->setCellValue($naCol.'3', '-');
        $sheet->setCellValue('N3', '');
        $sheet->setCellValue('O3', '');
        $sheet->setCellValue('P3', '');
        $sheet->setCellValue('Q3', '');

        // Data siswa
        $row = 4;
        foreach ($siswa as $idx => $s) {
            $sheet->setCellValue('A'.$row, $idx+1);
            $sheet->setCellValue('B'.$row, $s['nama']);
            for ($i = 0; $i < $sumatifCount; $i++) {
                $col = chr(ord($firstSumatifCol) + $i);
                $sheet->setCellValue($col.$row, '');
            }
            $sheet->setCellValue($naCol.$row, '');
            $sheet->setCellValue('N'.$row, '');
            $sheet->setCellValue('O'.$row, '');
            $sheet->setCellValue('P'.$row, '');
            $sheet->setCellValue('Q'.$row, '');
            $row++;
        }

        // Download
        $filename = 'template_asesmen_sumatif.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}