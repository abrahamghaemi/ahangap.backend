<?php


namespace Espo\SelectManagers;

class EmailAccount extends \Espo\Core\SelectManagers\Base
{
    protected function access(&$result)
    {
        if (!$this->user->isAdmin()) {
        	$result['whereClause'][] = [
        		'assignedUserId' => $this->user->id
        	];
        }
    }
}
