<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Tests\Matcher\Pattern\Expander;

use Coduo\PHPMatcher\Backtrace;
use Coduo\PHPMatcher\Matcher\Pattern\Expander\Before;
use PHPUnit\Framework\TestCase;

class BeforeTest extends TestCase
{
    public static function examplesProvider() : array
    {
        return [
            ['+2 day', 'today'],
            ['2018-02-06T04:20:33', '2017-02-06T04:20:33'],
            ['2017-02-06T04:20:33', '2018-02-06T04:20:33', 'Value "2018-02-06T04:20:33" is after or equal to "2017-02-06T04:20:33+00:00".'],
            ['2024-04-21', '2024-04-21', 'Value "2024-04-21" is after or equal to "2024-04-21T00:00:00+00:00".'],
            ['10:00:00', '00:01:00'],
            ['10:00:00', '00:01:00'],
            ['10:00:00.123456', '10:00:00.000000'],
            ['10:00:00.123456', '10:00:00.123457', 'Value "10:00:00.123457" is after or equal to "@date@T10:00:00+00:00".'],
            ['10:30', '00:08:00'],
            ['8:30', '8:29'],
            ['10:00:00', '10:00:00', 'Value "10:00:00" is after or equal to "@date@T10:00:00+00:00".'],
            ['10:00:00', '10:00:01', 'Value "10:00:01" is after or equal to "@date@T10:00:00+00:00".'],
        ];
    }

    public static function invalidCasesProvider() : array
    {
        return [
            ['today', 'ipsum lorem', 'Value "ipsum lorem" is not a valid date, date time or time.'],
            ['2017-02-06T04:20:33', 'ipsum lorem', 'Value "ipsum lorem" is not a valid date, date time or time.'],
            ['today', 5, 'before expander require "string", got "5".'],
            ['03:00', '99:88:77', 'Value "99:88:77" is not a valid date, date time or time.'],
        ];
    }

    /**
     * @dataProvider examplesProvider
     */
    public function test_examples(string $boundary, string $value, ?string $expectedError = null) : void
    {
        $expander = new Before($boundary);
        $expander->setBacktrace(new Backtrace\InMemoryBacktrace());
        $this->assertEquals($expectedError === null, $expander->match($value));

        if (\is_string($expectedError)) {
            $expectedError = \str_replace('@date@', (new \DateTime())->format('Y-m-d'), $expectedError);
        }

        $this->assertEquals($expectedError, $expander->getError());
    }

    /**
     * @dataProvider invalidCasesProvider
     */
    public function test_error_when_matching_fail(string $boundary, mixed $value, string $errorMessage) : void
    {
        $expander = new Before($boundary);
        $expander->setBacktrace(new Backtrace\InMemoryBacktrace());
        $this->assertFalse($expander->match($value));
        $this->assertEquals($errorMessage, $expander->getError());
    }
}
