<?php
namespace Jgauthi\Component\Utils;

class Numeric
{
    /**
     * @noinspection PhpUnnecessaryLocalVariableInspection
     */
    static public function number_format(
        int|float|string $number,
        int $decimal = 2,
        string $decimal_separator = '.',
        string $thousands_separator = ',',
    ): string {
        $numberFormatted = number_format(floatval($number), $decimal, $decimal_separator, $thousands_separator);
        $numberFormatted = str_replace(',00', '', $numberFormatted);
        $numberFormatted = preg_replace('#(,\d)0$#i', '$1', $numberFormatted);

        return $numberFormatted;
    }

    static public function number_format_fr(int|float|string $number, int $decimal = 2): string
    {
        return self::number_format($number, $decimal, ',', ' ');
    }
}