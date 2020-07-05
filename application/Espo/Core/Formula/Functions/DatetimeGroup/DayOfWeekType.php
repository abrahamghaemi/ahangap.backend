<?php


namespace Espo\Core\Formula\Functions\DateTimeGroup;

use \Espo\Core\Exceptions\Error;

class DayOfWeekType extends \Espo\Core\Formula\Functions\Base
{
    protected function init()
    {
        $this->addDependency('dateTime');
    }

    public function process(\StdClass $item)
    {
        if (!property_exists($item, 'value')) {
            return true;
        }

        if (!is_array($item->value)) {
            throw new Error();
        }

        if (count($item->value) < 1) {
             throw new Error();
        }

        $value = $this->evaluate($item->value[0]);

        $timezone = null;
        if (count($item->value) > 1) {
             $timezone = $this->evaluate($item->value[1]);
        }

        if (empty($value)) return -1;

        if (strlen($value) > 11) {
            $resultString = $this->getInjection('dateTime')->convertSystemDateTime($value, $timezone, 'e');
        } else {
            $resultString = $this->getInjection('dateTime')->convertSystemDate($value, 'e');
        }

        $result = intval($resultString);

        return $result;
    }
}
