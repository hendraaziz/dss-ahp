<?php
include_once('functions.php');
?>
<div class="page-header">
    <h1>Tambah Tema</h1>
</div>
<div class="row">
    <div class="col-sm-6">
        <?php if($_POST) include'aksi.php'?>
        <form method="post">
            <div class="form-group">
                <label>Kode <span class="text-danger">*</span></label>
                <input class="form-control" type="text" name="kode_tema" value="<?=$_POST[kode_tema]?>" required/>
            </div>
            <div class="form-group">
                <label>Nama Tema <span class="text-danger">*</span></label>
                <input class="form-control" type="text" name="nama_tema" value="<?=$_POST[nama_tema]?>" required/>
            </div>
            <div class="form-group">
                <label>Deskripsi</label>
                <textarea class="form-control" name="deskripsi"><?=$_POST[deskripsi]?></textarea>
            </div>
            <div class="form-group">
                <button class="btn btn-primary"><span class="glyphicon glyphicon-save"></span> Simpan</button>
                <a class="btn btn-danger" href="?m=tema"><span class="glyphicon glyphicon-arrow-left"></span> Kembali</a>
            </div>
        </form>
    </div>
</div>