<?php
$row = $db->get_row("SELECT * FROM tb_alternatif WHERE kode_alternatif='$_GET[ID]'");
?>
<div class="page-header">
    <h1>Ubah Alternatif</h1>
</div>
<div class="row">
    <div class="col-sm-6">
        <?php if ($_POST) include 'aksi.php' ?>
        <form method="post">
            
            <div class="form-group">
                <label>Kode <span class="text-danger">*</span></label>
                <input class="form-control" type="text" name="kode" readonly="readonly" value="<?= $row->kode_alternatif ?>" />
            </div>

            <div class="form-group">
                <label>Nama Alternatif <span class="text-danger">*</span></label>
                <input class="form-control" type="text" name="nama" value="<?= $row->nama_alternatif ?>" />
            </div>

            <div class="form-group">
                <label>Tema DSS</label>
                <select class="form-control" name="kode_tema">
                    <option value="">Pilih Tema</option>
                    <?php
                    $rows = $db->get_results("SELECT * FROM tb_tema ORDER BY kode_tema");
                    foreach($rows as $row_tema){
                        $selected = $row_tema->kode_tema==$row->kode_tema ? 'selected' : '';
                        echo "<option value='$row_tema->kode_tema' $selected>$row_tema->nama_tema</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <button class="btn btn-primary"><span class="glyphicon glyphicon-save"></span> Simpan</button>
                <a class="btn btn-danger" href="?m=alternatif"><span class="glyphicon glyphicon-arrow-left"></span> Kembali</a>
            </div>

        </form>
    </div>
</div>