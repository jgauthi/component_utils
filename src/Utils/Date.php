<?php
namespace Jgauthi\Component\Utils;

use DateInterval;
use DatePeriod;
use DateTime;
use DateTimeInterface;
use DateTimeZone;
use Exception;
use Nette\InvalidArgumentException;
use Nette\Utils\DateTime as NetteDateTime;

// Additionnals methods with https://doc.nette.org/en/utils/datetime
class Date extends NetteDateTime
{
    /**
     * Init a datetime class with several check exception
     * @throws Exception
     */
    static public function new(string $time = 'now', ?DateTimeZone $timezone = null, ?bool $future = null): static
    {
        $date = new static($time, $timezone);
        return self::valideDate($date, $timezone, $future);
    }

    /**
     * Init a date class from string format (YYYY-MM-DD) or (YYYY-M-D) with several check exception
     * @throws Exception
     */
    static public function createFromString(?string $time, ?DateTimeZone $timezone = null): static
    {
        $format = 'Y-m-d';

        if (empty($time)) {
            return self::new();
        } elseif (!preg_match('#^([0-9]{4})(-[0-1]?[0-9])(-[0-3]?[0-9])( ([0-2]?[0-9])(:[0-5]?[0-9])(:[0-5]?[0-9]))?#', $time, $extract)) { // Date US
            throw new InvalidArgumentException('The date don\'t match with the format: YYYY-MM-DD');
        }

        $format = (!empty($extract[4]) ? 'Y-m-d H:i:s' : 'Y-m-d');
        $date = self::createFromFormat($format, $time, $timezone);
        if ($format === 'Y-m-d') {
            $date->setTime(0, 0);
        }

        return $date;
    }

    /**
     * Valide a DateTimeInterface object
     * @param DateTimeInterface|false $date new static can return false, this method invalidate this value
     * @param DateTimeZone|null $timezone
     * @param bool|null $future null=no check, false=date must be in past, true=date must be future
     * @return DateTime|DateTimeInterface
     * @throws Exception
     */
    static public function valideDate(DateTimeInterface|false $date, ?DateTimeZone $timezone = null, ?bool $future = null): static
    {
        $error = DateTime::getLastErrors();
        if (!empty($error['warning_count']) && 'The parsed date was invalid' == current($error['warnings'])) {
            throw new InvalidArgumentException( current($error['warnings']) );
        } elseif (!empty($error['error_count'])) {
            throw new Exception( current($error['errors']) );
        } elseif (!$date instanceof DateTimeInterface) {
            throw new InvalidArgumentException(__METHOD__.': Invalid Date');
        } elseif ($future !== null) {
            $today = new static('now', $timezone);
            if ($future && $date <= $today) {
                throw new InvalidArgumentException("The date {$date->format('d/m/Y')} must be future.");
            } elseif (false === $future && $date > $today) {
                throw new InvalidArgumentException("The date {$date->format('d/m/Y')} must be in the past.");
            }
        }

        return $date;
    }

    /**
     * Date export to string with localisation Day / Month
     * @param string $pattern Some patterns from php.net/manual/fr/datetime.format.php
     */
    static public function export(DateTimeInterface $dateTime, string $pattern, string $localisation = 'fr_FR'): string
    {
        $pattern = str_replace(
            ['l', 'd', 'm', 'M', 'Y', 'H', '\h', 'i', 's', 'P', 'e'],
            ['EEEE', 'dd', 'LL', 'LLLL', 'YYYY', 'HH', "'h'", 'mm', 'ss', 'OOOO', 'VV'],
            $pattern
        );

        return datefmt_format(datefmt_create($localisation, pattern: $pattern), $dateTime);
    }

    /* // Difference ENTRE $date_debut = '2009-08' ET $date_fin = '2010-04';
        return array
        (
          2009 => array (
            0 => 8,
            1 => 9,
            2 => 10,
            3 => 11,
            4 => 12,
          ),
          2010 => array (
            0 => 1,
            1 => 2,
            2 => 3,
            3 => 4,
          ),
        )*/


    static public function mois_difference(string $date_debut, string $date_fin): array
    {
        $mois = (int) (mb_substr($date_debut, -2, 2));
        $annee = (int) (mb_substr($date_debut, 0, 4));
        $fin_annee = (int) (mb_substr($date_fin, 0, 4));
        $fin_mois = (int) (mb_substr($date_fin, -2, 2));

        $liste_date = [];
        while (true) {
            if ($mois > $fin_mois && $annee === $fin_annee) {
                break;
            } elseif ($mois > 12) {
                $mois = 1;
                ++$annee;
            }

            if (!isset($liste_date[$annee])) {
                $liste_date[$annee] = [];
            }

            $liste_date[$annee][] = $mois++;
        }

        return $liste_date;
    }


