<?php
/**
 * This file is part of the nepada/presenter-mapping.
 * Copyright (c) 2016 Petr Morávek (petr@pada.cz)
 */

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
