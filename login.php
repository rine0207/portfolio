<?php

ini_set('display_errors',1);
ini_set('log_errors','on');
ini_set('errors_log','php.log');

//関数ファイル読み込み
require('link/function.php');

debug('----ｰｰｰｰｰｰｰｰｰｰｰｰｰｰｰｰｰ');
debug('--ログインページ--');
debug('----ｰｰｰｰｰｰｰｰｰｰｰｰｰｰｰｰｰ');

debugLogStart();

//ログイン認証
require('link/auth.php');

if(!empty($_POST)){
    debug('POST送信があります');
    
    //ユーザー情報代入
    $email = $_POST['email'];
    $pass = $_POST['pass'];
    $pass_save = (!empty($_POST['pass_save'])) ? true : false;
    
    //email未入力チェック
    Minyuuryoku($email,'email');
    //pass未入力
    Minyuuryoku($pass,'pass');
    
    //emial形式チェック
    Keisiki($email,'email');
    //email文字数チェック
    Maxlen($email,'email');
    
    //pass形式チェック
    Hankaku($pass,'pass');
    //pass最大文字数
    Maxlen($pass,'pass');
    //pass最小文字数
    Minlen($pass,'pass');
    
    if(empty($err_msg)){
        debug('バリデーション完了');
        
        try{
            $dbh = dbConnect();
            $sql = 'SELECT pass,id FROM users WHERE email = :email AND delete_flg = 0';
            $data = array(':email' => $email);
            $stmt = queryPost($dbh,$sql,$data);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            debug('$resultの中身：'.print_r($result,true));
            
            if(!empty($result) && password_verify($pass,array_shift($result))){
                debug('パスワードがマッチしました');
                
                $sesLimit = 60*3;
                
                $_SESSION['login_date'] = time();
                
                if($pass_save){
                    debug('$pass_saveの中身：'.print_r($pass_save,true));
                    debug('ログイン保持にチェックがあります');
                    //ログインう有効期限5日
                    $_SESSION['login_limit'] = $sesLimit * 24 * 5;
                }else{
                    debug('ログイン保持にチェックがありません');
                    //有効期限を3分後にセット
                    $_SESSION['login_limit'] = $sesLimit;
                }
                //ユーザーid格納
                $_SESSION['user_id'] = $result['id'];
                
                //ログイン成功の場合
                debug('$_SESSIONの中身：'.print_r($_SESSION,true));
                $_SESSION['msg_success'] = SUC06;
                debug('$_SESSIONの中身：'.print_r($_SESSION,true));
                debug('メインメニューへ');
                header("Location:mypage.php");
            }else{
                debug('パスワードが合っていません');
                $err_msg['common'] = MSG09;
            }
        }catch (Exception $e){
            error_log('エラー発生：'.$e->getMessage());
            $err_msg['common'] = MSG07;
        }
    }
}

//postされていた場合
debug('画面表示処理終了');
?>

<!-- html始まり -->
<?php
$siteTitle = 'ログイン';
require('link/head.php');
?>
<?php
$siteTitle;
require('link/header.php');
?>

<!--メニュー-->
      <div class="main">
        <h1 class="title">ログインフォーム</h1>
          <div class="form-area">
           <form action="" method="post" class="form">
          <div class="err_msg">
              <?php
              if(!empty($err_msg['common'])) echo $err_msg['common'];
              ?>
          </div>
          
           <label>Email:
           <input type="text" name="email" placeholder="email" value="<?php if(!empty($_POST['email'])) echo $_POST['email']; ?>">
           <span class="err_msg"><?php if(!empty($err_msg['email'])) echo $err_msg['email']; ?></span>
            </label>
            
           <label>パスワード<span style="font-size:13px;">※英数半角6文字以上</span>
           <input type="password" name="pass" placeholder="パスワード" value="<?php if(!empty($_POST['pass'])) echo $_POST['pass']; ?>">
           
           <span class="err_msg"><?php if(!empty($err_msg['pass'])) echo $err_msg['pass']; ?></span>
            </label>
            
           <label>
           <input type="checkbox" name="pass_save">次回ログインを省略する
           </label>
           
           <!--ボタン-->
         <!---------------------------------------------------------------------------------------->
          <div class="btn">
           <input type="submit" name="submit" class="btn-mid" value="ログイン">
           <input type="reset" name="reset" class="btn-mid" value="リセット">
          </div>
           
           パスワードを忘れた方はこちら<a href="pass.php">コチラ</a>
       </form>
          </div>
        </div>
<!--footer-->
<?php
require('link/footer.php');
?>
