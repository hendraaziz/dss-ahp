<?php
$tema = $_GET['tema'];
$tema_data = $db->get_row("SELECT * FROM tb_tema WHERE kode_tema='$tema'");

// Mengambil matriks perbandingan berpasangan antar kriteria
$matriks = get_relkriteria($tema);
$total = get_baris_total($matriks);
$normal = normalize($matriks, $total);
$rata = get_rata($normal);
$cm = consistency_measure($matriks, $rata);
?>

<h1>Laporan Hasil Perhitungan AHP</h1>
<p>Tema: <?=$tema_data->nama_tema?></p>

<h3>1. Matriks Perbandingan Kriteria</h3>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>Kode</th>
            <th>Nama</th>
            <?php foreach ($matriks as $key => $val) : ?>
                <th><?= $key ?></th>
            <?php endforeach ?>
        </tr>
    </thead>
    <?php foreach ($matriks as $key => $val) : ?>
        <tr>
            <td><?= $key ?></td>
            <td><?= $KRITERIA[$key] ?></td>
            <?php foreach ($val as $k => $v) : ?>
                <td><?= round($v, 3) ?></td>
            <?php endforeach ?>
        </tr>
    <?php endforeach ?>
    <tfoot>
        <tr>
            <td>&nbsp;</td>
            <td>Total</td>
            <?php foreach ($total as $k => $v) : ?>
                <td><?= round($v, 3) ?></td>
            <?php endforeach ?>
        </tr>
    </tfoot>
</table>

<h3>2. Normalisasi</h3>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>Kode</th>
            <?php foreach ($matriks as $key => $val) : ?>
                <th><?= $key ?></th>
            <?php endforeach ?>
            <th>Jumlah</th>
            <th>Prioritas</th>
            <th>Eigen</th>
        </tr>
    </thead>
    <?php foreach ($normal as $key => $val) : ?>
        <tr>
            <td><?= $key ?></td>
            <?php foreach ($val as $k => $v) : ?>
                <td><?= round($v, 3) ?></td>
            <?php endforeach ?>
            <td><?= round(array_sum($val), 3) ?></td>
            <td><?= round($rata[$key], 3) ?></td>
            <td><?= round($cm[$key], 3) ?></td>
        </tr>
    <?php endforeach ?>
</table>

<h3>3. Hasil Konsistensi</h3>
<?php
$JML = array_sum($cm);
$LMD = ((array_sum($cm) / count($cm)) - count($cm));
$CI = ((array_sum($cm) / count($cm)) - count($cm)) / (count($cm) - 1);
$RI = $nRI[count($matriks)];
$CR = $CI / $RI;
?>
<table class="table table-bordered">
    <tr>
        <td>Jumlah</td>
        <td><?= round($JML, 3) ?></td>
    </tr>
    <tr>
        <td>λmax</td>
        <td><?= round($LMD, 3) ?></td>
    </tr>
    <tr>
        <td>CI</td>
        <td><?= round($CI, 3) ?></td>
    </tr>
    <tr>
        <td>RI</td>
        <td><?= round($RI, 3) ?></td>
    </tr>
    <tr>
        <td>CR</td>
        <td><?= round($CR, 3) ?> <?= $CR > 0.1 ? '(Tidak Konsisten)' : '(Konsisten)' ?></td>
    </tr>
</table>

<h3>4. Hasil Analisa</h3>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>Kode</th>
            <th>Nama Alternatif</th>
            <?php 
            $kriteria_tema = $db->get_results("SELECT * FROM tb_kriteria WHERE kode_tema='$tema' ORDER BY kode_kriteria");
            foreach ($kriteria_tema as $row) : ?>
                <th><?= $row->nama_kriteria ?></th>
            <?php endforeach ?>
        </tr>
    </thead>
    <?php
    $data = get_rel_alternatif();
    foreach ($data as $key => $val) : ?>
        <tr>
            <td><?= $key ?></td>
            <td><?= $ALTERNATIF[$key] ?></td>
            <?php foreach ($val as $k => $v) : ?>
                <td><?= $SUB[$v]['nama'] ?></td>
            <?php endforeach ?>
        </tr>
    <?php endforeach; ?>
</table>

<h3>5. Hasil Pembobotan</h3>
<?php
function get_hasil_bobot($data)
{
    global $SUB;
    $arr = array();
    foreach ($data as $key => $val) {
        foreach ($val as $k => $v) {
            $arr[$key][$k] = $SUB[$v]['nilai_sub'];
        }
    }
    return $arr;
}
$hasil_bobot = get_hasil_bobot($data);
?>
<table class="table table-bordered">
    <thead>
        <tr>
            <th rowspan="2">Kode</th>
            <th rowspan="2">Nama Alternatif</th>
            <?php 
            $kriteria_tema = $db->get_results("SELECT * FROM tb_kriteria WHERE kode_tema='$tema' ORDER BY kode_kriteria");
            foreach ($kriteria_tema as $row) : ?>
                <th><?= $row->nama_kriteria ?></th>
            <?php endforeach ?>
        </tr>
        <tr>
            <?php foreach ($rata as $key => $val) : ?>
                <th><?= round($val, 4) ?></th>
            <?php endforeach ?>
        </tr>
    </thead>
    <?php foreach ($hasil_bobot as $key => $val) : ?>
        <tr>
            <td><?= $key ?></td>
            <td><?= $ALTERNATIF[$key] ?></td>
            <?php foreach ($val as $k => $v) : ?>
                <td><?= round($v, 4) ?></td>
            <?php endforeach ?>
        </tr>
    <?php endforeach; ?>
</table>

<h3>6. Perangkingan</h3>
<?php
function get_total($hasil_bobot, $rata)
{
    global $SUB;
    $arr = array();

    foreach ($hasil_bobot as $key => $val) {
        foreach ($val as $k => $v) {
            $arr[$key] += $v * $rata[$k];
        }
    }
    return $arr;
}
$total = get_total($hasil_bobot, $rata);
?>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>Kode</th>
            <th>Nama</th>
            <th>Ranking</th>
            <th>Total</th>
        </tr>
    </thead>
    <?php
    $rows = $db->get_results("SELECT * FROM tb_alternatif WHERE kode_tema='$tema' ORDER BY total DESC");
    foreach ($rows as $row) : ?>
        <tr>
            <td><?= $row->kode_alternatif ?></td>
            <td><?= $row->nama_alternatif ?></td>
            <td><?= $row->rank ?></td>
            <td><?= round($row->total, 4) ?></td>
        </tr>
    <?php endforeach ?>
</table>

<?php
if (!empty($rows)) {
    $best = $rows[0]->kode_alternatif;
    echo "<div style='margin-top: 20px; padding: 10px; background-color: #f5f5f5; border: 1px solid #ddd;'>";
    echo "<h4 style='margin: 0;'>Kesimpulan:</h4>";
    echo "<p style='margin: 10px 0 0 0;'>Berdasarkan hasil perhitungan AHP untuk tema <strong>" . $tema_data->nama_tema . "</strong>, ";
    echo "pilihan terbaik adalah <strong>" . $ALTERNATIF[$best] . "</strong> dengan nilai <strong>" . round($total[$best], 3) . "</strong></p>";
    echo "</div>";
}
?>