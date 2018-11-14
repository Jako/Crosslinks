<?php
/**
 * @package crosslinks
 * @subpackage plugin
 */

abstract class CrosslinksPlugin
{
    /** @var modX $modx */
    protected $modx;
    /** @var Crosslinks $crosslinks */
    protected $crosslinks;
    /** @var array $scriptProperties */
    protected $scriptProperties;

    public function __construct($modx, &$scriptProperties)
    {
        $this->scriptProperties =& $scriptProperties;
        $this->modx = &$modx;
        $corePath = $this->modx->getOption('crosslinks.core_path', null, $this->modx->getOption('core_path') . 'components/crosslinks/');
        $this->crosslinks = $this->modx->getService('crosslinks', 'Crosslinks', $corePath . 'model/crosslinks/', array(
            'core_path' => $corePath
        ));
    }

    abstract public function run();
}
