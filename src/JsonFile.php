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

use JsonException;
use Throwable;

/**
 * JsonFile
 */
class JsonFile extends File
{
    /**
     * If file is json extension.
     *
     * @return bool
     */    
    public function isJson(): bool
    {
        return $this->isExtension(['json']);
    }

    /**
     * Converts json string to array.
     *
     * @param null|string $string A json string.
     * @return array
     */    
    public function toArray(?string $string = null): array
    {
        if ($string === null) {
            $string = $this->getContent();
        }
        
        try {
            return json_decode($string, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            return [];
        }
    }
    
    /**
     * Gets the content.
     *
     * @return string A string
     */    
    public function getContent(): string
    {
        if (! $this->isJson()) {
            return '';
        }
        
        return parent::getContent();
    }
}