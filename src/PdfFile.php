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

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;

/**
 * PdfFile
 */
class PdfFile extends File
{
    /**
     * @var string
     */    
    protected string $content = '';
    
    /**
     * If file is pdf extension.
     *
     * @return bool
     */    
    public function isPdf(): bool
    {
        return $this->isExtension(['pdf']);
    }

    /**
     * Set the content.
     *
     * @param string $content The content
     * @return static
     */    
    public function content(string $content): static
    {
        $new = clone $this;
        $new->content = $content;
        return $new;
    }
    
    /**
     * Gets the content.
     *
     * @return string A string
     */    
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * Gets the size of the file.
     *
     * @return int The size.
     */    
    public function getSize(): int
    {
        if (!$this->isFile() || $this->mustExist === false) {
            return strlen($this->getContent());
        }
        
        return (int) filesize($this->file);
    }

    /**
     * Get the response to download the file.
     * 
     * @param ResponseInterface $response
     * @param StreamFactoryInterface $streamFactory
     * @return ResponseInterface
     */
    public function downloadResponse(
        ResponseInterface $response,
        StreamFactoryInterface $streamFactory
    ): ResponseInterface {
        
        if ($this->isFile()) {
            return parent::downloadResponse($response, $streamFactory);
        }
 
        if ($this->getSize() === 0) {
            return $response->withStatus(404);
        }
        
        $response->getBody()->write($this->getContent());
            
        return $response->withHeader('Content-Type', 'application/pdf')
                        ->withHeader('Content-Disposition', 'attachment; filename='.$this->getBasename())
                        ->withHeader('Content-Length', (string) $this->getSize());
    }

    /**
     * Get the response to display the file on browser.
     * 
     * @param ResponseInterface $response
     * @param StreamFactoryInterface $streamFactory
     * @return ResponseInterface
     */
    public function fileResponse(
        ResponseInterface $response,
        StreamFactoryInterface $streamFactory
    ): ResponseInterface {
        
        if ($this->isFile()) {
            return parent::fileResponse($response, $streamFactory);
        }

        if ($this->getSize() === 0) {
            return $response->withStatus(404);
        }
        
        $response->getBody()->write($this->getContent());
        
        return $response->withHeader('Content-Type', 'application/pdf')
                        ->withHeader('Content-Disposition', 'inline; filename='.$this->getBasename())
                        ->withHeader('Content-Length', (string) $this->getSize());
    }      
}