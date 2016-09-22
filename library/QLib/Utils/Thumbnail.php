<?php

/**
 * 缩略图处理
 *
 * example：
 * <pre>
 *
 * </pre>
 *
 * @name QLib_Utils_Thumbnail
 * @version 
 * @package QLib.Utils.Thumbnail
 * @since 1.0
 */
class QLib_Utils_Thumbnail {

    /**
     *
     * @return array
     */
    public static function getnewsize($pic_width, $pic_height, $max_width = 0, $max_height = 0) {
        $resizewidth_tag = false;
        $resizeheight_tag = false;
        $ratio = 1;
        if($max_width>0 && $max_height>0){
            if ($pic_width > $max_width || $pic_height > $max_height) {
                if ($pic_width > $max_width) {
                    $widthratio = $max_width / $pic_width;
                    $resizewidth_tag = true;
                }
                if ($pic_height > $max_height) {
                    $heightratio = $max_height / $pic_height;
                    $resizeheight_tag = true;
                }
                if ($resizewidth_tag && $resizeheight_tag) {
                    if ($widthratio < $heightratio) {
                        $ratio = $widthratio;
                    } else {
                        $ratio = $heightratio;
                    }
                }
                if ($resizewidth_tag && !$resizeheight_tag) {
                    $ratio = $widthratio;
                }
                if ($resizeheight_tag && !$resizewidth_tag) {
                    $ratio = $heightratio;
                }
            }
        }
        return array('width'=>$pic_width*$ratio,'height'=>$pic_height*$ratio);
    }

}
