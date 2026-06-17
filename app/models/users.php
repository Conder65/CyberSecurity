<?php

class Users {
    private $db;
    public function __construct($db) {
        $this->db = $db;
    }
    public function getUserbyId($id) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

}