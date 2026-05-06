<?php

namespace NFePHP\NFe\Traits;

trait FormatDecimalTrait
{
    /**
     * Format a value to a specific number of decimal places
     *
     * @param mixed $value
     * @param int $decimals
     * @return string
     */
    protected function formatDecimal($value, $decimals = 2)
    {
        if ($value === null || $value === '') {
            return '';
        }
        return number_format((float) $value, $decimals, '.', '');
    }
}
