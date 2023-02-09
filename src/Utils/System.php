<?php
namespace Jgauthi\Component\Utils;

class System
{
    static public function moreSystemMemory(int $timeLimit = 3600): void
    {
        set_time_limit($timeLimit);
        ini_set('max_execution_time', $timeLimit);
        ini_set('max_input_time', $timeLimit);
        ini_set('memory_limit', '2038M');
    }

    static public function hideAllErrors(): void
    {
        error_reporting(0);
    }
}
