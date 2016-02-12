<?php

include_once 'vendor/autoload.php';

use Cron\CronExpression;
use \DateTime;
use \DateInterval;

$crontab = [];
if (isPost()) {
    define('GOOD', (int) $_POST['good']);
    define('WARN', (int) $_POST['warn']);
    $crontab = parseCrontab($_POST['crontab']);
}

$end = new DateTime('midnight tomorrow');
$minuteInterval = new DateInterval('PT1M');
$runs = [];
$totalRuns = 0;
foreach ($crontab as $schedule => $cronAmount) {
    $cron = CronExpression::factory($schedule);

    $nextRunDate = new DateTime('midnight today');
    //First date should be included in the count
    $nextRunDate = $cron->getNextRunDate($nextRunDate, 0, true);
    while ($nextRunDate <= $end) {
        $date = $nextRunDate->format('Y-m-d H:i:s');
        $runs[$date] = isset($runs[$date]) ? $runs[$date] + $cronAmount : $cronAmount;
        $totalRuns += $cronAmount;
        $nextRunDate = $cron->getNextRunDate($nextRunDate);
    }
}
$begin = new DateTime('midnight today');
?>

<html>
    <head>
        <title>Cronjob Planner</title>
    </head>
    <body>
        <form method="POST">
            <textarea name="crontab" rows="15" cols="200"><?php echo isset($_POST['crontab']) ? $_POST['crontab'] : ''; ?></textarea>
            <div></div>
            <label>Good Treshold</label>
            <input type="text" name="good" value="<?php echo isset($_POST['good']) ? $_POST['good'] : '5';?>">
            <label>Warn Treshold</label>
            <input type="text" name="warn" value="<?php echo isset($_POST['warn']) ? $_POST['warn'] : '12';?>">
            <input type="submit" value="GO!">
        </form>
        <div>Run amount: <?php echo $totalRuns; ?></div>
        <div>Average runs per minute: <?php echo round($totalRuns / count($runs), 2); ?></div>
    <?php if (!empty($runs)) : ?>
        <table style="text-align: center;">
            <tr>
                <td></td>
            <?php for ($minute = 0; $minute <= 59; $minute++) : ?>
                <th><?php printf('%02d', $minute); ?></th>
            <?php endfor; ?>
            </tr>
        <?php
        while ($begin != $end) :
            $currentHour = $begin->format('H');
        ?>
            <tr>
                <th><?php echo $currentHour; ?>:00</th>
            <?php
            while ($begin->format('H') === $currentHour) :
                $date = $begin->format('Y-m-d H:i:s');
                $runCount = isset($runs[$date]) ? $runs[$date] : 0;
            ?>
                <td data-runCount="<?php echo $runCount; ?>" title="<?php echo $begin->format('H:i:s'); ?>">
                    <?php echo ($runCount !== 0) ? $runCount : '&nbsp;'; ?>
                </td>
            <?php
                $begin->add($minuteInterval);
            endwhile;
            ?>
            </tr>
        <?php endwhile; ?>
        </table>
    <?php endif; ?>
    <script>
        var goodTreshold = <?php echo GOOD; ?>,
            warnTreshold = <?php echo WARN; ?>,
            cells = document.getElementsByTagName("td");
        for (var i = 0; i < cells.length; i++) {
            //do something to each div like
            var amount = cells[i].getAttribute('data-runCount');
            if (amount === null) {
                continue;
            }
            if (amount <= goodTreshold) {
                cells[i].style.backgroundColor = "#00FF00";
            } else if (amount <= warnTreshold) {
                cells[i].style.backgroundColor = "#FEA500";
            } else {
                cells[i].style.backgroundColor = "#FC0000";
            }
        }
    </script>
    </body>
</html>
