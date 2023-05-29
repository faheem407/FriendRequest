<?php
    require("config.php");
    class DB {
        public function __construct() {
            // Create a new instance and connect to the database
            $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            if ($this->conn->connect_error) {
                die("Connection failed: " . $this->conn->connect_error);
            }
        }
        public function get_results($query) {
            // Get results of select query in proper format
            $result = $this->conn->query($query);
            if ($result->num_rows > 0) {
                return $result->fetch_all();
            }
            else {
                return FALSE;
            }
        }
        public function __destruct() {
            $this->conn->close();
        }
    }
?>