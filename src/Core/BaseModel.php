<?php
/**
 * Základná Trieda Pre Modely
 * CRUD Operácie a Databázové Metódy
 */

namespace App\Core;

use App\Config\Database;
use PDO;

abstract class BaseModel {
    protected $table = '';
    protected $primary_key = 'id';
    protected $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Zisti všetky záznamy
     */
    public function all() {
        $sql = "SELECT * FROM {$this->table}";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Zisti záznam podľa ID
     */
    public function find($id) {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primary_key} = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Zisti záznam podľa podmienky
     */
    public function findBy($column, $value) {
        $sql = "SELECT * FROM {$this->table} WHERE {$column} = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$value]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Zisti všetky záznamy podľa podmienky
     */
    public function findAllBy($column, $value) {
        $sql = "SELECT * FROM {$this->table} WHERE {$column} = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$value]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Uloženie záznamu (INSERT alebo UPDATE)
     */
    public function save($data) {
        if (isset($data[$this->primary_key]) && $data[$this->primary_key]) {
            return $this->update($data[$this->primary_key], $data);
        } else {
            return $this->insert($data);
        }
    }
    
    /**
     * Vloženie nového záznamu
     */
    public function insert($data) {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        
        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
        $stmt = $this->db->prepare($sql);
        
        if ($stmt->execute(array_values($data))) {
            return Database::getInstance()->lastInsertId();
        }
        return false;
    }
    
    /**
     * Aktualizácia záznamu
     */
    public function update($id, $data) {
        // Odstráň ID z dát ak je tam
        unset($data[$this->primary_key]);
        
        $set = implode(', ', array_map(function($key) {
            return "{$key} = ?";
        }, array_keys($data)));
        
        $values = array_values($data);
        $values[] = $id;
        
        $sql = "UPDATE {$this->table} SET {$set} WHERE {$this->primary_key} = ?";
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute($values);
    }
    
    /**
     * Zmazanie záznamu
     */
    public function delete($id) {
        $sql = "DELETE FROM {$this->table} WHERE {$this->primary_key} = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
    
    /**
     * Vlastný SQL query
     */
    public function query($sql, $params = []) {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
    
    /**
     * Počítaj záznamy
     */
    public function count($where = null, $params = []) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table}";
        
        if ($where) {
            $sql .= " WHERE {$where}";
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['count'] ?? 0;
    }
    
    /**
     * Overená existencia záznamu
     */
    public function exists($column, $value) {
        return $this->count("{$column} = ?", [$value]) > 0;
    }
}
