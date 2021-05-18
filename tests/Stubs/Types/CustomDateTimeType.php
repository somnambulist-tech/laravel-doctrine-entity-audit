<?php

namespace Somnambulist\EntityAudit\Tests\Stubs\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Types\Types;
use Somnambulist\EntityAudit\Tests\Stubs\MyDateTime;

/**
 * Class CustomDateTimeType
 *
 * @package    Somnambulist\EntityAudit\Tests\Stubs\Types
 * @subpackage Somnambulist\EntityAudit\Tests\Stubs\Types\CustomDateTimeType
 */
class CustomDateTimeType extends Type
{
    public function getName()
    {
        return Types::DATETIME_MUTABLE;
    }

    /**
     * {@inheritdoc}
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return $platform->getDateTimeTypeDeclarationSQL($fieldDeclaration);
    }

    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return ($value !== null) ? $value->format($platform->getDateTimeFormatString()) : null;
    }

    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value === null || $value instanceof MyDateTime) {
            return $value;
        }

        $val = MyDateTime::createFromFormat($platform->getDateTimeFormatString(), $value);

        if (!$val) {
            $val = new MyDateTime($value);
        }

        if (!$val) {
            throw ConversionException::conversionFailedFormat($value, $this->getName(), $platform->getDateTimeFormatString());
        }

        return $val;
    }
}
