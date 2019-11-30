<?php

require('link/function.php');

debug('----------------------------');
debug('退会');
debug('----------------------------');
debugLogStart();

//ログイン認証
require('link/auth.php');

//post送信されていた場合
if(!empty($_POST)){
    debug('POST送信があります');
    try{
        $dbh = dbConnect();
        $sql = 'UPDATE users SET delete_flg = 1 WHERE id = :us_id';
        $data = array(':us_id'=>$_SESSION['user_id']);
        $stmt = queryPost($dbh,$sql,$data);
        
        //クエリ成功の場合
        if($stmt){
        session_destroy();
        debug('セッションの中身'.print_r($_SESSION,true));
        debug('トップページ遷移');
        header("Location:signup.php");//ユーザー登録へ
    }else{
        debug('クエリが失敗しました');
        $err_msg['common'] = MSG07;
    }
}catch(Exception $e){
    error_log('エラー発生：'.$e->getMessage());
    $err_msg['common'] = MSG07;
}
}
debug('画面表示終了');
?>

<!--↓↓html↓↓-->
<?php
$siteTitle = '退会';
require('link/head.php');
?>
<?php
$siteTitle;
require('link/header.php');
?>
       <div class="main">
        <h1>ポートフォリオ/退会</h1>
        <section id="main-form">
           <div class="form-area">
            <form action="" method="post" class="form">
                <div class="area-msg">
                    <?php
                    if(!empty($err_msg['common'])) echo $err_msg['common'];
                    ?>
                </div>
                <div class="btn-with">
            <input type="submit" name="submit" value="退会">
                </div>
             </form>
                <div class="link">
        <a href="mypage.php" class="file">&lt; マイページへ戻る</a>
                </div>
            </div>
        </section>
<?php
require('link/sidebar.php');
?>
       </div>
<?php
require('link/footer.php');
?>
