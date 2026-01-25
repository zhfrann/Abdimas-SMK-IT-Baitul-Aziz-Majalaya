<?php

namespace App\Http\Controllers;

use App\Models\Intrakurikuler;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Excel as ExcelFormat;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class DummyExcelController extends Controller
{
    // public function downloadFormatif()
    // {
    //     // Dummy data
    //     $tps = [
    //         ['nomor' => 1, 'deskripsi' => 'membaca Al-Qur’an dengan meyakini bahwa kontrol diri (Mujahadah An-Nafs) adalah perintah agama'],
    //         ['nomor' => 2, 'deskripsi' => 'menunjukan perilaku control diri (Mujahadah An-Nafs), sebagai implementasi dari perintah Q.S. Al-Anfal /8:72 serta Hadits terkait.'],
    //         ['nomor' => 3, 'deskripsi' => 'menganalisis Q.S. Al-Anfal/8:72, serta hadits tentang control diri (Mujahadah An-Nafs).'],
    //         ['nomor' => 4, 'deskripsi' => 'membaca Q.S. Al-Anfal/8:72, sesuai dengan kaidah tajwid dan Makharijul Huruf.'],
    //         ['nomor' => 5, 'deskripsi' => 'menghafal Q.S. Al-Anfal/8:72, dengan fasih dan lancar.'],
    //         ['nomor' => 5, 'deskripsi' => 'menyajikan hubungan antara kualitas keimanan dengan control diri (Mujahadah An-Nafs), sesuai dengan pesan Q.S. Al-Anfal /8:72, serta hadits.'],
    //     ];
    //     $siswa = [
    //         ['nama' => 'ADITYA RIZKI ARIFIN'],
    //         ['nama' => 'ALYA NUR ZAHRA'],
    //         ['nama' => 'ARSYAD FATHI MAWARDI'],
    //         ['nama' => 'BABY CANTIKA CAHAYA PERMATA'],
    //     ];

    //     $spreadsheet = new Spreadsheet();
    //     $sheet = $spreadsheet->getActiveSheet();

    //     // Header baris 1
    //     $sheet->setCellValue('A1', 'No');
    //     $sheet->setCellValue('B1', 'Nama Siswa');
    //     $sheet->setCellValue('C1', 'Tujuan Pembelajaran');
    //     $tpCount = count($tps);
    //     $lastTPCol = chr(67 + ($tpCount * 2) - 1); // C = 67
    //     $sheet->mergeCells("C1:{$lastTPCol}1");
    //     $sheet->setCellValue(chr(ord($lastTPCol)+1).'1', 'Deskripsi Capaian Tertinggi dalam Rapor');
    //     $sheet->setCellValue(chr(ord($lastTPCol)+2).'1', 'Deskripsi Capaian Terendah dalam Rapor');

    //     // Header baris 2
    //     $sheet->setCellValue('A2', '');
    //     $sheet->setCellValue('B2', '');
    //     for ($i = 0; $i < $tpCount; $i++) {
    //         $col = chr(67 + ($i * 2));
    //         $sheet->setCellValue($col.'2', 'TP '.($i+1));
    //         $sheet->mergeCells($col.'2:'.chr(ord($col)+1).'2');
    //     }

    //     // Baris 3: Deskripsi TP
    //     $sheet->setCellValue('A3', '');
    //     $sheet->setCellValue('B3', '');
    //     for ($i = 0; $i < $tpCount; $i++) {
    //         $col = chr(67 + ($i * 2));
    //         $sheet->setCellValue($col.'3', $tps[$i]['deskripsi']);
    //         $sheet->mergeCells($col.'3:'.chr(ord($col)+1).'3');
    //     }

    //     // // Header baris 3
    //     // $sheet->setCellValue('A3', '');
    //     // $sheet->setCellValue('B3', '');
    //     // for ($i = 0; $i < $tpCount; $i++) {
    //     //     $col = chr(67 + ($i * 2));
    //     //     $sheet->setCellValue($col.'3', 'KKTP');
    //     //     $sheet->setCellValue(chr(ord($col)+1).'3', 'Tampil/Tidak');
    //     // }

    //     $sheet->setCellValue('A4', '');
    //     $sheet->setCellValue('B4', '');
    //     for ($i = 0; $i < $tpCount; $i++) {
    //         $col = chr(67 + ($i * 2));
    //         $sheet->setCellValue($col.'4', 'KKTP');
    //         $sheet->setCellValue(chr(ord($col)+1).'4', 'Tampil/Tidak');
    //     }

    //     // // Header baris 4 (deskripsi TP)
    //     // $sheet->setCellValue('A4', '');
    //     // $sheet->setCellValue('B4', '');
    //     // for ($i = 0; $i < $tpCount; $i++) {
    //     //     $col = chr(67 + ($i * 2));
    //     //     $sheet->setCellValue($col.'4', $tps[$i]['deskripsi']);
    //     //     $sheet->mergeCells($col.'4:'.chr(ord($col)+1).'4');
    //     // }
    //     // $sheet->setCellValue(chr(ord($lastTPCol)+1).'4', 'Deskripsi Capaian Tertinggi dalam Rapor');
    //     // $sheet->setCellValue(chr(ord($lastTPCol)+2).'4', 'Deskripsi Capaian Terendah dalam Rapor');

    //     // Data siswa
    //     $row = 5;
    //     foreach ($siswa as $idx => $s) {
    //         $sheet->setCellValue('A'.$row, $idx+1);
    //         $sheet->setCellValue('B'.$row, $s['nama']);
    //         for ($i = 0; $i < $tpCount; $i++) {
    //             $col = chr(67 + ($i * 2));
    //             $sheet->setCellValue($col.$row, ''); // KKTP
    //             $sheet->setCellValue(chr(ord($col)+1).$row, ''); // Tampil/Tidak
    //         }
    //         $sheet->setCellValue(chr(ord($lastTPCol)+1).$row, '');
    //         $sheet->setCellValue(chr(ord($lastTPCol)+2).$row, '');
    //         $row++;
    //     }

    //     // Download
    //     $filename = 'template_asesmen_formatif.xlsx';
    //     header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    //     header("Content-Disposition: attachment; filename=\"$filename\"");
    //     $writer = new Xlsx($spreadsheet);
    //     $writer->save('php://output');
    //     exit;
    // }

    public function downloadFormatif(Request $request, $intrakurikuler_id)
    {
        $intrakurikuler = Intrakurikuler::with([
            'tujuanPembelajaran',
            'kelasAjar.riwayatKelas.siswa.user',
            'kelasAjar.riwayatKelas.asesmenFormatif.details'
        ])->findOrFail($intrakurikuler_id);

        $tpList = $intrakurikuler->tujuanPembelajaran;
        $tpCount = $tpList->count();

        // Ambil semua siswa di kelas ajar
        $siswaList = $intrakurikuler->kelasAjar->riwayatKelas->map(function ($rk) {
            return $rk->siswa;
        })->unique('siswa_id')->values();

        // Map: riwayat_kelas_id => asesmen_formatif (beserta details)
        $formatifMap = [];
        foreach ($intrakurikuler->kelasAjar->riwayatKelas as $rk) {
            $formatif = $rk->asesmenFormatif
                ->where('intrakurikuler_id', $intrakurikuler_id)
                ->first();
            if ($formatif) {
                $details = $formatif->details->keyBy('tujuan_pembelajaran_id');
                $formatifMap[$rk->riwayat_kelas_id] = [
                    'formatif' => $formatif,
                    'details' => $details,
                ];
            }
        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Hitung kolom TP
        $startTPCol = 'C';
        $lastTPCol = chr(ord($startTPCol) + ($tpCount * 2) - 1);

        // Kolom deskripsi
        $colTertinggi = chr(ord($lastTPCol) + 1);
        $colTerendah  = chr(ord($lastTPCol) + 2);

        // Merge header
        $sheet->mergeCells('A1:A3');
        $sheet->setCellValue('A1', 'No');
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $sheet->mergeCells('B1:B3');
        $sheet->setCellValue('B1', 'Nama Siswa');
        $sheet->getStyle('B1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $sheet->mergeCells("C1:{$lastTPCol}1");
        $sheet->setCellValue('C1', 'Tujuan Pembelajaran');
        $sheet->getStyle("C1:{$lastTPCol}1")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $sheet->mergeCells($colTertinggi . '1:' . $colTertinggi . '3');
        $sheet->setCellValue($colTertinggi . '1', 'Deskripsi Capaian Tertinggi dalam Rapor');
        $sheet->getStyle($colTertinggi . '1')->getAlignment()->setWrapText(true)->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->getColumnDimension($colTertinggi)->setWidth(28);

        $sheet->mergeCells($colTerendah . '1:' . $colTerendah . '3');
        $sheet->setCellValue($colTerendah . '1', 'Deskripsi Capaian Terendah dalam Rapor');
        $sheet->getStyle($colTerendah . '1')->getAlignment()->setWrapText(true)->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->getColumnDimension($colTerendah)->setWidth(28);

        // Header baris 2: TP, KKTP, Tampil/Tidak
        $sheet->setCellValue('A2', '');
        $sheet->setCellValue('B2', '');
        for ($i = 0; $i < $tpCount; $i++) {
            $colKKTP = chr(ord($startTPCol) + ($i * 2));
            $colTampil = chr(ord($startTPCol) + ($i * 2) + 1);
            $sheet->setCellValue($colKKTP . '2', 'TP ' . ($i + 1) . ' KKTP');
            $sheet->setCellValue($colTampil . '2', 'TP ' . ($i + 1) . ' Tampil/Tidak');
            $sheet->getColumnDimension($colTampil)->setWidth(15);
        }

        // Header baris 3: Deskripsi TP (wrap text)
        $sheet->setCellValue('A3', '');
        $sheet->setCellValue('B3', '');
        for ($i = 0; $i < $tpCount; $i++) {
            $colKKTP = chr(ord($startTPCol) + ($i * 2));
            $colTampil = chr(ord($startTPCol) + ($i * 2) + 1);
            $desc = $tpList[$i]->deskripsi;
            $sheet->setCellValue($colKKTP . '3', $desc);
            $sheet->mergeCells($colKKTP . '3:' . $colTampil . '3');
            $sheet->getStyle($colKKTP . '3:' . $colTampil . '3')
                ->getAlignment()
                ->setWrapText(true)
                ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER)
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        }

        // Data siswa
        $row = 4;
        foreach ($intrakurikuler->kelasAjar->riwayatKelas as $rk) {
            $siswa = $rk->siswa;
            $namaSiswa = $siswa->user->name ?? $siswa->nama ?? '-';

            $sheet->setCellValue('A' . $row, $row - 3);
            $sheet->setCellValue('B' . $row, $namaSiswa);

            // Ambil data formatif & details jika ada
            $formatif = $formatifMap[$rk->riwayat_kelas_id]['formatif'] ?? null;
            $details = $formatifMap[$rk->riwayat_kelas_id]['details'] ?? collect();

            // Kolom KKTP & Tampil/Tidak, isi default dari database jika ada
            for ($i = 0; $i < $tpCount; $i++) {
                $tp = $tpList[$i];
                $colKKTP = chr(ord($startTPCol) + ($i * 2));
                $colTampil = chr(ord($startTPCol) + ($i * 2) + 1);

                $kktpVal = '';
                $tampilVal = '';
                if ($details && $details->has($tp->tujuan_pembelajaran_id)) {
                    $detail = $details->get($tp->tujuan_pembelajaran_id);
                    $kktpVal = $detail->kktp ? 1 : 0;
                    $tampilVal = $detail->tampil ? 1 : 0;
                }

                $sheet->setCellValue($colKKTP . $row, $kktpVal);
                $sheet->setCellValue($colTampil . $row, $tampilVal);

                // Validasi KKTP
                $validationKKTP = $sheet->getCell($colKKTP . $row)->getDataValidation();
                $validationKKTP->setType(DataValidation::TYPE_LIST);
                $validationKKTP->setErrorStyle(DataValidation::STYLE_STOP);
                $validationKKTP->setAllowBlank(true);
                $validationKKTP->setShowInputMessage(true);
                $validationKKTP->setShowErrorMessage(true);
                $validationKKTP->setShowDropDown(true);
                $validationKKTP->setFormula1('"0,1"');
                $validationKKTP->setErrorTitle('Input Salah');
                $validationKKTP->setError('Hanya boleh 0 atau 1');
                $validationKKTP->setPromptTitle('Input');
                $validationKKTP->setPrompt('Masukkan 1 jika KKTP Tercapai, 0 sebaliknya');

                // Validasi Tampil/Tidak
                $validationTampil = $sheet->getCell($colTampil . $row)->getDataValidation();
                $validationTampil->setType(DataValidation::TYPE_LIST);
                $validationTampil->setErrorStyle(DataValidation::STYLE_STOP);
                $validationTampil->setAllowBlank(true);
                $validationTampil->setShowInputMessage(true);
                $validationTampil->setShowErrorMessage(true);
                $validationTampil->setShowDropDown(true);
                $validationTampil->setFormula1('"0,1"');
                $validationTampil->setErrorTitle('Input Salah');
                $validationTampil->setError('Hanya boleh 0 atau 1');
                $validationTampil->setPromptTitle('Input');
                $validationTampil->setPrompt('Masukkan 0 jika tidak ingin ditampilkan di deskripsi capaian, 1 sebaliknya');
            }

            // Buat array untuk referensi deskripsi TP dan kolom Excel
            $tpDeskripsi = [];
            $tpKKTPCols = [];
            $tpTampilCols = [];
            for ($i = 0; $i < $tpCount; $i++) {
                $colKKTP = chr(ord($startTPCol) + ($i * 2));
                $colTampil = chr(ord($startTPCol) + ($i * 2) + 1);
                $tpDeskripsi[] = $tpList[$i]->deskripsi;
                $tpKKTPCols[] = $colKKTP . $row;
                $tpTampilCols[] = $colTampil . $row;
            }

            // Rumus Excel untuk capaian tertinggi: gabungkan deskripsi TP yang KKTP=1 dan Tampil=1
            $formulaTertinggi = '';
            for ($i = 0; $i < $tpCount; $i++) {
                $formulaTertinggi .= 'IF(AND(' . $tpKKTPCols[$i] . '=1,' . $tpTampilCols[$i] . '=1),"' . str_replace('"', '""', $tpDeskripsi[$i]) . ', ","")';
                if ($i < $tpCount - 1) $formulaTertinggi .= '&';
            }
            // Hilangkan koma terakhir jika ada
            $formulaTertinggi = '="' . $namaSiswa . ' menunjukkan pemahaman dalam " &' . $formulaTertinggi;

            // Rumus Excel untuk capaian terendah: gabungkan deskripsi TP yang KKTP=0 dan Tampil=1
            $formulaTerendah = '';
            for ($i = 0; $i < $tpCount; $i++) {
                $formulaTerendah .= 'IF(AND(' . $tpKKTPCols[$i] . '=0,' . $tpTampilCols[$i] . '=1),"' . str_replace('"', '""', $tpDeskripsi[$i]) . ', ","")';
                if ($i < $tpCount - 1) $formulaTerendah .= '&';
            }
            $formulaTerendah = '="' . $namaSiswa . ' membutuhkan bimbingan dalam " &' . $formulaTerendah;

            // Kolom capaian tertinggi/terendah
            // $sheet->setCellValue($colTertinggi . $row, $formatif->deskripsi_catatan_tertinggi ?? '');
            // $sheet->setCellValue($colTerendah . $row, $formatif->deskripsi_catatan_terendah ?? '');

            // Set formula ke cell
            $sheet->setCellValueExplicit($colTertinggi . $row, $formulaTertinggi, DataType::TYPE_FORMULA);
            $sheet->setCellValueExplicit($colTerendah . $row, $formulaTerendah, DataType::TYPE_FORMULA);

            $sheet->getStyle($colTertinggi)->getAlignment()->setWrapText(true);
            $sheet->getStyle($colTerendah)->getAlignment()->setWrapText(true);

            // Set alignment center untuk seluruh kolom dari A sampai L pada baris siswa
            $sheet->getStyle("A{$row}:{$colTerendah}{$row}")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);

            $row++;
        }

        // Auto size kolom Nama Siswa (B)
        $sheet->getColumnDimension('B')->setAutoSize(true);

        // Download
        $filename = 'template_asesmen_formatif_' . $intrakurikuler->nama_pelajaran . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
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
        $sheet->setCellValue($firstSumatifCol . '1', 'Sumatif Akhir Lingkup Materi (Wajib)');
        $sheet->mergeCells($firstSumatifCol . '1:' . $naCol . '1');

        // Kolom setelah NA: N = Non Tes, O = Tes, P = NA Sumatif Akhir Semester, Q = Nilai Rapor
        $sheet->setCellValue('N1', 'Sumatif Akhir Semester (Tidak Wajib)');
        $sheet->mergeCells('N1:P1');
        $sheet->setCellValue('Q1', 'Nilai Rapor');

        // Header baris 2
        $sheet->setCellValue('A2', '');
        $sheet->setCellValue('B2', '');
        for ($i = 0; $i < $sumatifCount; $i++) {
            $col = chr(ord($firstSumatifCol) + $i);
            $sheet->setCellValue($col . '2', 'Sumatif ' . ($i + 1));
        }
        $sheet->setCellValue($naCol . '2', 'NA Sumatif Lingkup Materi');
        $sheet->setCellValue('N2', 'Non Tes');
        $sheet->setCellValue('O2', 'Tes');
        $sheet->setCellValue('P2', 'NA Sumatif Akhir Semester');
        $sheet->setCellValue('Q2', '');

        // Header baris 3: deskripsi sumatif
        $sheet->setCellValue('A3', '');
        $sheet->setCellValue('B3', '');
        for ($i = 0; $i < $sumatifCount; $i++) {
            $col = chr(ord($firstSumatifCol) + $i);
            $sheet->setCellValue($col . '3', $sumatifs[$i]['deskripsi']);
        }
        $sheet->setCellValue($naCol . '3', '-');
        $sheet->setCellValue('N3', '');
        $sheet->setCellValue('O3', '');
        $sheet->setCellValue('P3', '');
        $sheet->setCellValue('Q3', '');

        // Data siswa
        $row = 4;
        foreach ($siswa as $idx => $s) {
            $sheet->setCellValue('A' . $row, $idx + 1);
            $sheet->setCellValue('B' . $row, $s['nama']);
            for ($i = 0; $i < $sumatifCount; $i++) {
                $col = chr(ord($firstSumatifCol) + $i);
                $sheet->setCellValue($col . $row, '');
            }
            $sheet->setCellValue($naCol . $row, '');
            $sheet->setCellValue('N' . $row, '');
            $sheet->setCellValue('O' . $row, '');
            $sheet->setCellValue('P' . $row, '');
            $sheet->setCellValue('Q' . $row, '');
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
