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
    $_SESSION['tokenSub'] = $csrf_token; // 代用
?>

<?php 
    if(isset($_SESSION['viewName'])){
        $viewName = $_SESSION['viewName'];
    }
 ?>

<?php
//ページネーション
    $num = 5;
    $page = 0;
        if (isset($_GET['page']) && $_GET['page'] > 0) {
                $page = intval($_GET['page']) - 1;
        }
            try {
                    $pdo = Connect();
                    $stmt = $pdo->prepare("SELECT * FROM question ORDER BY date DESC LIMIT :start , :num");
                    $start = $page * $num;
                    $stmt->bindValue(':start', $start, PDO::PARAM_INT);
                    $stmt->bindValue(':num', $num, PDO::PARAM_INT);
                    $stmt->execute();
            } catch (PDOException $e) {
                        die('ごめんね、素直じゃなくて。エラーです。' . $e->getMessage());
        }
?>

<?php
// DB件数チェック
    $st = $pdo->prepare("SELECT COUNT(*) FROM question");
    $st->execute();
    $count = $st->fetchColumn();

    if ($count != 0) {
        //echo "レコードあり";
    } else {
        $miss = "まだ投稿がありません。";
    }
?>

<?php
    date_default_timezone_set('Asia/Tokyo');
    $time = intval(date('H')); // 時刻を整数型にする
        if (4 <= $time && $time <= 11) { // 4時～11時の時間帯の時
            $welcome = "&#x1f31e;おはようございます、";
            } elseif (11 <= $time && $time <= 17) { // 11時〜17時の時間帯の時
            $welcome = "&#x1f308;こんにちは、";
            } else { // それ以外の時間帯のとき
            $welcome = "&#x1f31d;こんばんは、";
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

    header , h2 {
        color: black;
        font-size:120%;
    }

    #logo {
        width: 75%;
        height: 75%;
    }
        
    #main h1{
        margin: 32px auto -72px 320px;
        font-size: 120%;
    }

    #main h2{
        margin: 4em auto 0 240px;
        font-size: 120%;
    }

    #name {
        font-size:108%;
        padding: 8px 0 8px;
        border-bottom:2px #6937E1 dotted;
    }
    #content {
        padding: 8px 0 8px;
	    padding-left: 1.6em;
        border-bottom:2px #6937E1 dotted;
    }

    #page {
        margin-left: 240px;
    }

    .questions {
        position: relative;
        padding:0.25em 1em;
        margin: 8px 240px 24px;
    }
    .sub {
        border: 6px #6937E1 solid;
        padding: 16px 56px;
        margin: 24px 56px;
        text-align: left;
        border-radius:64px;
    }

    .questions:before,.questions:after{ 
        content:'';
        width: 20px;
        height: 30px;
        position: absolute;
        display: inline-block;
    }
    
    .questions h2 {
        margin: 5px 0; 
        padding: 0;
    }

    .questions p {
        margin: 5px 4.8em; 
        padding: 0;
    }

    .questions .detail{             
        padding-left:0;
        width: -webkit-fill-available;
        display: flex;
        justify-content: flex-start;
        align-items: center;
    }

    .questions .detail .delete{             
        margin-left:25px;
        width: -webkit-fill-available;
        display: flex;
        justify-content: flex-start;
        align-items: center;
    }

    .questions .questionDate{
        font-size: xx-small;
        padding-top:8px;
    }

    .qbtn{
        padding: 10em,70em,0em,60em;
    }

    /*ログインボタン*/
    .btn {
        /*background: #9cdf9c;/*ボタン色*/
        /*color: black;*/
        display: inline-block;
        padding: 1em 3em;
        text-decoration: none;
        background: #9cc3ec;/*ボタン色*/
        color: #FFF;
        border: none;
        border-radius: 24px;
    }

    /*詳細ボタン*/
    .btn-square {
        display: inline-block;
        padding: 0.5em 1em;
        text-decoration: none;
        background: #9cdf9c;/*ボタン色*/
        color: #FFF;
        border: none;
        border-radius: 16px;
    }

     /*削除ボタン*/
     .btn-squares {
        display: inline-block;
        padding: 0.5em 1em;
        text-decoration: none;
        background: #9cc3ec;/*ボタン色*/
        color: #FFF;
        border: none;
        border-radius: 16px;
    }

    .btn-square:active ,.btn-squares:active{
    /*ボタンを押したとき*/
    -webkit-transform: translateY(4px);
    transform: translateY(4px);/*下に動く*/
    }  

    .que {
        position: relative;
        padding:0.25em 1em;
        margin:64px 320px 8px;
        font-weight: bold;
        font-size: 120%;
    }

    .welcome {
        padding-left: 400px;
    }

    .detail {
        padding-top: 8px;
    }


