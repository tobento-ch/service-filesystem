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
use Tobento\Service\Filesystem\Test\Mock\Formats;

/**
 * FileFormatsTest tests
 */
class FileFormatsTest extends TestCase
{    
    public function testAddFormatMethod()
    {
        $f = new Formats();
        $f->addFormat(format: 'jpeg', mimeType: 'image/jpeg');
        
        $this->assertSame('jpeg', $f->getFormat(mimeType: 'image/jpeg'));
    }
    
    public function testMergeFormatMethod()
    {
        $f = new Formats();
        $f->addFormat(format: 'jpeg', mimeType: 'image/jpeg');
        $f->addFormat(format: 'jpg', mimeType: 'image/jpeg');
        $f->addFormat('csv', 'text/csv');
        $f->addFormat('csv', 'text/plain');
        
        $f->mergeFormats([
            'jpeg' => ['image/jpeg'],
            'csv' => ['text/x-csv'],
            'gif' => ['image/gif'],
        ]);
        
        $this->assertEquals(
            [
                'jpeg' => ['image/jpeg'],
                'jpg' => ['image/jpeg'],
                'csv' => ['text/x-csv'],
                'gif' => ['image/gif'],
            ],
            $f->getFormats()
        );
    }    

    public function testGetFormatMethod()
    {
        $f = new Formats();
        
        $f->addFormat(format: 'jpeg', mimeType: 'image/jpeg');
                
        $this->assertSame('jpeg', $f->getFormat(mimeType: 'image/jpeg'));
    }
    
    public function testGetFormatMethodReturnNullIfNotFound()
    {
        $f = new Formats();
                
        $this->assertSame(null, $f->getFormat(mimeType: 'image/jpeg'));
    }    
    
    public function testGetFormatMethodWithMultipleOfSameMimeTypesReturnsFirstFound()
    {
        $f = new Formats();
        
        $f->addFormat(format: 'jpeg', mimeType: 'image/jpeg');
        $f->addFormat(format: 'jpg', mimeType: 'image/jpeg');
                
        $this->assertSame('jpeg', $f->getFormat(mimeType: 'image/jpeg'));
    }
    
    public function testGetFormatMethodWithMultipleOfSameFormatsReturnsFirstFound()
    {
        $f = new Formats();
        
        $f->addFormat('txt', 'text/plain');
        $f->addFormat('csv', 'text/csv');
        $f->addFormat('csv', 'text/plain');
                
        $this->assertSame('txt', $f->getFormat(mimeType: 'text/plain'));
    }
    
    public function testGetFormatMethodWithFormatSpecifiedReturnsFormat()
    {
        $f = new Formats();
        
        $f->addFormat(format: 'jpeg', mimeType: 'image/jpeg');
                
        $this->assertSame('jpeg', $f->getFormat(mimeType: 'jpeg'));
    }
    
    public function testGetFormatMethodWithFormatSpecifiedReturnsNullIfInvalid()
    {
        $f = new Formats();
        
        $f->addFormat(format: 'jpeg', mimeType: 'image/jpeg');
                
        $this->assertSame(null, $f->getFormat(mimeType: 'jpe'));
    }    
    
    public function testGetFormatsMethod()
    {
        $f = new Formats();
        
        $f->addFormat(format: 'jpeg', mimeType: 'image/jpeg');
        $f->addFormat(format: 'jpg', mimeType: 'image/jpeg');
        $f->addFormat('csv', 'text/csv');
        $f->addFormat('csv', 'text/plain');
        
        $this->assertEquals(
            [
                'jpeg' => ['image/jpeg'],
                'jpg' => ['image/jpeg'],
                'csv' => ['text/csv', 'text/plain']
            ],
            $f->getFormats()
        );
    }

    public function testGetMimeTypeMethod()
    {
        $f = new Formats();
        
        $f->addFormat('txt', 'text/plain');
                
        $this->assertSame('text/plain', $f->getMimeType(format: 'txt'));
    }
    
    public function testGetMimeTypeMethodWithPartOne()
    {
        $f = new Formats();
        
        $f->addFormat('txt', 'text/plain');

        $this->assertSame('text', $f->getMimeType(format: 'txt', part: 1));
    }
    
    public function testGetMimeTypeMethodWithPartTwo()
    {
        $f = new Formats();
        
        $f->addFormat('txt', 'text/plain');

        $this->assertSame('plain', $f->getMimeType(format: 'txt', part: 2));
    }
    
    public function testGetMimeTypeMethodReturnsFirstFound()
    {
        $f = new Formats();
        
        $f->addFormat('csv', 'text/csv');
        $f->addFormat('csv', 'text/plain');

        $this->assertSame('text/csv', $f->getMimeType('csv'));
    }

    public function testGetMimeTypesMethod()
    {
        $f = new Formats();
        
        $f->addFormat('txt', 'text/plain');
                
        $this->assertSame(['text/plain'], $f->getMimeTypes(formats: ['txt']));
    }
    
    public function testGetMimeTypesMethodWithMulipleFormats()
    {
        $f = new Formats();
        
        $f->addFormat('txt', 'text/plain');
        $f->addFormat('csv', 'text/csv');
                
        $this->assertSame(['text/plain', 'text/csv'], $f->getMimeTypes(formats: ['txt', 'csv']));
    }

    public function testGetMimeTypesMethodWithMulipleFormatsOfSameFormat()
    {
        $f = new Formats();
        
        $f->addFormat('txt', 'text/plain');
        $f->addFormat('csv', 'text/csv');
        $f->addFormat('csv', 'text/plain');
                
        $this->assertEquals(
            ['text/plain', 'text/csv'],
            $f->getMimeTypes(formats: ['txt', 'csv'])
        );
    }
    
    public function testGetMimeTypesMethodWithMulipleFormatsOfSameFormatAndPartOne()
    {
        $f = new Formats();
        
        $f->addFormat('txt', 'text/plain');
        $f->addFormat('csv', 'text/csv');
        $f->addFormat('csv', 'text/plain');
        $f->addFormat('jpg', 'image/jpeg');
                
        $this->assertEquals(
            ['text', 'image'],
            $f->getMimeTypes(formats: ['txt', 'csv', 'jpg'], part: 1)
        );
    }

    public function testGetMimeTypesMethodWithMulipleFormatsOfSameFormatAndPartTwo()
    {
        $f = new Formats();
        
        $f->addFormat('txt', 'text/plain');
        $f->addFormat('csv', 'text/csv');
        $f->addFormat('csv', 'text/plain');
        $f->addFormat('jpg', 'image/jpeg');
                
        $this->assertEquals(
            ['plain', 'csv', 'jpeg'],
            $f->getMimeTypes(formats: ['txt', 'csv', 'jpg'], part: 2)
        );
    }    
}