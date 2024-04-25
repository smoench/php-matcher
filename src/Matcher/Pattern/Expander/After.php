<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Matcher\Pattern\Expander;

use Aeon\Calendar\Gregorian\DateTime;
use Coduo\PHPMatcher\Matcher\Pattern\PatternExpander;
use Coduo\ToString\StringConverter;

final class After implements PatternExpander
{
    use DateTimeComparisonTrait;

    /**
     * @var string
     */
    public const NAME = 'after';

    protected function handleComparison(string $value, DateTime $datetime) : bool
    {
        if ($datetime->isBeforeOrEqualTo($this->boundary)) {
            $this->error = \sprintf('Value "%s" is before or equal to "%s".', new StringConverter($value), new StringConverter($this->boundary));
            $this->backtrace->expanderFailed(self::NAME, $value, $this->error);

            return false;
        }

        $this->backtrace->expanderSucceed(self::NAME, $value);

        return true;
    }

    protected static function getName() : string
    {
        return self::NAME;
    }
}