</style>
    <title>アイデア倉庫：質問一覧ページ</title>
</head>

<body>
<div class="wrapper">
    <header>
        <a href="questions.php"><img id="logo" src="images/4.png"></a>
        <div id="logout">
            <form action="login.php" method="POST">
                <?php
                if(isset($_SESSION['userId'])){
                echo '<input type="submit" name="logout" class="btn" value="ログアウト">';}
                ?>
            </form>
            <form action="userAdd.php" method="GET" style="padding-left: 8px;">
                <?php
                if(isset($_SESSION['userId'])){
                echo '<input type="submit" name="logout" class="btn" value="アカウント作成">';}
                ?>
            </form>
        </div>
    </header>
    <div id="main">
        <h2 class="welcome">
            <?php if(!empty($viewName)): ?>
                <p class=""><?php echo $welcome . h($viewName) . "さん。"; ?></p>
            <?php endif; ?>
            <?php if(empty($_SESSION['viewName'])): ?>
                <p class="err"><?php echo '<a href="login.php">ログインしてください。</a>'; ?></p>
            <?php endif; ?>
       </h2>

        <h1>【 質問一覧 】</h1>

        <div id="menu">
            <p class="err"></p>
            <?php if(!empty($_SESSION['viewName'])){ ?>
            <form action="questionInput.php" method="GET">
                <input type="submit" class="btn-square" value="質問する">
            </form>
            <?php } ?>
        </div>

        <div class="que">
            <?php if(isset($_POST['questionId']) && empty($_SESSION['csrfErrQ'])): ?>
            <p class="err"><?php echo $_SESSION['userId'] .  "さんの質問を削除しました。"; ?></p>
            <?php endif; ?>
            <?php if(isset($_SESSION['csrfErrQ'])): ?>
            <p class="err"><?php echo $_SESSION['csrfErrQ']; ?></p>
            <?php endif; ?>
            <?php if(isset($_SESSION['csrfErrAddQ'])): ?>
            <p class="err"><?php echo $_SESSION['csrfErrAddQ']; ?></p>
            <?php endif; ?>
        </div>
        
            <div class="questions">

            <?php if(!empty($miss)): ?>
                <p class="err"><?php echo $miss; ?></p>
            <?php endif; ?>
            
            <?php foreach($stmt as $loop): ?>
                <?php if($loop['deleteFlg'] === 0){ ?>

                <div class="sub">

                <div id="name">No.<?php echo $loop['id'] ?>：<?php echo h($loop['userId']) ?>さん</div>
                <div id="content"><?php echo nl2br(h($loop['question'])) ?></div>
                <div class="questionDate"><?php echo $loop['date'] ?>
                <div class="detail">
                <form action="detail.php" method="GET">
                    <input type="hidden" name="questionId" value="<?php echo $loop['id'] ?>">
                    <input type="submit" name="detail" class="btn-square" value="詳細">
                </form>
            
                <?php if(!empty($_SESSION['userId']) && $_SESSION['userId'] == $loop['userId']){ ?>
                <form action="questionInput.php" method="POST" onsubmit="return confirm_del()">
                    <input type="submit" name="questionDelete" class="btn-squares delete" value="削除">
                    <input type="hidden" name="questionId" value="<?php echo $loop['id'] ?>">
                    <input type="hidden" name="token" value="<?php echo $csrf_token ?>">
                </form>
                <?php } ?>
                </div>
            </div>
        </div>
           
            <?php }endforeach; ?>
    </div>
    <script>
        function confirm_del(){
            let select = confirm("本当に削除しますか？");
            return select;
        }
    </script>
    <div id="page">
            <?php
                try {
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM question");
                $stmt->execute();
                } catch(PDOException $e) {
                die('思考回路はショート寸前、エラーです。' . $e->getMessage());
                }

                $messages = $stmt->fetchColumn();
                $max_page = ceil($messages / $num);
                echo '<p>';
                for ($i = 1; $i <= $max_page; ++$i) {
                echo '<a href="questions.php?page=' . $i . '">' . $i . '</a>&nbsp;';
                }
                echo '</p>';
            ?>
    </div>
</body>
    <?php include dirname(__FILE__)."/footer.html"; ?>
</html>
