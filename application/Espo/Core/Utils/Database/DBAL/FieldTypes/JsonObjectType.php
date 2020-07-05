<?php


namespace Espo\Core\Utils\Database\DBAL\FieldTypes;

class JsonObjectType extends \Doctrine\DBAL\Types\TextType
{
    const JSON_OBJECT = 'jsonObject';

    public function getName()
    {
        return self::JSON_OBJECT;
    }

    public static function getDbTypeName()
    {
        return 'TEXT';
    }

}
