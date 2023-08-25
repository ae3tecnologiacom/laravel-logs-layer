<?php

namespace Ae3\LaravelLogsLayer\Tests\Constraints;

use PHPUnit\Framework\Constraint\Constraint;

class InstanceOfAtLeastOne extends Constraint
{
    /**
     * @var string
     */
    protected string $expectedClass;

    /**
     * @param string $expectedClass
     */
    public function __construct(string $expectedClass)
    {
        parent::__construct();
        $this->expectedClass = $expectedClass;
    }

    /**
     * @param $other
     * @return bool
     */
    public function matches($other): bool
    {
        foreach ($other as $item) {
            if ($item instanceof $this->expectedClass) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return "contains at least one instance of '{$this->expectedClass}'";
    }
}