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

/**
 * Service trait formats interface
 */
interface FileFormatsInterface
{
    /**
     * Sets format with its corresponding mime type.
     *
     * @param string The format such as html, xml, js, css, json etc.
     * @param string The mime type such as 'text/plain', 'application/javascript'
     * @return static $this
     */    
    public function addFormat(string $format, string $mimeType): static;

    /**
     * Merge the formats with other formats.
     *
     * @param array $formats The formats.
     * @return static $this
     */    
    public function mergeFormats(array $formats): static;
    
    /**
     * Gets format with its corresponding mime type.
     *
     * @param string $mimeType The mime type such as 'text/plain', 'application/javascript'.
     * @return string|null The format such as such as html, xml, js, css, json etc. (null is no mimeType matches a format.)
     */    
    public function getFormat(string $mimeType): null|string;

    /**
     * Gets all formats.
     *
     * @return array All formats added.
     */    
    public function getFormats(): array;
        
    /**
     * Gets the mime type for the format.
     *
     * @param string $format The format such as such as html, xml, js, css, json etc.
     * @param int $part image(part 1 ) / gif(part 2 )  0 = full
     * @return string|null The mime type such as application/json etc. (null if no format matches).
     */
    public function getMimeType(string $format, int $part = 0): null|string;

    /**
     * Gets the mime types for the format.
     *
     * @param array $formats The formats such as such as ['html', 'xml', 'js', 'css', 'json']
     * @param int $part image(part 1 ) / gif(part 2 )  0 = full
     * @return array The mime types.
     */
    public function getMimeTypes(array $formats, int $part = 0): array;
}