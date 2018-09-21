<?php

class User {

    public function newUser() {

        $request = \Slim\Slim::getInstance()->request();
        $user = json_decode($request->getBody());
        $sql = "INSERT INTO user(username, email, login, password, access_levels, organization) VALUES (:username, :email, :login, :password, :access_levels, :organization)";

        try {
            $db = getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam(":username", $user->username, PDO::PARAM_STR);
            $stmt->bindParam(":email", $user->email, PDO::PARAM_STR);
            $stmt->bindParam(":login", $user->login, PDO::PARAM_STR);
            $stmt->bindParam(":password", md5($user->password), PDO::PARAM_STR);
            $stmt->bindParam(":access_levels", $user->access_levels, PDO::PARAM_STR);
            $stmt->bindParam(":organization", $user->organization, PDO::PARAM_STR);
            $stmt->execute();
            $user->id = $db->lastInsertId();
            $db = null;
            echo '{"type":true, "user":' . json_encode($user) . '}';
        } catch (PDOException $e) {
            echo '{"type":false, "data":"' . $e->getMessage() . '"}';
        }
    }


    public function updateUser($id) {
        $request = \Slim\Slim::getInstance()->request();
        $user = json_decode($request->getBody());
        $sql = "UPDATE user SET username=:username, email=:email, login=:login, password=:password, access_levels=:access_levels, organization=:organization WHERE id=:id";
        try {
            $db = getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam(":username", $user->username, PDO::PARAM_STR);
            $stmt->bindParam(":email", $user->email, PDO::PARAM_STR);
            $stmt->bindParam(":login", $user->login, PDO::PARAM_STR);
            $stmt->bindParam(":password", md5($user->password), PDO::PARAM_STR);
            $stmt->bindParam(":access_levels", $user->access_levels, PDO::PARAM_STR);
            $stmt->bindParam(":organization", $user->organization, PDO::PARAM_STR);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            $stmt->execute();
            $db = null;
            echo '{"type":true, "user":' . json_encode($user) . '}';
        } catch (PDOException $e) {
            echo '{"type":false, "data":"' . $e->getMessage() . '"}';
        }
    }

    public function deleteUser($id) {

        $sql = "DELETE FROM user WHERE id=:id";
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

    public function getOneUser($id) {
        $sql = "SELECT u.id, u.username, u.email, u.login, al.description AS access_levels, o.description AS organization,o.id as organization_id FROM user u INNER JOIN organization o ON o.id = u.organization INNER JOIN access_levels al ON al.idaccess_levels = u.access_levels WHERE u.id=:id";
        try {
            $db = getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam(":id", $id);
            $stmt->execute();
            $user = $stmt->fetchObject();
            $db = null;
            echo '{"type":true, "user":' . json_encode($user) . '}';
        } catch (PDOException $e) {
            echo '{"type":false, "data":"' . $e->getMessage() . '"}';
        }
    }

    public function getAllUser() {
        $sql = "SELECT u.id, u.username, u.email, u.login, al.description AS access_levels, o.description AS organization FROM user u INNER JOIN organization o ON o.id = u.organization INNER JOIN access_levels al ON al.idaccess_levels = u.access_levels ORDER BY u.username DESC";
        try {
            $db = getConnection();
            $stmt = $db->query($sql);
            $user = $stmt->fetchAll(PDO::FETCH_OBJ);
            $db = null;
            echo '{"type":true, "user":' . json_encode($user) . '}';
        } catch (PDOException $e) {
            echo '{"type":false, "data":"' . $e->getMessage() . '"}';
        }
    }

    public function getAllUserOfOrganization($organization) {

        $sql = "SELECT u.id, u.username, u.email, u.login, al.description AS access_levels, o.description AS organization
                FROM user u
                INNER JOIN access_levels al ON al.idaccess_levels = u.access_levels
                INNER JOIN organization o ON o.id = u.organization
                WHERE o.id = :organization 
                ORDER BY u.id;";
        try {
            $db = getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam(":organization", $organization);
            $stmt->execute();
            $user = $stmt->fetchAll(PDO::FETCH_OBJ);
            $db = null;
            echo '{"type":true, "user":' . json_encode($user) . '}';
        } catch (PDOException $e) {
            echo '{"type":false, "data":"' . $e->getMessage() . '"}';
        }
    }

    
}

?>