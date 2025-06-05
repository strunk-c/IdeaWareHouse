<?php declare(strict_types=1);
define('DB_USERNAME', 'root');
define('DB_PASSWORD', 'pass');
define('DSN', 'mysql:host=localhost:3306; dbname=ideastock; charset=utf8mb4');

function Connect(): PDO
{
    $pdo = new PDO(DSN, DB_USERNAME, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    return $pdo;
}

function isUser(string $userId, string $userPw):bool
{
    try {
        //DBに接続する
        $pdo = Connect();
        //クエリの準備
        $sql = "SELECT COUNT(*) AS cnt FROM user WHERE loginId=:userId and password=:userPw";
        //ステートメントの準備
        $statement = $pdo->prepare($sql);
        //値のバインド
        $statement->bindValue(":userId",$userId,PDO::PARAM_STR);
        $statement->bindValue(":userPw",$userPw,PDO::PARAM_STR);
        //実行
        $statement->execute();
        if ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            if($row["cnt"]==1){
                return true;
            }
        }
        return false;
    } catch (PDOException $ex) {
        return false;
    }finally{
        $pdo = null;
    }
}

function getUser(string $userId, string $userPw):array|false
{
    try {
        //DBに接続する
        $pdo = Connect();
        //クエリの準備
        $sql = "SELECT id, name FROM user WHERE loginId=:userId and password=:userPw";
        //ステートメントの準備
        $statement = $pdo->prepare($sql);
        //値のバインド
        $statement->bindValue(":userId",$userId,PDO::PARAM_STR);
        $statement->bindValue(":userPw",$userPw,PDO::PARAM_STR);
        //実行
        $statement->execute();
        if ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            return $row;
        }
        return false;
    } catch (PDOException $ex) {
        return false;
    }finally{
        $pdo = null;
    }
}

function addUser(string $viewName, string $userId, string $userPw):bool
{
    try {
        if(isUser($userId, $userPw)){
            //登録済ユーザー
            return false;
        }
        //DBに接続する
        $pdo = Connect();
        //クエリの準備
        $sql = "INSERT INTO user (loginId, password, name)VALUES(:userId, :userPw, :viewName)";
        //ステートメントの準備
        $statement = $pdo->prepare($sql);
        //値のバインド
        $statement->bindValue(":userId",$userId,PDO::PARAM_STR);
        $statement->bindValue(":userPw",$userPw,PDO::PARAM_STR);
        $statement->bindValue(":viewName",$viewName,PDO::PARAM_STR);
        //実行
        return $statement->execute();
    } catch (PDOException $ex) {
        return false;
    }finally{
        $pdo = null;
    }
}

function getQuestions():array | false
{
    try {
        //DBに接続する
        $pdo = Connect();
        //クエリの準備
        $sql = "SELECT ";
        $sql .= " q.id as questionId ";
        $sql .= ",user.id as userId ";
        $sql .= ",user.name as name";
        $sql .= ",question ";
        $sql .= ",DATE_FORMAT(date,'%Y/%m/%d %H:%i') as date ";
        $sql .= "FROM question as q ";
        $sql .= "LEFT JOIN user as user ";
        $sql .= "ON q.userId = user.id ";
        $sql .= "WHERE q.deleteFlg = false ";
        $sql .= "ORDER BY date asc,q.id ";
        //ステートメントの準備
        $statement = $pdo->prepare($sql);
        //実行
        $statement->execute();
        $datas = array();
        $cnt = 0;
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $datas[$cnt] = $row;
            $cnt++;
        }
        if($cnt == 0){
            return false;
        }
        return $datas;
    } catch (PDOException $ex) {
        return false;
    }finally{
        $pdo = null;
    }
}

function addQuestion(int $userId, string $question):bool{  
    try {
        //DBに接続する
        $pdo = Connect();
        //クエリの準備
        $sql  = "INSERT INTO question (userId,question)VALUES";
        $sql .= "(:userId,:question)";
        //ステートメントの準備
        $statement = $pdo->prepare($sql);
        //値のバインド
        $statement->bindValue(":userId",$userId,PDO::PARAM_INT);
        $statement->bindValue(":question",$question,PDO::PARAM_STR);
        //実行
        if($statement->execute() == 0){
            return false;
        }; 
        return true;
    } catch (PDOException $ex) {
        return false;
    }finally{
        $pdo = null;
    }
}

