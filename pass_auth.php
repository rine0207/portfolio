<?php

require('link/function.php');

debug('-----------------------------------');
debug('パスワード再発行認証キー入力');
debug('-----------------------------------');
debugLogStart();

//sessionに認証キーがあるか確認
if(empty($_SESSION['auth.key'])){
    header("Location:pass.php"); //認証キー送信ページ
}

    //post送信されていた場合
    if(!empty($_POST)){
        debug('POST送信があります');
        debug('$_POSTの中身；'.print_r($_POST,true));
            
            //変数に認証キーを代入
            $auth_key = $_POST['token'];
        
            //未入力チェック
            Minyuuryoku($auth_key,'token');
        
            if(empty($err_msg)){
                debug('未入力ok');
                
                //固定長チェック
                validLength($auth_key,'token');
                //半角チェック
                Hankaku($auth_key,'token');
                
                if(empty($err_msg)){
                    debug('バリデーションok');
                    
                    if($auth_key !== $_SESSION['auth_key']){
                        $err_msg['common'] = MSG18;
                    }
                    if(time()>$_SESSION['auth_key_limit']){
                        $err_msg['common'] = MSG19;
                    }
                    
                    if(empty($err_msg)){
                        debug('認証ok');
                        
                        $pass = makeRandKey(); //パスワード生成
                        
                        //例外処理
                        try{
                            $dbh = dbConnect();
                            $sql = 'UPDATE users SET pass = :pass WHERE email = :email AND delete_flg = 0';
                            $data = array(':email'=>$_SESSION['auth_email'],':pass'=>password_hash($pass,PASSWORD_DEFAULT));
                            $stmt = queryPost($dbh,$sql,$data);
                            
                            //クエリ成功
                            if($stmt){
                                debug('クエリ成功');
                                
                                //セッション削除
                                session_unset();
                                $_SESSION['msg_success'] = SUC03;
                                debug('$_SESSIONの中身：'.print_r($_SESSION,true));
                                
                                header("Location:login.php");
                            }else{
                                debug('クエリ失敗');
                                $err_msg['common'] = MSG07;
                            }
                        }catch (Exception $e){
                            error_log('エラー発生：'.$e->getMessage());
                            $err_msg['common'] = MSG07;
                        }
                    }
                }
            }
    }

?>
<?php
$siteTitle = 'パスワード再発行認証';
require('link/head.php');
?>
<?php
$siteTitle;
require('link/header.php');
?>
    
       <p id="js-show-msg" style="display:none;" class="msg-slide">
           <?php
           echo getSessionFlash('msg_success');
           ?>
       </p>
       
      <div class="main">
          <h1>ポートフォリオ/認証キー発行</h1>
          <div class="form-area">
              <form action="" method="post" class="form">
                  <p>認証キーを入力してください</p>
                  <div class="area-msg">
                      <?php
                      if(!empty($err_msg['common'])) echo $err_msg['common'];
                      ?>
                  </div>
                  <label class="<?php if(!empty($err_msg['token'])) echo 'err' ?>">
                      認証キー
                      <input type="text" name="token" value="<?php echo getFormData('token'); ?>">
                  </label>
                  <div class="area-msg">
                      <?php
                      if(!empty($err_msg['token'])) echo $err_msg['token'];
                      ?>
                  </div>
                  <div class="btn">
                      <input type="submit" name="btn-mid" value="再発行する">
                  </div>
              </form>
          </div>
      </div>
      
<?php
require('link/footer.php');
?>