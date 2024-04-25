<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Matcher\Pattern\Expander;

use Aeon\Calendar\Gregorian\DateTime;
use Coduo\ToString\StringConverter;

trait DateTimeComparisonTrait
{
    use BacktraceBehavior;

    private readonly DateTime $boundary;

    private ?string $error = null;

    public function __construct(string $boundary)
    {
        if (!\is_string($boundary)) {
            throw new \InvalidArgumentException(\sprintf('Before expander require "string", got "%s".', new StringConverter($boundary)));
        }

        try {
            $this->boundary = DateTime::fromString($boundary);
        } catch (\Exception $exception) {
            throw new \InvalidArgumentException(\sprintf('Boundary value "%s" is not a valid date, date time or time.', new StringConverter($boundary)));
        }
    }

    public static function is(string $name) : bool
    {
        return static::getName() === $name;
    }

    public function match($value) : bool
    {
        $this->backtrace->expanderEntrance(static::getName(), $value);

        if (!\is_string($value)) {
            $this->error = \sprintf('%s expander require "string", got "%s".', static::getName(), new StringConverter($value));
            $this->backtrace->expanderFailed(static::getName(), $value, $this->error);

            return false;
        }

        return $this->compare($value);
    }

    public function getError() : ?string
    {
        return $this->error;
    }

    abstract protected static function getName() : string;

    /**
     * @param string $value raw value
     * @param DateTime $datetime value converted in DateTime object
     */
    abstract protected function handleComparison(string $value, DateTime $datetime) : bool;

    private function compare(string $value) : bool
    {
        try {
            $datetime = DateTime::fromString($value);
        } catch (\Exception $e) {
            $this->error = \sprintf('Value "%s" is not a valid date, date time or time.', new StringConverter($value));
            $this->backtrace->expanderFailed(static::getName(), $value, $this->error);

            return false;
        }

        return $this->handleComparison($value, $datetime);
    }
}
