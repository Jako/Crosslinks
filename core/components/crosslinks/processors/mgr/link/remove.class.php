<?php
/**
 * Remove a Link
 *
 * @package crosslinks
 * @subpackage processors
 */

use TreehillStudio\Crosslinks\Processors\ObjectRemoveProcessor;

class CrosslinksLinkRemoveProcessor extends ObjectRemoveProcessor
{
    public $classKey = 'CrosslinksLink';
    public $objectType = 'crosslinks.link';
}

return 'CrosslinksLinkRemoveProcessor';
