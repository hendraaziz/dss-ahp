<?php
require_once 'functions.php';

/** LOGIN **/
if ($act == 'login') {
    $user = esc_field($_POST['user']);
    $pass = esc_field($_POST['pass']);

    $row = $db->get_row("SELECT * FROM tb_admin WHERE user='$user' AND pass='$pass'");
    if ($row) {
        $_SESSION['login'] = $row->user;
        $_SESSION['level'] = strtolower($row->level);
        redirect_js("index.php");
    } else {
        print_msg("Salah kombinasi username dan password.");
    }
} else if ($mod == 'password') {
    $pass1 = $_POST['pass1'];
    $pass2 = $_POST['pass2'];
    $pass3 = $_POST['pass3'];

    $row = $db->get_row("SELECT * FROM tb_admin WHERE user='$_SESSION[login]' AND pass='$pass1'");

    if ($pass1 == '' || $pass2 == '' || $pass3 == '')
        print_msg("Field bertanda * tidak boleh kosong!");
    elseif (!$row)
        print_msg('Password lama salah.');
    elseif ($pass2 != $pass3)
        print_msg('Password baru dan konfirmasi password baru tidak sama.');
    else {
        $db->query("UPDATE tb_user SET pass='$pass2' WHERE user='$_SESSION[login]'");
        print_msg('Password berhasil diubah.', 'success');
    }
} elseif ($act == 'logout') {
    unset($_SESSION['login'], $_SESSION['level']);
    header("location:login.php");
}


