<?php
error_reporting(~E_NOTICE);
session_start();

include 'config.php';
include 'includes/db.php';
// Inisialisasi koneksi database menggunakan konfigurasi lokal
$db = new DB();
include 'includes/general.php';
include 'includes/paging.php';

function input_get($key)
{
    if (isset($_GET[$key]))
        return $_GET[$key];
}
function input_post($key)
{
    if (isset($_POST[$key]))
        return $_POST[$key];
}
$mod = isset($_GET['m']) ? $_GET['m'] : '';
$act = isset($_GET['act']) ? $_GET['act'] : '';

$nRI = array(
    1 => 0,
    2 => 0,
    3 => 0.58,
    4 => 0.9,
    5 => 1.12,
    6 => 1.24,
    7 => 1.32,
    8 => 1.41,
    9 => 1.46,
    10 => 1.49
);

$rows = $db->get_results("SELECT kode_alternatif, nama_alternatif FROM tb_alternatif ORDER BY kode_alternatif");
foreach ($rows as $row) {
    $ALTERNATIF[$row->kode_alternatif] = $row->nama_alternatif;
}

$rows = $db->get_results("SELECT kode_kriteria, nama_kriteria FROM tb_kriteria ORDER BY kode_kriteria");
foreach ($rows as $row) {
    $KRITERIA[$row->kode_kriteria] = $row->nama_kriteria;
}

$rows = $db->get_results("SELECT * FROM tb_sub ORDER BY kode_sub");
foreach ($rows as $row) {
    $SUB[$row->kode_sub] = array(
        'nama' => $row->nama_sub,
        'kode_kriteria' => $row->kode_kriteria,
        'nilai_sub' => $row->nilai_sub,
    );
}

