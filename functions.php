<?php

use Cron\CronExpression;

/**
 * Parse provided crontab and return an array whose keys are schedules
 * and values the amount of times they appear in the crontab.
 * Empty lines and comment lines will be stripped
 *
 * @param  string $crontab
 * @return array
 */
function parseCrontab($crontab)
{
    $result = array_map(
        'getScheduleExpression',
        explode(PHP_EOL, $_POST['crontab'])
    );
    //Clear empty values
    $result = array_filter($result);
    return joinDuplicateCrons($result);
}

/**
 * Extract a schedule expression from a cron line.
 * Empty lines and comment lines will be stripped
 *
 * @param  string $cronLine
 * @return string|null
 */
function getScheduleExpression($cronLine)
{
    $cronLine = trim($cronLine);
    //Discard empty lines and comments
    if (empty($cronLine) || strpos($cronLine, '#') === 0) {
        return;
    }
    //Keep only the schedule part and get rid of everything else
    list($minute, $hour, $day, $month, $weekday) = explode(' ', $cronLine);
    $schedule = implode(' ', [$minute, $hour, $day, $month, $weekday]);
    if (CronExpression::isValidExpression($schedule)) {
        return $schedule;
    }
    //Special cases like @yearly, @hourly etc
    if (CronExpression::isValidExpression($minute)) {
        return $minute;
    }
    return;
}

/**
 * Return an array with all schedules from provided crontab as keys
 * and the number they occur as values
 *
 * @param  array $crontab
 * @return array
 */
function joinDuplicateCrons(array $crontab)
{
    $crons = [];
    foreach ($crontab as $schedule) {
        $crons[$schedule] = isset($crons[$schedule]) ? ++$crons[$schedule] : 1;
    }
    return $crons;
}

/**
 * Check if request is a post request
 *
 * @return boolean
 */
function isPost()
{
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}
