<?php
/**
 * Get list Links
 *
 * @package crosslinks
 * @subpackage processors
 */

use TreehillStudio\Crosslinks\Processors\ObjectGetListProcessor;

class CrosslinksLinkGetListProcessor extends ObjectGetListProcessor
{
    public $classKey = 'CrosslinksLink';
    public $defaultSortField = 'text';
    public $objectType = 'crosslinks.link';

    /**
     * {@inheritDoc}
     * @param xPDOQuery $c
     * @return xPDOQuery
     */
    public function prepareQueryBeforeCount(xPDOQuery $c)
    {
        $query = $this->getProperty('query');
        if (!empty($query)) {
            $c->where([
                'text:LIKE' => '%' . $query . '%'
            ]);
        }
        $c->select($this->modx->getSelectColumns($this->classKey, $this->classKey));
        $c->leftJoin('modResource', 'Resource');
        $c->select($this->modx->getSelectColumns('modResource', 'Resource', 'resource_', ['id', 'pagetitle']));
        return $c;
    }

    public function prepareRow(xPDOObject $object)
    {
        $ta = $object->toArray('', false, true);

        $parameter = json_decode($ta['parameter'], true);
        $parameterArray = [];
        $i = 1;
        if ($parameter) {
            foreach ($parameter as $key => $value) {
                $parameterArray[] = [
                    'id' => $i,
                    'key' => $key,
                    'value' => $value,
                    'rank' => $i,
                ];
            }
        }
        $ta['parameter'] = ($parameterArray) ? json_encode($parameterArray) : '';
        $ta['pagetitle'] = $ta['resource_pagetitle'] . ' (' . $ta['resource_id'] . ')';
        return $ta;
    }
}

return 'CrosslinksLinkGetListProcessor';
