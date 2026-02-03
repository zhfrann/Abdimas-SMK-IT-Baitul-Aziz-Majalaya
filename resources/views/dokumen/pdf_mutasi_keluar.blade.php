@php
    $baris = $jumlah ?? 3;
@endphp

<div class="page">
    <div style="text-align:center; font-weight:bold; font-size:16pt;">
        KETERANGAN PINDAH SEKOLAH
    </div>
    <div style="margin-top: 5mm;">
        Nama Peserta Didik : …………………………………………………………
    </div>
    <table border="1" cellpadding="6" cellspacing="0"
        style="width:100%; font-size:12pt; border-collapse:collapse; margin-top:5mm;">
        <thead>
            <tr>
                <th style="width:15%;">Tanggal</th>
                <th style="width:20%;">Kelas yang Ditinggalkan</th>
                <th style="width:20%;">Alasan</th>
                <th style="width:45%;">Tanda Tangan Kepala sekolah, Stempel Sekolah dan Tanda Tangan Orang Tua/Wali</th>
            </tr>
        </thead>
        <tbody>
            @for ($i = 0; $i < $baris; $i++)
                <tr style="page-break-inside: avoid;">
                    <td style="height:80px;"></td>
                    <td></td>
                    <td></td>
                    <td>
                        <br>
                        …………., …………………………………<br>
                        Kepala Sekolah<br>
                        <br><br>
                        ___________________________<br>
                        NUPTK.<br><br>
                        Orang Tua/Wali<br><br><br>
                        ___________________________<br><br>
                    </td>
                </tr>
            @endfor
        </tbody>
    </table>
</div>

<style>
    @page {
        size: A4;
        margin: 18mm 12mm 12mm 12mm;
    }

    .page {
        page-break-after: always;
    }

    body {
        font-family: "Times New Roman", Times, serif;
        font-size: 12pt;
        color: #000;
    }

    table,
    th,
    td {
        border: 1px solid #000;
    }
</style>
