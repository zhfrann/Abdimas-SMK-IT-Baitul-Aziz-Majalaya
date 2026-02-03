@php
    $baris = $jumlah ?? 3;
@endphp

<div class="page">
    <div style="text-align:center; font-weight:bold; font-size:16pt;">
        KETERANGAN PINDAH SEKOLAH
    </div>
    <div style="margin-top: 5mm;">
        Nama Peserta Didik : …………………………………………………
    </div>
    <table border="1" cellpadding="6" cellspacing="0"
        style="width:100%; font-size:12pt; border-collapse:collapse; margin-top: 5mm;">
        <thead>
            <tr>
                <th style="width:4%;">No.</th>
                <th style="width:46%;" colspan="3">MASUK</th>
            </tr>
        </thead>
        <tbody>
            @for ($i = 0; $i < $baris; $i++)
                <tr style="page-break-inside: avoid;">
                    <td style="vertical-align:top; text-align:center;">{{ $i + 1 }}.</td>
                    <td style="vertical-align:top;">
                        Nama Peserta Didik<br>
                        Nomor Induk<br>
                        NISN<br>
                        Nama Sekolah<br>
                        Masuk di Sekolah ini :<br>
                        a. Tanggal<br>
                        b. Di Kelas<br>
                        c. Tahun Pelajaran
                    </td>
                    <td style="width: 20%">
                    </td>
                    <td style="vertical-align:top; width: 8%;">
                        <br>
                        …………., ………………………………………<br>
                        Kepala Sekolah<br>
                        <br><br>
                        ___________________________<br>
                        NUPTK.
                        <br>
                    </td>
                </tr>
            @endfor
        </tbody>
    </table>

    <div style="margin-top:10mm; width:100%;">
        <div style="display:flex; justify-content:space-between; width:100%;">
            <div style="text-align:center;">
                Mengetahui<br><br>
                Orang Tua/Wali<br>
                <br><br><br>
                ..................................................<br>
            </div>
            <div style="text-align:center;">
                …………., ………………………………………<br><br>
                Guru Kelas<br>
                <br><br><br>
                ........................................................<br>
                NUPTK..................................................
            </div>
        </div>
        <div style="margin-top:30mm; text-align:center;">
            Mengetahui<br><br>
            Kepala Sekolah<br>
            <br><br><br>
            ........................................................<br>
            NUPTK..................................................
        </div>
    </div>
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
