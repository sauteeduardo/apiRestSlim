<?php

class Log {

    public function newLog() {

        $request = \Slim\Slim::getInstance()->request();
        $log = json_decode($request->getBody());
        $sql = "INSERT INTO log(module, action, description, user, user) VALUES (:module, :action, :description, :user, :user)";

        try {
            $db = getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam(":module", $log->module, PDO::PARAM_STR);
            $stmt->bindParam(":action", $log->action, PDO::PARAM_STR);
            $stmt->bindParam(":description", $log->description, PDO::PARAM_STR);
            $stmt->bindParam(":user", $log->user, PDO::PARAM_INT);
            $stmt->bindParam(":user", $log->user, PDO::PARAM_INT);
            $stmt->execute();
            $log->id = $db->lastInsertId();
            $db = null;
            echo '{"type":true, "log":' . json_encode($log) . '}';
        } catch (PDOException $e) {
            echo '{"type":false, "data":"' . $e->getMessage() . '"}';
        }
    }

    
    public function getAllLogOfUserByModule($user, $module) {
        $sql = "SELECT l.module, l.action ,l.description, l.date , u.username AS user, o.description AS organization
                FROM log l
                INNER JOIN user u ON l.user = u.id
                INNER JOIN organization o ON o.id = l.organization
                WHERE u.id =:user
                and l.module =:module
                ORDER BY l.date DESC;";
        try {
            $db = getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam(":user", $user);
            $stmt->bindParam(":module", $module);
            $stmt->execute();
            $log = $stmt->fetchAll(PDO::FETCH_OBJ);
            $db = null;
            echo '{"type":true, "log":' . json_encode($log) . '}';
        } catch (PDOException $e) {
            echo '{"type":false, "data":"' . $e->getMessage() . '"}';
        }
    }

}

?>