<?php
/**
 * This class is responsible for data formatting
 *
 * @author Fike Etki <etki@etki.name>
 * @version 0.1.0
 * @since 0.1.0
 * @package blogmvc
 * @subpackage yii
 */
class DateFormatter extends \CComponent
{
    /**
     * Cached current datetime.
     *
     * @var \DateTime
     * @since 0.1.0
     */
    protected $now;
    /**
     * Isolated list of time intervals/units to clarify code.
     *
     * @var string[]
     * @since 0.1.0
     */
    protected $intervals = array(
        'y' => 'years',
        'm' => 'months',
        'd' => 'days',
        'h' => 'hours',
        'i' => 'minutes',
        's' => 'seconds',
    );

    /**
     * Typical initializer.
     *
     * @since 0.1.0
     */
    public function init()
    {
        $this->reset();
    }

    /**
     * Formats date to 'xxx {unit}, zzz {unit} ago'.
     *
     * @throws \BadMethodCallException Thrown if incorrect $unitsLimit is
     * provided.
     *
     * @param string|\DateTime $date Date to be formatted.
     * @param int $unitsLimit How many date units (years, months, days, etc.)
     * should be processed.
     * @return string Formatted date.
     * @since 0.1.0
     */
    public function format($date, $unitsLimit=2)
    {
        if ($unitsLimit < 1) {
            $message = 'Maximum units limit should be not less than one';
            throw new \BadMethodCallException($message);
        }
        if (!$date instanceof \DateTime) {
            try {
                $date = new \DateTime($date);
            } catch (\Exception $e) {
                $message = 'Invalid date provided';
                throw new \BadMethodCallException($message, 0, $e);
            }
        }
        $diff = $date->diff($this->now);

        $counter = 0; // Number of date units already analyzed. I need just two @tm.
        $dateInterval = array();
        foreach ($this->intervals as $key => $interval) {
            if ($counter >= $unitsLimit) {
                break;
            } elseif ($counter > 0) {
                $counter++;
            }
            if ($diff->$key > 0) {
                $dateInterval[] = $this->formatInterval($diff->$key, $interval);
                break;
            }
        }
        if (sizeof($dateInterval) > 0) {
            return \Yii::t('templates', 'timeInterval.ago', array(
                '{interval}' => implode(', ', $dateInterval),
            ));
        }
        // No seconds ago? Return 'just now'.
        return \Yii::t('templates', 'timeInterval.justNow');
    }

    /**
     * Internal function to clarify the code.
     *
     * @param int $interval Interval in specified time units,
     * @param string $tKey Translation key.
     * @return string Formatted interval.
     * @since 0.1.0
     */
    protected function formatInterval($interval, $tKey)
    {
        return \Yii::t('templates', 'timeInterval.'.$tKey, $interval);
    }

    /**
     * Resets current time.
     *
     * @since 0.1.0
     */
    public function reset()
    {
        $this->now = new \DateTime;
    }
}
 