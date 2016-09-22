<?php

/**
 * Enter description here...
 *
 * example：
 * <pre>
 *
 * </pre>
 *
 * @name QLib_Utils_XHProf
 * @package QLib.Utils.XHProf
 */
class QLib_Utils_XHProf {
    
    /**
     * 
     * 
     * 
     * XHPROF_FLAGS_CPU 分析结果中添加 CPU 数据
       XHPROF_FLAGS_MEMORY 分析结果中添加内存数据
       XHPROF_FLAGS_NO_BUILTINS 跳过 PHP 内置函数
     * 
     * 
     * xhprof_enable(XHPROF_FLAGS_NO_BUILTINS);
       xhprof_enable(XHPROF_FLAGS_CPU + XHPROF_FLAGS_MEMORY);
       xhprof_enable(XHPROF_FLAGS_NO_BUILTINS | XHPROF_FLAGS_CPU | XHPROF_FLAGS_MEMORY);
     * 
     */
    public static function start(){
        xhprof_enable(XHPROF_FLAGS_NO_BUILTINS); //加上这个参数可以使得xhprof显示cpu和内存相关的数据。
    }
    
     /**
     * 访问http://debug.localhost.org/index.php?run=$run_id&source=test就能够看到一个统计列表了。
     * @return type
     */
    public static function end(){
        $data = xhprof_disable();
        //得到统计数据之后，以下的工作就是为页面显示做准备。
        $xhprof_root = "/data/www/xhprof"; //这里填写的就是你的xhprof的路径
        include_once $xhprof_root . "/xhprof_lib/utils/xhprof_lib.php";
        include_once $xhprof_root . "/xhprof_lib/utils/xhprof_runs.php";
        $xhprof_runs = new XHprofRuns_Default();
        $run_id = $xhprof_runs->save_run($data, "test"); //第二个参数在接下来的地方作为命名空间一样的概念来使用
        $url = "http://debug.9939.com/?run={$run_id}&source=test";
        echo "<a href='".$url."' target='_blank'>".$run_id."</a>";
        exit;
    }

}
