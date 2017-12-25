<?php
/**
 * Created by PhpStorm.
 * User: Joql
 * Date: 2017/9/11
 * Time: 22:08
 */
require './init.php';
$str= <<<END
&lt;p&gt;&lt;img src=&quot;https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQqupj97MprdYb094Ji19vT9z-ystHy6zlONxVVouwV93BFja1f&quot; alt=&quot;Joql博客&quot;/&gt;&lt;/p&gt;
&lt;p&gt;&lt;img src=&quot;https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQqupj97MprdYb094Ji19vT9z-ystHy6zlONxVVouwV93BFja1f&quot; alt=&quot;Joql博客&quot;/&gt;&lt;/p&gt;
END;



//$preg='/\/Upload\/image\/ueditor\/\d*\/\d*\.[jpg|jpeg|png|bmp|gif]*/i';
$preg='/img src=&quot;(.*?)&quot;/i';
preg_match_all($preg, $str,$data);
var_dump(next($data));
