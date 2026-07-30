<?php
/**
 * Database Connection Singleton Class
 * Uses PDO for MySQL Database operations in XAMPP
 */

class Database {
    private $host = "localhost";
    private $db_name = "school_db";
    private $username = "root";
    private $password = "";
    private $conn = null;

    /**
     * Get Database Connection Instance
     * @return PDO|null
     */
    public function getConnection() {
        if ($this->conn === null) {
            try {
                $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4";
                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ];
                $this->conn = new PDO($dsn, $this->username, $this->password, $options);
            } catch (PDOException $exception) {
                // Return null if connection fails so the app can handle fallback gracefully
                $this->conn = null;
            }
        }
        return $this->conn;
    }
}
?>
