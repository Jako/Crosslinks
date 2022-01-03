<?php
/**
 * Update link
 *
 * @package crosslinks
 * @subpackage processors
 */

class CrosslinksLinkUpdateProcessor extends modObjectUpdateProcessor
{
    public $classKey = 'CrosslinksLink';
    public $languageTopics = array('crosslinks:default');
    public $objectType = 'crosslinks.link';

    public function beforeSave()
    {
        $text = $this->getProperty('text');
        if (empty($text)) {
            $this->addFieldError('text', $this->modx->lexicon('crosslinks.link_err_ns_text'));
        } elseif (preg_match('/[^\d\w-_.:,; ]+/\u', $text)) {
            $this->addFieldError('text', $this->modx->lexicon('crosslinks.link_err_nv_text'));
        }

        $resource = $this->getProperty('resource');
        if (empty($resource)) {
            $this->addFieldError('resource', $this->modx->lexicon('crosslinks.link_err_ns_resource'));
        }

        $this->object->set('editedon', time());
        $this->object->set('editedby', $this->modx->user->get('id'));

        return parent::beforeSave();
    }
}

return 'CrosslinksLinkUpdateProcessor';
