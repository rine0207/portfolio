<?php

require('link/function.php');

debug('----------------------------------------');
debug('パスワード再発行');
debug('----------------------------------------');
debugLogStart();

//post送信されていた場合
if(!empty($_POST)){
    debug('POST送信があります');
    debug('$_POSTの中身：'.print_r($_POST,true));
    
    //変数にPOST情報代入
        $email = $_POST['email'];
    
    //未入力バリデーション
        Minyuuryoku($email,'email');
    
    if(empty($err_msg)){
        debug('未入力チェックok');
        
        Maxlen($email,'email');
        Keisiki($email,'email');
        
        if(empty($err_msg)){
            debug('バリデーションOK');
            
            try{
                $dbh = dbConnect();
                $sql = 'SELECT count(*) FROM users WHERE email = :email AND delete_flg = 0';
                $data = array(':email'=>$email);
                $stmt= queryPost($dbh,$sql,$data);
                //クエリ結果の値を取得
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                
                //EmailがDBに登録されている場合
                if($stmt && array_shift($result)){
                    debug('$resultの中身：'.print_r($result,true));
                    debug('クエリ成功');
                    $_SESSION['msg_success'] = SUC03;
                    
                    $auth_key = makeRandKey(); //認証キー生成
                    
                    
                    
                    //認証に必要な情報をセッションへ保存
                    $_SESSION['auth_key'] = $auth_key;
                    $_SESSION['auth_email'] = $email;
                    $_SESSION['auth_key_limit'] = time()+(60*30); //30分後のタイムスタンプを入れる
                    debug('$_SESSIONの中身：'.print_r($_SESSION,true));
                    
                    header("Location:pass_auth.php"); //認証キー入力ページへ
                }else{
                    debug('クエリ失敗またはDBに登録のないEmailが入力された');
                    $err_msg['common'] = MSG07;
                }
            }catch (Exception $e){
                error_log('エラー発生：'.$e->getMseesage());
                $err_msg['common'] = MSG07;
            }
        }
    }
}
?>
<?php
$siteTitle = 'パスワード再発行';
require('link/head.php');
?>
<?php
$siteTitle;
require('link/header.php');
?>

        <div class="main">
            <h1>ポートフォリオ/パスワード再発行</h1>
               <div class="form-area">
                <form action="" method="post" class="form">
                    <p>認証キーを送信します</p>
                    <div class="area-msg">
                        <?php if(!empty($err_msg['common'])) echo $err_msg['common']; ?>
                    </div>
                    <label class="<?php if(!empty($err_msg['email'])) echo 'err' ?>">
                        Email
                        <input type="text" name="email" value="<?php echo getFormData('email'); ?>">
                    </label>
                    <div class="area-msg">
                        <?php
                        if(!empty($err_msg['email'])) echo $err_msg['email'];
                        ?>
                    </div>
                    <div class="btn">
                        <input type="submit" name="送信" class="btn-mid">
                    </div>
                <a href="login.php">&lt; ログインページへ戻る</a>
                </form>
                </div>
        </div>
<?php
require('link/footer.php');
?>

