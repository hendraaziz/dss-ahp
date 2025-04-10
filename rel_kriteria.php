<div class="page-header">
    <h1>Nilai Bobot Kriteria</h1>
</div>
<?php
if ($_POST) include 'aksi.php';

$tema = $_GET['tema'];
$where = "";
if($tema) {
    $where = "WHERE k.kode_tema='$tema'";
}

$rows = $db->get_results("SELECT k.nama_kriteria, rk.ID1, rk.ID2, nilai 
    FROM tb_rel_kriteria rk 
    INNER JOIN tb_kriteria k ON k.kode_kriteria=rk.ID1 
    INNER JOIN tb_kriteria k2 ON k2.kode_kriteria=rk.ID2 
    $where AND k2.kode_tema=k.kode_tema 
    ORDER BY ID1, ID2");
$criterias = array();
$data = array();
foreach ($rows as $row) {
    $criterias[$row->ID1] = $row->nama_kriteria;
    $data[$row->ID1][$row->ID2] = $row->nilai;
}
?>
<div class="panel panel-default">
    <div class="panel-heading">
        <form class="form-inline">
            <input type="hidden" name="m" value="rel_kriteria" />
            <div class="form-group">
                <select class="form-control" name="tema" onchange="this.form.submit()">
                    <option value="">Pilih Tema</option>
                    <?php
                    $rows_tema = $db->get_results("SELECT * FROM tb_tema ORDER BY kode_tema");
                    foreach($rows_tema as $row){
                        $selected = $_GET['tema']==$row->kode_tema ? 'selected' : '';
                        echo "<option value='$row->kode_tema' $selected>$row->nama_tema</option>";
                    }
                    ?>
                </select>
            </div>
        </form>
        <?php if($tema): ?>
        <br>
        <form class="form-inline" action="?m=rel_kriteria&tema=<?=$tema?>" method="post">
            <div class="form-group">
                <select class="form-control" name="ID1">
                    <?= get_kriteria_option($_POST['ID1'], $tema) ?>
                </select>
            </div>
            <div class="form-group">
                <select class="form-control" name="nilai">
                    <?= get_nilai_option($_POST['nilai']) ?>
                </select>
            </div>
            <div class="form-group">
                <select class="form-control" name="ID2">
                    <?= get_kriteria_option($_POST['ID2'], $tema) ?>
                </select>
            </div>
            <div class="form-group">
                <button style="background-color: #981A40;" class="btn btn-default"><span class="glyphicon glyphicon-edit"></span> Ubah</a>
            </div>
        </form>
        <?php endif; ?>
    </div>

    <?php if($tema): ?>
    <table class="table table-bordered table-hover table-striped">
        <thead>
            <tr>
                <th>Kode</th>
                <?php
                foreach ($data as $key => $value) {
                    echo "<th>$key</th>";
                }
                ?>
            </tr>
        </thead>
        <?php
        $no = 1;
        foreach ($data as $key => $value) : ?>
            <tr>
                <th><?= $key ?></th>
                <?php
                foreach ($value as $dt) {
                    echo "<td>" . round($dt, 3) . "</td>";
                }
                $no++;
                ?>
            </tr>
        <?php endforeach ?>
    </table>
    <?php else: ?>
    <div class="alert alert-info">
        Silakan pilih tema terlebih dahulu untuk melihat nilai bobot kriteria.
    </div>
    <?php endif; ?>
</div>