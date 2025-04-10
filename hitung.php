<div class="page-header">
    <h1>Perhitungan</h1>
</div>

<?php
if(!isset($_GET['tema'])){
?>
<div class="panel panel-primary">
    <div style="background-color: #981A40;" class="panel-heading">
        <h3 class="panel-title">Pilih Tema</h3>
    </div>
    <div class="panel-body">
        <form class="form-inline">
            <input type="hidden" name="m" value="hitung" />
            <div class="form-group">
                <select class="form-control" name="tema" onchange="this.form.submit()">
                    <option value="">Pilih Tema</option>
                    <?php
                    $rows = $db->get_results("SELECT * FROM tb_tema ORDER BY kode_tema");
                    foreach($rows as $row){
                        echo "<option value='$row->kode_tema'>$row->nama_tema</option>";
                    }
                    ?>
                </select>
            </div>
        </form>
    </div>
</div>
<?php
} else {
    $tema = $_GET['tema'];
    $matriks = get_relkriteria($tema);
    if(empty($matriks)){
        echo "<div class='alert alert-warning'>Belum ada data perbandingan kriteria untuk tema ini.</div>";
        return;
    }
    $total = get_baris_total($matriks);
    ?>
    <div class="page-header">
        <h1>Perhitungan</h1>
        <div class="row">
            <div class="col-md-4">
                <form class="form-inline">
                    <input type="hidden" name="m" value="hitung" />
                    <div class="form-group">
                        <select class="form-control" name="tema" onchange="this.form.submit()">
                            <?php
                            $rows = $db->get_results("SELECT * FROM tb_tema ORDER BY kode_tema");
                            foreach($rows as $row){
                                $selected = $row->kode_tema==$tema ? 'selected' : '';
                                echo "<option value='$row->kode_tema' $selected>$row->nama_tema</option>";
                            }
                            ?>
                        </select>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="panel panel-primary">
        <div style="background-color: #981A40;" class="panel-heading">
            <h3 class="panel-title">Mengukur Konsistensi Kriteria</h3>
        </div>
        <div class="panel-body">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Matriks Perbandingan Kriteria</h3>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
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
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Normalisasi</h3>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <?php
                                $normal = normalize($matriks, $total);
                                $rata = get_rata($normal);
                                $cm = consistency_measure($matriks, $rata);
                                foreach ($matriks as $key => $val) : ?>
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
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-body">
                    Berikut tabel ratio index berdasarkan ordo matriks.
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <tr>
                            <th>Ordo matriks</th>
                            <?php
                            foreach ($nRI as $key => $value) {
                                if (count($matriks) == $key)
                                    echo "<td class='text-primary'>$key</td>";
                                else
                                    echo "<td>$key</td>";
                            }
                            ?>
                        </tr>
                        <tr>
                            <th>Ratio index</th>
                            <?php
                            foreach ($nRI as $key => $value) {
                                if (count($matriks) == $key)
                                    echo "<td class='text-primary'>$value</td>";
                                else
                                    echo "<td>$value</td>";
                            }
                            ?>
                        </tr>
                    </table>
                </div>
                <div class="panel-body">
                    <?php
                    $JML = array_sum($cm);
                    $LMD = ((array_sum($cm) / count($cm)) - count($cm));
                    $CI = ((array_sum($cm) / count($cm)) - count($cm)) / (count($cm) - 1);
                    $RI = $nRI[count($matriks)];
                    $CR = $CI / $RI;
                    echo "<p>Jumlah: " . round($JML, 3) . "<br />";
                    echo "&lambda;max: " . round($LMD, 3) . "<br />";
                    echo "Consistency Index: " . round($CI, 3) . "<br />";
                    echo "Ratio Index: " . round($RI, 3) . "<br />";
                    echo "Consistency Ratio: " . round($CR, 3);
                    if ($CR > 0.10) {
                        echo " (Tidak konsisten)<br />";
                    } else {
                        echo " (Konsisten)<br />";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <div class="panel panel-primary">
        <div style="background-color: #981A40;" class="panel-heading">
            <h3 class="panel-title">Hasil Analisa</h3>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-striped">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama Alternatif</th>
                        <?php 
                        $KRITERIA = array();
                        $rows = $db->get_results("SELECT * FROM tb_kriteria WHERE kode_tema='$tema' ORDER BY kode_kriteria");
                        foreach($rows as $row) {
                            $KRITERIA[$row->kode_kriteria] = $row->nama_kriteria;
                        }
                        foreach ($KRITERIA as $key => $val) : ?>
                            <th><?= $val ?></th>
                        <?php endforeach ?>
                    </tr>
                </thead>
                <?php
                $ALTERNATIF = array();
                $rows = $db->get_results("SELECT * FROM tb_alternatif WHERE kode_tema='$tema' ORDER BY kode_alternatif");
                foreach($rows as $row) {
                    $ALTERNATIF[$row->kode_alternatif] = $row->nama_alternatif;
                }

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
        </div>
    </div>

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

    <div class="panel panel-primary">
        <div style="background-color: #981A40;" class="panel-heading">
            <h3 class="panel-title">Hasil Pembobotan</h3>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-striped">
                <thead>
                    <tr>
                        <th rowspan="2">Kode</th>
                        <th rowspan="2">Nama Alternatif</th>
                        <?php foreach ($KRITERIA as $key => $val) : ?>
                            <th><?= $val ?></th>
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
        </div>
    </div>

    <div class="panel panel-primary">
        <div style="background-color: #981A40;" class="panel-heading">
            <h3 class="panel-title">Perangkingan</h3>
        </div>
        <div class="table-responsive">
            <table class="table table-hover table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama</th>
                        <th>Ranking</th>
                        <th>Total</th>
                    </tr>
                </thead>
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
                FAHP_save($total);
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
        </div>
        <div class="panel-body">
            <?php
            if(!empty($rows)) {
                $best = $rows[0]->kode_alternatif;
                echo "<p>Jadi pilihan terbaik adalah <strong>" . $ALTERNATIF[$best] . "</strong> dengan nilai <strong>" . round($total[$best], 3) . "</strong></p>";
            }
            ?>
            <p><a class="btn btn-default" target="_blank" href="cetak.php?m=hitung&tema=<?=$tema?>"><span class="glyphicon glyphicon-print"></span> Cetak</a></p>
        </div>
    </div>
<?php
}
?>