    /**
     * @throws Exception
     */
    static public function date_interval(DateTimeInterface $start, DateTime $end, string $format = 'Y-m-d'): array
    {
        // Calcul date période
        $interval = new DateInterval('P1D');
        $dateRange = new DatePeriod($start, $interval, $end->modify('+1 day'));
        $datePeriode = [];

        /** @var DateTimeInterface $date */
        foreach ($dateRange as $date) {
            $datePeriode[] = $date->format($format);
        }

        return $datePeriode;
    }


    /**
     * Convertir une date(time) en timestamp, Date FR(DD-MM-YYYY) et US(YYYY-MM-DD) supporté seulement
     * @throws InvalidArgumentException
     */
    static public function get_timestamp_from_date(string $date): int
    {
        if (empty($date)) {
            throw new InvalidArgumentException('Date is empty');
        }

        $exp = [
            'y' => '([0-9]{4})',
            'm' => '([0-1]?[0-9])',
            'd' => '([0-3]?[0-9])',
            'h' => '([0-2]?[0-9])',
            'i' => '([0-5]?[0-9])?',
            's' => '([0-5]?[0-9])?',
        ];

        $heure = "{$exp['h']}(:|h){$exp['i']}:?{$exp['s']}";
        $regexp_us = "{$exp['y']}(-|/){$exp['m']}(-|/){$exp['d']}( $heure)?";
        $regexp_fr = "{$exp['d']}(-|/){$exp['m']}(-|/){$exp['y']}( $heure)?";

        // Format date de base (définir emplacement du mois selon le format)
        if (preg_match("#^{$regexp_us}#i", trim($date), $row)) {
            $index = ['y' => 1, 'd' => 5];
        } elseif (preg_match("#^{$regexp_fr}#i", trim($date), $row)) {
            $index = ['y' => 5, 'd' => 1];
        } else {
            return strtotime($date);
        }

        // Récupérer les données
        if ('1970' === $row[$index['y']]) {
            throw new InvalidArgumentException("The date format of {$date} is incorrect.");
        }

        $ts = [
            'y' => $row[$index['y']],
            'm' => $row[3],
            'd' => $row[$index['d']],
            'h' => 1,
            'i' => 0,
            's' => 0,
        ];

        // Ajouter les heures/minutes/secondes
        if (!empty($row[6]) && isset($row[7])) {
            $ts['h'] = $row[7];
            if (!empty($row[9])) {
                $ts['i'] = $row[9];
            }
            if (!empty($row[10])) {
                $ts['s'] = $row[10];
            }
        }

        return $timestamp = mktime($ts['h'], $ts['i'], $ts['s'], $ts['m'], $ts['d'], $ts['y']);
    }

    /**
     * Convert ISO 8601 values like P2DT15M33S to a total value of seconds.
     * @throws Exception
     */
    static public function ISO8601ToSeconds(string $ISO8601): int
    {
        $interval = new DateInterval($ISO8601);

        return ($interval->d * 24 * 60 * 60) +
            ($interval->h * 60 * 60) +
            ($interval->i * 60) +
            $interval->s;
    }

    /**
     * Fournit la différence d'une date interval en minute
     * Usage: applis/mindphp/date_diff.php
     */
    static public function intervalInMinutes(DateInterval $interval): int
    {
        $days = $interval->format('%a');
        $diff_minute = ($days * 24 * 60) + ($interval->h * 60) + $interval->i;

        return ($interval->invert) ? -$diff_minute : $diff_minute;
    }

    static public function DiffMinute(DateTimeInterface $start, DateTimeInterface $end): int
    {
        $interval = $start->diff($end);

        return self::intervalInMinutes($interval);
    }

    /**
     * Return format from date, can be use for detect date incomplet (ex: 2020-07) and usable on DateTime::format()
     * @throws InvalidArgumentException
     */
    static public function getFormatFromDate(string $date): string
    {
        if (preg_match('#^([0-9]{4})(/|-[0-1]?[0-9])?(/|-[0-3]?[0-9])?#', $date, $export)) { // Date US
            $year = 1;
            $month = 2;
            $day = 3;
        } elseif (preg_match('#^([0-3]?[0-9]/|-)?([0-1]?[0-9])(/|-[0-9]{4})#', $date, $export)) { // Date FR
            $year = 3;
            $month = 2;
            $day = 1;
        } else {
            throw new InvalidArgumentException(
                "The date format {$date} is incorrect, please use format YYYY-MM-DD or DD-MM-YYYY."
            );
        }

        $format = [$year => 'Y'];
        if (!empty($export[$month])) {
            $format[$month] = 'm';
        }
        if (!empty($export[$day])) {
            $format[$date] = 'd';
        }
        ksort($format);

        return implode('-', $format);
    }

    /**
     * @param DateTimeInterface|string $time example: 00:01:33
     */
    static public function time2seconds(DateTimeInterface|string $time): int
    {
        if ($time instanceof DateTimeInterface) {
            $time = $time->format('H:i:s');
        }

        $str_time = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $time);
        sscanf($str_time, "%d:%d:%d", $hours, $minutes, $seconds);

        return ($hours * 3600 + $minutes * 60 + $seconds);
    }
}
