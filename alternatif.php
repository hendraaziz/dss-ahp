<div class="page-header">
    <h1>Alternatif</h1>
</div>
<div class="panel panel-default">
    <div class="panel-heading">
        <form class="form-inline">
            <input type="hidden" name="m" value="alternatif" />
            <div class="form-group">
                <input class="form-control" type="text" placeholder="Pencarian. . ." name="q" value="<?= isset($_GET['q']) ? $_GET['q'] : '' ?>" />
            </div>
            <div class="form-group">
                <select class="form-control" name="tema">
                    <option value="">Semua Tema</option>
                    <?php
                    $rows = $db->get_results("SELECT * FROM tb_tema ORDER BY kode_tema");
                    foreach($rows as $row){
                        $selected = (isset($_GET['tema']) && $_GET['tema']==$row->kode_tema) ? 'selected' : '';
                        echo "<option value='$row->kode_tema' $selected>$row->nama_tema</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <button class="btn btn-success"><span class="glyphicon glyphicon-refresh"></span> Refresh</button>
            </div>
            <div class="form-group">
                <a style="background-color: #981A40;" class="btn btn-default" href="?m=alternatif_tambah"><span class="glyphicon glyphicon-plus"></span> Tambah</a>
            </div>
        </form>
    </div>
    <?php if(!isset($_GET['tema']) || !$_GET['tema']): ?>
    <div class="alert alert-info">
        Silakan pilih tema terlebih dahulu untuk melihat data alternatif.
    </div>
    <?php else: ?>
    <div class="table-responsive">
        <table class="table table-bordered table-hover table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode</th>
                    <th>Nama Alternatif</th>
                    <th>Tema</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <?php
            $q = esc_field(isset($_GET['q']) ? $_GET['q'] : '');
            $tema = isset($_GET['tema']) ? $_GET['tema'] : '';
            $where = "WHERE (kode_alternatif LIKE '%$q%' OR nama_alternatif LIKE '%$q%')";
            if($tema)
                $where .= " AND a.kode_tema='$tema'";
            $rows = $db->get_results("SELECT a.*, t.nama_tema 
                FROM tb_alternatif a 
                LEFT JOIN tb_tema t ON t.kode_tema=a.kode_tema 
                $where 
                ORDER BY a.kode_alternatif");
            $no = 0;
            foreach ($rows as $row) : ?>
                <tr>
                    <td><?= ++$no ?></td>
                    <td><?= $row->kode_alternatif ?></td>
                    <td><?= $row->nama_alternatif ?></td>
                    <td><?= $row->nama_tema ?></td>
                    <td>
                        <a class="btn btn-xs btn-warning" href="?m=alternatif_ubah&ID=<?= $row->kode_alternatif ?>"><span class="glyphicon glyphicon-edit"></span></a>
                        <a class="btn btn-xs btn-danger" href="aksi.php?act=alternatif_hapus&ID=<?= $row->kode_alternatif ?>"><span class="glyphicon glyphicon-trash"></span></a>
                    </td>
                </tr>
            <?php endforeach ?>
        </table>
    </div>
    <?php endif; ?>
</div>