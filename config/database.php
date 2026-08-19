<?php
/**
 * Databázové Pripojenie
 * Singleton pattern - jediná instancia PDO pripojenia
 */

namespace App\Config;

use PDO;
use PDOException;

class Database {
    private static $instance = null;
    private $connection;
    
    private function __construct() {
        try {
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
            
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            
            $this->connection = new PDO($dsn, DB_USER, DB_PASS, $options);
            
            if (APP_DEBUG) {
                error_log('✅ Database connected successfully');
            }
        } catch (PDOException $e) {
            if (APP_DEBUG) {
                die('❌ Database Connection Error: ' . $e->getMessage());
            } else {
                die('❌ Database Connection Error. Please try again later.');
            }
        }
    }
    
    /**
     * Získaj singleton instanciu databázy
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Vráť PDO pripojenie
     */
    public function getConnection() {
        return $this->connection;
    }
    
    /**
     * Vykonaj prepare statement
     */
    public function prepare($sql) {
        return $this->connection->prepare($sql);
    }
    
    /**
     * Vykonaj query bez parametrov
     */
    public function query($sql) {
        return $this->connection->query($sql);
    }
    
    /**
     * Zisti ID posledne vloženého záznamu
     */
    public function lastInsertId() {
        return $this->connection->lastInsertId();
    }
    
    /**
     * Začni transakciu
     */
    public function beginTransaction() {
        return $this->connection->beginTransaction();
    }
    
    /**
     * Potvrď transakciu
     */
    public function commit() {
        return $this->connection->commit();
    }
    
    /**
     * Vrátenie transakcie
     */
    public function rollBack() {
        return $this->connection->rollBack();
    }
    
    // Zabrániť kloningu
    private function __clone() {}
    private function __wakeup() {}
}
