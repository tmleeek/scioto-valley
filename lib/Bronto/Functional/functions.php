<?php
/**
 * This file was generated by the ConvertToLegacy class in bronto-legacy.
 * The purpose of the conversion was to maintain PSR-0 compliance while
 * the main development focuses on modern styles found in PSR-4.
 *
 * For the original:
 * @see src/Bronto/Functional/functions.php
 */

/**
 * Package level convenience for creating the some option
 *
 * @param mixed $value
 * @retun Bronto_Functional_Some
 */
function some($value)
{
    return new Bronto_Functional_Some($value);
}

/**
 * Package level convenience for creating the none type
 *
 * @return Bronto_Functional_None
 */
function none()
{
    return new Bronto_Functional_None();
}
