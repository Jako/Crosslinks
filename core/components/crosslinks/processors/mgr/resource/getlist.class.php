<?php
/**
 * Get list resources
 *
 * @package crosslinks
 * @subpackage processors
 */

// Compatibility between 2.x/3.x
if (file_exists(MODX_PROCESSORS_PATH . 'resource/getlist.class.php')) {
    require_once MODX_PROCESSORS_PATH . 'resource/getlist.class.php';
} elseif (!class_exists('modResourceGetListProcessor')) {
    class_alias(\MODX\Revolution\Processors\Resource\GetList::class, \modResourceGetListProcessor::class);
}

class CrosslinksResourceGetListProcessor extends modResourceGetListProcessor
{
    public function prepareQueryBeforeCount(xPDOQuery $c)
    {
        $query = $this->getProperty('query');
        if (!empty($query)) {
            $c->where([
                'pagetitle:LIKE' => '%' . $query . '%',
                'OR:id:=' => $query
            ]);
        }
        $parents = $this->getProperty('parents', '');
        if ($parents) {
            $c->where([
                'parent:IN' => array_map('intval', explode(',', $parents))
            ]);
        }
        return $c;
    }

    public function prepareQueryAfterCount(xPDOQuery $c)
    {
        $id = $this->getProperty('id', '');
        if ($id) {
            $c->where([
                'id:IN' => array_map('intval', explode(',', $id))
            ]);
        }
        return $c;
    }

    public function beforeIteration(array $list)
    {
        if (!$this->getProperty('id') && $this->getProperty('combo', false) === 'true') {
            $empty = [
                'id' => '',
                'pagetitle' => '',
            ];
            $list[] = $empty;
        }

        return $list;
    }

    public function prepareRow(xPDOObject $object)
    {
        $ta = parent::prepareRow($object);
        if ($this->getProperty('combo', false) === 'true') {
            $ta['pagetitle'] = $ta['pagetitle'] . ' (' . $ta['id'] . ')';
        }

        return $ta;
    }
}

return 'CrosslinksResourceGetListProcessor';
