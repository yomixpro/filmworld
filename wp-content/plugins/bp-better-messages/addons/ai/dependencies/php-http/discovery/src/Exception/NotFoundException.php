<?php
/**
 * @license MIT
 *
 * Modified by __root__ on 08-April-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace BetterMessages\Http\Discovery\Exception;

use BetterMessages\Http\Discovery\Exception;

/**
 * Thrown when a discovery does not find any matches.
 *
 * @final do NOT extend this class, not final for BC reasons
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
/* final */ class NotFoundException extends \RuntimeException implements Exception
{
}
