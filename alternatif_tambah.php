<div class="page-header">
    <h1>Tambah Alternatif</h1>
</div>
<div class="row">
    <div class="col-sm-6">
        <?php if ($_POST) include 'aksi.php' ?>
        <form method="post">

            <div class="form-group">
                <label>Kode <span class="text-danger">*</span></label>
                <input class="form-control" type="text" name="kode" value="<?= $_POST['kode'] ?>" />
            </div>

            <div class="form-group">
                <label>Nama Alternatif <span class="text-danger">*</span></label>
                <input class="form-control" type="text" name="nama" value="<?= $_POST['nama'] ?>" />
            </div>

            <div class="form-group">
                <label>Tema DSS</label>
                <select class="form-control" name="kode_tema">
                    <option value="">Pilih Tema</option>
                    <?php
                    $rows = $db->get_results("SELECT * FROM tb_tema ORDER BY kode_tema");
                    foreach($rows as $row){
                        $selected = $row->kode_tema==$_POST[kode_tema] ? 'selected' : '';
                        echo "<option value='$row->kode_tema' $selected>$row->nama_tema</option>";
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