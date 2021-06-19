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
 * FileFormats
 *
 * @see https://wiki.selfhtml.org/wiki/MIME-Type/Übersicht
 */
trait FileFormats
{
    /**
     * @var array The formats with its corresponding mime types. ['css' => ['text/css']]
     */    
    protected array $formats = [];
        
    /**
     * Adds the defaults formats.
     *
     * @return void
     */    
    protected function addDefaultFormats(): void
    {
        // Add default formats.
        $this->addFormat('html', 'text/html');
        $this->addFormat('html', 'application/xhtml+xml');
        $this->addFormat('txt', 'text/plain');
        $this->addFormat('json', 'application/json');
        $this->addFormat('json', 'application/x-json');
        $this->addFormat('js', 'application/javascript');
        $this->addFormat('js', 'application/x-javascript');
        $this->addFormat('js', 'text/javascript');
        $this->addFormat('css', 'text/css');
        $this->addFormat('php', 'application/x-httpd-php');
        $this->addFormat('php', 'text/x-php');
        $this->addFormat('phtml', 'application/x-httpd-php');
        $this->addFormat('xml', 'text/xml');
        $this->addFormat('xml', 'application/xml');
        $this->addFormat('xml', 'application/x-xml');
        $this->addFormat('rdf', 'application/rdf+xml');
        $this->addFormat('atom', 'application/atom+xml');
        $this->addFormat('rss', 'application/rss+xml');
        $this->addFormat('form', 'application/x-www-form-urlencoded');
        $this->addFormat('pdf', 'application/pdf');
        $this->addFormat('jpg', 'image/jpeg');
        $this->addFormat('jpeg', 'image/jpeg');
        $this->addFormat('jpe', 'image/jpeg');
        $this->addFormat('png', 'image/png');
        $this->addFormat('gif', 'image/gif');
        $this->addFormat('webp', 'image/webp');
        $this->addFormat('tif', 'image/tiff');
        $this->addFormat('svg', 'image/svg+xml');
        $this->addFormat('psd', 'image/vnd.adobe.photoshop');
        $this->addFormat('bmp', 'image/bmp');
        $this->addFormat('ico', 'image/vnd.microsoft.icon');
        $this->addFormat('ai', 'application/postscript');
        $this->addFormat('eps', 'application/postscript');
        $this->addFormat('zip', 'application/zip');
        $this->addFormat('csv', 'text/csv');
        $this->addFormat('csv', 'text/x-csv');
        $this->addFormat('csv', 'text/plain');
        $this->addFormat('csv', 'application/csv');
        $this->addFormat('csv', 'application/x-csv');
        $this->addFormat('csv', 'application/vnd.ms-excel');
    }

    /**
     * Sets format with its corresponding mime type.
     *
     * @param string The format such as html, xml, js, css, json etc.
     * @param string The mime type such as 'text/plain', 'application/javascript'
     * @return static $this
     */    
    public function addFormat(string $format, string $mimeType): static
    {
        $this->formats[$format][] = $mimeType;
        return $this;
    }

    /**
     * Merge the formats with other formats.
     *
     * @param array $formats The formats.
     * @return static $this
     */    
    public function mergeFormats(array $formats): static
    {
        $this->formats = array_merge($this->formats, $formats);
        return $this;
    }
    
    /**
     * Gets format with its corresponding mime type.
     *
     * @param string $mimeType The mime type such as 'text/plain', 'application/javascript'.
     * @return string|null The format such as such as html, xml, js, css, json etc. (null is no mimeType matches a format.)
     */    
    public function getFormat(string $mimeType): null|string
    {
        $mimeType = strtolower($mimeType);
        
        foreach ($this->formats as $format => $mimeTypes) {

            if (in_array($mimeType, (array) $mimeTypes)) {
                return $format;
            }
        }
        
        return null;
    }

    /**
     * Gets all formats.
     *
     * @return array All formats added.
     */    
    public function getFormats(): array
    {
        return $this->formats;
    }
        
    /**
     * Gets the mime type for the format.
     *
     * @param string $format The format such as such as html, xml, js, css, json etc.
     * @param int $part image(part 1 ) / gif(part 2 )  0 = full
     * @return string|null The mime type such as application/json etc. (null if no format matches).
     */
    public function getMimeType(string $format, int $part = 0): null|string
    {
        $format = strtolower($format);
        
        if ($part === 0) {
            return isset($this->formats[$format]) ? $this->formats[$format][0] : null;
        }
            
        return (isset($this->formats[$format])) ? $this->getMimeTypePart($this->formats[$format][0], $part) : null;
    }

    /**
     * Gets the mime types for the format.
     *
     * @param array $formats The formats such as such as ['html', 'xml', 'js', 'css', 'json']
     * @param int $part image(part 1 ) / gif(part 2 )  0 = full
     * @return array The mime types.
     */
    public function getMimeTypes(array $formats, int $part = 0): array
    {
        $mimeTypes = [];
        
        foreach($formats as $format) {
            
            if (isset($this->formats[$format])) {
                
                foreach($this->formats[$format] as $mimeType) {
                    
                    if ($part === 0) {
                        $mimeTypes[] = $mimeType;
                    } else {
                        $mimeTypes[] = $this->getMimeTypePart($mimeType, $part);
                    }
                }
            }
        }
        
        return array_values(array_unique($mimeTypes));
    }
    
    /**
     * Gets the mime type part set. Note: This cannot be 100% trusted.
     *
     * @param string $mimeType The mime type such as 'image/jpeg'
     * @param int $part image(part 1 ) / gif(part 2 )
     * @return string|null The mime type part or null.
     */    
    protected function getMimeTypePart(string $mimeType, int $part): null|string
    {
        $part = $part-1;
        $parts = explode('/', $mimeType);
        return isset($parts[$part]) ? $parts[$part] : null;
    }    
}