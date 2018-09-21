<?php

class AccessLevels{

    public function getOneAccessLevels($id) {
        $sql = "SELECT idaccess_levels,description AS description, code FROM access_levels WHERE idaccess_levels=:id";
        try {
            $db = getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam(":id", $id);
            $stmt->execute();
            $accessLevels = $stmt->fetchObject();
            $db = null;
            echo '{"type":true,"access_levels": ' . json_encode($accessLevels) . '}';
        } catch (PDOException $e) {
            echo '{"type": false,"data": "'.$e->getMessage().'"}';
        }
    }

    public function getAllAccessLevels() {
        $sql = "SELECT idaccess_levels,description AS description, code FROM access_levels";
        try {
            $db = getConnection();
            $stmt = $db->query($sql);
            $accessLevels = $stmt->fetchAll(PDO::FETCH_OBJ);
            $db = null;
            echo '{"type":true,"access_levels": ' . json_encode($accessLevels) . '}';
        } catch (PDOException $e) {
            echo '{"type":false,"data":"'.$e->getMessage().'"}';
        }
    }
    
    public function getAllAccessLevelsNotRoot() {
        $sql = "SELECT idaccess_levels,description AS description,code FROM access_levels WHERE idaccess_levels <> 1";
        try {
            $db = getConnection();
            $stmt = $db->query($sql);
            $accessLevels = $stmt->fetchAll(PDO::FETCH_OBJ);
            $db = null;
            echo '{"type":true,"access_levels": ' . json_encode($accessLevels) . '}';
        } catch (PDOException $e) {
            echo '{"type":false,"data":"'.$e->getMessage().'"}';
        }
    }
    
}


?>