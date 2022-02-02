<?php
/**
 * Update a Link From Grid
 *
 * @package crosslinks
 * @subpackage processors
 */

require_once(dirname(__FILE__) . '/update.class.php');

class CrosslinksLinkUpdateFromGridProcessor extends CrosslinksLinkUpdateProcessor
{
    /**
     * {@inheritDoc}
     * @return bool|string
     */
    public function initialize()
    {
        $data = $this->getProperty('data');
        if (empty($data)) {
            return $this->modx->lexicon('invalid_data');
        }
        $data = json_decode($data, true);
        if (empty($data)) {
            return $this->modx->lexicon('invalid_data');
        }
        $this->setProperties($data);
        $this->unsetProperty('data');

        return parent::initialize();
    }
}

return 'CrosslinksLinkUpdateFromGridProcessor';
