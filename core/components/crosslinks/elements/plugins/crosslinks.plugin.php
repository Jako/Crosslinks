<?php
/**
 * Crosslinks Plugin
 *
 * @package crosslinks
 * @subpackage plugin
 *
 * @var modX $modx
 * @var array $scriptProperties
 */

$className = 'Crosslinks' . $modx->event->name;

$corePath = $modx->getOption('crosslinks.core_path', null, $modx->getOption('core_path') . 'components/crosslinks/');
/** @var Crosslinks $crosslinks */
$crosslinks = $modx->getService('crosslinks', 'Crosslinks', $corePath . 'model/crosslinks/', array(
    'core_path' => $corePath
));

$modx->loadClass('CrosslinksPlugin', $crosslinks->getOption('modelPath') . 'crosslinks/events/', true, true);
$modx->loadClass($className, $crosslinks->getOption('modelPath') . 'crosslinks/events/', true, true);
if (class_exists($className)) {
    /** @var CrosslinksPlugin $handler */
    $handler = new $className($modx, $scriptProperties);
    $handler->run();
}

return;
