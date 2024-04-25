<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Matcher\Pattern\Expander;

use Aeon\Calendar\Gregorian\DateTime;
use Coduo\PHPMatcher\Matcher\Pattern\PatternExpander;
use Coduo\ToString\StringConverter;

final class Before implements PatternExpander
{
    use DateTimeComparisonTrait;

    /**
     * @var string
     */
    public const NAME = 'before';

    protected function handleComparison(string $value, DateTime $datetime) : bool
    {
        if ($datetime->isAfterOrEqualTo($this->boundary)) {
            $this->error = \sprintf('Value "%s" is after or equal to "%s".', $value, new StringConverter($this->boundary));
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
