$(function(){
    //文字数カウント
    $('.count-set').keyup(function(){
        $('.show-count').text($(this).val().length);
    });


//スライド
    var $jsShowMsg = $('#js-show-msg');
    var msg = $jsShowMsg.text();
    if(msg.replace(/^[\s ]+|[\s ]+$/g, "").length){
        $jsShowMsg.slideToggle('slow');
        setTimeout(function(){$jsShowMsg.slideToggle('slow'); }, 5000);
    }
    
    
    //画像ライブプレビュー
    var $droparea = $('.pic-drop');
    var $fileinput = $('.input-file');
    $droparea.on('dragover',function(e){
        e.stopPropagation();
        e.preventDefault();
            $(this).css('border','3px #ccc dashed');
        });
    $droparea.on('dragleave',function(e){
        e.stopPropagation();
        e.preventDefault();
        $(this).css('border','none');
    });
    $fileinput.on('change',function(e){
        $droparea.css('border','none');
        var file=this.files[0],
            $img = $(this).siblings('.prev-img'),
            fileReader = new FileReader();
         
        fileReader.onload = function(event){
            $img.attr('src',event.target.result).show();
        };
        fileReader.readAsDataURL(file);
    });
    
    //天気画像選択
    $('#weather').change(function(){
        var opt = $('#weather option:selected');
        var url = opt.css('background-image');
        $('#weather').css('background-image',url);
    });
    
    
});


