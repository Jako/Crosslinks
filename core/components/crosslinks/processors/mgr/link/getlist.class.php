<?php
/**
 * Get list links
 *
 * @package crosslinks
 * @subpackage processors
 */

class CrosslinksLinkGetListProcessor extends modObjectGetListProcessor
{
    public $classKey = 'CrosslinksLink';
    public $languageTopics = array('crosslinks:default');
    public $defaultSortField = 'text';
    public $defaultSortDirection = 'ASC';
    public $objectType = 'crosslinks.link';

    public function prepareQueryBeforeCount(xPDOQuery $c)
    {
        $query = $this->getProperty('query');
        if (!empty($query)) {
            $c->where(array(
                'text:LIKE' => '%' . $query . '%'
            ));
        }
        $c->select($this->modx->getSelectColumns($this->classKey, $this->classKey));
        $c->leftJoin('modResource', 'Resource');
        $c->select($this->modx->getSelectColumns('modResource', 'Resource', 'resource_', array('id', 'pagetitle')));
        return $c;
    }

    public function prepareRow(xPDOObject $object)
    {
        $ta = $object->toArray('', false, true);

        $parameter = json_decode($ta['parameter'], true);
        $parameterArray = array();
        $i = 1;
        if ($parameter) {
            foreach ($parameter as $value) {
                $parameterArray[] = array(
                    'id' => $i,
                    'key' => key($value),
                    'value' => reset($value),
                    'rank' => $i,
                );
            }
        }
        $ta['parameter'] = json_encode($parameterArray);
        $ta['pagetitle'] = $ta['resource_pagetitle'] . ' (' . $ta['resource_id'] . ')';
        return $ta;
    }
}

return 'CrosslinksLinkGetListProcessor';
