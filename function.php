<?php
function Connect(): PDO{
    $pdo = new PDO("mysql:host=127.0.0.1:3306;dbname=ideastockC;charset=utf8mb4", "root", "pass");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    return $pdo;
}
?>

<?php
function logout(){
    $_SESSION = array();
//クッキーに登録されているセッションidの情報を削除
    if (ini_get("session.use_cookies")) {
    setcookie(session_name(), '', time() - 42000, '/');
    }
//セッションを破棄
    session_destroy();
    header("Location:./questions.php",true,307);
    exit();
    }
?>

<?php
function add(){
    if(isset($_POST['viewName'])) {
        $pdo = Connect();

        $hp = $_POST["userPw"];
        $hash = password_hash($hp, PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare('INSERT INTO user (loginId, password, name) VALUES(:userId,:userPw,:viewName)');
        $stmt->bindValue(':userId', $_POST['userId'], PDO::PARAM_STR);
        $stmt->bindValue(':userPw', $hash, PDO::PARAM_STR);
        $stmt->bindValue(':viewName', $_POST['viewName'], PDO::PARAM_STR);
        $stmt->execute();
        }
    }
?>

<?php
function getQ(){
        try{
            $pdo = Connect();
            $sql = 'SELECT * FROM question ORDER BY date DESC';
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }catch(PDOException $ex){
            return false;
        }
    }
?>

<?php
function getQid($userId) {
    try{
        $pdo = Connect();
        $sql = "SELECT * FROM question WHERE userId = :userId";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(":userId", $userId, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }catch(PDOException $ex){
        return false;
    }
}
?>

<?php
function addQ(){
    if(isset($_POST['question'])){
        if(isset($_POST['token']) && $_POST['token'] === $_SESSION['token']){
            unset($_SESSION['csrfErrAddQ']);
            $pdo = Connect();
            $userId = $_SESSION['userId'];
            $stmt = $pdo->prepare('INSERT INTO question (userId,question) VALUES(:userId,:question)');
            //$stmt->bindParam(':id',  $_POST['question']);
            $stmt->bindValue(':userId', $userId);
            $stmt->bindValue(':question', $_POST['question']);
            //$stmt->bindParam(':date',  $_POST['question']);
            //$stmt->bindParam(':deleteFlg', $_POST['question']);
            $stmt->execute();
    }else{
        $_SESSION['csrfErrAddQ'] = "投稿失敗だよ。不正ダメだよ。ErrorCode:CSRF";
    }
    }
}
?>

<?php
function addA(){
    if(isset($_POST['answer'])){
            $pdo = Connect();
            $userId = $_SESSION['userId'];
            $stmt = $pdo->prepare('INSERT INTO answer (userId,questionId,answer) VALUES(:userId,:questionId,:answer)');
            //$stmt->bindParam(':id',  $_POST['question']);
            $stmt->bindValue(':userId', $userId);
            $stmt->bindValue(':questionId',$_POST['questionId']);
            $stmt->bindValue(':answer', $_POST['answer']);
            //$stmt->bindParam(':date',  $_POST['question']);
            //$stmt->bindParam(':deleteFlg', $_POST['question']);
            $stmt->execute();
    }
}
?>

<?php
//XSS対策
function h($s){
    return htmlspecialchars($s, ENT_QUOTES, "UTF-8");
}

//セッションにトークンセット
function setToken(){
    $token = sha1(uniqid(mt_rand(), true));
    $_SESSION['token'] = $token;
}

//セッション変数のトークンとPOSTされたトークンをチェック
function checkToken(){
    if(empty($_SESSION['token']) || ($_SESSION['token'] != $_POST['token'])){
        echo 'Invalid POST', PHP_EOL;
        exit;
    }
}

//POSTされたユーザーIDのバリデーション
function valiId($loginId){
    $err = "";
if(empty($loginId)) {
    $err = "ユーザーIDを入力してください。";
}else if(mb_strlen($loginId) > 10) {
    $err = "10文字以内で入力してください。";
}
    return $err;
}

//パスワードチェック（正規表現）
function valiPw($userPw){
    $err = "";
if(empty($userPw)){
    $err  = "パスワードを入力してください。";
}else if(!preg_match('/\A[a-z\d]{3,10}+\z/i',$datas["userPw"])){
    $err = "3文字以上で入力してください。";
}
    return $errors;
}
?>