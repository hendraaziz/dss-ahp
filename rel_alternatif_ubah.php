<?php
$row = $db->get_row("SELECT * FROM tb_alternatif WHERE kode_alternatif='$_GET[ID]'");
?>
<div class="page-header">
    <h1>Ubah Nilai Bobot &raquo; <small><?= $row->nama_alternatif ?></small></h1>
</div>
<div class="panel panel-primary">
    <div style="background-color: #981A40;" class="panel-heading">
        <h3 class="panel-title">Data Alternatif</h3>
    </div>
</div>
<div class="row">
    <div class="col-sm-4">
        <form method="post" action="aksi.php?act=rel_alternatif_ubah">
            <?php
            $alternatif = $db->get_row("SELECT * FROM tb_alternatif WHERE kode_alternatif='$_GET[ID]'");
            $rows = $db->get_results("SELECT k.kode_kriteria, k.nama_kriteria, COALESCE(ra.ID, '') as ID, COALESCE(ra.kode_sub, '') as kode_sub
                FROM tb_kriteria k 
                LEFT JOIN tb_rel_alternatif ra ON k.kode_kriteria=ra.kode_kriteria AND ra.kode_alternatif='$_GET[ID]'
                WHERE k.kode_tema='$alternatif->kode_tema' 
                ORDER BY k.kode_kriteria");
            foreach ($rows as $row) : ?>
                <div class="form-group">
                    <label><?= $row->nama_kriteria ?></label>
                    <select class="form-control" name="nilai[<?= $row->ID ?>]">
                        <?= get_sub_option($row->kode_sub, $row->kode_kriteria) ?>
                    </select>
                </div>
            <?php endforeach ?>
            <button class="btn btn-primary"><span class="glyphicon glyphicon-save"></span> Simpan</button>
            <a class="btn btn-danger" href="?m=rel_alternatif"><span class="glyphicon glyphicon-arrow-left"></span> Kembali</a>
        </form>
    </div>
</div>