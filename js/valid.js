$('.form').validate({
    rules: {
        title:{
            required: true
        },
        text:{
            required: true
        }
    },
    
    
    messages: {
        title:{
            required: "入力してください"
        },
        text:{
            required: "入力してください"
        }
        
    },
    //attrメソッドで属性→'name'をとってくる
    errorPlacement: function(err,element){
        if(element.attr('name')=='sei'){
            err.insertAfter('#err_msg');
        }else{
            err.insertAfter(element);
        }
    }

});
