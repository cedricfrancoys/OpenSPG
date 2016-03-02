<?php

namespace LocationBundle\Type;

use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;

class PointType extends Type
{
	const POINT = 'point';

	public function getName()
	{
		return self::POINT;
	}

	public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
	{
        return 'POINT';
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        //Null fields come in as empty strings
        if($value == '') {
            return null;
        }
 
        $data = unpack('x/x/x/x/corder/Ltype/dlat/dlon', $value);
        return $data;
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (!$value) return;
 
        return pack('xxxxcLdd', '0', 1, $value['lat'], $value['lon']);
    }
}