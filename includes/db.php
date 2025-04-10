<?php
class DB
{
    var $conn = null;
    public $insert_id = 0;

    public function __construct($host, $username, $passwd, $dbname)
    {   
        #$this->conn = mysqli_connect($host, $username, $passwd, $dbname);
        global $config;
        if (isset($config['ssl'])) {
            mysqli_ssl_set($this->conn, NULL, NULL, $config['ssl']['ca'], NULL, NULL);
        }
        $port = isset($config['port']) ? $config['port'] : 3306;
        $this->conn = mysqli_connect($host, $username, $passwd, $dbname, $port);
        if (isset($config['ssl']) && $config['ssl']['verify_server_cert']) {
            mysqli_options($this->conn, MYSQLI_OPT_SSL_VERIFY_SERVER_CERT, true);
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
