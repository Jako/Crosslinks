<?php
/**
 * Home Manager Controller class for Crosslinks
 *
 * @package crosslinks
 * @subpackage controller
 */

/**
 * Class CrosslinksHomeManagerController
 */
class CrosslinksHomeManagerController extends modExtraManagerController
{
    /** @var Crosslinks $crosslinks */
    public $crosslinks;

    public function initialize()
    {
        $corePath = $this->modx->getOption('crosslinks.core_path', null, $this->modx->getOption('core_path') . 'components/crosslinks/');
        $this->crosslinks = $this->modx->getService('crosslinks', 'Crosslinks', $corePath . '/model/crosslinks/', array(
            'core_path' => $corePath
        ));
    }

    public function loadCustomCssJs()
    {
        $assetsUrl = $this->crosslinks->getOption('assetsUrl');
        $jsUrl = $this->crosslinks->getOption('jsUrl') . 'mgr/';
        $jsSourceUrl = $assetsUrl . '../../../source/js/mgr/';
        $cssUrl = $this->crosslinks->getOption('cssUrl') . 'mgr/';
        $cssSourceUrl = $assetsUrl . '../../../source/css/mgr/';

        if ($this->crosslinks->getOption('debug') && ($this->crosslinks->getOption('assetsUrl') != MODX_ASSETS_URL . 'components/crosslinks/')) {
            $this->addCss($cssSourceUrl . 'crosslinks.css');
            $this->addJavascript($jsSourceUrl . 'crosslinks.js');
            $this->addJavascript($jsSourceUrl . 'helper/combo.js');
            $this->addJavascript($jsSourceUrl . 'helper/jsongrid.js');
            $this->addJavascript($jsSourceUrl . 'widgets/home.panel.js');
            $this->addJavascript($jsSourceUrl . 'widgets/links.grid.js');
            $this->addLastJavascript($jsSourceUrl . 'sections/home.js');
        } else {
            $this->addCss($cssUrl . 'crosslinks.min.css?v=v' . $this->crosslinks->version);
            $this->addLastJavascript($jsUrl . 'crosslinks.min.js');
        }
        $this->addHtml('<script type="text/javascript">
        Ext.onReady(function() {
            Crosslinks.config = ' . $this->modx->toJSON($this->crosslinks->config) . ';
            MODx.load({xtype: "crosslinks-page-home"});
        });
        </script>');
    }

    public function getLanguageTopics()
    {
        return array('crosslinks:default');
    }

    public function process(array $scriptProperties = array())
    {
    }

    public function getPageTitle()
    {
        return $this->modx->lexicon('crosslinks');
    }

    public function getTemplateFile()
    {
        return $this->crosslinks->getOption('templatesPath') . 'home.tpl';
    }
}