function get_kriteria_option($selected = '', $tema = '')
{
    global $db;
    $where = "";
    if($tema) {
        $where = "WHERE k.kode_tema='$tema'";
    }
    $rows = $db->get_results("SELECT k.kode_kriteria, k.nama_kriteria, t.nama_tema 
        FROM tb_kriteria k 
        LEFT JOIN tb_tema t ON t.kode_tema=k.kode_tema 
        $where
        ORDER BY k.kode_kriteria");
    $a = '';
    foreach ($rows as $row) {
        if ($row->kode_kriteria == $selected)
            $a .= "<option value='$row->kode_kriteria' selected>$row->kode_kriteria - $row->nama_kriteria ($row->nama_tema)</option>";
        else
            $a .= "<option value='$row->kode_kriteria'>$row->kode_kriteria - $row->nama_kriteria ($row->nama_tema)</option>";
    }
    return $a;
}
function get_alternatif_option($selected = '')
{
    global $db;
    $rows = $db->get_results("SELECT kode_alternatif, nama_alternatif FROM tb_alternatif ORDER BY kode_alternatif");
    $a = '';
    foreach ($rows as $row) {
        if ($row->kode_alternatif == $selected)
            $a .= "<option value='$row->kode_alternatif' selected>$row->kode_alternatif - $row->nama_alternatif</option>";
        else
            $a .= "<option value='$row->kode_alternatif'>$row->kode_alternatif - $row->nama_alternatif</option>";
    }
    return $a;
}
function get_nilai_option($selected = '')
{
    $nilai = array(
        '1' => 'Sama penting dengan',
        '2' => 'Nilai tengah diantara dua nilai pertimbangan yang berdekatan',
        '3' => 'Sedikit lebih penting dari',
        '4' => 'Nilai tengah diantara dua nilai pertimbangan yang berdekatan',
        '5' => 'Cukup penting dari',
        '6' => 'Nilai tengah diantara dua nilai pertimbangan yang berdekatan',
        '7' => 'Sangat penting dari',
        '8' => 'Nilai tengah diantara dua nilai pertimbangan yang berdekatan',
        '9' => 'Mutlak sangat penting dari',
    );
    $a = '';
    foreach ($nilai as $key => $value) {
        if ($selected == $key)
            $a .= "<option value='$key' selected>$key - $value</option>";
        else
            $a .= "<option value='$key'>$key - $value</option>";
    }
    return $a;
}
function get_sub_option($kode_kriteria, $selected = '')
{
    global $db;
    $where = "WHERE kode_kriteria='$kode_kriteria'";
    $rows = $db->get_results("SELECT kode_sub, nama_sub FROM tb_sub $where ORDER BY kode_sub");
    $a = '';
    foreach ($rows as $row) {
        if ($row->kode_sub == $selected)
            $a .= "<option value='$row->kode_sub' selected>$row->kode_sub - $row->nama_sub</option>";
        else
            $a .= "<option value='$row->kode_sub'>$row->kode_sub - $row->nama_sub</option>";
    }
    return $a;
}

/**
 * Mengambil matriks perbandingan berpasangan antar kriteria
 * Data diambil dari tabel tb_rel_kriteria yang berisi nilai perbandingan
 * Nilai perbandingan menunjukkan tingkat kepentingan antara kriteria i dan j
 * @param string $tema ID tema yang dipilih
 * @return array Matriks perbandingan berpasangan kriteria
 */
function get_relkriteria($tema = null) {
    global $db;
    $rows = $db->get_results("SELECT k.kode_kriteria, k2.kode_kriteria AS kode_kriteria2, nilai 
        FROM tb_kriteria k 
        INNER JOIN tb_rel_kriteria r ON r.ID1=k.kode_kriteria
        INNER JOIN tb_kriteria k2 ON k2.kode_kriteria=r.ID2
        WHERE k.kode_tema='$tema' AND k2.kode_tema=k.kode_tema
        ORDER BY k.kode_kriteria, k2.kode_kriteria");
    $matriks = array();
    foreach($rows as $row){
        $matriks[$row->kode_kriteria][$row->kode_kriteria2] = $row->nilai;
    }
    return $matriks;
}

function get_rel_alternatif($kriteria = '')
{
    global $db;
    $tema = isset($_GET['tema']) ? $_GET['tema'] : '';
    $q = isset($_GET['q']) ? esc_field($_GET['q']) : '';
    $rows = $db->get_results("SELECT
       a.kode_alternatif, ra.kode_kriteria, s.kode_sub                	            
       FROM tb_rel_alternatif ra 
       INNER JOIN tb_alternatif a ON a.kode_alternatif = ra.kode_alternatif
       INNER JOIN tb_kriteria k ON k.kode_kriteria = ra.kode_kriteria
       LEFT JOIN tb_sub s ON s.kode_sub=ra.kode_sub
       WHERE a.kode_tema='$tema' AND k.kode_tema='$tema' AND nama_alternatif LIKE '%$q%'
       ORDER BY kode_alternatif, ra.kode_kriteria");
    $arr = array();
    if ($rows) {
        foreach ($rows as $row) {
            if (!isset($arr[$row->kode_alternatif])) {
                $arr[$row->kode_alternatif] = array();
            }
            $arr[$row->kode_alternatif][$row->kode_kriteria] = $row->kode_sub;
        }
    }
    return $arr;
}


/**
 * Menghitung jumlah kolom dari matriks perbandingan berpasangan
 * Rumus: Σaij, dimana i=1,2,...,n
 * Hasil penjumlahan digunakan untuk normalisasi matriks
 * @param array $matriks Matriks perbandingan berpasangan
 * @return array Total nilai per kolom
 */
function get_baris_total($matriks = array())
{
    $total = array();
    // Initialize total array with zeros for all possible keys
    foreach ($matriks as $value) {
        foreach ($value as $k => $v) {
            if (!isset($total[$k])) {
                $total[$k] = 0;
            }
        }
    }
    // Calculate the sum
    foreach ($matriks as $value) {
        foreach ($value as $k => $v) {
            $total[$k] += $v;
        }
    }
    return $total;
}

/**
 * Normalisasi matriks perbandingan berpasangan
 * Rumus: aij / Σaij, dimana i=1,2,...,n
 * Setiap elemen dibagi dengan jumlah kolom masing-masing
 * @param array $matriks Matriks perbandingan berpasangan
 * @param array $total Jumlah nilai per kolom
 * @return array Matriks ternormalisasi
 */
function normalize($matriks = array(), $total = array())
{
    foreach ($matriks as $key => $value) {
        foreach ($value as $k => $v) {
            $matriks[$key][$k] = $matriks[$key][$k] / $total[$k];
        }
    }
    return $matriks;
}

/**
 * Menghitung nilai prioritas/bobot kriteria
 * Rumus: Σ(aij/Σaij)/n, dimana i,j=1,2,...,n
 * Rata-rata baris dari matriks ternormalisasi
 * @param array $normal Matriks ternormalisasi
 * @return array Nilai prioritas/bobot masing-masing kriteria
 */
function get_rata($normal)
{
    $rata = array();
    foreach ($normal as $key => $value) {
        $rata[$key] = array_sum($value) / count($value);
    }
    return $rata;
}

/**
 * Menghitung perkalian matriks dengan vektor prioritas
 * Digunakan untuk menghitung rasio konsistensi
 * Rumus: aij * wj, dimana i,j=1,2,...,n
 * @param array $matriks Matriks perbandingan berpasangan
 * @param array $rata Vektor prioritas/bobot kriteria
 * @return array Hasil perkalian matriks dengan vektor prioritas
 */
function mmult($matriks = array(), $rata = array())
{
    $arr = array();
    foreach ($matriks as $key => $val) {
        foreach ($val as $k => $v) {
            $arr[$key][$k] = $v * $rata[$k];
        }
    }
    return $arr;
}

function consistency_measure($matriks, $rata)
{
    $matriks = mmult($matriks, $rata);
    $data = array();
    foreach ($matriks as $key => $value) {
        $data[$key] = array_sum($value) / $rata[$key];
    }
    return $data;
}

function get_rank($array)
{
    $data = $array;
    arsort($data);
    $no = 1;
    $new = array();
    foreach ($data as $key => $value) {
        $new[$key] = $no++;
    }
    return $new;
}

function FAHP_save($total = array())
{
    global $db;

    arsort($total);
    $no = 1;
    foreach ($total as $key => $val) {
        $db->query("UPDATE tb_alternatif SET total='$val', rank='$no' WHERE kode_alternatif='$key'");
        $no++;
    }
}

function set_value($key = null, $default = null)
{
    global $_POST;
    if (isset($_POST[$key]))
        return $_POST[$key];

    if (isset($_GET[$key]))
        return $_GET[$key];

    return $default;
}

function kode_oto($field, $table, $prefix, $length)
{
    global $db;
    $var = (string) $db->get_var("SELECT $field FROM $table WHERE $field REGEXP '{$prefix}[0-9]{{$length}}' ORDER BY $field DESC");
    if ($var) {
        return $prefix . substr(str_repeat('0', $length) . ((int) substr($var, -$length) + 1), -$length);
    } else {
        return $prefix . str_repeat('0', $length - 1) . 1;
    }
}

function get_level_option($selected = '')
{
    $arr = array(
        'admin' => 'Admin',
        'user' => 'User',
    );
    $a = '';
    foreach ($arr as $key => $val) {
        if ($selected == $key)
            $a .= "<option value='$key' selected>$val</option>";
        else
            $a .= "<option value='$key'>$val</option>";
    }
    return $a;
}
