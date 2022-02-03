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

namespace Tobento\Service\Filesystem\Test;

use PHPUnit\Framework\TestCase;
use Tobento\Service\Filesystem\File;
use Psr\Http\Message\ResponseInterface;
use Nyholm\Psr7\Factory\Psr17Factory;

/**
 * FileResponseTest tests
 */
class FileResponseTest extends TestCase
{    
    public function testDownloadResponseMethod()
    {
        $file = new File(__DIR__.'/src/foo/image.jpg');

        $psr17Factory = new Psr17Factory();
        $response = $psr17Factory->createResponse(200);

        $downloadResponse = $file->downloadResponse($response, $psr17Factory);
        
        $this->assertInstanceOf(ResponseInterface::class, $downloadResponse);
        
        $this->assertSame(200, $downloadResponse->getStatusCode());
    }
    
    public function testDownloadResponseMethodReturns404StatusCodeIfFileDoesNotExist()
    {
        $file = new File(__DIR__.'/src/foo/image-a.jpg');

        $psr17Factory = new Psr17Factory();
        $response = $psr17Factory->createResponse(200);

        $downloadResponse = $file->downloadResponse($response, $psr17Factory);
        
        $this->assertInstanceOf(ResponseInterface::class, $downloadResponse);
        
        $this->assertSame(404, $downloadResponse->getStatusCode());
    }
    
    public function testFileResponseMethod()
    {
        $file = new File(__DIR__.'/src/foo/image.jpg');

        $psr17Factory = new Psr17Factory();
        $response = $psr17Factory->createResponse(200);

        $fileResponse = $file->fileResponse($response, $psr17Factory);
        
        $this->assertInstanceOf(ResponseInterface::class, $fileResponse);
        
        $this->assertSame(200, $fileResponse->getStatusCode());
    }
    
    public function testFileResponseMethodReturns404StatusCodeIfFileDoesNotExist()
    {
        $file = new File(__DIR__.'/src/foo/image-a.jpg');

        $psr17Factory = new Psr17Factory();
        $response = $psr17Factory->createResponse(200);

        $fileResponse = $file->fileResponse($response, $psr17Factory);
        
        $this->assertInstanceOf(ResponseInterface::class, $fileResponse);
        
        $this->assertSame(404, $fileResponse->getStatusCode());
    }    
}