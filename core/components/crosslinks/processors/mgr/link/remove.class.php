<?php
/**
 * Remove link
 *
 * @package crosslinks
 * @subpackage processor
 */

class CrosslinksLinkRemoveProcessor extends modObjectRemoveProcessor
{
    public $classKey = 'CrosslinksLink';
    public $languageTopics = array('crosslinks:default');
    public $objectType = 'crosslinks.link';
}

return 'CrosslinksLinkRemoveProcessor';
