<?php
class DB
{
    var $conn = null;
    public $insert_id = 0;
    
    public function __construct($host = null, $username = null, $passwd = null, $dbname = null)
    {   
        global $config;
        
        // Menggunakan konfigurasi cloud jika parameter host kosong
        if ($host === null) {
            $config_active = $config;
        } else {
            $config_active = array(
                'server' => $host,
                'username' => $username,
                'password' => $passwd,
                'database_name' => $dbname
            );
        }
        
        $this->conn = mysqli_connect(
            $config_active['server'], 
            $config_active['username'], 
            $config_active['password'], 
            $config_active['database_name']
        );
        
        if (!$this->conn) {
            die('Koneksi Database Gagal: ' . mysqli_connect_error());
        }

    }

    public function query($sql)
    {
        $query = mysqli_query($this->conn, $sql) or die('<pre>Error mysqli_query: ' . mysqli_error($this->conn) . '<br />' . $sql . '</pre>');

        if (preg_match("/^(insert|replace)\s+/i", $sql)) {
            $this->insert_id = @$this->conn->insert_id;
        }

        return $query;
    }

    public function get_row($sql)
    {
        $query = $this->query($sql);
        return mysqli_fetch_object($query);
    }

    public function get_results($sql)
    {
        $query = $this->query($sql);
        $arr = array();
        while ($row = mysqli_fetch_object($query)) {
            $arr[] = $row;
        }
        return $arr;
    }

    public function get_var($sql)
    {
        $query = $this->query($sql);
        $row = mysqli_fetch_row($query);
        return $row[0];
    }
}
