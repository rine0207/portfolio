<?php

require('link/function.php');

debug('------------------------------------------');
debug('プロフィール編集');
debug('------------------------------------------');

//ログイン認証
require('link/auth.php');


$dbFormData = getUser($_SESSION['user_id']);

debug('dbFormDataの変数：'.print_r($dbFormData,true));

//postされていた場合
if(!empty($_POST)){
    debug('POST送信があります');
    debug('POST情報：'.print_r($_POST,true));
    
    //ユーザー情報代入
    $tel = $_POST['tel'];
    $zip = (!empty($_POST['zip'])) ? $_POST['zip'] : 0; //バリデーションに引っかかるため空で送信されてきたら０をいれる
    $addr = $_POST['addr'];
    $age = $_POST['age'];
    $email = $_POST['email'];

//DBの情報と入力情報が異なる場合にバリデーションを行う
if($dbFormData['tel'] !== $tel){
    //tel形式バリデーション
    Telkeisiki($tel,'tel');
}

if($dbFormData['addr'] !== $addr){
    //最大文字数チェック
    Maxlen($addr,'addr');
}

if((int)$dbFormData['zip'] !== $zip){ //dbデータをintに変換
    //郵便番号形式チェック
    Zipkeisiki($zip,'zip');
}

if($dbFormData['age'] !== $age){
    //年齢半角形式チェック
    Hankakuage($age,'age');
}

if($dbFormData['email'] !== $email){
    //未入力チェック
    Minyuuryoku($email,'email');
    //最大文字数チェック
    Maxlen($email,'email');
    //形式チェック
    Keisiki($email,'email');
    //重複チェック
    if(empty($err_msg['email'])){
        Jufuku($email);
    }
}

if(empty($err_msg)){
    debug('バリデーション通過');
    
    try{
    //例外処理
    $dbh = dbConnect();
    //sql文作成
    $sql = 'UPDATE users SET tel = :tel,zip = :zip,addr = :addr,age = :age,email = :email WHERE id = :u_id';
    $data = array(':tel'=>$tel,':zip'=>$zip,':addr'=>$addr,':age'=>$age,':email'=>$email,':u_id' => $dbFormData['id']);
    //クエリ実行
    $stmt = queryPost($dbh,$sql,$data);
    
    //成功の場合
    if($stmt){
        $_SESSION['msg_success'] = SUC02;
        debug('クエリ成功');
        debug('マイページ遷移');
        header("Location:mypage.php"); //マイページへ
    }
    }catch(Exception $e){
        error_log('エラー発生：'.$e->getMessage());
        $err_msg['common'] = MSG07;
    }
}
}
debug('画面表示終了');

?>
<?php
$siteTitle = 'プロフィール編集';
require('link/head.php');
?>

<!-- メインコンテンツ -->
<?php
$siteTitle;
require('link/header.php');
?>
   <!--メニュー-->
   <div class="main">
       <h1>ポートフォリオ/プロフィール編集</h1>
       <section id="main-form">
           <div class="form-area">
               <form action="" method="post" class="form">
                   <div class="area-msg">
                       <?php if(!empty($err_msg['common'])) echo $err_msg['common']; ?>
                   </div>
                   
                   <label class="<?php if(!empty($err_msg['tel'])) echo 'err'; ?>">
                       TEL<span style="font-size:12px;">※半角数字・ハイフンなし</span>
                       <input type="text" name="tel" placeholder="08011112222" value="<?php echo getFormData('tel'); ?>">
                   </label>
                       <div class="area-msg">
                           <?php if(!empty($err_msg['tel'])) echo $err_msg['tel']; ?>
                       </div>
                    <label class="<?php if(!empty($err_msg['zip'])) echo 'err' ?>">
                        郵便番号<span style="font-size:12px;">※ハイフンなし</span>
                        <input type="text" name="zip" value="<?php if(!empty(getFormData('zip'))){echo getFormData('zip');} ?>">
                    </label>
                    <div class="area-msg">
                        <?php if(!empty($err_msg['zip'])) echo $err_msg['zip']; ?>
                    </div>
                    <label class="<?php if(!empty($err_msg['addr'])) echo 'err' ?>">
                        住所
                        <input type="text" name="addr" value="<?php echo getFormData('addr'); ?>">
                   </label>
                    <div class="area-msg">
                        <?php if(!empty($err_msg['addr'])) echo $err_msg['addr']; ?>
                    </div>
                    <label style="text-align:left;" class="<?php if(!empty($err_msg['age'])) echo 'err' ?>">
                        年齢
                        <input type="number" name="age" value="<?php echo getFormData('age'); ?>">
                    </label>
                    <div class="area-msg">
                        <?php if(!empty($err_msg['age'])) echo $err_msg['age']; ?>
                    </div>
                    <label class="<?php if(!empty($err_msg['email'])) echo 'err'; ?>">
                        email
                        <input type="text" name="email" value="<?php echo getFormData('email') ?>">
                    </label>
                    <div class="area-msg">
                        <?php if(!empty($err_msg['email'])) echo $err_msg['email']; ?>
                    </div>
                    <div class="btn">
                        <input type="submit" class="btn-mid" value="変更する">
                    </div>
               </form>
           </div>
       </section>
<?php require('link/sidebar.php'); ?>
   </div>
<?php require('link/footer.php'); ?>

