<html>
    <head>
        <style>
            .Huifold .item{ position:relative}
            .Huifold .item h4{margin:0;font-weight:bold;position:relative;border-top: 1px solid #fff;font-size:15px;line-height:22px;padding:7px 10px;background-color:#eee;cursor:pointer;padding-right:30px}
            .Huifold .item h4 b{position:absolute;display: block; cursor:pointer;right:10px;top:7px;width:16px;height:16px; text-align:center; color:#666}
            .Huifold .item .info{display:none;padding:10px}
        </style>
    </head>
    <body>
        <ul id="Huifold1" class="Huifold">
            <?php
            /**
             * Created by PhpStorm.
             * User: Joql
             * Date: 2018/1/17
             * Time: 20:59
             */

            //format("gd_info",print_r(gd_info()));
            //********************
            // 创建一个 200X200 的图像
            $img  =  imagecreatetruecolor ( 200 ,  200 );
            // 分配颜色
            $white  =  imagecolorallocate ( $img ,  255 ,  255 ,  255 );
            $black  =  imagecolorallocate ( $img ,  0 ,  0 ,  0 );
            // 画一个黑色的圆
            imagearc ( $img ,  100 ,  100 ,  150 ,  150 ,  0 ,  360 ,  $black );
            // 将图像输出到浏览器
            header ( "Content-type: image/png" );
            imagepng ( $img );
            // 释放内存
            imagedestroy ( $img );


            function format($title,$content){
                echo <<<EOD
                  <li class="item">
                    <h4>$title<b>+</b></h4>
                    <div class="info">$content</div>
                  </li>
EOD;

            }
            ?>
        </ul>
    </body>
    <script type="text/javascript" src="/public/js/jquery.min.js"></script>
    <script>
        jQuery.Huifold = function(obj,obj_c,speed,obj_type,Event){
            if(obj_type == 2){
                $(obj+":first").find("b").html("-");
                $(obj_c+":first").show()}
            $(obj).bind(Event,function(){
                if($(this).next().is(":visible")){
                    if(obj_type == 2){
                        return false}
                    else{
                        $(this).next().slideUp(speed).end().removeClass("selected");
                        $(this).find("b").html("+")}
                }
                else{
                    if(obj_type == 3){
                        $(this).next().slideDown(speed).end().addClass("selected");
                        $(this).find("b").html("-")}else{
                        $(obj_c).slideUp(speed);
                        $(obj).removeClass("selected");
                        $(obj).find("b").html("+");
                        $(this).next().slideDown(speed).end().addClass("selected");
                        $(this).find("b").html("-")}
                }
            })}
    </script>
    <script>
        $(function(){
            $.Huifold("#Huifold1 .item h4","#Huifold1 .item .info","fast",1,"click"); /*5个参数顺序不可打乱，分别是：相应区,隐藏显示的内容,速度,类型,事件*/
        });
    </script>
</html>
