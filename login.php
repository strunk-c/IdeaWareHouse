<?php
    declare(strict_types=1); 
    require_once dirname(__FILE__)."/function.php"; 
    include dirname(__FILE__)."/header.html";
    session_start();
?>

<?php

    if($_SERVER["REQUEST_METHOD"] == "POST"){

        if(isset($_POST['login'])) {

            $pdo = Connect();
            $stmt = $pdo->prepare("SELECT * FROM user WHERE loginId=:userId");
            $stmt->bindValue(':userId', $_POST['userId']);
            $stmt->execute();

    if($rows = $stmt->fetch()) {
        if(password_verify($_POST['userPw'] , $rows['password'])) {

            $viewName = $rows['name'];
            $userId = $rows['loginId'];
            $userPw = $rows['password'];

            session_regenerate_id(TRUE); //セッションid発行
            $_SESSION['viewName'] = $viewName;//セッションにviewNameを登録
            $_SESSION['userId'] = $userId;//セッションにuserIdを登録
            $_SESSION['userPw'] = $userPw;//セッションにuserPwを登録
            
            /*setcookie("viewName",$viewName,time() + 60 * 60 * 24 * 7,"/","",false,true);
            setcookie("userId",$userId,time() + 60 * 60 * 24 * 7,"/","",false,true);
            setcookie("userPw",$userPw,time() + 60 * 60 * 24 * 7,"/","",false,true);*/
           
            $msg = "ログイン成功！". "&emsp;";
            $link = "&emsp;" . "<a href='questions.php'>質問一覧へ</a>";

            /*確認用
            echo $_SESSION['viewName'];
            echo $_SESSION['userId'];
            echo $_SESSION['userPw'];
            echo "<hr>";
            echo $_COOKIE['viewName'];
            echo $_COOKIE['userId'];
            echo $_COOKIE['userPw'];*/

        }else {
            $err = "ログイン失敗！";
            //$err = "パスワードが違います。";分岐確認用
        }

        }else {
            $errs = "ログイン失敗！";
        }
    }
}
?>

<?php
    if(isset($_POST['logout'])){
        logout();
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
            margin-top: 8em;
            margin-left: 504px;
            font-size: 120%;
            font-weight: bold;
        }

        th , td {
            padding: 4px;
        }

        .result {
            margin-top: 2em;
            margin-bottom: 1em;
            margin-left: 600px;
            font-size: 120%;
            font-weight: bold;
        
        }
        .err {
            margin-left: 88px; 
        }

        .btn-square {
            background: #9cdf9c;/*ボタン色*/
            color: black;
        }

        .login {
            position: relative;
            padding:0.25em 1em;
            margin:0px auto 20px;
            width: 440px;
            font-size: 160%;
        }
        .login:before,.login:after{ 
            content:'';
            width: 20px;
            height: 30px;
            position: absolute;
            display: inline-block;
        }
      
        .login p {
            margin: 5px 0; 
            padding: 0;
        }

        .btns {
            flex-flow: column;
            text-align: right;
        }

        /* ログインボタン */
        .btn-square {
        display: inline-block;
        padding: 0.5em 1em;
        text-decoration: none;
        background: #9cdf9c;/*ボタン色*/
        color: #FFF;
        border: none;
        border-radius: 3px;
        }
        /* 新規登録ボタン */
        .btn-squares {
        display: inline-block;
        padding: 0.5em 1em;
        text-decoration: none;
        background: #9cc3ec;/*ボタン色*/
        color: #FFF;
        border: none;
        border-radius: 3px;
        }

        .btn-square ,.btn-squares{
            flex-flow: column;
        }

        .btn-square:active ,.btn-squares:active {
        /*ボタンを押したとき*/
        -webkit-transform: translateY(4px);
        transform: translateY(4px);/*下に動く*/
        }  
        
</style>
    <title>アイデア倉庫：ログインページ</title>
</head>

    <div class="wrapper">
        <header>
            <a href="questions.php"><img id="logo" src="images/4.png"></a>
            <div id="logout"></div>
        </header>
<body>
    <div id="main">
        <h1>ログイン</h1>
        <div class="result">
            <?php if(!empty($msg)): ?>
                <p class=""><?php echo $msg , $link; ?></p>
            <?php endif; ?>
            <?php if(!empty($err)): ?>
                <p class="err"><?php echo $err; ?></p>
            <?php endif; ?>
            <?php if(!empty($errs)): ?>
                <p class="err"><?php echo $errs; ?></p>
            <?php endif; ?>
            <?php if(!empty($logout)): ?>
                <p class="err"><?php echo $logout; ?></p>
            <?php endif; ?>
        </div>
        <div class="login">
            <p class="err"></p>
            <form action="" method="POST" id="loginForm"></form>
            <form action="userAdd.php" method="GET" id="newAdd"></form>
                <table border='0'>
                    <tr>
                        <th>ニックネーム</th>
                        <td><input type="text" name="userId" form="loginForm" value="itadu" style="font-size: 80%;" required></td>
                    </tr>
                    <tr>
                        <th>パスワード</th>
                        <td><input type="password" name="userPw" form="loginForm" value="pass" style="font-size: 80%;" required></td>
                    </tr>
                </table>
                <div class = "btns">
                    <input type="submit" class="btn-squares" form="newAdd" value="新規登録" style="font-size: 56%;">    
                    <input type="submit" class="btn-square" name="login" form="loginForm" value="ログイン" style="font-size: 56%;">
                    <input type="button" name="" id="like-button" class="btn-square" style="background: #E84040;" value="いいね">
                    <i><u><span id="like-count">0</span></u></i>
                </div>   
        </div>
    </div>
</div>

    <script>
        let likeButton = document.getElementById('like-button');
        let count = 0;
        likeButton.addEventListener('click', function() {
        count++;
        document.getElementById('like-count').textContent = count;
        });
    </script>

</body>
    <?php include dirname(__FILE__)."/footer.html"; ?>
</html>