function deleteQuestion(int $questionId):bool{
    try {
        //DBに接続する
        $pdo = Connect();
        $pdo->beginTransaction();
        //クエリの準備
        $sql  = "UPDATE answer SET deleteFlg=1 ";
        $sql .= "WHERE questionId = :questionId";
        //ステートメントの準備
        $statement = $pdo->prepare($sql);
        //値のバインド
        $statement->bindValue(":questionId",$questionId,PDO::PARAM_INT);
        //実行
        $statement->execute();        
        $sql  = "UPDATE question SET deleteFlg=1 ";
        $sql .= "WHERE id = :questionId";
        //ステートメントの準備
        $statement = $pdo->prepare($sql);
        //値のバインド
        $statement->bindValue(":questionId",$questionId,PDO::PARAM_INT);
        //実行
        if($statement->execute() == 0){
            $pdo->rollBack();
            return false;
        }; 
        $pdo->commit();
        return true;
    } catch (PDOException $ex) {
        $pdo->rollBack();
        return false;
    }finally{
        $pdo = null;
    }
}

function getQuestionById(int $questionId):array | false{
    
    try {
        //DBに接続する
        $pdo = Connect();
        //クエリの準備
        $sql = "SELECT ";
        $sql .= " q.id as questionId ";
        $sql .= ",user.id as userId ";
        $sql .= ",user.name as name";
        $sql .= ",question ";
        $sql .= ",DATE_FORMAT(date,'%Y/%m/%d %H:%i') as date ";
        $sql .= "FROM question as q ";
        $sql .= "LEFT JOIN user as user ";
        $sql .= "ON q.userId = user.id ";
        $sql .= "WHERE q.deleteFlg = false ";
        $sql .= "AND q.id = :questionId ";
        $sql .= "ORDER BY date asc,q.id ";
        //ステートメントの準備
        $statement = $pdo->prepare($sql);
        //値のバインド
        $statement->bindValue(":questionId",$questionId,PDO::PARAM_INT);
        //実行
        $statement->execute();
        if ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            return $row;
        }
        return false;
    } catch (PDOException $ex) {
        return false;
    }finally{
        $pdo = null;
    }
}

function getAnswersByquestionId(int $questionId):array | false{
    
    try {
        //DBに接続する
        $pdo = Connect();
        //クエリの準備
        $sql = "SELECT ";
        $sql .= " ans.questionid as questionId ";
        $sql .= ",ans.id as answerId ";
        $sql .= ",user.id as userId ";
        $sql .= ",user.name as name";
        $sql .= ",answer ";
        $sql .= ",DATE_FORMAT(date,'%Y/%m/%d %H:%i') as date ";
        $sql .= "FROM answer as ans ";
        $sql .= "LEFT JOIN user as user ";
        $sql .= "ON ans.userId = user.id ";
        $sql .= "WHERE ans.deleteFlg = false ";
        $sql .= "AND ans.questionid = :questionId ";
        $sql .= "ORDER BY date asc,ans.id ";
        //ステートメントの準備
        $statement = $pdo->prepare($sql);
        //値のバインド
        $statement->bindValue(":questionId",$questionId,PDO::PARAM_INT);
        //実行
        $statement->execute();        
        $datas = array();
        $cnt = 0;
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $datas[$cnt] = $row;
            $cnt++;
        }
        if($cnt == 0){
            return false;
        }
        return $datas;
    } catch (PDOException $ex) {
        return false;
    }finally{
        $pdo = null;
    }
}

function addAnswer(int $userId, int $questionId, string $answer):bool{  
    try {
        //DBに接続する
        $pdo = Connect();
        //クエリの準備
        $sql  = "INSERT INTO answer (userId,questionId,answer)VALUES";
        $sql .= "(:userId,:questionId,:answer)";
        //ステートメントの準備
        $statement = $pdo->prepare($sql);
        //値のバインド
        $statement->bindValue(":userId",$userId,PDO::PARAM_INT);
        $statement->bindValue(":questionId",$questionId,PDO::PARAM_INT);
        $statement->bindValue(":answer",$answer,PDO::PARAM_STR);
        //実行
        if($statement->execute() == 0){
            return false;
        }; 
        return true;
    } catch (PDOException $ex) {
        return false;
    }finally{
        $pdo = null;
    }
}

function deleteAnswer(int $answerId):bool{    
    try {
        //DBに接続する
        $pdo = Connect();
        //クエリの準備
        $sql  = "UPDATE answer SET deleteFlg=1 ";
        $sql .= "WHERE id = :answerId";
        //ステートメントの準備
        $statement = $pdo->prepare($sql);
        //値のバインド
        $statement->bindValue(":answerId",$answerId,PDO::PARAM_INT);
        //実行
        return $statement->execute();
    } catch (PDOException $ex) {
        return false;
    }finally{
        $pdo = null;
    }
}
