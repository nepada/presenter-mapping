<?php
declare(strict_types = 1);

namespace Nepada\PresenterMapping;

use RuntimeException;

/**
 * Common interface for exceptions
 */
interface Exception extends \Throwable
{

}


/**
 * The exception that is thrown when a method call is invalid for the object's
 * current state, method has been invoked at an illegal or inappropriate time.
 */
class InvalidStateException extends RuntimeException implements Exception
{

}
