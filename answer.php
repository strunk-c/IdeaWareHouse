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

    $msg = "（".$_SESSION['userId']."）";
    $msg = h($msg);

?>

<?php
    if($_SERVER["REQUEST_METHOD"] == "GET"){
    $questionId = $_GET["questionId"];
    session_regenerate_id(TRUE); //セッションid発行
    $_SESSION['questionId'] = $questionId;
    }

    if(isset($_POST['answer'])){
        if(isset($_POST['token']) && $_POST['token'] === $_SESSION['tokenSub']){
            unset($_SESSION['csrfErrAddA']);
                $pdo = Connect();
                $userId = $_SESSION['userId'];
                $stmt = $pdo->prepare('INSERT INTO answer (userId,questionId,answer) VALUES(:userId,:questionId,:answer)');
                $stmt->bindValue(':userId', $userId);
                $stmt->bindValue(':questionId',$_POST['questionId']);
                $stmt->bindValue(':answer', $_POST['answer']);
                $stmt->execute();
            }else{
                $_SESSION['csrfErrAddA'] = "回答失敗！不正ダメ！ErrorCode:CSRF";
        }
    }
?>

<?php
    if(isset($_GET['answer'])){
    $questionId = $_GET['questionId'];
        $pdo = Connect();
        $stmt = $pdo->prepare("SELECT * FROM question WHERE id = :questionId");
        $stmt->bindValue(':questionId', $questionId, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);}
    ?>

<?php
    if(isset($_SESSION['questionId'])){
    $questionId = $_SESSION['questionId'];
        $pdo = Connect();
        $stmt = $pdo->prepare("SELECT * FROM question WHERE id = :questionId");
        $stmt->bindValue(':questionId', $questionId, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);}
?>

<?php
//追加　回答一覧用
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
        margin-top: 7.2em;
        margin-left: 8.8em;
    }

    #main h2{
        margin: 2em 24em -0.5em;
       
    }

    #content {
        padding-left: 1.6em;
    }

    .btn-square {
        background: #9cdf9c;/*ボタン色*/
        color: black;
    }
    
    .questions, .answers {
        position: relative;
        padding:0.3em 2em;
        margin:8px 160px 20px;
        background-color: #e8e8e8;
        border-radius:32px;
    }    

    .answers {
        margin:16px 400px 24px 400px;
    }
    
    .questions:before,.questions:after,
    .answers:before,.answers:after{ 
        content:'';
        width: 20px;
        height: 30px;
        position: absolute;
        display: inline-block;
    }
    
    .questions h3, .answers h3 {
        margin: 5px 0; 
        padding: 0.25em;
    }

    .questions p, .answers p {
        margin: 5px 0; 
        padding: 0;
    }

    .answers .detail{             
        width: -webkit-fill-available;
        display: flex;
        justify-content: flex-top;
        align-items: center;
    }
    
    .answers .detail form{    
        width: -webkit-fill-available;
    }

    .questions .questionDate,
    .answers .answerDate{
        font-size: xx-small;
        padding: 0.3em 0.5em;
       
    }

    .return{
        display: flex;
        width: -webkit-fill-available;
        justify-content:flex-end;
        padding-right: 50px;
        margin-top: 0.72em;
    }

    textarea{
        width: -webkit-fill-available;
        height: 130px;
        border-radius:8px;
        resize: none;
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

    /*戻るボタン*/
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
        padding-left: 1120px;
        font-weight: bold;
        margin-top: -0.72em;
        
    }

    .detail {
        padding-top: 0.25em;
    }

    #page-top {
    position: fixed;
    right: 5px;
    bottom: 20px;
    height: 50px;
    text-decoration: none;
    font-weight: bold;
    transform: rotate(90deg);
    font-size: 90%;
    line-height: 1.5rem;
    color: #737373;
    padding: 0 0 0 35px;
    border-top: solid 1px;
}

#page-top::before {
    content: "";
    display: block;
    position: absolute;
    top: -1px;
    left: 0px;
    width: 15px;
    border-top: solid 1px;
    transform: rotate(35deg);
    transform-origin: left top;
}

</style>
     <title>アイデア倉庫：回答投稿ページ</title>
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
        <h1>質問内容</h1>
        <div class="questions">
            <h3><?= h($row['userId']); ?>さん</h3>
            <p id="content"><?= h($row['question']); ?></p>
            <p class="questionDate"><?= $row['date']; ?></p>
        </div>

        <h2>回答内容<?php echo $msg; ?>※255字以内で入力してください。</h2>

        <div class="answers">
            <div class="detail">
                <form action="answer.php" method="POST">
                    <input type="hidden" name="questionId" value="<?php echo $_SESSION['questionId']; ?>">
                    <textarea name="answer" maxlength="255" required></textarea>
                    <div style="text-align:right;">
                        <input type="submit" class="btn-square" value="回答">
                        <input type="hidden" name="token" value="<?php echo $_SESSION['tokenSub'] ?>">
                    </div>
                </form>
            </div>
        </div>

        <div class="que">
            <?php if(isset($_POST['questionId']) && empty($_SESSION['csrfErrAddA'])): ?>
            <p><?php echo '"' . mb_substr($_POST['answer'], 0, 10) . '"' . " を投稿しました。"; ?></p>
            <?php endif; ?>
            <?php if(isset($_SESSION['csrfErrAddA'])): ?>
            <p class="err"><?php echo $_SESSION['csrfErrAddA']; ?></p>
            <?php endif; ?>
        </div>
        
        <div class="return">
            <form action="detail.php" method="GET" id="returnForm">
                <input type="hidden" name="questionId"  value="<?php echo $_SESSION['questionId']; ?>">
                <input type="submit" class="btn-squares" form="returnForm" value="戻る">
            </form>
        </div>
    </div>
</div>

<div class="answers">
    <?php foreach($stmt as $loop): ?>
            <?php if($loop['deleteFlg'] === 0){ ?>
            <h3 id="name"><?php echo h($loop['userId']) ?>さん</h3>
            <p id="content"><?php echo nl2br(h($loop['answer'])) ?></p>
            <p class="answerDate"><?php echo $loop['date'] ?></p>
            <hr>
            <?php }endforeach; ?>  
            <a href="#" id="page-top">TOP</a>
    </div>
    
</body>
    <?php include dirname(__FILE__)."/footer.html"; ?>
</html>
