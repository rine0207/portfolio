<?php
error_reporting(E_ALL);
ini_set('display_errors','On');
ini_get('date.timezone');

//関数ファイルを読み込む
require('link/function.php');

debug('--------------------------------------');
debug('------------ｰｰユーザー登録-----ｰ---------');
debug('--------------------------------------');
debugLogStart();
        
        //post送信されていた場合
        if(!empty($_POST)){
            debug('POST送信あり');
            $katakana = $_POST['katakana'];
            $kanjif = $_POST['kanjif'];
            $kanjil = $_POST['kanjil'];
            $tel = $_POST['tel'];
            $email = $_POST['email'];
            debug('$emailの中身:'.print_r($email,true));
            $pass = $_POST['pass'];
            $pass_re = $_POST['pass_re'];

            
        //未入力バリデーション
        Minyuuryoku($katakana,'katakana');
        Minyuuryoku($kanjif,'kanjif');
        Minyuuryoku($tel,'tel');
        Minyuuryoku($email,'email');
        Minyuuryoku($pass,'pass');
        Minyuuryoku($pass_re,'pass_re');
        
        
        if(empty($err_msg)){
            
        //kanji正規バリデーション
        KanjiKeisiki($kanjif,'kanjif');
        KanjiKeisiki($kanjil,'kanjil');
        //kana正規バリデーション
        KatakanaKeisiki($katakana,'katakana');
        
        //tel正規バリデーション
        Telkeisiki($tel,'tel');

            
        //email正規表現バリデーション
        Keisiki($email,'email');
        //email最大文字バリデーション
        Maxlen($email,'email');
        //email重複バリデーション
        Jufuku($email);
        
        //pass 半角英数字バリデーション
        Hankaku($pass,'pass');
        //pass 最大文字数バリデーション
        Maxlen($pass,'pass');
        //pass 最小文字数バリデーション
        Minlen($pass,'pass');

            
        if(empty($err_msg)){
            //同値バリデーション
            Match($pass, $pass_re, 'pass_re');
            
            if(empty($err_msg)){
                
                //例外処理
                
                try{
                    //DB接続
                    $dbh = dbConnect(); //これを代入するだけでfuncの接続ができる
                    $sql = 'INSERT INTO users(name,tel,email,pass,login_time,create_date)
                    VALUES(:name,:tel,:email,:pass,:login_time,:create_date)';
                    $data = array(':name'=>$kanjif.$kanjil,':tel'=>$tel,':email'=>$email,':pass'=>password_hash($pass,PASSWORD_DEFAULT),':login_time'=>date('Y-m-d H:i:s'),':create_date'=> date('Y-m-d H:i:s'));
                    
                    //クエリ実行
                    $stmt = queryPost($dbh,$sql,$data);
                    
                    //クエリ成功の場合
                    if($stmt){
                        debug('$stmtの中身:'.print_r($stmt,true));
                        $sesLimit = 60*3;
                        //最終ログイン日時を現在日時に
                        $_SESSION['login_date'] = time();
                        $_SESSION['login_limit'] = $sesLimit;
                        //ユーザーidを格納
                        $_SESSION['user_id'] = $dbh->lastInsertId();
                        $_SESSION['msg_success'] = SUC05;
                        debug('セッションの変数の中身：'.print_r($_SESSION,true));
                        header("Location:mypage.php"); //マイページへ遷移
                    }
                }catch(Exception $e){
                    error_log('エラー発生：'.$e->getMessage());
                    $err_msg['common'] = MSG07;
                }
            }
        }
        }
        }
?>


<?php
$siteTitle = 'ユーザー登録';
include('link/head.php');
?>
<?php
$siteTitle;
include('link/header.php');
?>
       
       <div class="main">
        <h1>ポートフォリオ/ユーザー登録</h1>
         <div class="form-area">
          <form action="" method="post" class="form">
          <div class="err_msg">
              <?php
              if(!empty($err_msg['common'])) echo $err_msg['common'];
              ?>
          </div>
          
          
        <!--名前-->
        <!---------------------------------------------------------------------------------------->
           
           <label class="fullname">ナマエ：<span style="font-size:13px;color: fuchsia;">※カタカナ</span>
               <div class="wider">
               <input type="text" name="katakana" class="name" placeholder="ヤマダ" style="" value="<?php if(!empty($_POST['katakana'])) echo $_POST['katakana']; ?>">
               
               <input type="text" name="katakana" class="name" placeholder="タロウ" style="" value="<?php if(!empty($_POST['katakana'])) echo $_POST['katakana']; ?>">
               </div>
               
               <span class="err_msg"><?php if(!empty($err_msg['katakana'])) echo $err_msg['katakana']; ?></span>
            </label>
            
            <label>名前：<span style="font-size:13px;color: fuchsia;">※漢字</span>
              <div class="wider">
               <input type="text" name="kanjif" placeholder="山田" style="" value="<?php if(!empty($_POST['kanjif'])) echo $_POST['kanjif']; ?>">
               
               <input type="text" name="kanjil" placeholder="太郎" style="" value="<?php if(!empty($_POST['kanjil'])) echo $_POST['kanjil']; ?>">
                </div>
               <span class="err_msg"><?php if(!empty($err_msg['kanjif'])) echo $err_msg['kanjif']; ?></span>
               <span class="err_msg"><?php if(!empty($err_msg['kanjil'])) echo $err_msg['kanjil']; ?></span>
            </label>
            
        <!--携帯-->
        <!---------------------------------------------------------------------------------------->
            
            <label>携帯電話：<span style="font-size:13px; color: fuchsia;">※半角数字・ハイフンなし</span>
           <input type="text" name="tel" placeholder="09011112222" value="<?php if(!empty($_POST['tel'])) echo $_POST['tel']; ?>">
           <span class="err_msg"><?php if(!empty($err_msg['tel'])) echo $err_msg['tel']; ?></span>
            </label>
        
        <!--email-->
        <!---------------------------------------------------------------------------------------->
            
           <label>Email：
           
           <input type="text" name="email" placeholder="email" value="<?php if(!empty($_POST['email'])) echo $_POST['email']; ?>">
           <span class="err_msg"><?php if(!empty($err_msg['email'])) echo $err_msg['email']; ?></span>
            </label>
            
        <!--pass-->
        <!---------------------------------------------------------------------------------------->
           
           <label>パスワード：<span style="font-size:13px;color: fuchsia;">※英数半角6文字以上</span>
           
           <input type="text" name="pass" placeholder="1111abc" value="<?php if(!empty($_POST['pass'])) echo $_POST['pass']; ?>">
           <span class="err_msg"><?php if(!empty($err_msg['pass'])) echo $err_msg['pass']; ?></span>
            </label>
            
            <label>パスワード（確認）：
           
           <input type="text" name="pass_re" placeholder="1111abc" value="<?php if(!empty($_POST['pass_re'])) echo $_POST['pass_re']; ?>">
           <span class="err_msg"><?php if(!empty($err_msg['pass_re'])) echo $err_msg['pass_re']; ?></span>
            </label>
              
         <!--ボタン-->
         <!---------------------------------------------------------------------------------------->
          <div class="btn">
           <input type="submit" name="submit" class="btn-mid" value="登録">
           
           <input type="reset" name="reset" class="btn-mid" value="リセット">
          </div>
           
       </form>
        </div>
       </div>
<?php
    include('link/footer.php');
?>
