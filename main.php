<?php

require('link/function.php');

debug('---------------------');
debug('メインコンテンツ');
debug('---------------------');
debugLogStart();

//ログイン認証
require('link/auth.php');


//getデータ格納
$m_id = (!empty($_GET['m_id'])) ? $_GET['m_id'] : '';
//DBから商品データを取得
$dbFormData = (!empty($m_id)) ? getProduct($_SESSION['user_id'],$m_id) : '';
//新規登録画面か編集画面か判別用フラグ
$edit_flg = (empty($dbFormData)) ? false : true;

//DBからカテゴリデータを取得
$dbCategory = getCategory();
debug('情報ID：'.$m_id);
debug('フォーム用DBデータ：'.print_r($dbFormData,true));
debug('カテゴリデータ：'.print_r($dbCategory,true));


//パラメータ改ざんチェック
if(!empty($m_id) && empty($dbFormData)){
    debug('GETパラメータの情報idが違います。マイページ遷移');
    header("Location:mypage.php");
}

//POST送信
if(!empty($_POST)){
    debug('POST送信があります');
    debug('POST情報：'.print_r($_POST,true));
    debug('FILE情報：'.print_r($_FILES,true));
    
    //ユーザー情報代入
    $title = $_POST['title'];
    $category = $_POST['weather'];
    $text = $_POST['text'];
    //画像アップロード・パス格納
    $pic = (empty($_FILES['pic']['title'])) ? uploadImg($_FILES['pic'],'pic') : '';
    //画像をPOSTしてないがすでに登録されている場合、DBのパスを入れる
    $pic = (!empty($pic) && !empty($dbFormData['pic'])) ? $dbFormData['pic'] : $pic;
    
    //バリデーションはjsで行うため省略


if(empty($err_msg)){
    debug('バリデーションok');
    
    try{
        $dbh = dbConnect();
        //編集の場合はUPDATE、新規登録の場合はINSERT
        if($edit_flg){
            debug('DB更新');
            $sql = 'UPDATE main SET title = :title,category_name = :weather,text = :text,pic = :pic WHERE user_id = :u_id AND id=:m_id';
            $data = array(':titlt'=>$title,':weather'=>$category,':text'=>$text,':pic'=>$pic,':u_id'=>$_SESSION['user_id'],':m_id'=>$m_id);
        }else{
            debug('DB新規登録');
            $sql = 'INSERT INTO main(title,category_name,text,pic,user_id,create_date) VALUES (:title,:weather,:text,:pic,:u_id,:date)';
            $data = array(':title'=>$title,':weather'=>$category,':text'=>$text,':pic'=>$pic,':u_id'=>$_SESSION['user_id'],':date'=>date('Y-m-d H:i:s'));
        }
        debug('SQL:'.$sql);
        debug('データ:'.print_r($data,true));
        //クエリ実行
        $stmt = queryPost($dbh,$sql,$data);
        
        //成功の場合
        if($stmt){
            $_SESSION['msg_success'] = SUC04;
            debug('$_SESSIONの中身：'.print_r($_SESSION,true));
            debug('クエリ成功');
            debug('マイページ遷移');
            header("Location:mypage.php");
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
$siteTitle = (!$edit_flg) ? '日記投稿' : '日記編集';
require('link/head.php');
?>
<?php
$siteTitle;
require('link/header.php');
?>
       
       <div class="main">
        <h1>ポートフォリオ/日記</h1>
          <section id="main-form">
          <div class="form-area">
           <form action="" method="post" enctype="multipart/form-data" class="form">
              <div class="arae-msg">
              </div>
               <label>タイトル：
               <input type="text" name="title" class="textbox">
               </label>
               
               <label>天気：
               <select name="weather" id="weather">
                   <option value="0" <?php if(getFormData('weather') == 0){ echo 'selected';} ?> >選択してください</option>
                   <?php
                        foreach($dbCategory as $key=>$val){
                   ?>
                   <option value="<?php echo $val['name']; ?>"<?php if(getFormData('weather')==$val['name']){echo 'selected';} ?> >
                       <?php echo $val['name']; ?>
                   </option>
                   <?php } ?>
               </select>
               </label>
               
               <label>本文：
               <pre><textarea name="text" id="js-count" class="count-set" minlength="0" maxlength="400"></textarea></pre>
               <div class="count"><span class="show-count">0</span>/400
               </div>
               </label>
               
               <div>
               <div class="pic-drop">
               <div style="font-size:20px;">画像：<span style="font-size:15px;">ドラッグ＆ドロップ↓</span></div>
               <label class="area-drop">
                <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
                <input type="file" name="pic" class="input-file">
                <img src="<?php echo getFormData('pic'); ?>" alt='' class="prev-img" style="<?php if(empty(getFormData('pic'))) echo 'display:none;' ?>">
                    
               </label>
               </div>
               </div>
               
               <div class="btn">
                  <input type="submit" name="submit" class="btn-mid" value="投稿する">
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

        <script src="js/jquery-3.4.1.min.js"></script>
        <script src="js/jquery.imageselect.js"></script>
        <script src="js/jquery.validate.min.js"></script>
        <script src="js/valid.js"></script>
        <script src="js/main.js"></script>