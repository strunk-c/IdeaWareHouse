<?php
    declare(strict_types=1); 
    require_once dirname(__FILE__)."/function.php"; 
    include dirname(__FILE__)."/header.html";
    session_start();
?>

<?php
//ログイン確認
    if(!isset($_SESSION['userId'])){
        header("Location:./questions.php",true,307);
        exit();
    }
?>

<?php
    if(isset($_SESSION['userId'])){
        $msg = "（".$_SESSION['userId']."）";
        $msg = h($msg);
    }
        if($_SERVER["REQUEST_METHOD"] == "POST"){
        header("Location:./questions.php",true,307);
    }
?>

<?php
    if(isset($_POST['question'])){
        if(isset($_POST['token']) && $_POST['token'] === $_SESSION['tokenSub']){
            unset($_SESSION['csrfErrAddQ']);
            $pdo = Connect();
            $userId = $_SESSION['userId'];
            $stmt = $pdo->prepare('INSERT INTO question (userId,question) VALUES(:userId,:question)');
            $stmt->bindValue(':userId', $userId, PDO::PARAM_STR);
            $stmt->bindValue(':question', $_POST['question'], PDO::PARAM_STR);
            $stmt->execute();
    }else{
        $_SESSION['csrfErrAddQ'] = "投稿失敗だよ。不正ダメだよ。ErrorCode:CSRF";
    }
}
?>

<?php
    if(isset($_POST['questionDelete'])){
        if(isset($_POST['token']) && $_POST['token'] === $_SESSION['token']){
        unset($_SESSION['csrfErrQ']);
            $pdo = Connect();
            $del = $pdo->prepare("UPDATE question SET deleteFlg = :dF WHERE id = :id");
            $del->bindValue(':id', $_POST['questionId'], PDO::PARAM_INT);
            $del->bindValue(':dF', '1', PDO::PARAM_INT);
            $del->execute();
            header("Location:./questions.php",true,307);
    }else{
        $_SESSION['csrfErrQ'] = "削除失敗だよ。不正ダメだよ。ErrorCode:CSRF";
    }
}
?>

<?php
    if(isset($_POST['answerDelete'])){
        if(isset($_POST['token']) && $_POST['token'] === $_SESSION['token']){
        unset($_SESSION['csrfErrA']);
            $pdo = Connect();
            $del = $pdo->prepare("UPDATE answer SET deleteFlg = :dF WHERE id = :id");
            $del->bindValue(':id', $_POST['answerId'], PDO::PARAM_INT);
            $del->bindValue(':dF', '1', PDO::PARAM_INT);
            $del->execute();
            header("Location:./detail.php",true,307);
    }else{
        $_SESSION['csrfErrA'] = "削除失敗だよ。不正ダメだよ。ErrorCode:CSRF";
    }
}
?>

<style>
        header , footer , .wrapper {
            background-color: white;        
        }

        footer {
            color: black;
            display: block; 
            text-align: right;
        }

        #logo {
            width: 75%;
            height: 75%;
        }
        
        #main h1{
            margin: 8.8em 0 -2em 32em;
        }

        .btn-square {
            background: #9cdf9c;/*ボタン色*/
            color: black;
        }
        
        .question {
            position: relative;
            padding:0.8em 1em;
            margin:50px auto 20px;
            background-color: #e8e8e8;
            border-radius:32px;
            width: 500px;
        }
        .question:before,.question:after{ 
            content:'';
            width: 20px;
            height: 30px;
            position: absolute;
            display: inline-block;
        }
       
        .question p {
            margin: 5px 0; 
            padding: 0;
        }
        textarea{
            width: -webkit-fill-available;
            height: 150px;
            border-radius:8px;
        resize: none;
        }

        .btns {
            flex-flow: column;
            text-align: right;
            padding: 0.16em;
        }
     
        /* ログインボタン */
        .btn {
        /*background: #9cdf9c;/*ボタン色*/
        /*color: black;*/
        display: inline-block;
        padding: 1em 3em;
        text-decoration: none;
        background: #9cc3ec;/*ボタン色*/
        color: #FFF;
        border: none;
        border-radius: 32px;

    }
        /* 登録ボタン */
        .btn-square {
        display: inline-block;
        padding: 0.5em 1em;
        text-decoration: none;
        background: #9cdf9c;/*ボタン色*/
        color: #FFF;
        border: none;
        border-radius: 16px;
        }
        /* 戻るボタン */
        .btn-squares {
        display: inline-block;
        padding: 0.5em 1em;
        text-decoration: none;
        background: #9cc3ec;/*ボタン色*/
        color: #FFF;
        border: none;
        border-radius: 16px;
        }

        .btn-square:active ,.btn-squares:active {
        /*ボタンを押したとき*/
        -webkit-transform: translateY(4px);
        transform: translateY(4px);/*下に動く*/
        }  
       
</style>
    <title>アイデア倉庫：質問投稿ページ</title>
</head>

<body>
<div class="wrapper">
    <header>
        <a href="questions.php"><img id="logo" src="images/4.png"></a>
        <div id="logout">
            <form action="login.php" method="POST">
                <input type="submit" name="logout" class="btn" value="ログアウト">
            </form>
        </div>
    </header>

    <div id="main">
        <h1>質問を投稿する<?php echo $msg; ?>※255字以内で入力してください。</h1>
        <div class="question">
            <p class="err"></p>
                    <form action="" method="POST" id="questionForm"></form>
                    <form action="questions.php" method="GET" id="returnForm"></form>
                        <textarea name="question" form="questionForm" maxlength="255" required></textarea>
                <div class="btns">
                    <input type="submit" class="btn-square" form="questionForm" value="投稿">
                    <input type="hidden" name="token" form="questionForm" value="<?php echo $_SESSION['tokenSub'] ?>">
                    <input type="submit" class="btn-squares" form="returnForm" value="戻る">
                </div>
        </div>
    </div>
</body>
    <?php include dirname(__FILE__)."/footer.html"; ?>
</html>

