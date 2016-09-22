<?php

/**
 * 根据Module区分布局
 * 
 * example：
 * <pre>
 * 
 * </pre>
 * 
 * @name QLib_Plugin_Layout
 * @version 1.0 (2009-5-12 上午11:23:56)
 * @package QLib.Plugin
 * @author peter.zyliu peter.zyliu@gmail.com
 * @since 1.0
 */
final class QLib_Plugin_Layout extends Zend_Controller_Plugin_Abstract {

    /**
     * Array of layout paths associating modules with layouts
     */
    private $_moduleLayouts;

    /**
     * Registers a module layout.
     * This layout will be rendered when the specified module is called.
     * If there is no layout registered for the current module, the default layout as specified
     * in Zend_Layout will be rendered
     *
     * @param String $module        The name of the module
     * @param String $layoutPath    The path to the layout
     * @param String $layout        The name of the layout to render
     */
    public function registerModuleLayout($module, $layoutPath, $layout = null) {
        $this->_moduleLayouts[$module] = array(
            'layoutPath' => $layoutPath,
            'layout' => $layout
        );
    }

    public function preDispatch(Zend_Controller_Request_Abstract $request) {
        $app_base_path = APP_ROOT .DIRECTORY_SEPARATOR.'application/';
        
        $sModuleName = $request->getModuleName();
        #获取全局Layout版本
        $version = isset(QConfigs_Site_Config::$template_version) ? QConfigs_Site_Config::$template_version : 'default';
        #设置全局Layout
        $globalRootPath = realpath($app_base_path.'layouts/' . $version);
        
        $layout = Zend_Layout::getMvcInstance();
        #设置全局helper
        $layout->getView()->addHelperPath($app_base_path . '/helpers', 'Web_View_Helper');
        $layout->getView()->setScriptPath($app_base_path . '/helpers/views');
        if ($layout->getMvcEnabled()) {
            $options = array(
                $globalRootPath
            );
            $layout->setLayoutPath($options);
            $layout->setLayout('main');
        }
    }

    public function __construct() {
        
    }

}