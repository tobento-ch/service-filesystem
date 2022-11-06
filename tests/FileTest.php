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

/**
 * FileTest tests
 */
class FileTest extends TestCase
{    
    public function testIsFileMethod()
    {
        $file = new File(__DIR__.'/src/foo/image.jpg');
            
        $this->assertTrue($file->isFile());
        
        $file = new File(__DIR__.'/src/foo/img.jpg');
            
        $this->assertFalse($file->isFile());
        
        $file = new File(__DIR__.'/src/foo/img');
            
        $this->assertFalse($file->isFile());
        
        $file = new File(__DIR__.'/src/foo/img.');
            
        $this->assertFalse($file->isFile());
        
        $file = new File(__DIR__.'/src/flowers.txt');
        
        $this->assertTrue($file->isFile());
    }
    
    public function testIsImageMethod()
    {
        $file = new File(__DIR__.'/src/foo/image.jpg');
            
        $this->assertTrue($file->isImage());
        
        $file = new File(__DIR__.'/src/foo/img.jpg');
            
        $this->assertFalse($file->isImage());
        
        $file = new File(__DIR__.'/src/foo/img');
            
        $this->assertFalse($file->isImage());
        
        $file = new File(__DIR__.'/src/foo/img.');
            
        $this->assertFalse($file->isImage());
        
        $file = new File(__DIR__.'/src/flowers.txt');
            
        $this->assertFalse($file->isImage());        
    }

    public function testIsHtmlImageMethod()
    {
        $file = new File(__DIR__.'/src/foo/image.jpg');
            
        $this->assertTrue($file->isHtmlImage());
        
        $file = new File(__DIR__.'/src/foo/img.jpg');
            
        $this->assertFalse($file->isHtmlImage());
        
        $file = new File(__DIR__.'/src/foo/img');
            
        $this->assertFalse($file->isHtmlImage());
        
        $file = new File(__DIR__.'/src/foo/img.');
            
        $this->assertFalse($file->isHtmlImage());
        
        $file = new File(__DIR__.'/src/flowers.txt');
            
        $this->assertFalse($file->isHtmlImage());        
    }
    
    public function testIsExtensionMethod()
    {
        $file = new File(__DIR__.'/src/foo/image.jpg');
            
        $this->assertTrue($file->isExtension(['jpeg']));
        
        $file = new File(__DIR__.'/src/foo/image.jpg');
            
        $this->assertTrue($file->isExtension(['jpeg', 'tiff']));
        
        $file = new File(__DIR__.'/src/foo/image.jpg');
            
        $this->assertFalse($file->isExtension(['gif', 'tiff']));
        
        $file = new File(__DIR__.'/src/flowers.txt');
            
        $this->assertTrue($file->isExtension(['plain']));         
    }
    
    public function testGetFileMethod()
    {
        $file = new File(__DIR__.'/src/foo/image.jpg');
            
        $this->assertSame(
            __DIR__.'/src/foo/image.jpg',
            $file->getFile()
        );         
    }
    
    public function testGetDirnameMethod()
    {
        $file = new File(__DIR__.'/src/foo/image.jpg');
            
        $this->assertSame(
            __DIR__.'/src/foo/',
            $file->getDirname()
        );         
    }

    public function testGetBasenameMethod()
    {
        $file = new File(__DIR__.'/src/foo/image.jpg');
            
        $this->assertSame(
            'image.jpg',
            $file->getBasename()
        );         
    }
    
    public function testGetFilenameMethod()
    {
        $file = new File(__DIR__.'/src/foo/image.jpg');
            
        $this->assertSame(
            'image',
            $file->getFilename()
        );         
    }
    
    public function testGetExtensionMethod()
    {
        $file = new File(__DIR__.'/src/foo/image.jpg');
            
        $this->assertSame('jpg', $file->getExtension());
        
        $file = new File(__DIR__.'/src/flowers.txt');
            
        $this->assertSame('txt', $file->getExtension());
    }
    
    public function testGetFolderPathMethod()
    {
        $file = new File(__DIR__.'/src/foo/image.jpg');
            
        $this->assertSame('', $file->getFolderPath());
    }
    
    public function testGetSizeMethod()
    {
        $file = new File(__DIR__.'/src/foo/image.jpg');
            
        $this->assertEquals(34221, $file->getSize());
    }
    
