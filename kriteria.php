<div class="page-header">
    <h1>Kriteria</h1>
</div>
<div class="panel panel-default">
    <div class="panel-heading">
        <form class="form-inline">
            <input type="hidden" name="m" value="kriteria" />
            <div class="form-group">
                <input class="form-control" type="text" placeholder="Pencarian..." name="q" value="<?=isset($_GET['q']) ? $_GET['q'] : ''?>" />
            </div>
            <div class="form-group">
                <select class="form-control" name="tema">
                    <option value="">Semua Tema</option>
                    <?php
                    $rows = $db->get_results("SELECT * FROM tb_tema ORDER BY kode_tema");
                    foreach($rows as $row){
                        $selected = $_GET['tema']==$row->kode_tema ? 'selected' : '';
                        echo "<option value='$row->kode_tema' $selected>$row->nama_tema</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <button class="btn btn-success"><span class="glyphicon glyphicon-refresh"></span> Refresh</button>
            </div>
            <div class="form-group">
                <a class="btn btn-primary" href="?m=kriteria_tambah"><span class="glyphicon glyphicon-plus"></span> Tambah</a>
            </div>
        </form>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered table-hover table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode</th>
                    <th>Nama Kriteria</th>
                    <th>Tema</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <?php
            $q = esc_field(isset($_GET['q']) ? $_GET['q'] : '');
            $tema = isset($_GET['tema']) ? $_GET['tema'] : '';
            $where = "WHERE (kode_kriteria LIKE '%$q%' OR nama_kriteria LIKE '%$q%')";
            if($tema)
                $where .= " AND k.kode_tema='$tema'";
            $rows = $db->get_results("SELECT k.*, t.nama_tema 
                FROM tb_kriteria k 
                LEFT JOIN tb_tema t ON t.kode_tema=k.kode_tema 
                $where 
                ORDER BY k.kode_kriteria");
            $no = 0;
            foreach ($rows as $row) : ?>
                <tr>
                    <td><?= ++$no ?></td>
                    <td><?= $row->kode_kriteria ?></td>
                    <td><?= $row->nama_kriteria ?></td>
                    <td><?= $row->nama_tema ?></td>
                    <td>
                        <a class="btn btn-xs btn-warning" href="?m=kriteria_ubah&ID=<?= $row->kode_kriteria ?>"><span class="glyphicon glyphicon-edit"></span></a>
                        <a class="btn btn-xs btn-danger" href="aksi.php?act=kriteria_hapus&ID=<?= $row->kode_kriteria ?>" onclick="return confirm('Hapus data?')"><span class="glyphicon glyphicon-trash"></span></a>
                    </td>
                </tr>
            <?php endforeach ?>
        </table>
    </div>
</div>