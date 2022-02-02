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

$className = 'TreehillStudio\Crosslinks\Plugins\Events\\' . $modx->event->name;

$corePath = $modx->getOption('crosslinks.core_path', null, $modx->getOption('core_path') . 'components/crosslinks/');
/** @var Crosslinks $crosslinks */
$crosslinks = $modx->getService('crosslinks', 'Crosslinks', $corePath . 'model/crosslinks/', [
    'core_path' => $corePath
]);

if ($crosslinks) {
    if (class_exists($className)) {
        $handler = new $className($modx, $scriptProperties);
        if (get_class($handler) == $className) {
            $handler->run();
        } else {
            $modx->log(xPDO::LOG_LEVEL_ERROR, $className. ' could not be initialized!', '', 'Crosslinks Plugin');
        }
    } else {
        $modx->log(xPDO::LOG_LEVEL_ERROR, $className. ' was not found!', '', 'Crosslinks Plugin');
    }
}

return;