<?php

require('link/function.php');

debug('----------------------------------');
debug('マイページ');
debug('----------------------------------');
debugLogStart();

//ログイン認証
require('link/auth.php');

//画面表示用データ取得
$u_id = $_SESSION['user_id'];
//DBから日記データを取得
$productData = getMyProduct($u_id);
//DBからカテゴリーデータを取得
$categoryData = getCategory();

debug('日記データ：'.print_r($productData,true));
debug('カテゴリデータ：'.print_r($categoryData,true));

debug('画面表示終了');
?>

<!-- ↓↓htmlスタート↓↓-->
<?php
$siteTitle = 'マイページ';
require('link/head.php');
?>

<?php
$siteTitle;
require('link/header.php');
?>
    <p id="js-show-msg" style="display:none;" class="msg-slide">
        <?php echo getSessionFlash('msg_success'); ?>
    </p>
    
    <div class="main">
        <h1>ポートフォリオ/マイページ</h1>
        <section id="main-form">
           <div class="form-area">
            <div class="form">
            <h2 class="diary">日記一覧</h2>
            <?php
            if(!empty($productData && $categoryData)):
            foreach($productData as $key => $val):
            ?>
            
            <div class="panel-head">
                <p class="panel-title"><?php echo '・'.sanitize($val['title']); ?></p>
                <p class="panel-title"><?php echo sanitize($val['category_name']); ?></p>
            </div>
            
                <div class="panel-body">
                   <pre><p class="panel-text"><?php echo sanitize($val['text']); ?></p></pre>
                </div>
                <div class="panel-footer">
                    <img src="<?php echo showimg(sanitize($val['pic'])); ?>" alt="<?php echo sanitize($val['title']); ?>">
                </div>
             <a href="main.php<?php echo (!empty(appendGetParam())) ? appendGetParam().'&m_id='.$val['id'] : '?m_id'.$val['id']; ?>" class="panel">
            <span style="padding: 10px;margin-left: 10px; color: #800080">編集する↑</span>
            </a>
            <?php
            endforeach;
            endif;
            ?>
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
       
        <script src="js/jquery-3.4.1.min.js"></script>
        <script src="js/jquery.validate.min.js"></script>
        <script src="js/valid.js"></script>
        <script src="js/main.js"></script>