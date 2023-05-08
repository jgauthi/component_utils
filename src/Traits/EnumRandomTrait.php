<?php
namespace Jgauthi\Component\Traits;

trait EnumRandomTrait
{
    public static function random(): self
    {
        $count = count(self::cases()) - 1;

        return self::cases()[rand(0, $count)];
    }
}
