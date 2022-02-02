<?php
/**
 * Create a Link
 *
 * @package crosslinks
 * @subpackage processors
 */

use TreehillStudio\Crosslinks\Processors\ObjectCreateProcessor;

class CrosslinksLinkCreateProcessor extends ObjectCreateProcessor
{
    public $classKey = 'CrosslinksLink';
    public $objectType = 'crosslinks.link';

    /**
     * {@inheritDoc}
     * @return bool
     */
    public function beforeSave()
    {
        $text = $this->getProperty('text');
        if (empty($text)) {
            $this->addFieldError('text', $this->modx->lexicon('crosslinks.link_err_ns_text'));
        } elseif ($this->doesAlreadyExist(['text' => $text])) {
            $this->addFieldError('text', $this->modx->lexicon('crosslinks.link_err_ae_text'));
        } elseif (preg_match('/[^\d\w\-_.:,; ]+/u', $text)) {
            $this->addFieldError('text', $this->modx->lexicon('crosslinks.link_err_nv_text'));
        }

        $resource = $this->getProperty('resource');
        if (empty($resource)) {
            $this->addFieldError('resource', $this->modx->lexicon('crosslinks.link_err_ns_resource'));
        }

        $parameter = json_decode($this->getProperty('parameter'), true);
        $parameterValues = [];
        if ($parameter) {
            foreach ($parameter as $value) {
                $parameterValues[$value['key']] = $value['value'];
            }
        }

        $this->object->set('parameter', $parameterValues ? json_encode($parameterValues) : '');
        $this->object->set('createdon', time());
        $this->object->set('createdby', $this->modx->user->get('id'));

        return parent::beforeSave();
    }
}

return 'CrosslinksLinkCreateProcessor';
