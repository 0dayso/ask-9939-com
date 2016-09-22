<?php

/**
 * Enter description here...
 * 
 * example：
 * <pre>
 * 
 * </pre>
 * 
 * @name QLib_Config_Site_Config
 * @package QLib.Config.Site.Config
 * @since 1.0
 */
class QConfigs_Site_Config {
    
    /**
	 * 模板版本 (主要用于头模板)
	 *
	 * @var Integer
	 */
	public static $template_version = 'default';

    //网站搜索配置
    public static $site_search_map = array(
        'baojian' => '17952251780120224976',
        'meirong' => '17386374947417716139',
        'baby' => '14180033748433892341',
        'man' => '13016033918216013041',
        'pianfang' => '16491119445869474020',
        'jianfei' => '12924095439294522188',
        'food' => '13267932654460177676',
        'jianshen' => '13588440307928992208',
        'liangxing' => '11415837046503984220',
        'nvxing' => '5922652107157267104',
        'zhongyi' => '17718288175228177515',
        'yaopin' => '12780083587161790468',
        'xinli' => '6710535021805148006',
        'zhengxing' => '15685383618608849492',
        'tijian' => '16759023474432677891',
        'pic' => '149676104683542016',
        'news' => '10902863911255591865',
        'home' => '7753950362912553037'
    );
    //网站模板配置
    public static $site_template_map = array(
        '1836' => array(
            'v' => 'v1',
            'file_save_dir' => 'meirong', //美容
            'tpl_include_paths' => array('common/v2', 'meirong/v2'),
            'tpl_paths' => array(
                'index' => 'tpl/channel/meirong/v1/index.phtml',
                'detail' => 'tpl/channel/meirong/v1/detail.phtml',
                'morelist' => 'tpl/channel/meirong/v1/morelist.phtml',
                'list' => 'tpl/channel/meirong/v1/list.phtml',
                'list_bottom' => 'tpl/public_list/meirong/v1/list_bottom.phtml',
                'list_top' => 'tpl/public_list/meirong/v1/list_top.phtml',
                'detail_top' => 'tpl/public_list/meirong/v1/detail_top.phtml',
            ),
            'index_css_include_path' => array(
                'http://www.9939.com/9939/res/base/v1/css/common.css',
                'http://www.9939.com/9939/res/base/v1/css/hf.css',
                'http://www.9939.com/9939/res/meirong/v1/css/index.css'
            ),
            'list_css_include_path' => array(
                'http://www.9939.com/9939/res/meirong/v2/css/main.css',
                'http://www.9939.com/9939/res/meirong/v2/css/list.css',
                'http://www.9939.com/9939/res/base/v1/css/bd_search.css'
            )
        ),
        '1979' => array(
            'v' => 'v1',
            'file_save_dir' => 'baby', //母婴
            'not_include' => array('keywords'),
            'tpl_include_paths' => array('common/v1', 'baby/v2'),
            'tpl_paths' => array(
                'index' => 'tpl/channel_child.phtml',
                'detail' => 'tpl/channel/baby/v1/detail.phtml',
                'morelist' => 'tpl/channel/baby/v1/morelist.phtml',
                'list' => 'tpl/channel/baby/v1/list.phtml',
                'list_bottom' => 'tpl/public_list/baby/v1/list_bottom.phtml',
                'list_top' => 'tpl/public_list/baby/v1/list_top.phtml',
                'detail_top' => 'tpl/public_list/baby/v1/detail_top.phtml',
            ),
            'index_css_include_path' => array(
                'http://www.9939.com/9939/res/base/v1/css/common.css',
                'http://www.9939.com/9939/res/base/v1/css/hf.css',
                'http://www.9939.com/9939/res/baby/v1/css/index.css'
            ),
            'list_css_include_path' => array(
                'http://www.9939.com/9939/res/baby/v2/css/main.css',
                'http://www.9939.com/9939/res/baby/v2/css/list.css',
                'http://www.9939.com/9939/res/base/v1/css/bd_search.css'
            )
        ),
        '2388' => array(
            'v' => 'v1',
            'file_save_dir' => 'zy', //**********************中医
            'tpl_include_paths' => array('common/v1', 'zhongyi/v2'),
            'tpl_paths' => array(
                'index' => 'tpl/channel_zhongyi.phtml',
                'detail' => 'tpl/channel/zhongyi/v1/detail.phtml',
                'morelist' => 'tpl/channel/zhongyi/v1/morelist.phtml',
                'list' => 'tpl/channel/zhongyi/v1/list.phtml',
                'list_bottom' => 'tpl/public_list/zhongyi/v1/list_bottom.phtml',
                'list_top' => 'tpl/public_list/zhongyi/v1/list_top.phtml',
                'detail_top' => 'tpl/public_list/zhongyi/v1/detail_top.phtml',
            ),
            'index_css_include_path' => array(
                'http://www.9939.com/9939/res/base/v1/css/common.css',
                'http://www.9939.com/9939/res/base/v1/css/hf.css',
                'http://www.9939.com/9939/res/zhongyi/v1/css/index.css'
            ),
            'list_css_include_path' => array(
                'http://www.9939.com/9939/res/zhongyi/v2/css/main.css',
                'http://www.9939.com/9939/res/zhongyi/v2/css/list.css',
                'http://www.9939.com/9939/res/base/v1/css/bd_search.css'
            )
        ),
        '2791' => array(
            'v' => 'v1',
            'file_save_dir' => 'ys', //饮食
            'tpl_include_paths' => array('common/v1', 'food/v2'),
            'tpl_paths' => array(
                'index' => 'tpl/channel_yinshi.phtml',
                'detail' => 'tpl/channel/food/v1/detail.phtml',
                'morelist' => 'tpl/channel/food/v1/morelist.phtml',
                'list' => 'tpl/channel/food/v1/list.phtml',
                'list_bottom' => 'tpl/public_list/food/v1/list_bottom.phtml',
                'list_top' => 'tpl/public_list/food/v1/list_top.phtml',
                'detail_top' => 'tpl/public_list/food/v1/detail_top.phtml',
            ),
            'index_css_include_path' => array(
                'http://www.9939.com/9939/res/base/v1/css/common.css',
                'http://www.9939.com/9939/res/base/v1/css/hf.css',
                'http://www.9939.com/9939/res/food/v1/css/index.css'
            ),
            'list_css_include_path' => array(
                'http://www.9939.com/9939/res/food/v2/css/main.css',
                'http://www.9939.com/9939/res/food/v2/css/list.css',
                'http://www.9939.com/9939/res/base/v1/css/bd_search.css'
            )
        ),
        '1808' => array(
            'v' => 'v1',
            'file_save_dir' => 'male', //男性
            'tpl_include_paths' => array('common/v2', 'man/v2'),
            'tpl_paths' => array(
                'index' => 'tpl/channel/man/v1/index.phtml',
                'detail' => 'tpl/channel/man/v1/detail.phtml',
                'morelist' => 'tpl/channel/man/v1/morelist.phtml',
                'list' => 'tpl/channel/man/v1/list.phtml',
                'list_bottom' => 'tpl/public_list/man/v1/list_bottom.phtml',
                'list_top' => 'tpl/public_list/man/v1/list_top.phtml',
                'detail_top' => 'tpl/public_list/man/v1/detail_top.phtml',
            ),
            'index_css_include_path' => array(
                'http://www.9939.com/9939/res/base/v1/css/common.css',
                'http://www.9939.com/9939/res/base/v1/css/hf.css',
                'http://www.9939.com/9939/res/man/v1/css/index.css'
            ),
            'list_css_include_path' => array(
                'http://www.9939.com/9939/res/man/v2/css/main.css',
                'http://www.9939.com/9939/res/man/v2/css/list.css',
                'http://www.9939.com/9939/res/base/v1/css/bd_search.css'
            )
        ),
        '2094' => array(
            'v' => 'v1',
            'file_save_dir' => 'jf', //减肥
            'tpl_include_paths' => array('common/v2', 'fitness/v2'),
            'tpl_paths' => array(
                'index' => 'tpl/channel_jianfei.phtml',
                'detail' => 'tpl/channel/fitness/v1/detail.phtml',
                'morelist' => 'tpl/channel/fitness/v1/morelist.phtml',
                'list' => 'tpl/channel/fitness/v1/list.phtml',
                'list_bottom' => 'tpl/public_list/fitness/v1/list_bottom.phtml',
                'list_top' => 'tpl/public_list/fitness/v1/list_top.phtml',
                'detail_top' => 'tpl/public_list/fitness/v1/detail_top.phtml',
            ),
            'index_css_include_path' => array(
                'http://www.9939.com/9939/res/base/v1/css/common.css',
                'http://www.9939.com/9939/res/base/v1/css/hf.css',
                'http://www.9939.com/9939/res/fitness/v2/css/index.css'
            ),
            'list_css_include_path' => array(
                'http://www.9939.com/9939/res/fitness/v2/css/main.css',
                'http://www.9939.com/9939/res/fitness/v2/css/list.css',
                'http://www.9939.com/9939/res/base/v1/css/bd_search.css'
            )
        ),
        '2464' => array(
            'v' => 'v2',
            'file_save_dir' => 'xa', //************两性
            'tpl_include_paths' => array('common/v2', 'lx/v2'),
            'tpl_paths' => array(
                'index' => 'tpl/channel/lx/v1/index.phtml',
                'detail' => 'tpl/channel/lx/v1/detail.phtml',
                'morelist' => 'tpl/channel/lx/v1/morelist.phtml',
                'list' => 'tpl/channel/lx/v1/list.phtml',
                'list_bottom' => 'tpl/public_list/lx/v1/list_bottom.phtml',
                'list_top' => 'tpl/public_list/lx/v1/list_top.phtml',
                'detail_top' => 'tpl/public_list/lx/v1/detail_top.phtml',
            ),
            'index_css_include_path' => array(
                'http://www.9939.com/9939/res/base/v1/css/common.css',
                'http://www.9939.com/9939/res/base/v1/css/hf.css',
                'http://www.9939.com/9939/res/lx/v2/css/index.css'
            ),
            'list_css_include_path' => array(
                'http://www.9939.com/9939/res/lx/v2/css/main.css',
                'http://www.9939.com/9939/res/lx/v2/css/list.css',
                'http://www.9939.com/9939/res/base/v1/css/bd_search.css'
            )
        ),
        '11470' => array(
            'v' => 'v1',
            'file_save_dir' => 'nvxing', //女性
            'tpl_include_paths' => array('common/v2', 'lady/v2'),
            'tpl_paths' => array(
                'index' => 'tpl/channel/lady/v1/index.phtml',
                'detail' => 'tpl/channel/lady/v1/detail.phtml',
                'morelist' => 'tpl/channel/lady/v1/morelist.phtml',
                'list' => 'tpl/channel/lady/v1/list.phtml',
                'list_bottom' => 'tpl/public_list/lady/v1/list_bottom.phtml',
                'list_top' => 'tpl/public_list/lady/v1/list_top.phtml',
                'detail_top' => 'tpl/public_list/lady/v1/detail_top.phtml',
            ),
            'index_css_include_path' => array(
                'http://www.9939.com/9939/res/base/v1/css/common.css',
                'http://www.9939.com/9939/res/base/v1/css/hf.css',
                'http://www.9939.com/9939/res/lady/v1/css/index.css'
            ),
            'list_css_include_path' => array(
                'http://www.9939.com/9939/res/lady/v2/css/main.css',
                'http://www.9939.com/9939/res/lady/v2/css/list.css',
                'http://www.9939.com/9939/res/base/v1/css/bd_search.css'
            )
        ),
        '2266' => array(
            'v' => 'v1',
            'file_save_dir' => 'pf', //偏方
            'tpl_include_paths' => array('common/v2', 'pianfang/v2'),
            'tpl_paths' => array(
                'index' => 'tpl/channel/pianfang/v1/index.phtml',
                'detail' => 'tpl/channel/pianfang/v1/detail.phtml',
                'morelist' => 'tpl/channel/pianfang/v1/morelist.phtml',
                'list' => 'tpl/channel/pianfang/v1/list.phtml',
                'list_bottom' => 'tpl/public_list/pianfang/v1/list_bottom.phtml',
                'list_top' => 'tpl/public_list/pianfang/v1/list_top.phtml',
                'detail_top' => 'tpl/public_list/pianfang/v1/detail_top.phtml',
            ),
            'index_css_include_path' => array(
                'http://www.9939.com/9939/res/base/v1/css/common.css',
                'http://www.9939.com/9939/res/base/v1/css/hf.css',
                'http://www.9939.com/9939/res/pianfang/v2/css/index.css'
            ),
            'list_css_include_path' => array(
                'http://www.9939.com/9939/res/pianfang/v2/css/main.css',
                'http://www.9939.com/9939/res/pianfang/v2/css/list.css',
                'http://www.9939.com/9939/res/base/v1/css/bd_search.css'
            )
        ),
        '2711' => array(
            'v' => 'v1',
            'file_save_dir' => 'bj', //保健
            'tpl_include_paths' => array('common/v2', 'baojian/v1'),
            'tpl_paths' => array(
                'index' => 'tpl/channel/baojian/v1/index.phtml',
                'detail' => 'tpl/channel/baojian/v1/detail.phtml',
                'morelist' => 'tpl/channel/baojian/v1/morelist.phtml',
                'list' => 'tpl/channel/baojian/v1/list.phtml',
                'list_bottom' => 'tpl/public_list/baojian/v1/list_bottom.phtml',
                'list_top' => 'tpl/public_list/baojian/v1/list_top.phtml',
                'detail_top' => 'tpl/public_list/baojian/v1/detail_top.phtml',
            ),
            'index_css_include_path' => array(
                'http://www.9939.com/9939/res/baojian/v1/css/main.css',
                'http://www.9939.com/9939/res/baojian/v1/css/index.css'
            ),
            'list_css_include_path' => array(
                'http://www.9939.com/9939/res/baojian/v1/css/main.css',
                'http://www.9939.com/9939/res/baojian/v1/css/list.css',
                'http://www.9939.com/9939/res/base/v1/css/bd_search.css'
            )
        ),
        '2280' => array(
            'v' => 'v1',
            'file_save_dir' => 'jianshen', //健身
            'tpl_include_paths' => array('common/v1', 'jianshen/v2'),
            'tpl_paths' => array(
                'index' => 'tpl/channel_jianshen.phtml',
                'detail' => 'tpl/channel/jianshen/v1/detail.phtml',
                'morelist' => 'tpl/channel/jianshen/v1/morelist.phtml',
                'list' => 'tpl/channel/jianshen/v1/list.phtml',
                'list_bottom' => 'tpl/public_list/jianshen/v1/list_bottom.phtml',
                'list_top' => 'tpl/public_list/jianshen/v1/list_top.phtml',
                'detail_top' => 'tpl/public_list/jianshen/v1/detail_top.phtml',
            ),
            'index_css_include_path' => array(
                'http://www.9939.com/9939/res/base/v1/css/common.css',
                'http://www.9939.com/9939/res/base/v1/css/hf.css',
                'http://www.9939.com/9939/res/js/v1/css/index.css'
            ),
            'list_css_include_path' => array(
                'http://www.9939.com/9939/res/js/v2/css/main.css',
                'http://www.9939.com/9939/res/js/v2/css/list.css',
                'http://www.9939.com/9939/res/base/v1/css/bd_search.css'
            )
        ),
        '11430' => array(
            'v' => 'v1',
            'file_save_dir' => 'zx', //整形
            'tpl_include_paths' => array('common/v1', 'zx/v1'),
            'tpl_paths' => array(
                'index' => 'tpl/channel_zx.phtml',
                'detail' => 'tpl/channel/zx/v1/detail.phtml',
                'morelist' => 'tpl/channel/zx/v1/morelist.phtml',
                'list' => 'tpl/channel/zx/v1/list.phtml',
                'list_bottom' => 'tpl/public_list/zx/v1/list_bottom.phtml',
                'list_top' => 'tpl/public_list/zx/v1/list_top.phtml',
                'detail_top' => 'tpl/public_list/zx/v1/detail_top.phtml',
            ),
            'index_css_include_path' => array(
                'http://www.9939.com/9939/res/base/v1/css/common.css',
                'http://www.9939.com/9939/res/base/v1/css/hf.css',
                'http://www.9939.com/9939/res/zx/v1/css/index.css'
            ),
            'list_css_include_path' => array(
                'http://www.9939.com/9939/res/zx/v1/css/main.css',
                'http://www.9939.com/9939/res/zx/v1/css/list.css',
                'http://www.9939.com/9939/res/base/v1/css/bd_search.css'
            )
        ),
        '1947' => array(
            'v' => 'v1',
            'file_save_dir' => 'xinli', //心理
            'tpl_include_paths' => array('common/v1', 'xinli/v1'),
            'tpl_paths' => array(
                'index' => 'tpl/channel_xinli.phtml',
                'detail' => 'tpl/channel/xinli/v1/detail.phtml',
                'morelist' => 'tpl/channel/xinli/v1/morelist.phtml',
                'list' => 'tpl/channel/xinli/v1/list.phtml',
                'list_bottom' => 'tpl/public_list/xinli/v1/list_bottom.phtml',
                'list_top' => 'tpl/public_list/xinli/v1/list_top.phtml',
                'detail_top' => 'tpl/public_list/xinli/v1/detail_top.phtml',
            ),
            'index_css_include_path' => array(
                'http://www.9939.com/9939/res/base/v1/css/common.css',
                'http://www.9939.com/9939/res/base/v1/css/hf.css',
                'http://www.9939.com/9939/res/xinli/v1/css/index.css'
            ),
            'list_css_include_path' => array(
                'http://www.9939.com/9939/res/xinli/v1/css/main.css',
                'http://www.9939.com/9939/res/xinli/v1/css/list.css',
                'http://www.9939.com/9939/res/base/v1/css/bd_search.css'
            )
        ),
        '9552' => array(
            'v' => 'v1',
            'file_save_dir' => 'tijian', //体检
            'tpl_include_paths' => array('common/v1', 'tijian/v1'),
            'tpl_paths' => array(
                'index' => 'tpl/channel_tiyan.phtml',
                'detail' => 'tpl/channel/tijian/v1/detail.phtml',
                'morelist' => 'tpl/channel/tijian/v1/morelist.phtml',
                'list' => 'tpl/channel/tijian/v1/list.phtml',
                'list_bottom' => 'tpl/public_list/tijian/v1/list_bottom.phtml',
                'list_top' => 'tpl/public_list/tijian/v1/list_top.phtml',
                'detail_top' => 'tpl/public_list/tijian/v1/detail_top.phtml',
            ),
            'index_css_include_path' => array(
                'http://www.9939.com/9939/res/base/v1/css/common.css',
                'http://www.9939.com/9939/res/base/v1/css/hf.css',
                'http://www.9939.com/9939/res/tijian/v1/css/index.css'
            ),
            'list_css_include_path' => array(
                'http://www.9939.com/9939/res/tijian/v1/css/main.css',
                'http://www.9939.com/9939/res/tijian/v1/css/list.css',
                'http://www.9939.com/9939/res/base/v1/css/bd_search.css'
            )
        ),
        '9687' => array(
            'v' => 'v1',
            'file_save_dir' => 'drug', //药品
            'tpl_include_paths' => array('common/v1', 'drug/v1'),
            'tpl_paths' => array(
                'index' => 'tpl/channel_yaopin.phtml',
                'detail' => 'tpl/channel/drug/v1/detail.phtml',
                'morelist' => 'tpl/channel/drug/v1/morelist.phtml',
                'list' => 'tpl/channel/drug/v1/list.phtml',
                'list_bottom' => 'tpl/public_list/drug/v1/list_bottom.phtml',
                'list_top' => 'tpl/public_list/drug/v1/list_top.phtml',
                'detail_top' => 'tpl/public_list/drug/v1/detail_top.phtml',
            ),
            'index_css_include_path' => array(
                'http://www.9939.com/9939/res/base/v1/css/common.css',
                'http://www.9939.com/9939/res/base/v1/css/hf.css',
                'http://www.9939.com/9939/res/drug/v1/css/index.css'
            ),
            'list_css_include_path' => array(
                'http://www.9939.com/9939/res/drug/v1/css/main.css',
                'http://www.9939.com/9939/res/drug/v1/css/list.css',
                'http://www.9939.com/9939/res/base/v1/css/bd_search.css'
            )
        ),
        '9456' => array(
            'v' => 'v1',
            'file_save_dir' => 'news', //新闻
            'tpl_include_paths' => array('common/v1', 'news/v1'),
            'tpl_paths' => array(
                'index' => 'tpl/channel_news.phtml',
                'detail' => 'tpl/channel/news/v1/detail.phtml',
                'morelist' => 'tpl/channel/news/v1/morelist.phtml',
                'list' => 'tpl/channel/news/v1/list.phtml',
                'list_bottom' => 'tpl/public_list/news/v1/list_bottom.phtml',
                'list_top' => 'tpl/public_list/news/v1/list_top.phtml',
                'detail_top' => 'tpl/public_list/news/v1/detail_top.phtml',
            ),
            'index_css_include_path' => array(
                'http://www.9939.com/9939/res/base/v1/css/common.css',
                'http://www.9939.com/9939/res/base/v1/css/hf.css',
                'http://www.9939.com/9939/res/news/v1/css/index.css'
            ),
            'list_css_include_path' => array(
                'http://www.9939.com/9939/res/news/v1/css/main.css',
                'http://www.9939.com/9939/res/news/v1/css/list.css',
                'http://www.9939.com/9939/res/base/v1/css/bd_search.css'
            )
        ),
        '9366' => array(
            'v' => 'v1',
            'file_save_dir' => 'jktp', //图谱
            'tpl_include_paths' => array('common/v1', 'pic/v1'),
            'tpl_paths' => array(
                'index' => 'tpl/channel_pic.phtml',
                'detail' => 'tpl/channel/pic/v1/detail.phtml',
                'morelist' => 'tpl/channel/pic/v1/morelist.phtml',
                'list' => 'tpl/channel/pic/v1/list.phtml',
                'list_bottom' => 'tpl/public_list/pic/v1/list_bottom.phtml',
                'list_top' => 'tpl/public_list/pic/v1/list_top.phtml',
                'detail_top' => 'tpl/public_list/pic/v1/detail_top.phtml',
            ),
            'index_css_include_path' => array(
                'http://www.9939.com/9939/res/base/v1/css/common.css',
                'http://www.9939.com/9939/res/base/v1/css/hf.css',
                'http://www.9939.com/9939/res/pic/v1/css/index.css'
            ),
            'list_css_include_path' => array(
                'http://www.9939.com/9939/res/pic/v1/css/main.css',
                'http://www.9939.com/9939/res/pic/v1/css/list.css',
                'http://www.9939.com/9939/res/base/v1/css/bd_search.css'
            )
        ),
        '9780' => array(
            'v' => 'v1',
            'file_save_dir' => '/bybyzt', //不孕不育
            'tpl_include_paths' => array('common/v2', 'disease/v1'),
            'tpl_paths' => array(
                'index' => 'tpl/channel_bybyzt.phtml',
                'detail' => 'tpl/channel/disease/v1/detail.phtml',
                'morelist' => 'tpl/channel/disease/v1/morelist.phtml',
                'list' => 'tpl/channel/disease/v1/list.phtml',
                'list_bottom' => 'tpl/public_new/list_bottom.phtml',
                'list_top' => 'tpl/public_new/top.phtml',
                'detail_top' => 'tpl/public_list/disease/v1/detail_top.phtml',
            ),
            'index_css_include_path' => array(
                'http://www.9939.com/9939/res/base/v1/css/common.css',
                'http://www.9939.com/9939/res/base/v1/css/hf.css',
                'http://www.9939.com/9939/res/pic/v1/css/index.css'
            ),
            'list_css_include_path' => array(
                'http://www.9939.com/9939/res/disease/v1/css/main.css',
                'http://www.9939.com/9939/res/disease/v1/css/list.css',
                'http://www.9939.com/9939/res/base/v1/css/bd_search.css'
            )
        ),
        '9782' => array(
            'v' => 'v1',
            'file_save_dir' => 'fukezt', //妇科频道
            'tpl_include_paths' => array('common/v2', 'disease/v1'),
            'tpl_paths' => array(
                'index' => 'tpl/channel_fukezt.phtml',
                'detail' => 'tpl/channel/disease/v1/detail.phtml',
                'morelist' => 'tpl/channel/disease/v1/morelist.phtml',
                'list' => 'tpl/channel/disease/v1/list.phtml',
                'list_bottom' => 'tpl/public_new/list_bottom.phtml',
                'list_top' => 'tpl/public_new/top.phtml',
                'detail_top' => 'tpl/public_list/disease/v1/detail_top.phtml',
            ),
            'index_css_include_path' => array(
                'http://www.9939.com/9939/res/base/v1/css/common.css',
                'http://www.9939.com/9939/res/base/v1/css/hf.css',
                'http://www.9939.com/9939/res/pic/v1/css/index.css'
            ),
            'list_css_include_path' => array(
                'http://www.9939.com/9939/res/disease/v1/css/main.css',
                'http://www.9939.com/9939/res/disease/v1/css/list.css',
                'http://www.9939.com/9939/res/base/v1/css/bd_search.css'
            )
        ),
        '9783' => array(
            'v' => 'v1',
            'file_save_dir' => 'ganbingzt', //肝病
            'tpl_include_paths' => array('common/v2', 'disease/v1'),
            'tpl_paths' => array(
                'index' => 'tpl/channel_gbzt.phtml',
                'detail' => 'tpl/channel/disease/v1/detail.phtml',
                'morelist' => 'tpl/channel/disease/v1/morelist.phtml',
                'list' => 'tpl/channel/disease/v1/list.phtml',
                'list_bottom' => 'tpl/public_new/list_bottom.phtml',
                'list_top' => 'tpl/public_new/top.phtml',
                'detail_top' => 'tpl/public_list/disease/v1/detail_top.phtml',
            ),
            'index_css_include_path' => array(
                'http://www.9939.com/9939/res/base/v1/css/common.css',
                'http://www.9939.com/9939/res/base/v1/css/hf.css',
                'http://www.9939.com/9939/res/pic/v1/css/index.css'
            ),
            'list_css_include_path' => array(
                'http://www.9939.com/9939/res/disease/v1/css/main.css',
                'http://www.9939.com/9939/res/disease/v1/css/list.css',
                'http://www.9939.com/9939/res/base/v1/css/bd_search.css'
            )
        ),
        '9784' => array(
            'v' => 'v1',
            'file_save_dir' => 'jingzbzt', //颈椎病
            'tpl_include_paths' => array('common/v2', 'disease/v1'),
            'tpl_paths' => array(
                'index' => 'tpl/channel_jzb.phtml',
                'detail' => 'tpl/channel/disease/v1/detail.phtml',
                'morelist' => 'tpl/channel/disease/v1/morelist.phtml',
                'list' => 'tpl/channel/disease/v1/list.phtml',
                'list_bottom' => 'tpl/public_new/list_bottom.phtml',
                'list_top' => 'tpl/public_new/top.phtml',
                'detail_top' => 'tpl/public_list/disease/v1/detail_top.phtml',
            ),
            'index_css_include_path' => array(
                'http://www.9939.com/9939/res/base/v1/css/common.css',
                'http://www.9939.com/9939/res/base/v1/css/hf.css',
                'http://www.9939.com/9939/res/pic/v1/css/index.css'
            ),
            'list_css_include_path' => array(
                'http://www.9939.com/9939/res/disease/v1/css/main.css',
                'http://www.9939.com/9939/res/disease/v1/css/list.css',
                'http://www.9939.com/9939/res/base/v1/css/bd_search.css'
            )
        ),
        '9785' => array(
            'v' => 'v1',
            'file_save_dir' => 'shenbingzt', //肾病
            'tpl_include_paths' => array('common/v2', 'disease/v1'),
            'tpl_paths' => array(
                'index' => 'tpl/channel_sbzt.phtml',
                'detail' => 'tpl/channel/disease/v1/detail.phtml',
                'morelist' => 'tpl/channel/disease/v1/morelist.phtml',
                'list' => 'tpl/channel/disease/v1/list.phtml',
                'list_bottom' => 'tpl/public_new/list_bottom.phtml',
                'list_top' => 'tpl/public_new/top.phtml',
                'detail_top' => 'tpl/public_list/disease/v1/detail_top.phtml',
            ),
            'index_css_include_path' => array(
                'http://www.9939.com/9939/res/base/v1/css/common.css',
                'http://www.9939.com/9939/res/base/v1/css/hf.css',
                'http://www.9939.com/9939/res/pic/v1/css/index.css'
            ),
            'list_css_include_path' => array(
                'http://www.9939.com/9939/res/disease/v1/css/main.css',
                'http://www.9939.com/9939/res/disease/v1/css/list.css',
                'http://www.9939.com/9939/res/base/v1/css/bd_search.css'
            )
        ),
        '9786' => array(
            'v' => 'v1',
            'file_save_dir' => 'smyyzt', //失眠抑郁
            'tpl_include_paths' => array('common/v2', 'disease/v1'),
            'tpl_paths' => array(
                'index' => 'tpl/channel_smyy.phtml',
                'detail' => 'tpl/channel/disease/v1/detail.phtml',
                'morelist' => 'tpl/channel/disease/v1/morelist.phtml',
                'list' => 'tpl/channel/disease/v1/list.phtml',
                'list_bottom' => 'tpl/public_new/list_bottom.phtml',
                'list_top' => 'tpl/public_new/top.phtml',
                'detail_top' => 'tpl/public_list/disease/v1/detail_top.phtml',
            ),
            'index_css_include_path' => array(
                'http://www.9939.com/9939/res/base/v1/css/common.css',
                'http://www.9939.com/9939/res/base/v1/css/hf.css',
                'http://www.9939.com/9939/res/pic/v1/css/index.css'
            ),
            'list_css_include_path' => array(
                'http://www.9939.com/9939/res/disease/v1/css/main.css',
                'http://www.9939.com/9939/res/disease/v1/css/list.css',
                'http://www.9939.com/9939/res/base/v1/css/bd_search.css'
            )
        ),
        '9787' => array(
            'v' => 'v1',
            'file_save_dir' => 'tnbzt', //糖尿病
            'tpl_include_paths' => array('common/v2', 'disease/v1'),
            'tpl_paths' => array(
                'index' => 'tpl/channel_tnbzt.phtml',
                'detail' => 'tpl/channel/disease/v1/detail.phtml',
                'morelist' => 'tpl/channel/disease/v1/morelist.phtml',
                'list' => 'tpl/channel/disease/v1/list.phtml',
                'list_bottom' => 'tpl/public_new/list_bottom.phtml',
                'list_top' => 'tpl/public_new/top.phtml',
                'detail_top' => 'tpl/public_list/disease/v1/detail_top.phtml',
            ),
            'index_css_include_path' => array(
                'http://www.9939.com/9939/res/base/v1/css/common.css',
                'http://www.9939.com/9939/res/base/v1/css/hf.css',
                'http://www.9939.com/9939/res/pic/v1/css/index.css'
            ),
            'list_css_include_path' => array(
                'http://www.9939.com/9939/res/disease/v1/css/main.css',
                'http://www.9939.com/9939/res/disease/v1/css/list.css',
                'http://www.9939.com/9939/res/base/v1/css/bd_search.css'
            )
        ),
        '9788' => array(
            'v' => 'v1',
            'file_save_dir' => 'weibingzt', //胃病
            'tpl_include_paths' => array('common/v2', 'disease/v1'),
            'tpl_paths' => array(
                'index' => 'tpl/channel_weibingzt.phtml',
                'detail' => 'tpl/channel/disease/v1/detail.phtml',
                'morelist' => 'tpl/channel/disease/v1/morelist.phtml',
                'list' => 'tpl/channel/disease/v1/list.phtml',
                'list_bottom' => 'tpl/public_new/list_bottom.phtml',
                'list_top' => 'tpl/public_new/top.phtml',
                'detail_top' => 'tpl/public_list/disease/v1/detail_top.phtml',
            ),
            'index_css_include_path' => array(
                'http://www.9939.com/9939/res/base/v1/css/common.css',
                'http://www.9939.com/9939/res/base/v1/css/hf.css',
                'http://www.9939.com/9939/res/pic/v1/css/index.css'
            ),
            'list_css_include_path' => array(
                'http://www.9939.com/9939/res/disease/v1/css/main.css',
                'http://www.9939.com/9939/res/disease/v1/css/list.css',
                'http://www.9939.com/9939/res/base/v1/css/bd_search.css'
            )
        ),
        '9789' => array(
            'v' => 'v1',
            'file_save_dir' => 'xinzangbingzt', //心脏病
            'tpl_include_paths' => array('common/v2', 'disease/v1'),
            'tpl_paths' => array(
                'index' => 'tpl/channel_xzbzt.phtml',
                'detail' => 'tpl/channel/disease/v1/detail.phtml',
                'morelist' => 'tpl/channel/disease/v1/morelist.phtml',
                'list' => 'tpl/channel/disease/v1/list.phtml',
                'list_bottom' => 'tpl/public_new/list_bottom.phtml',
                'list_top' => 'tpl/public_new/top.phtml',
                'detail_top' => 'tpl/public_list/disease/v1/detail_top.phtml',
            ),
            'index_css_include_path' => array(
                'http://www.9939.com/9939/res/base/v1/css/common.css',
                'http://www.9939.com/9939/res/base/v1/css/hf.css',
                'http://www.9939.com/9939/res/pic/v1/css/index.css'
            ),
            'list_css_include_path' => array(
                'http://www.9939.com/9939/res/disease/v1/css/main.css',
                'http://www.9939.com/9939/res/disease/v1/css/list.css',
                'http://www.9939.com/9939/res/base/v1/css/bd_search.css'
            )
        ),
        '9790' => array(
            'v' => 'v1',
            'file_save_dir' => 'yaojanpanzt', //腰间盘
            'tpl_include_paths' => array('common/v2', 'disease/v1'),
            'tpl_paths' => array(
                'index' => 'tpl/channel_yaojanpanzt.phtml',
                'detail' => 'tpl/channel/disease/v1/detail.phtml',
                'morelist' => 'tpl/channel/disease/v1/morelist.phtml',
                'list' => 'tpl/channel/disease/v1/list.phtml',
                'list_bottom' => 'tpl/public_new/list_bottom.phtml',
                'list_top' => 'tpl/public_new/top.phtml',
                'detail_top' => 'tpl/public_list/disease/v1/detail_top.phtml',
            ),
            'index_css_include_path' => array(
                'http://www.9939.com/9939/res/base/v1/css/common.css',
                'http://www.9939.com/9939/res/base/v1/css/hf.css',
                'http://www.9939.com/9939/res/pic/v1/css/index.css'
            ),
            'list_css_include_path' => array(
                'http://www.9939.com/9939/res/disease/v1/css/main.css',
                'http://www.9939.com/9939/res/disease/v1/css/list.css',
                'http://www.9939.com/9939/res/base/v1/css/bd_search.css'
            )
        ),
        '10236' => array(
            'v' => 'v1',
            'file_save_dir' => 'ggtzt', //股骨头坏死
            'tpl_include_paths' => array('common/v2', 'disease/v1'),
            'tpl_paths' => array(
                'index' => 'tpl/channel_ggtzt.phtml',
                'detail' => 'tpl/channel/disease/v1/detail.phtml',
                'morelist' => 'tpl/channel/disease/v1/morelist.phtml',
                'list' => 'tpl/channel/disease/v1/list.phtml',
                'list_bottom' => 'tpl/public_new/list_bottom.phtml',
                'list_top' => 'tpl/public_new/top.phtml',
                'detail_top' => 'tpl/public_list/disease/v1/detail_top.phtml',
            ),
            'index_css_include_path' => array(
                'http://www.9939.com/9939/res/base/v1/css/common.css',
                'http://www.9939.com/9939/res/base/v1/css/hf.css',
                'http://www.9939.com/9939/res/pic/v1/css/index.css'
            ),
            'list_css_include_path' => array(
                'http://www.9939.com/9939/res/disease/v1/css/main.css',
                'http://www.9939.com/9939/res/disease/v1/css/list.css',
                'http://www.9939.com/9939/res/base/v1/css/bd_search.css'
            )
        ),
        '10249' => array(
            'v' => 'v1',
            'file_save_dir' => 'gczt', //肛肠
            'tpl_include_paths' => array('common/v2', 'disease/v1'),
            'tpl_paths' => array(
                'index' => 'tpl/channel_gczt.phtml',
                'detail' => 'tpl/channel/disease/v1/detail.phtml',
                'morelist' => 'tpl/channel/disease/v1/morelist.phtml',
                'list' => 'tpl/channel/disease/v1/list.phtml',
                'list_bottom' => 'tpl/public_new/list_bottom.phtml',
                'list_top' => 'tpl/public_new/top.phtml',
                'detail_top' => 'tpl/public_list/disease/v1/detail_top.phtml',
            ),
            'index_css_include_path' => array(
                'http://www.9939.com/9939/res/base/v1/css/common.css',
                'http://www.9939.com/9939/res/base/v1/css/hf.css',
                'http://www.9939.com/9939/res/pic/v1/css/index.css'
            ),
            'list_css_include_path' => array(
                'http://www.9939.com/9939/res/disease/v1/css/main.css',
                'http://www.9939.com/9939/res/disease/v1/css/list.css',
                'http://www.9939.com/9939/res/base/v1/css/bd_search.css'
            )
        ),
        '10797' => array(
            'v' => 'v1',
            'file_save_dir' => 'mnzt', //泌尿
            'tpl_include_paths' => array('common/v2', 'disease/v1'),
            'tpl_paths' => array(
                'index' => 'tpl/channel_mnzt.phtml',
                'detail' => 'tpl/channel/disease/v1/detail.phtml',
                'morelist' => 'tpl/channel/disease/v1/morelist.phtml',
                'list' => 'tpl/channel/disease/v1/list.phtml',
                'list_bottom' => 'tpl/public_new/list_bottom.phtml',
                'list_top' => 'tpl/public_new/top.phtml',
                'detail_top' => 'tpl/public_list/disease/v1/detail_top.phtml',
            ),
            'index_css_include_path' => array(
                'http://www.9939.com/9939/res/base/v1/css/common.css',
                'http://www.9939.com/9939/res/base/v1/css/hf.css',
                'http://www.9939.com/9939/res/pic/v1/css/index.css'
            ),
            'list_css_include_path' => array(
                'http://www.9939.com/9939/res/disease/v1/css/main.css',
                'http://www.9939.com/9939/res/disease/v1/css/list.css',
                'http://www.9939.com/9939/res/base/v1/css/bd_search.css'
            )
        ),
        '10819' => array(
            'v' => 'v1',
            'file_save_dir' => 'yk', //眼科
            'tpl_include_paths' => array('common/v2', 'disease/v1'),
            'tpl_paths' => array(
                'index' => '/tpl/channel_eye.phtml',
                'detail' => 'tpl/channel/disease/v1/detail.phtml',
                'morelist' => 'tpl/channel/disease/v1/morelist.phtml',
                'list' => 'tpl/channel/disease/v1/list.phtml',
                'list_bottom' => 'tpl/public_new/list_bottom.phtml',
                'list_top' => 'tpl/public_new/top.phtml',
                'detail_top' => 'tpl/public_list/disease/v1/detail_top.phtml',
            ),
            'index_css_include_path' => array(
                'http://www.9939.com/9939/res/base/v1/css/common.css',
                'http://www.9939.com/9939/res/base/v1/css/hf.css',
                'http://www.9939.com/9939/res/pic/v1/css/index.css'
            ),
            'list_css_include_path' => array(
                'http://www.9939.com/9939/res/disease/v1/css/main.css',
                'http://www.9939.com/9939/res/disease/v1/css/list.css',
                'http://www.9939.com/9939/res/base/v1/css/bd_search.css'
            )
        ),
        '10820' => array(
            'v' => 'v1',
            'file_save_dir' => 'zlzt', //肿瘤
            'tpl_include_paths' => array('common/v2', 'disease/v1'),
            'tpl_paths' => array(
                'index' => 'tpl/channel_zlzt.phtml',
                'detail' => 'tpl/channel/disease/v1/detail.phtml',
                'morelist' => 'tpl/channel/disease/v1/morelist.phtml',
                'list' => 'tpl/channel/disease/v1/list.phtml',
                'list_bottom' => 'tpl/public_new/list_bottom.phtml',
                'list_top' => 'tpl/public_new/top.phtml',
                'detail_top' => 'tpl/public_list/disease/v1/detail_top.phtml',
            ),
            'index_css_include_path' => array(
                'http://www.9939.com/9939/res/base/v1/css/common.css',
                'http://www.9939.com/9939/res/base/v1/css/hf.css',
                'http://www.9939.com/9939/res/pic/v1/css/index.css'
            ),
            'list_css_include_path' => array(
                'http://www.9939.com/9939/res/disease/v1/css/main.css',
                'http://www.9939.com/9939/res/disease/v1/css/list.css',
                'http://www.9939.com/9939/res/base/v1/css/bd_search.css'
            )
        ),
        '10896' => array(
            'v' => 'v1',
            'file_save_dir' => 'fengshi', //风湿
            'tpl_include_paths' => array('common/v2', 'disease/v1'),
            'tpl_paths' => array(
                'index' => 'tpl/channel_fszt.phtml',
                'detail' => 'tpl/channel/disease/v1/detail.phtml',
                'morelist' => 'tpl/channel/disease/v1/morelist.phtml',
                'list' => 'tpl/channel/disease/v1/list.phtml',
                'list_bottom' => 'tpl/public_new/list_bottom.phtml',
                'list_top' => 'tpl/public_new/top.phtml',
                'detail_top' => 'tpl/public_list/disease/v1/detail_top.phtml',
            ),
            'index_css_include_path' => array(
                'http://www.9939.com/9939/res/base/v1/css/common.css',
                'http://www.9939.com/9939/res/base/v1/css/hf.css',
                'http://www.9939.com/9939/res/pic/v1/css/index.css'
            ),
            'list_css_include_path' => array(
                'http://www.9939.com/9939/res/disease/v1/css/main.css',
                'http://www.9939.com/9939/res/disease/v1/css/list.css',
                'http://www.9939.com/9939/res/base/v1/css/bd_search.css'
            )
        ),
        '10916' => array(
            'v' => 'v1',
            'file_save_dir' => 'zhongfen', //中风
            'tpl_include_paths' => array('common/v2', 'disease/v1'),
            'tpl_paths' => array(
                'index' => '/tpl/channel_zhfeng.phtml',
                'detail' => 'tpl/channel/disease/v1/detail.phtml',
                'morelist' => 'tpl/channel/disease/v1/morelist.phtml',
                'list' => 'tpl/channel/disease/v1/list.phtml',
                'list_bottom' => 'tpl/public_new/list_bottom.phtml',
                'list_top' => 'tpl/public_new/top.phtml',
                'detail_top' => 'tpl/public_list/disease/v1/detail_top.phtml',
            ),
            'index_css_include_path' => array(
                'http://www.9939.com/9939/res/base/v1/css/common.css',
                'http://www.9939.com/9939/res/base/v1/css/hf.css',
                'http://www.9939.com/9939/res/pic/v1/css/index.css'
            ),
            'list_css_include_path' => array(
                'http://www.9939.com/9939/res/disease/v1/css/main.css',
                'http://www.9939.com/9939/res/disease/v1/css/list.css',
                'http://www.9939.com/9939/res/base/v1/css/bd_search.css'
            )
        ),
        '10936' => array(
            'v' => 'v1',
            'file_save_dir' => 'nkzt', //男科专区
            'tpl_include_paths' => array('common/v2', 'disease/v1'),
            'tpl_paths' => array(
                'index' => 'tpl/channel_nkzt.phtml',
                'detail' => 'tpl/channel/disease/v1/detail.phtml',
                'morelist' => 'tpl/channel/disease/v1/morelist.phtml',
                'list' => 'tpl/channel/disease/v1/list.phtml',
                'list_bottom' => 'tpl/public_new/list_bottom.phtml',
                'list_top' => 'tpl/public_new/top.phtml',
                'detail_top' => 'tpl/public_list/disease/v1/detail_top.phtml',
            ),
            'index_css_include_path' => array(
                'http://www.9939.com/9939/res/base/v1/css/common.css',
                'http://www.9939.com/9939/res/base/v1/css/hf.css',
                'http://www.9939.com/9939/res/pic/v1/css/index.css'
            ),
            'list_css_include_path' => array(
                'http://www.9939.com/9939/res/disease/v1/css/main.css',
                'http://www.9939.com/9939/res/disease/v1/css/list.css',
                'http://www.9939.com/9939/res/base/v1/css/bd_search.css'
            )
        ),
        '10976' => array(
            'v' => 'v1',
            'file_save_dir' => 'heart', //心血管
            'tpl_include_paths' => array('common/v2', 'disease/v1'),
            'tpl_paths' => array(
                'index' => 'tpl/channel_xxgzt.phtml',
                'detail' => 'tpl/channel/disease/v1/detail.phtml',
                'morelist' => 'tpl/channel/disease/v1/morelist.phtml',
                'list' => 'tpl/channel/disease/v1/list.phtml',
                'list_bottom' => 'tpl/public_new/list_bottom.phtml',
                'list_top' => 'tpl/public_new/top.phtml',
                'detail_top' => 'tpl/public_list/disease/v1/detail_top.phtml',
            ),
            'index_css_include_path' => array(
                'http://www.9939.com/9939/res/base/v1/css/common.css',
                'http://www.9939.com/9939/res/base/v1/css/hf.css',
                'http://www.9939.com/9939/res/pic/v1/css/index.css'
            ),
            'list_css_include_path' => array(
                'http://www.9939.com/9939/res/disease/v1/css/main.css',
                'http://www.9939.com/9939/res/disease/v1/css/list.css',
                'http://www.9939.com/9939/res/base/v1/css/bd_search.css'
            )
        ),
        '11003' => array(
            'v' => 'v1',
            'file_save_dir' => 'jskzt', //精神科
            'tpl_include_paths' => array('common/v2', 'disease/v1'),
            'tpl_paths' => array(
                'index' => 'tpl/channel_jszt.phtml',
                'detail' => 'tpl/channel/disease/v1/detail.phtml',
                'morelist' => 'tpl/channel/disease/v1/morelist.phtml',
                'list' => 'tpl/channel/disease/v1/list.phtml',
                'list_bottom' => 'tpl/public_new/list_bottom.phtml',
                'list_top' => 'tpl/public_new/top.phtml',
                'detail_top' => 'tpl/public_list/disease/v1/detail_top.phtml',
            ),
            'index_css_include_path' => array(
                'http://www.9939.com/9939/res/base/v1/css/common.css',
                'http://www.9939.com/9939/res/base/v1/css/hf.css',
                'http://www.9939.com/9939/res/pic/v1/css/index.css'
            ),
            'list_css_include_path' => array(
                'http://www.9939.com/9939/res/disease/v1/css/main.css',
                'http://www.9939.com/9939/res/disease/v1/css/list.css',
                'http://www.9939.com/9939/res/base/v1/css/bd_search.css'
            )
        ),
        '11037' => array(
            'v' => 'v1',
            'file_save_dir' => 'kqzt', //口腔
            'tpl_include_paths' => array('common/v2', 'disease/v1'),
            'tpl_paths' => array(
                'index' => 'tpl/channel_kqzt.phtml',
                'detail' => 'tpl/channel/disease/v1/detail.phtml',
                'morelist' => 'tpl/channel/disease/v1/morelist.phtml',
                'list' => 'tpl/channel/disease/v1/list.phtml',
                'list_bottom' => 'tpl/public_new/list_bottom.phtml',
                'list_top' => 'tpl/public_new/top.phtml',
                'detail_top' => 'tpl/public_list/disease/v1/detail_top.phtml',
            ),
            'index_css_include_path' => array(
                'http://www.9939.com/9939/res/base/v1/css/common.css',
                'http://www.9939.com/9939/res/base/v1/css/hf.css',
                'http://www.9939.com/9939/res/pic/v1/css/index.css'
            ),
            'list_css_include_path' => array(
                'http://www.9939.com/9939/res/disease/v1/css/main.css',
                'http://www.9939.com/9939/res/disease/v1/css/list.css',
                'http://www.9939.com/9939/res/base/v1/css/bd_search.css'
            )
        ),
        '11066' => array(
            'v' => 'v1',
            'file_save_dir' => 'gxy', //高血压
            'tpl_include_paths' => array('common/v2', 'disease/v1'),
            'tpl_paths' => array(
                'index' => 'tpl/channel_gxy.phtml',
                'detail' => 'tpl/channel/disease/v1/detail.phtml',
                'morelist' => 'tpl/channel/disease/v1/morelist.phtml',
                'list' => 'tpl/channel/disease/v1/list.phtml',
                'list_bottom' => 'tpl/public_new/list_bottom.phtml',
                'list_top' => 'tpl/public_new/top.phtml',
                'detail_top' => 'tpl/public_list/disease/v1/detail_top.phtml',
            ),
            'index_css_include_path' => array(
                'http://www.9939.com/9939/res/base/v1/css/common.css',
                'http://www.9939.com/9939/res/base/v1/css/hf.css',
                'http://www.9939.com/9939/res/pic/v1/css/index.css'
            ),
            'list_css_include_path' => array(
                'http://www.9939.com/9939/res/disease/v1/css/main.css',
                'http://www.9939.com/9939/res/disease/v1/css/list.css',
                'http://www.9939.com/9939/res/base/v1/css/bd_search.css'
            )
        ),
        '11091' => array(
            'v' => 'v1',
            'file_save_dir' => 'pfzt', //皮肤专区
            'tpl_include_paths' => array('common/v2', 'disease/v1'),
            'tpl_paths' => array(
                'index' => 'tpl/channel_pfzt.phtml',
                'detail' => 'tpl/channel/disease/v1/detail.phtml',
                'morelist' => 'tpl/channel/disease/v1/morelist.phtml',
                'list' => 'tpl/channel/disease/v1/list.phtml',
                'list_bottom' => 'tpl/public_new/list_bottom.phtml',
                'list_top' => 'tpl/public_new/top.phtml',
                'detail_top' => 'tpl/public_list/disease/v1/detail_top.phtml',
            ),
            'index_css_include_path' => array(
                'http://www.9939.com/9939/res/base/v1/css/common.css',
                'http://www.9939.com/9939/res/base/v1/css/hf.css',
                'http://www.9939.com/9939/res/pic/v1/css/index.css'
            ),
            'list_css_include_path' => array(
                'http://www.9939.com/9939/res/disease/v1/css/main.css',
                'http://www.9939.com/9939/res/disease/v1/css/list.css',
                'http://www.9939.com/9939/res/base/v1/css/bd_search.css'
            )
        ),
        '11135' => array(
            'v' => 'v1',
            'file_save_dir' => 'gkzt', //骨科
            'tpl_include_paths' => array('common/v2', 'disease/v1'),
            'tpl_paths' => array(
                'index' => 'tpl/channel_gkzt.phtml',
                'detail' => 'tpl/channel/disease/v1/detail.phtml',
                'morelist' => 'tpl/channel/disease/v1/morelist.phtml',
                'list' => 'tpl/channel/disease/v1/list.phtml',
                'list_bottom' => 'tpl/public_new/list_bottom.phtml',
                'list_top' => 'tpl/public_new/top.phtml',
                'detail_top' => 'tpl/public_list/disease/v1/detail_top.phtml',
            ),
            'index_css_include_path' => array(
                'http://www.9939.com/9939/res/base/v1/css/common.css',
                'http://www.9939.com/9939/res/base/v1/css/hf.css',
                'http://www.9939.com/9939/res/pic/v1/css/index.css'
            ),
            'list_css_include_path' => array(
                'http://www.9939.com/9939/res/disease/v1/css/main.css',
                'http://www.9939.com/9939/res/disease/v1/css/list.css',
                'http://www.9939.com/9939/res/base/v1/css/bd_search.css'
            )
        ),
        '11161' => array(
            'v' => 'v1',
            'file_save_dir' => 'ekzt', //儿科
            'tpl_include_paths' => array('common/v2', 'disease/v1'),
            'tpl_paths' => array(
                'index' => 'tpl/channel_ekzt.phtml',
                'detail' => 'tpl/channel/disease/v1/detail.phtml',
                'morelist' => 'tpl/channel/disease/v1/morelist.phtml',
                'list' => 'tpl/channel/disease/v1/list.phtml',
                'list_bottom' => 'tpl/public_new/list_bottom.phtml',
                'list_top' => 'tpl/public_new/top.phtml',
                'detail_top' => 'tpl/public_list/disease/v1/detail_top.phtml',
            ),
            'index_css_include_path' => array(
                'http://www.9939.com/9939/res/base/v1/css/common.css',
                'http://www.9939.com/9939/res/base/v1/css/hf.css',
                'http://www.9939.com/9939/res/pic/v1/css/index.css'
            ),
            'list_css_include_path' => array(
                'http://www.9939.com/9939/res/disease/v1/css/main.css',
                'http://www.9939.com/9939/res/disease/v1/css/list.css',
                'http://www.9939.com/9939/res/base/v1/css/bd_search.css'
            )
        ),
        '11169' => array(
            'v' => 'v1',
            'file_save_dir' => 'lczt', //人流
            'tpl_include_paths' => array('common/v2', 'disease/v1'),
            'tpl_paths' => array(
                'index' => 'tpl/channel_lczt.phtml',
                'detail' => 'tpl/channel/disease/v1/detail.phtml',
                'morelist' => 'tpl/channel/disease/v1/morelist.phtml',
                'list' => 'tpl/channel/disease/v1/list.phtml',
                'list_bottom' => 'tpl/public_new/list_bottom.phtml',
                'list_top' => 'tpl/public_new/top.phtml',
                'detail_top' => 'tpl/public_list/disease/v1/detail_top.phtml',
            ),
            'index_css_include_path' => array(
                'http://www.9939.com/9939/res/base/v1/css/common.css',
                'http://www.9939.com/9939/res/base/v1/css/hf.css',
                'http://www.9939.com/9939/res/pic/v1/css/index.css'
            ),
            'list_css_include_path' => array(
                'http://www.9939.com/9939/res/disease/v1/css/main.css',
                'http://www.9939.com/9939/res/disease/v1/css/list.css',
                'http://www.9939.com/9939/res/base/v1/css/bd_search.css'
            )
        ),
        '11277' => array(
            'v' => 'v1',
            'file_save_dir' => 'npx', //牛皮癣
            'tpl_include_paths' => array('common/v2', 'disease/v1'),
            'tpl_paths' => array(
                'index' => '/tpl/channel_npx.phtml',
                'detail' => 'tpl/channel/disease/v1/detail.phtml',
                'morelist' => 'tpl/channel/disease/v1/morelist.phtml',
                'list' => 'tpl/channel/disease/v1/list.phtml',
                'list_bottom' => 'tpl/public_new/list_bottom.phtml',
                'list_top' => 'tpl/public_new/top.phtml',
                'detail_top' => 'tpl/public_list/disease/v1/detail_top.phtml',
            ),
            'index_css_include_path' => array(
                'http://www.9939.com/9939/res/base/v1/css/common.css',
                'http://www.9939.com/9939/res/base/v1/css/hf.css',
                'http://www.9939.com/9939/res/pic/v1/css/index.css'
            ),
            'list_css_include_path' => array(
                'http://www.9939.com/9939/res/disease/v1/css/main.css',
                'http://www.9939.com/9939/res/disease/v1/css/list.css',
                'http://www.9939.com/9939/res/base/v1/css/bd_search.css'
            )
        ),
        '11290' => array(
            'v' => 'v1',
            'file_save_dir' => 'bdf', //白癜风
            'tpl_include_paths' => array('common/v2', 'disease/v1'),
            'tpl_paths' => array(
                'index' => '/tpl/channel_bdf.phtml',
                'detail' => 'tpl/channel/disease/v1/detail.phtml',
                'morelist' => 'tpl/channel/disease/v1/morelist.phtml',
                'list' => 'tpl/channel/disease/v1/list.phtml',
                'list_bottom' => 'tpl/public_new/list_bottom.phtml',
                'list_top' => 'tpl/public_new/top.phtml',
                'detail_top' => 'tpl/public_list/disease/v1/detail_top.phtml',
            ),
            'index_css_include_path' => array(
                'http://www.9939.com/9939/res/base/v1/css/common.css',
                'http://www.9939.com/9939/res/base/v1/css/hf.css',
                'http://www.9939.com/9939/res/pic/v1/css/index.css'
            ),
            'list_css_include_path' => array(
                'http://www.9939.com/9939/res/disease/v1/css/main.css',
                'http://www.9939.com/9939/res/disease/v1/css/list.css',
                'http://www.9939.com/9939/res/base/v1/css/bd_search.css'
            )
        ),
        '11305' => array(
            'v' => 'v1',
            'file_save_dir' => 'dxzt', //癫痫
            'tpl_include_paths' => array('common/v2', 'disease/v1'),
            'tpl_paths' => array(
                'index' => '/tpl/channel_dxzt.phtml',
                'detail' => 'tpl/channel/disease/v1/detail.phtml',
                'morelist' => 'tpl/channel/disease/v1/morelist.phtml',
                'list' => 'tpl/channel/disease/v1/list.phtml',
                'list_bottom' => 'tpl/public_new/list_bottom.phtml',
                'list_top' => 'tpl/public_new/top.phtml',
                'detail_top' => 'tpl/public_list/disease/v1/detail_top.phtml',
            ),
            'index_css_include_path' => array(
                'http://www.9939.com/9939/res/base/v1/css/common.css',
                'http://www.9939.com/9939/res/base/v1/css/hf.css',
                'http://www.9939.com/9939/res/pic/v1/css/index.css'
            ),
            'list_css_include_path' => array(
                'http://www.9939.com/9939/res/disease/v1/css/main.css',
                'http://www.9939.com/9939/res/disease/v1/css/list.css',
                'http://www.9939.com/9939/res/base/v1/css/bd_search.css'
            )
        ),
        '11334' => array(
            'v' => 'v1',
            'file_save_dir' => 'shidaoai', //食道癌
            'tpl_include_paths' => array('common/v2', 'disease/v1'),
            'tpl_paths' => array(
                'index' => '/tpl/channel_sdazt.phtml',
                'detail' => 'tpl/channel/disease/v1/detail.phtml',
                'morelist' => 'tpl/channel/disease/v1/morelist.phtml',
                'list' => 'tpl/channel/disease/v1/list.phtml',
                'list_bottom' => 'tpl/public_new/list_bottom.phtml',
                'list_top' => 'tpl/public_new/top.phtml',
                'detail_top' => 'tpl/public_list/disease/v1/detail_top.phtml',
            ),
            'index_css_include_path' => array(
                'http://www.9939.com/9939/res/base/v1/css/common.css',
                'http://www.9939.com/9939/res/base/v1/css/hf.css',
                'http://www.9939.com/9939/res/pic/v1/css/index.css'
            ),
            'list_css_include_path' => array(
                'http://www.9939.com/9939/res/disease/v1/css/main.css',
                'http://www.9939.com/9939/res/disease/v1/css/list.css',
                'http://www.9939.com/9939/res/base/v1/css/bd_search.css'
            )
        ),
        '11364' => array(
            'v' => 'v1',
            'file_save_dir' => 'hblc', //红斑狼疮
            'tpl_include_paths' => array('common/v2', 'disease/v1'),
            'tpl_paths' => array(
                'index' => '/tpl/channel_hblc.phtml',
                'detail' => 'tpl/channel/disease/v1/detail.phtml',
                'morelist' => 'tpl/channel/disease/v1/morelist.phtml',
                'list' => 'tpl/channel/disease/v1/list.phtml',
                'list_bottom' => 'tpl/public_new/list_bottom.phtml',
                'list_top' => 'tpl/public_new/top.phtml',
                'detail_top' => 'tpl/public_list/disease/v1/detail_top.phtml',
            ),
            'index_css_include_path' => array(
                'http://www.9939.com/9939/res/base/v1/css/common.css',
                'http://www.9939.com/9939/res/base/v1/css/hf.css',
                'http://www.9939.com/9939/res/pic/v1/css/index.css'
            ),
            'list_css_include_path' => array(
                'http://www.9939.com/9939/res/disease/v1/css/main.css',
                'http://www.9939.com/9939/res/disease/v1/css/list.css',
                'http://www.9939.com/9939/res/base/v1/css/bd_search.css'
            )
        ),
        '11743' => array(
            'v' => 'v1',
            'file_save_dir' => 'mbzx', //慢病中心
            'tpl_include_paths' => array('common/v2', 'disease/v1'),
            'tpl_paths' => array(
                'index' => 'tpl/channel_manbing.phtml',
                'detail' => 'tpl/channel/disease/v1/detail.phtml',
                'morelist' => 'tpl/channel/disease/v1/morelist.phtml',
                'list' => 'tpl/channel/disease/v1/list.phtml',
                'list_bottom' => 'tpl/public_new/list_bottom.phtml',
                'list_top' => 'tpl/public_new/top.phtml',
                'detail_top' => 'tpl/public_list/disease/v1/detail_top.phtml',
            ),
            'index_css_include_path' => array(
                'http://www.9939.com/9939/res/base/v1/css/common.css',
                'http://www.9939.com/9939/res/base/v1/css/hf.css',
                'http://www.9939.com/9939/res/pic/v1/css/index.css'
            ),
            'list_css_include_path' => array(
                'http://www.9939.com/9939/res/disease/v1/css/main.css',
                'http://www.9939.com/9939/res/disease/v1/css/list.css',
                'http://www.9939.com/9939/res/base/v1/css/bd_search.css'
            )
        )
    );

}
