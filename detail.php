<?php
    declare(strict_types=1); 
    require_once dirname(__FILE__)."/function.php";
    include dirname(__FILE__)."/header.html"; 
    session_start();
?>

<?php
//CSRF対策
    $token_byte = openssl_random_pseudo_bytes(16); // ワンタイムトークン生成
    $csrf_token = bin2hex($token_byte); // 2進数を16進数に変換
    $_SESSION['token'] = $csrf_token; // セッションにトークンを保存
?>

<?php
//ログイン確認
    if(!isset($_SESSION['userId'])){
        header("Location:./questions.php",true,307);
        exit();
    }
?>

<?php
if(isset($_GET['detail'])){
    $questionId = $_GET["questionId"];
    session_regenerate_id(TRUE); //セッションid発行
    $_SESSION['questionId'] = $questionId;
        $pdo = Connect();
        $stmt = $pdo->prepare("SELECT * FROM question WHERE id = :questionId");
        $stmt->bindValue(':questionId', $questionId, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
    }
?>

<?php
    if(isset($_POST['questionId'])){
    $questionId = $_SESSION['questionId'];
        $pdo = Connect();
        $stmt = $pdo->prepare("SELECT * FROM question WHERE id = :questionId");
        $stmt->bindValue(':questionId', $questionId, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
    }
?>

    <?php
    if(isset($_GET['questionId'])){
    $questionId = $_SESSION['questionId'];
        $pdo = Connect();
        $stmt = $pdo->prepare("SELECT * FROM question WHERE id = :questionId");
        $stmt->bindValue(':questionId', $questionId, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
    }
?>

<?php
$questionId = $_SESSION['questionId'];
            try {
                    $pdo = Connect();
                    $stmt = $pdo->prepare("SELECT * FROM answer WHERE questionId = :questionId");
                    $stmt->bindValue(':questionId', $questionId, PDO::PARAM_INT);
                    $stmt->execute();
            } catch (PDOException $e) {
                        die('ごめんね、素直じゃなくて。エラーです。' . $e->getMessage());
        }
?>

<?php
// DB件数チェック
    $st = $pdo->prepare("SELECT COUNT(*) FROM answer WHERE questionId = :questionId");
    $st->bindValue(':questionId', $_SESSION['questionId'], PDO::PARAM_INT);
    $st->execute();
    $count = $st->fetchColumn();

    if ($count != 0) {
        //echo "レコードあり";
    } else {
        $miss = "まだ投稿がありません。";
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
        margin: 8em 0 -2em 4em;
    }
    
    #main h2{
        margin-left: 300px;
    }

     #name {
        font-size:108%;
        padding: 8px 0 8px;
       
    }
    #content {
        padding: 8px 0 8px;
	    padding-left: 1.6em;
       
    }

    .btn-square {
        background: #9cdf9c;/*ボタン色*/
        color: black;
    }

    .questions, .answers {
        border: 6px #6937E1 solid;
        padding: 16px 56px;
        margin: 24px 56px;
        text-align: left;
        border-radius:64px;
    }   

    .answers {
        margin:5px 50px 20px 300px;
    }

    .questions:before,.questions:after,
    .answers:before,.answers:after{ 
        content:'';
        width: 20px;
        height: 30px;
        position: absolute;
        display: inline-block;
    }
   
    .questions h3,
    .answers h3 {
        margin: 5px 0; 
        padding: 0;
    }

    .questions p,
    .answers p {
        margin: 5px 0; 
        padding: 0;
    }

    .questions .detail,
    .answers .detail{             
        padding-left:0;
        width: -webkit-fill-available;
        display: flex;
        justify-content: flex-top;
        align-items: center;
    }

    .questions .questionDate,
    .answers .answerDate{
        font-size: x-small;
        padding-top:8px;
    }

    .return{
        display: flex;
        width: -webkit-fill-available;
        justify-content:flex-end;
        padding-right: 50px;
    }

    /*登録ボタン*/
    .btn-square {
        display: inline-block;
        padding: 0.5em 1em;
        text-decoration: none;
        background: #9cdf9c;/*ボタン色*/
        color: #FFF;
        border: none;
        border-radius: 16px;
    }
    /*削除、戻るボタン*/
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

    .que {
        position: relative;
        padding:0.25em 1em;
        margin: 8px 280px 0px;
        font-weight: bold;
        font-size: 120%;
    }

    .detail {
        padding-top: 8px;
    }
  
</style>
    <title>アイデア倉庫：質問詳細ページ</title>
</head>

<body>
<div class="wrapper">
    <header>
        <a href="questions.php"><img id="logo" src="images/4.png"></a>
        <div id="logout">
            <form action="login.php" method="POST">
                <input type="submit" name="logout" class="btn-squares" value="ログアウト">
            </form>
        </div>
    </header>
    <div id="main">

        <h1>質問</h1>

        <div class="que">
        <?php if(isset($_POST['answerId']) && empty($_SESSION['csrfErrA'])): ?>
            <p class="err"><?php echo $_SESSION['userId'] .  "さんの回答を削除しました。"; ?></p>
            <?php endif; ?>
            <?php if(isset($_SESSION['csrfErrA'])): ?>
            <p class="err"><?php echo $_SESSION['csrfErrA']; ?></p>
            <?php endif; ?>
        </div>

        <div class="questions">
            <h3 id="name"><?= h($row['userId']); ?>さん</h3>
            <p id="content"><?= h($row['question']); ?></p>
            <p class="questionDate"><?= $row['date']; ?></p>
            <div class="detail">
                <form action="answer.php" method="GET">
                    <input type="hidden" name="questionId" value="<?php echo $row['id'] ?>">
                    <input type="submit" name="answer" class="btn-square" value="回答">
                </form>
            </div>
        </div>

        <h2>回答</h2>
       
            <div class="answers">
            <?php if(!empty($miss)): ?>
                <p class="err"><?php echo $miss; ?></p>
            <?php endif; ?>
            <?php foreach($stmt as $loop): ?>
            <?php if($loop['deleteFlg'] === 0){ ?>
            <h3 id="name"><?php echo h($loop['userId']) ?>さん</h3>
            <p id="content"><?php echo nl2br(h($loop['answer'])) ?></p>
            <p class="answerDate"><?php echo $loop['date'] ?></p>

            <div class="detail">
            <?php if(!empty($_SESSION['userId']) && $_SESSION['userId'] == $loop['userId']){ ?>
                <form action="questionInput.php" method="POST" onsubmit="return confirm_del()">
                    <input type="submit" name="answerDelete" class="btn-squares" value="削除">
                    <input type="hidden" name="questionId"  value="<?php echo $loop['questionId'] ?>">
                    <input type="hidden" name="answerId"  value="<?php echo $loop['id'] ?>">
                    <input type="hidden" name="token" value="<?php echo $csrf_token ?>">
                </form>
                <?php } ?>
            </div>
            <hr>
            <?php }endforeach; ?>  
        </div>

        <script>
        function confirm_del(){
            let select = confirm("本当に削除しますか？");
            return select;
        }
    </script>
        <div class="return">
            <form action="questions.php" method="GET" id="returnForm">
                <input type="submit" class="btn-squares" form="returnForm" value="戻る">
            </form>          
    </div>
</div>

</body>
    <?php include dirname(__FILE__)."/footer.html"; ?>
</html>