    public function testSizeMethod()
    {
        $file = new File(__DIR__.'/src/foo/image.jpg');
            
        $this->assertSame('33.42 KB', $file->size());
    }
    
    public function testGetImageSizeMethod()
    {
        $file = new File(__DIR__.'/src/foo/image.jpg');
        
        $this->assertEquals(500, $file->getImageSize(0));
        $this->assertEquals(375, $file->getImageSize(1));
        $this->assertEquals(8, $file->getImageSize('bits'));
    }
    
    public function testGetImageSizeMethodReturnsNullOnFail()
    {
        $file = new File(__DIR__.'/src/foo/foo.jpg');
        
        $this->assertEquals(null, $file->getImageSize());
        $this->assertEquals(null, $file->getImageSize(0));
    }
    
    public function testGetExifDataMethod()
    {
        $file = new File(__DIR__.'/src/foo/image.jpg');
        
        $this->assertEquals(34221, $file->getExifData()['FileSize']);
    }     
    
    public function testIsReadableMethod()
    {
        $file = new File(__DIR__.'/src/foo/image.jpg');
            
        $this->assertTrue($file->isReadable());
        
        $file = new File(__DIR__.'/src/flowers.txt');
            
        $this->assertTrue($file->isReadable());
        
        $file = new File(__DIR__.'/src/img.jpg');
            
        $this->assertFalse($file->isReadable());
    }
    
    public function testIsWritableMethod()
    {
        $file = new File(__DIR__.'/src/foo/image.jpg');
            
        $this->assertTrue($file->isWritable());
        
        $file = new File(__DIR__.'/src/flowers.txt');
            
        $this->assertTrue($file->isWritable());
        
        $file = new File(__DIR__.'/src/img.jpg');
            
        $this->assertFalse($file->isWritable());
    }
    
    public function testGetMimeTypeMethod()
    {
        $file = new File(__DIR__.'/src/foo/image.jpg');
        
        $this->assertSame('image/jpeg', $file->getMimeType());
        $this->assertSame('image', $file->getMimeType(1));
        $this->assertSame('jpeg', $file->getMimeType(2));

        $file = new File(__DIR__.'/src/flowers.txt');
        
        $this->assertSame('text/plain', $file->getMimeType());
        $this->assertSame('text', $file->getMimeType(1));
        $this->assertSame('plain', $file->getMimeType(2));
    }

    public function testGetMimeTypeJsonMethod()
    {
        $file = new File(__DIR__.'/src/flowers.json');
        
        $this->assertSame('application/json', $file->getMimeType());
        $this->assertSame('application', $file->getMimeType(1));
        $this->assertSame('json', $file->getMimeType(2));
    }

    public function testGetMimeTypeInvalidJsonMethodReturnsTextPlain()
    {
        $file = new File(__DIR__.'/src/flowers-invalid.json');
        
        $this->assertSame('text/plain', $file->getMimeType());
        $this->assertSame('text', $file->getMimeType(1));
        $this->assertSame('plain', $file->getMimeType(2));
    }
    
    public function testWithDirnameMethod()
    {
        $file = new File(__DIR__.'/src/flowers.txt');
        
        $newFile = $file->withDirname(__DIR__.'/src/foo/');
        
        $this->assertSame(__DIR__.'/src/foo/flowers.txt', $newFile->getFile());
        
        $this->assertNotEquals($file, $newFile);
        
        $newFile = $file->withDirname(__DIR__.'/src/foo');
        
        $this->assertSame(__DIR__.'/src/foo/flowers.txt', $newFile->getFile());
    }
    
    public function testWithFilenameMethod()
    {
        $file = new File(__DIR__.'/src/flowers.txt');
        
        $newFile = $file->withFilename('new-flowers');
        
        $this->assertSame(__DIR__.'/src/new-flowers.txt', $newFile->getFile());
        
        $this->assertNotEquals($file, $newFile);
    }

    public function testWithFolderPathMethod()
    {
        $file = new File(__DIR__.'/src/flowers.txt');
        
        $newFile = $file->withFolderPath('bar/foo');
        
        $this->assertSame(__DIR__.'/src/flowers.txt', $newFile->getFile());
        
        $this->assertSame('bar/foo', $newFile->getFolderPath());
        
        $this->assertNotEquals($file, $newFile);
    }
    
