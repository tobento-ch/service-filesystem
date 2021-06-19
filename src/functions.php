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

namespace Tobento\Service\Filesystem;

use Tobento\Service\Filesystem\File;

if (!function_exists('_file')) {
    /**
     * Helper function to create file object.
     *
     * @param string $file The file /home/www/file.txt
     * @return File
     */
    function _file(string $file): File
    {
        return new File($file);
    }
}