/** alternatif **/
elseif ($mod == 'alternatif_tambah') {
    $kode = $_POST['kode'];
    $nama = $_POST['nama'];
    $kode_tema = $_POST['kode_tema'];

    if ($kode == '' || $nama == '')
        print_msg("Field bertanda * tidak boleh kosong!");
    elseif ($db->get_results("SELECT * FROM tb_alternatif WHERE kode_alternatif='$kode'"))
        print_msg("Kode sudah ada!");
    else {
        $db->query("INSERT INTO tb_alternatif (kode_alternatif, nama_alternatif, kode_tema) VALUES ('$kode', '$nama', '$kode_tema')");
        $db->query("INSERT INTO tb_rel_alternatif(kode_alternatif, kode_kriteria, kode_sub) 
            SELECT '$kode', kode_kriteria, 0 FROM tb_kriteria");
        redirect_js("index.php?m=alternatif");
    }
} elseif ($mod == 'alternatif_ubah') {
    $kode = $_POST['kode'];
    $nama = $_POST['nama'];
    $kode_tema = $_POST['kode_tema'];

    if ($kode == '' || $nama == '')
        print_msg("Field bertanda * tidak boleh kosong!");
    elseif ($db->get_results("SELECT * FROM tb_alternatif WHERE kode_alternatif='$kode' AND kode_alternatif<>'$_GET[ID]'"))
        print_msg("Kode sudah ada!");
    else {
        $db->query("UPDATE tb_alternatif SET kode_alternatif='$kode', nama_alternatif='$nama', kode_tema='$kode_tema' WHERE kode_alternatif='$_GET[ID]'");
        redirect_js("index.php?m=alternatif");
    }
} elseif ($act == 'alternatif_hapus') {
    $db->query("DELETE FROM tb_alternatif WHERE kode_alternatif='$_GET[ID]'");
    $db->query("DELETE FROM tb_rel_alternatif WHERE kode_alternatif='$_GET[ID]'");
    header("location:index.php?m=alternatif");
}


/** tema **/
elseif ($mod == 'tema_tambah') {
    $kode = $_POST['kode_tema'];
    $nama = $_POST['nama_tema'];
    $deskripsi = $_POST['deskripsi'];

    if ($kode == '' || $nama == '')
        print_msg("Field bertanda * tidak boleh kosong!");
    elseif ($db->get_results("SELECT * FROM tb_tema WHERE kode_tema='$kode'"))
        print_msg("Kode tema sudah ada!");
    else {
        $db->query("INSERT INTO tb_tema (kode_tema, nama_tema, deskripsi) VALUES ('$kode', '$nama', '$deskripsi')");
        redirect_js("index.php?m=tema");
    }
} else if ($mod == 'tema_ubah') {
    $kode = $_POST['kode_tema'];
    $nama = $_POST['nama_tema'];
    $deskripsi = $_POST['deskripsi'];

    if ($kode == '' || $nama == '')
        print_msg("Field bertanda * tidak boleh kosong!");
    else {
        $db->query("UPDATE tb_tema SET nama_tema='$nama', deskripsi='$deskripsi' WHERE kode_tema='$kode'");
        redirect_js("index.php?m=tema");
    }
} else if ($act == 'tema_hapus') {
    // Set tema_id to NULL for related records before deleting
    $db->query("UPDATE tb_kriteria SET kode_tema=NULL WHERE kode_tema='$_GET[ID]'");
    $db->query("UPDATE tb_alternatif SET kode_tema=NULL WHERE kode_tema='$_GET[ID]'");
    $db->query("DELETE FROM tb_tema WHERE kode_tema='$_GET[ID]'");
    header("location:index.php?m=tema");
}

/** kriteria */
elseif ($mod == 'kriteria_tambah') {
    $kode = $_POST['kode'];
    $nama = $_POST['nama'];
    $kode_tema = $_POST['kode_tema'];
    if ($kode == '' || $nama == '')
        print_msg("Field bertanda * tidak boleh kosong!");
    elseif ($db->get_results("SELECT * FROM tb_kriteria WHERE kode_kriteria='$kode'"))
        print_msg("Kode sudah ada!");
    else {
        $db->query("INSERT INTO tb_kriteria (kode_kriteria, nama_kriteria, kode_tema) VALUES ('$kode', '$nama', '$kode_tema')");
        $db->query("INSERT INTO tb_rel_kriteria(ID1, ID2, nilai) SELECT '$kode', kode_kriteria, 1 FROM tb_kriteria");
        $db->query("INSERT INTO tb_rel_kriteria(ID1, ID2, nilai) SELECT kode_kriteria, '$kode', 1 FROM tb_kriteria WHERE kode_kriteria<>'$kode'");

        $db->query("INSERT INTO tb_rel_alternatif(kode_alternatif, kode_kriteria, kode_sub) 
            SELECT kode_alternatif, '$kode', 0 FROM tb_alternatif");

        redirect_js("index.php?m=kriteria");
    }
} else if ($mod == 'kriteria_ubah') {
    $kode = $_POST['kode'];
    $nama = $_POST['nama'];
    $kode_tema = $_POST['kode_tema'];
    if ($kode == '' || $nama == '')
        print_msg("Field bertanda * tidak boleh kosong!");
    elseif ($db->get_results("SELECT * FROM tb_kriteria WHERE kode_kriteria='$kode' AND kode_kriteria<>'$_GET[ID]'"))
        print_msg("Kode sudah ada!");
    else {
        $db->query("UPDATE tb_kriteria SET kode_kriteria='$kode', nama_kriteria='$nama', kode_tema='$kode_tema' WHERE kode_kriteria='$_GET[ID]'");
        redirect_js("index.php?m=kriteria");
    }
} else if ($act == 'kriteria_hapus') {
    $db->query("DELETE FROM tb_kriteria WHERE kode_kriteria='$_GET[ID]'");
    $db->query("DELETE FROM tb_rel_kriteria WHERE ID1='$_GET[ID]' OR ID2='$_GET[ID]'");
    $db->query("DELETE FROM tb_rel_sub 
        WHERE ID1 IN(SELECT kode_sub FROM tb_sub WHERE kode_kriteria='$_GET[ID]')
        OR ID2 IN(SELECT kode_sub FROM tb_sub WHERE kode_kriteria='$_GET[ID]')");
    $db->query("DELETE FROM tb_rel_alternatif
        WHERE kode_sub IN(SELECT kode_sub FROM tb_sub WHERE kode_kriteria='$_GET[ID]')");
    $db->query("DELETE FROM tb_sub WHERE kode_kriteria='$_GET[ID]'");
    $db->query("DELETE FROM tb_rel_alternatif WHERE kode_kriteria='$_GET[ID]'");
    header("location:index.php?m=kriteria");
}


/** rel_alternatif */
else if ($act == 'rel_alternatif_ubah') {
    foreach ((array) $_POST['nilai'] as $key => $val) {
        $db->query("UPDATE tb_rel_alternatif SET kode_sub='$val' WHERE ID='$key'");
    }
    header("location:index.php?m=rel_alternatif");
}


/** rel_kriteria */
else if ($mod == 'rel_kriteria') {
    $ID1 = $_POST['ID1'];
    $ID2 = $_POST['ID2'];
    $nilai = abs($_POST['nilai']);

    if ($ID1 == $ID2 && $nilai <> 1)
        print_msg("Kriteria yang sama harus bernilai 1.");
    else {
        $db->query("UPDATE tb_rel_kriteria SET nilai=$nilai WHERE ID1='$ID1' AND ID2='$ID2'");
        $db->query("UPDATE tb_rel_kriteria SET nilai=1/$nilai WHERE ID2='$ID1' AND ID1='$ID2'");
        print_msg("Nilai kriteria berhasil diubah.", 'success');
    }
}


/** sub */
elseif ($mod == 'sub_tambah') {
    $kode_kriteria = $_POST['kode_kriteria'];
    $kode = $_POST['kode'];
    $nama = $_POST['nama'];
    if ($kode_kriteria == '' || $kode == '' || $nama == '')
        print_msg("Field bertanda * tidak boleh kosong!");
    elseif ($db->get_results("SELECT * FROM tb_sub WHERE kode_sub='$kode'"))
        print_msg("Kode sudah ada!");
    else {
        $db->query("INSERT INTO tb_sub (kode_sub, kode_kriteria, nama_sub) VALUES ('$kode', '$kode_kriteria', '$nama')");
        $db->query("INSERT INTO tb_rel_sub(ID1, ID2, nilai) SELECT '$kode', kode_sub, 1 FROM tb_sub");
        $db->query("INSERT INTO tb_rel_sub(ID1, ID2, nilai) SELECT kode_sub, '$kode', 1 FROM tb_sub WHERE kode_sub<>'$kode'");

        redirect_js("index.php?m=sub");
    }
} else if ($mod == 'sub_ubah') {
    $kode_kriteria = $_POST['kode_kriteria'];
    $kode = $_POST['kode'];
    $nama = $_POST['nama'];
    if ($kode_kriteria == '' || $kode == '' || $nama == '')
        print_msg("Field bertanda * tidak boleh kosong!");
    elseif ($db->get_results("SELECT * FROM tb_sub WHERE kode_sub='$kode' AND kode_sub<>'$_GET[ID]'"))
        print_msg("Kode sudah ada!");
    else {
        $db->query("UPDATE tb_sub SET kode_sub='$kode', kode_kriteria='$kode_kriteria', nama_sub='$nama' WHERE kode_sub='$_GET[ID]'");
        redirect_js("index.php?m=sub");
    }
} else if ($act == 'sub_hapus') {
    $db->query("DELETE FROM tb_sub WHERE kode_sub='$_GET[ID]'");
    $db->query("DELETE FROM tb_rel_sub WHERE ID1='$_GET[ID]' OR ID2='$_GET[ID]'");
    header("location:index.php?m=sub");
}


/** rel_sub */
else if ($mod == 'rel_sub') {
    $ID1 = $_POST['ID1'];
    $ID2 = $_POST['ID2'];
    $nilai = abs($_POST['nilai']);

    if ($ID1 == $ID2 && $nilai <> 1)
        print_msg("Kriteria yang sama harus bernilai 1.");
    else {
        $db->query("UPDATE tb_rel_sub SET nilai=$nilai WHERE ID1='$ID1' AND ID2='$ID2'");
        $db->query("UPDATE tb_rel_sub SET nilai=1/$nilai WHERE ID2='$ID1' AND ID1='$ID2'");
        print_msg("Nilai sub kriteria berhasil diubah.", 'success');
    }
}