    public function testWithUniqueFilenameMethod()
    {
        $file = new File(__DIR__.'/src/flowers.txt');
        
        $newFile = $file->withUniqueFilename();
        
        $this->assertNotSame(__DIR__.'/src/flowers.txt', $newFile->getFile());
        
        $this->assertNotEquals($file, $newFile);
    }
    
    public function testCopyMethod()
    {
        $file = new File(__DIR__.'/src/flowers.txt');
        
        $copiedFile = $file->copy(__DIR__.'/src/tmp/flowers.txt');
        
        $this->assertTrue($copiedFile->isFile());
        
        $copiedFile->delete();
    }
    
    public function testCopyMethodReturnsNullOnFail()
    {
        $file = new File(__DIR__.'/src/foo.txt');
        
        $copiedFile = $file->copy(__DIR__.'/src/tmp/flowers.txt');
        
        $this->assertEquals(null, $copiedFile);
    }    

    public function testMoveMethod()
    {
        $file = new File(__DIR__.'/src/flowers.txt');
        
        $copiedFile = $file->copy(__DIR__.'/src/tmp/flowers.txt');
        
        $this->assertTrue($copiedFile->isFile());
        
        $movedFile = $copiedFile->move(__DIR__.'/src/tmp/flowers-moved.txt');
        
        $this->assertTrue($movedFile->isFile());
        
        $this->assertFalse($copiedFile->isFile());
        
        $movedFile->delete();
    }

    public function testMoveMethodReturnsNullOnFail()
    {
        $file = new File(__DIR__.'/src/foo.txt');
        
        $movedFile = $file->move(__DIR__.'/src/tmp/flowers.txt');
        
        $this->assertEquals(null, $movedFile);
    }
    
    public function testRenameMethod()
    {
        $file = new File(__DIR__.'/src/flowers.txt');
        
        $copiedFile = $file->copy(__DIR__.'/src/tmp/flowers.txt');
        
        $renamedFile = $copiedFile->rename('flowers-renamed');
        
        $this->assertTrue($renamedFile->isFile());
        
        $this->assertSame(__DIR__.'/src/tmp/flowers-renamed.txt', $renamedFile->getFile());
        
        $this->assertFalse($copiedFile->isFile());
                
        $renamedFile->delete();
    }

    public function testRenameMethodReturnsNullOnFail()
    {
        $file = new File(__DIR__.'/src/foo.txt');
        
        $renamedFile = $file->rename(__DIR__.'/src/tmp/flowers.txt');
        
        $this->assertEquals(null, $renamedFile);
    }
    
    public function testDeleteMethod()
    {
        $file = new File(__DIR__.'/src/flowers.txt');
        
        $copiedFile = $file->copy(__DIR__.'/src/tmp/flowers.txt');
        
        $this->assertTrue($copiedFile->delete());
        
        $this->assertFalse($copiedFile->isFile());
    }

    public function testDeleteMethodReturnsTrueIfFileDoesNotExist()
    {
        $file = new File(__DIR__.'/src/foo.txt');
        
        $this->assertTrue($file->delete());
    }
    
    public function testGetContentMethod()
    {
        $file = new File(__DIR__.'/src/flowers.json');
        
        $this->assertSame('{"name": "Scaevola"}', $file->getContent());
    }
    
    public function testIsWithinDirMethod()
    {
        $file = new File(__DIR__.'/src/flowers.json');
        $this->assertTrue($file->isWithinDir(__DIR__.'/src/'));
        $this->assertTrue($file->isWithinDir(__DIR__.'/src'));
        $this->assertTrue($file->isWithinDir(__DIR__));
        $this->assertFalse($file->isWithinDir(__DIR__.'/foo/'));
        $this->assertFalse($file->isWithinDir(__DIR__.'/foo'));
        
        $file = new File(__DIR__.'/src/foo/../image.jpg');
        $this->assertFalse($file->isWithinDir(__DIR__.'/src/foo/'));
        $this->assertFalse($file->isWithinDir(__DIR__.'/src/foo'));
        $this->assertTrue($file->isWithinDir(__DIR__.'/src/'));
        
        $file = new File(__DIR__.'/src/foo/%2e%2e/image.jpg');
        $this->assertFalse($file->isWithinDir(__DIR__.'/src/foo'));
        $this->assertFalse($file->isWithinDir(__DIR__.'/src/'));
    }
}