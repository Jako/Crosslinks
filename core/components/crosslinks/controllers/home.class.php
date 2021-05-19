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
        $this->crosslinks = $this->modx->getService('crosslinks', 'Crosslinks', $corePath . 'model/crosslinks/', array(
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
            $this->addCss($cssSourceUrl . 'crosslinks.css?v=v' . $this->crosslinks->version);
            $this->addJavascript($jsSourceUrl . 'crosslinks.js?v=v' . $this->crosslinks->version);
            $this->addJavascript($jsSourceUrl . 'helper/combo.js?v=v' . $this->crosslinks->version);
            $this->addJavascript($jsSourceUrl . 'helper/jsongrid.js?v=v' . $this->crosslinks->version);
            $this->addJavascript($jsSourceUrl . 'widgets/home.panel.js?v=v' . $this->crosslinks->version);
            $this->addJavascript($jsSourceUrl . 'widgets/links.grid.js?v=v' . $this->crosslinks->version);
            $this->addJavascript(MODX_MANAGER_URL . 'assets/modext/widgets/core/modx.grid.settings.js');
            $this->addJavascript($jsSourceUrl . 'widgets/settings.panel.js?v=v' . $this->crosslinks->version);
            $this->addLastJavascript($jsSourceUrl . 'sections/home.js?v=v' . $this->crosslinks->version);
        } else {
            $this->addCss($cssUrl . 'crosslinks.min.css?v=v' . $this->crosslinks->version);
            $this->addJavascript(MODX_MANAGER_URL . 'assets/modext/widgets/core/modx.grid.settings.js');
            $this->addLastJavascript($jsUrl . 'crosslinks.min.js?v=v' . $this->crosslinks->version);
        }
        $this->addHtml('<script type="text/javascript">
        Ext.onReady(function() {
            Crosslinks.config = ' . json_encode($this->crosslinks->options, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . ';
            MODx.load({xtype: "crosslinks-page-home"});
        });
        </script>');
    }

    public function getLanguageTopics()
    {
        return array('core:setting', 'crosslinks:default');
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
