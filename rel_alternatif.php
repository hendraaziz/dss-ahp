<div class="page-header">
    <h1>Nilai Bobot Alternatif</h1>
</div>
<div class="panel panel-default">
    <div class="panel-heading">
        <form class="form-inline">
            <input type="hidden" name="m" value="rel_alternatif" />
            <div class="form-group">
                <input class="form-control" type="text" name="q" value="<?= isset($_GET['q']) ? $_GET['q'] : '' ?>" placeholder="Pencarian..." />
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
                    <th>Kode</th>
                    <th>Nama Alternatif</th>
                    <?php 
                    $tema = isset($_GET['tema']) ? $_GET['tema'] : '';
                    $KRITERIA = array();
                    $rows = $db->get_results("SELECT * FROM tb_kriteria WHERE kode_tema='$tema' ORDER BY kode_kriteria");
                    foreach($rows as $row){
                        $KRITERIA[$row->kode_kriteria] = $row->nama_kriteria;
                        echo "<th>$row->nama_kriteria</th>";
                    }
                    ?>
                    <th>Aksi</th>
                </tr>
            </thead>
            <?php
            $q = esc_field($_GET['q']);
            $rows = $db->get_results("SELECT a.* 
                FROM tb_alternatif a 
                WHERE a.kode_tema='$tema' AND (a.kode_alternatif LIKE '%$q%' OR a.nama_alternatif LIKE '%$q%')
                ORDER BY a.kode_alternatif");
            foreach($rows as $row){
                echo "<tr>";
                echo "<td>$row->kode_alternatif</td>";
                echo "<td>$row->nama_alternatif</td>";
                foreach($KRITERIA as $k=>$v){
                    $rel = $db->get_row("SELECT ra.*, s.nama_sub 
                        FROM tb_rel_alternatif ra 
                        LEFT JOIN tb_sub s ON s.kode_sub=ra.kode_sub 
                        WHERE ra.kode_alternatif='$row->kode_alternatif' AND ra.kode_kriteria='$k'");
                    echo "<td>" . ($rel ? $rel->nama_sub : '') . "</td>";
                }
                echo "<td><a class='btn btn-xs btn-warning' href='?m=rel_alternatif_ubah&ID=$row->kode_alternatif'><span class='glyphicon glyphicon-edit'></span></a></td>";
                echo "</tr>";
            }
            ?>
        </table>
    </div>
    <?php endif; ?>
</div>