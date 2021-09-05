<?php
/**
 * Get list resources
 *
 * @package crosslinks
 * @subpackage processor
 */

include_once MODX_CORE_PATH . 'model/modx/processors/resource/getlist.class.php';

class CrosslinksResourceGetListProcessor extends modResourceGetListProcessor
{
    public function prepareQueryBeforeCount(xPDOQuery $c)
    {
        $query = $this->getProperty('query');
        if (!empty($query)) {
            $c->where(array(
                'pagetitle:LIKE' => '%' . $query . '%',
                'OR:id:=' => $query
            ));
        }
        $parents = $this->getProperty('parents', '');
        if ($parents) {
            $c->where(array(
                'parent:IN' => array_map('intval', explode(',', $parents))
            ));
        }
        return $c;
    }

    public function prepareQueryAfterCount(xPDOQuery $c)
    {
        $id = $this->getProperty('id', '');
        if ($id) {
            $c->where(array(
                'id:IN' => array_map('intval', explode(',', $id))
            ));
        }
        return $c;
    }

    public function beforeIteration(array $list)
    {
        if (!$this->getProperty('id') && $this->getProperty('combo', false) === 'true') {
            $empty = array(
                'id' => '',
                'pagetitle' => '',
            );
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
