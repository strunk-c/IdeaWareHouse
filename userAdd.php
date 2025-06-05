<?php
    declare(strict_types=1);
    require_once(dirname(__FILE__)."/function.php");
    include dirname(__FILE__)."/header.html";
    session_start();
?>

<?php
// POST通信だった場合はDBへの新規登録処理を開始
    if($_SERVER["REQUEST_METHOD"] == "POST"){

//フォームからの値をそれぞれ変数に代入
    $viewName = $_POST['viewName'];
    $userId = $_POST['userId'];
    $userPw = password_hash($_POST['userPw'], PASSWORD_DEFAULT);

//フォームに入力された値が登録されていないかチェック
    $pdo = Connect();
    $sql = 'SELECT * FROM user WHERE loginId = :loginId';
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':loginId', $userId);
    $stmt->execute();
    $member = $stmt->fetch();
    if ($member['loginId']??"" === $userId) {
        $err = '登録内容を変更してからもう一度作成してください。';
        //$err = '同じユーザーIDが存在します。';セキュリティ対策のため情報を表示させない
    } else {
//登録されていなければinsert 
    $sql = 'INSERT INTO user(loginId, password, name) VALUES (:userId,:userPw,:viewName)';
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':userId', $_POST['userId'], PDO::PARAM_STR);
    $stmt->bindParam(':userPw', $userPw, PDO::PARAM_STR);
    $stmt->bindParam(':viewName', $_POST['viewName'], PDO::PARAM_STR);
    $stmt->execute();
    $msg = 'アカウント登録が完了しました。';
    $link = '<a href="login.php">ログインページに進む</a>';
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
            margin-left: 544px;
            font-size: 120%;
            font-weight: bold;
        
        }
        
        .btn-square {
            background: #9cdf9c;/*ボタン色*/
            color: black;
        }

        .useradd {
            position: relative;
            padding:0.25em 1em;
            margin:50px auto 20px;
            /*background-color: #e8e8e8;*/
            width: 440px;
            font-size: 160%;
        }

        .useradd:before,.login:after{ 
            content:'';
            width: 20px;
            height: 30px;
            position: absolute;
            display: inline-block;
        }
        
        .useradd p {
            margin: 5px 0; 
            padding: 0;
        }

        .btns {
            flex-flow: column;
            text-align: right;
            margin-top: 0.4em;
        }

        .btn-square {
        display: inline-block;
        padding: 0.5em 1em;
        text-decoration: none;
        background: #9cdf9c;/*ボタン色*/
        color: #FFF;
        border: none;
        border-radius: 16px;
        }

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
      <title>アイデア倉庫：利用者登録ページ</title>
</head>

<body>
<div class="wrapper">
    <header>
        <a href="questions.php"><img id="logo" src="images/4.png"></a>
        <div id="logout"></div>
    </header>
    <div id="main">
        <h1>アカウント作成</h1>
        <div class="result">
            <?php if(!empty($err)): ?>
                <p class="err"><?php echo $err; ?></p>
            <?php endif; ?>
            <?php if(!empty($msg)): ?>
                <p class=""><?php echo $msg , $link; ?></p>
            <?php endif; ?>
         </div>
        <div class="useradd">
            <form action="" method="POST" id="userForm"></form>
            <form action="questions.php" method="GET" id="indexForm"></form>
                <table border='0'>
                    <tr>
                        <th>アカウント名</th>
                        <td><input type="text" name="viewName" form="userForm" placeholder="10文字以内" maxlength="10" style="font-size: 80%;" required></td>
                    </tr>
                    <tr>
                        <th>ニックネーム</th>
                        <td><input type="text" name="userId" form="userForm" placeholder="10文字以内" maxlength="10" style="font-size: 80%;" required></td>
                    </tr>
                    <tr>
                        <th>パスワード</th>
                        <td><input type="password" name="userPw" form="userForm" placeholder="4文字以上" minlength="4" style="font-size: 80%;" required></td>
                    </tr>
                    </table>    
                    <div class = "btns">
                        <input type="submit" class="btn-squares" form="userForm" value="登録" style="font-size: 56%;">
                        <input type="submit" class="btn-square" form="indexForm" value="戻る" style="font-size: 56%;">        
                    </div>
    </div>
</div>
</body>
    <?php include dirname(__FILE__)."/footer.html"; ?>
</html>
