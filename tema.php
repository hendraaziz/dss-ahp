<?php
include_once('functions.php');
?>
<div class="page-header">
    <h1>Tema DSS</h1>
</div>
<div class="panel panel-default">
    <div class="panel-heading">
        <form class="form-inline">
            <input type="hidden" name="m" value="tema" />
            <div class="form-group">
                <input class="form-control" type="text" placeholder="Pencarian..." name="q" value="<?=isset($_GET['q']) ? $_GET['q'] : ''?>" />
            </div>
            <div class="form-group">
                <button class="btn btn-success"><span class="glyphicon glyphicon-refresh"></span> Refresh</button>
            </div>
            <div class="form-group">
                <a class="btn btn-primary" href="?m=tema_tambah"><span class="glyphicon glyphicon-plus"></span> Tambah</a>
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover table-striped">
        <thead>
            <tr class="nw">
                <th>No</th>
                <th>Kode</th>
                <th>Nama Tema</th>
                <th>Deskripsi</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <?php
        $q = esc_field(isset($_GET['q']) ? $_GET['q'] : '');
        $rows = $db->get_results("SELECT * FROM tb_tema 
            WHERE kode_tema LIKE '%$q%' OR nama_tema LIKE '%$q%' OR deskripsi LIKE '%$q%' 
            ORDER BY kode_tema");
        $no=0;
        foreach($rows as $row):?>
        <tr>
            <td><?=++$no ?></td>
            <td><?=$row->kode_tema?></td>
            <td><?=$row->nama_tema?></td>
            <td><?=$row->deskripsi?></td>
            <td class="nw">
                <a class="btn btn-xs btn-warning" href="?m=tema_ubah&ID=<?=$row->kode_tema?>"><span class="glyphicon glyphicon-edit"></span></a>
                <a class="btn btn-xs btn-danger" href="aksi.php?act=tema_hapus&ID=<?=$row->kode_tema?>" onclick="return confirm('Hapus data?')"><span class="glyphicon glyphicon-trash"></span></a>
            </td>
        </tr>
        <?php endforeach;?>
        </table>
    </div>
</div>