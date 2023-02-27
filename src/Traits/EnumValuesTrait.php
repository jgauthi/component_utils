<?php
namespace Jgauthi\Component\Traits;

/**
 * @required php 8.1+
 * Add new method list() for get array(KEY=>VALUES) on Enum
 */
trait EnumValuesTrait
{
    public static function list(): array
    {
        return array_reduce(
            self::cases(),
            static fn (array $choices, self $type) => $choices + [$type->name => $type->value],
            [],
        );
    }
}