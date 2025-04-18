<div class="page-header">
    <h1>Sub Kriteria</h1>
</div>
<div class="panel panel-default">
    <div class="panel-heading">
        <form class="form-inline">
            <input type="hidden" name="m" value="sub" />
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
                <a style="background-color: #981A40;" class="btn btn-default" href="?m=sub_tambah"><span class="glyphicon glyphicon-plus"></span> Tambah</a>
            </div>
        </form>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered table-hover table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kriteria</th>
                    <th>Kode</th>
                    <th>Nama sub</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <?php
            $q = esc_field(isset($_GET['q']) ? $_GET['q'] : '');
            $tema = isset($_GET['tema']) ? $_GET['tema'] : '';
            $where = "nama_sub LIKE '%$q%'";
            if($tema) {
                $where .= " AND k.kode_tema='$tema'";
            }
            $rows = $db->get_results("SELECT * FROM tb_sub s
                INNER JOIN tb_kriteria k ON s.kode_kriteria=k.kode_kriteria 
                WHERE $where ORDER BY k.kode_kriteria, s.kode_sub");
            $no = 0;
            foreach ($rows as $row) : ?>
                <tr>
                    <td><?= ++$no ?></td>
                    <td><?= $row->nama_kriteria ?></td>
                    <td><?= $row->kode_sub ?></td>
                    <td><?= $row->nama_sub ?></td>
                    <td>
                        <a class="btn btn-xs btn-warning" href="?m=sub_ubah&ID=<?= $row->kode_sub ?>"><span class="glyphicon glyphicon-edit"></span></a>
                        <a class="btn btn-xs btn-danger" href="aksi.php?act=sub_hapus&ID=<?= $row->kode_sub ?>" onclick="return confirm('Hapus data?')"><span class="glyphicon glyphicon-trash"></span></a>
                    </td>
                </tr>
            <?php endforeach ?>
        </table>
    </div>
</div>