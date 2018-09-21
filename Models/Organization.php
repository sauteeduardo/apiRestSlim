<?php

class Organization {

    
    public function newOrganization() {
        $request = \Slim\Slim::getInstance()->request();
        $organization = json_decode($request->getBody());
        $sql = "INSERT INTO organization(description, symbol) VALUES (:description,:symbol)";
        try {
            $db = getConnection();
            $stmt = $db->prepare($sql);

            $stmt->bindParam(":description", $organization->description, PDO::PARAM_STR);
            $stmt->bindParam(":symbol", $organization->symbol, PDO::PARAM_STR);

            $stmt->execute();
            $organization->id = $db->lastInsertId();
            $db = null;
            echo '{"type":true, "organization":' . json_encode($organization) . '}';
        } catch (PDOException $e) {
            echo '{"type":false, "data":"' . $e->getMessage() . '"}';
        }
    }

    
    public function updateOrganization($id) {
        $request = \Slim\Slim::getInstance()->request();
        $organization = json_decode($request->getBody());
        $sql = "UPDATE organization SET description=:description,symbol=:symbol WHERE id=:id";
        try {
            $db = getConnection();
            $stmt = $db->prepare($sql);

            $stmt->bindParam(":description", $organization->description, PDO::PARAM_STR);
            $stmt->bindParam(":symbol", $organization->symbol, PDO::PARAM_STR);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);

            $stmt->execute();
            $db = null;
            echo '{"type":true, "organization":' . json_encode($organization) . '}';
        } catch (PDOException $e) {
            echo '{"type":false, "data":"' . $e->getMessage() . '"}';
        }
    }

    
    public function deleteOrganization($id) {
        $sql = "DELETE FROM organization WHERE id=:id";
        try {
            $db = getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam(":id", $id);
            $stmt->execute();
            $db = null;
        } catch (PDOException $e) {
            echo '{"type":false, "data":"' . $e->getMessage() . '"}';
        }
    }

    
    public function getOneOrganization($id) {
        $sql = "SELECT * FROM organization WHERE id=:id";
        try {
            $db = getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam(":id", $id);
            $stmt->execute();
            $organization = $stmt->fetchObject();
            $db = null;
            echo '{"type":true, "organization":' . json_encode($organization) . '}';
        } catch (PDOException $e) {
            echo '{"type": false, "data": "' . $e->getMessage() . '"}';
        }
    }

    public function getAllOrganization() {
        $sql = "SELECT * FROM organization ORDER BY description DESC";
        try {
            $db = getConnection();
            $stmt = $db->query($sql);
            $organization = $stmt->fetchAll(PDO::FETCH_OBJ);
            $db = null;
            echo '{"type":true,"organization":' . json_encode($organization) . '}';
        } catch (PDOException $e) {
            echo '{"type":false,"data": "' . $e->getMessage() . '"}';
        }
    }

}

?>