<?php

/**
 * TOBENTO
 *
 * @copyright   Tobias Strub, TOBENTO
 * @license     MIT License, see LICENSE file distributed with this source code.
 * @author      Tobias Strub
 * @link        https://www.tobento.ch
 */

declare(strict_types=1);

namespace Tobento\Service\Filesystem\Test\Mock;

use Tobento\Service\Filesystem\FileFormatsInterface;
use Tobento\Service\Filesystem\FileFormats;

/**
 * Formats
 */
class Formats implements FileFormatsInterface
{
    use FileFormats; 
}