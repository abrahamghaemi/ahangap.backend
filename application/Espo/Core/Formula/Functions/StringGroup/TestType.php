<?php


namespace Espo\Core\Formula\Functions\StringGroup;

use \Espo\Core\Exceptions\Error;

class TestType extends \Espo\Core\Formula\Functions\Base
{
    public function process(\StdClass $item)
    {
        if (!property_exists($item, 'value') || !is_array($item->value)) {
            throw new Error('Value for \'String\\Test\' item is not an array.');
        }
        if (count($item->value) < 2) {
            throw new Error('Bad arguments passed to \'String\\Test\'.');
        }
        $string = $this->evaluate($item->value[0]);
        $regexp = $this->evaluate($item->value[1]);

        if (!is_string($string)) {
            return false;
        }
        if (!is_string($regexp)) {
            return false;
        }

        return !!preg_match($regexp, $string);
    }
}
