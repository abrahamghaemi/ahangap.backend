<?php


namespace Espo\Core\Templates\SelectManagers;

class Event extends \Espo\Core\SelectManagers\Base
{
    protected function filterPlanned(&$result)
    {
        $result['whereClause'][] = array(
            'status' => 'Planned'
        );
    }

    protected function filterHeld(&$result)
    {
        $result['whereClause'][] = array(
            'status' => 'Held'
        );
    }

    protected function filterTodays(&$result)
    {
        $result['whereClause'][] = $this->convertDateTimeWhere(array(
            'type' => 'today',
            'attribute' => 'dateStart',
            'timeZone' => $this->getUserTimeZone()
        ));
    }
}

