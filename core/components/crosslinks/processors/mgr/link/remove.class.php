<?php
/**
 * Remove link
 *
 * @package crosslinks
 * @subpackage processors
 */

class CrosslinksLinkRemoveProcessor extends modObjectRemoveProcessor
{
    public $classKey = 'CrosslinksLink';
    public $languageTopics = array('crosslinks:default');
    public $objectType = 'crosslinks.link';
}

return 'CrosslinksLinkRemoveProcessor';
