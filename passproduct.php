<?php

require('link/function.php');

debug('--------------------------------------------------------------');
debug('パスワード変更ページ');
debug('--------------------------------------------------------------');
debugLogStart();

//ログイン認証
require('link/auth.php');

//画面処理開始
$userData = getUser($_SESSION['user_id']);
debug('$userDataの中身：'.print_r($userData,true));

//post送信されていた場合
if(!empty($_POST)){
    debug('POST送信があります');
    debug('POST情報：'.print_r($_POST,true));
    
    //変数似ユーザー情報代入
    $pass_old = $_POST['pass_old'];
    $pass_new = $_POST['pass_new'];
    $pass_new_re = $_POST['pass_new_re'];
    
    //未入力チェック
    Minyuuryoku($pass_old,'pass_old');
    Minyuuryoku($pass_new,'pass_new');
    Minyuuryoku($pass_new_re,'pass_new_re');
    
    if(empty($err_msg)){
        debug('未入力完了');
        
        //古いパスワードのチェック
        validPass($pass_old,'pass_old');
        
        //新しいパスワードのチェック
        validPass($pass_new,'pass_new');
        debug('パスワードチェック完了');
        
        //古いパスワードとDBパスワードを照合
        if(!password_verify($pass_old,$userData['pass'])){
            $err_msg['pass_old'] = MSG15;
            debug('DB照合完了');
        }
        
        //新しいパスワードと同じ場合
        if($pass_old === $pass_new){
            $err_msg['pass_new'] = MSG16;
            debug('パスワード同完了');
        }
        //同値チェック
        Match($pass_new,$pass_new_re,'pass_new_re');
            debug('同値完了');
        
        
        if(empty($err_msg)){
            debug('バリデーション完了');
            
            //例外処理
            try{
                //DB接続
                $dbh = dbConnect();
                $sql = 'UPDATE users SET pass = :pass WHERE id = :id';
                $data = array(':id'=>$_SESSION['user_id'],':pass'=>password_hash($pass_new,PASSWORD_DEFAULT));
                
                //クエリ実行
                $stmt = queryPost($dbh,$sql,$data);
                
                //成功の場合
                if($stmt){
                    debug('クエリ成功');
                    $_SESSION['msg_success'] = SUC01;
                    
                    //メール送信プログラム
                    
                    header('Location:mypage.php');//マイページへ
                }
            }catch(Exception $e){
                error_log('エラー発生：'.$e->getMessage());
                $err_msg['common'] = MSG07;
            }
        }
    }
}
?>

<?php
$siteTitle = 'パスワード変更';
require('link/head.php');
?>
<?php
require('link/header.php');
?>

    <!--メニュー-->
    <div class="main">
        <h1>ポートフォリオ/パスワード変更</h1>
        <section id="main-form">
            <div class="form-area">
                <form action="" method="post" class="form">
                    <div class="area-msg">
                        <?php
                        echo Geterrmsg('common');
                        ?>
                    </div>
                    
                    <label class="<?php if(!empty($err_msg['pass_old'])) echo 'err'; ?>">
                        古いパスワード
                        <input type="password" name="pass_old" value="<?php echo getFormData('pass_old'); ?>">
                    </label>
                    <div class="area-msg">
                        <?php echo Geterrmsg('pass_old'); ?>
                    </div>
                    
                    <label class="<?php if(!empty($err_msg['pass_new'])) echo 'err'; ?>">
                        新しいパスワード
                        <input type="password" name="pass_new" value="<?php echo getFormData('pass_new'); ?>">
                    </label>
                    <div class="area-msg">
                        <?php echo Geterrmsg('pass_new'); ?>
                    </div>
                    
                    <label class="<?php if(!empty($err_msg['pass_new_re'])) echo 'err'; ?>">
                        新しいパスワード（再入力）
                        <input type="password" name="pass_new_re" value="<?php echo getFormData('pass_new_re'); ?>">
                    </label>
                    <div class="area-msg">
                        <?php echo Geterrmsg('pass_new_re'); ?>
                    </div>
                    
                    <div class="btn">
                        <input type="submit" class="btn-mid" value="変更">
                    </div>
                </form>
            </div>
        </section>
<?php
require('link/sidebar.php');
?>
    </div>
<?php
require('link/footer.php');
?>
