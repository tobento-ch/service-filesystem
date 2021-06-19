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
use Tobento\Service\Filesystem\Dir;
use Tobento\Service\Filesystem\File;
use Tobento\Service\Filesystem\Folder;

/**
 * DirTest tests
 */
class DirTest extends TestCase
{    
    public function testIsDirMethod()
    {            
        $this->assertTrue((new Dir())->isDir(__DIR__.'/src/foo/'));
        
        $this->assertTrue((new Dir())->isDir(__DIR__.'/src/foo'));
        
        $this->assertTrue((new Dir())->isDir(__DIR__.'/src/foo//'));
        
        $this->assertTrue((new Dir())->isDir(__DIR__.'/src/foo/////'));
        
        $this->assertFalse((new Dir())->isDir(__DIR__.'/src/foos'));
        
        $this->assertFalse((new Dir())->isDir(__DIR__.'/src/foo/image.jpg'));
    }
    
    public function testIsFileMethod()
    {
        $this->assertTrue((new Dir())->isFile(__DIR__.'/src/foo/image.jpg'));
        
        $this->assertFalse((new Dir())->isFile(__DIR__.'/src/foo/image-a.jpg'));
        
        $this->assertFalse((new Dir())->isFile(__DIR__.'/src/foo/'));
        
        $this->assertFalse((new Dir())->isFile(__DIR__.'/src/foo'));
        
        $this->assertFalse((new Dir())->isFile(__DIR__.'/src/foo//'));
        
        $this->assertFalse((new Dir())->isFile(__DIR__.'/src/foo/////'));
        
        $this->assertFalse((new Dir())->isFile(__DIR__.'/src/foos'));
    }
    
    public function testIsWritableMethod()
    {
        $this->assertTrue((new Dir())->isWritable(__DIR__.'/src/foo/image.jpg'));
        
        $this->assertFalse((new Dir())->isWritable(__DIR__.'/src/foo/image-a.jpg'));
        
        $this->assertTrue((new Dir())->isWritable(__DIR__.'/src/foo/'));
        
        $this->assertTrue((new Dir())->isWritable(__DIR__.'/src/foo'));
        
        $this->assertTrue((new Dir())->isWritable(__DIR__.'/src/foo//'));
        
        $this->assertTrue((new Dir())->isWritable(__DIR__.'/src/foo/////'));
        
        $this->assertFalse((new Dir())->isWritable(__DIR__.'/src/foos'));
    }
    
    public function testHasMethod()
    {
        $this->assertTrue((new Dir())->has(__DIR__.'/src/foo/image.jpg'));
        
        $this->assertFalse((new Dir())->has(__DIR__.'/src/foo/image-a.jpg'));
        
        $this->assertTrue((new Dir())->has(__DIR__.'/src/foo/'));
        
        $this->assertTrue((new Dir())->has(__DIR__.'/src/foo'));
        
        $this->assertTrue((new Dir())->has(__DIR__.'/src/foo//'));
        
        $this->assertTrue((new Dir())->has(__DIR__.'/src/foo/////'));
        
        $this->assertFalse((new Dir())->has(__DIR__.'/src/foos'));
    }
    
    public function testIsEmptyMethod()
    {
        $this->assertFalse((new Dir())->isEmpty(__DIR__.'/src/foo/'));
        
        $this->assertFalse((new Dir())->isEmpty(__DIR__.'/src/foo'));
        
        $this->assertFalse((new Dir())->isEmpty(__DIR__.'/src/foo//'));
        
        $this->assertFalse((new Dir())->isEmpty(__DIR__.'/src/foo/////'));
        
        $this->assertTrue((new Dir())->isEmpty(__DIR__.'/src/foos'));
        
        $this->assertFalse((new Dir())->isEmpty(__DIR__.'/src/foo/image.jpg'));
        
        $this->assertTrue((new Dir())->isEmpty(__DIR__.'/src/foo/image-a.jpg'));
    }    
    
    public function testGetFilesMethod()
    {
        $dir = new Dir();
        
        $files = $dir->getFiles(__DIR__.'/src/foo/');
        
        $this->assertInstanceOf(File::class, $files[0]);
        $this->assertEquals(1, count($files));
        
        $files = $dir->getFiles(__DIR__.'/src/foo');
        
        $this->assertEquals([], $files);
        
        $files = $dir->getFiles(__DIR__.'/src/foos/');
        
        $this->assertEquals([], $files);
        
        $files = $dir->getFiles(__DIR__.'/src/');
        
        $this->assertInstanceOf(File::class, $files[0]);
        $this->assertInstanceOf(File::class, $files[1]);
        $this->assertInstanceOf(File::class, $files[2]);
        $this->assertEquals(3, count($files));
    }
    
    public function testGetFilesMethodWithFilesToIgnore()
    {
        $dir = new Dir();
        
        $dir->setFilesToIgnore(['flowers.txt']);
        
        $files = $dir->getFiles(__DIR__.'/src/');
        
        $this->assertInstanceOf(File::class, $files[0]);
        $this->assertInstanceOf(File::class, $files[1]);
        $this->assertEquals(2, count($files));
        
        $dir->setFilesToIgnore(['foo/bar/flowers.txt']);
        
        $files = $dir->getFiles(__DIR__.'/src/');
        
        $this->assertInstanceOf(File::class, $files[0]);
        $this->assertInstanceOf(File::class, $files[1]);
        $this->assertInstanceOf(File::class, $files[2]);
        $this->assertEquals(3, count($files));
        
        $dir->setFilesToIgnore(['foo/bar/flowers.txt']);
        
        $files = $dir->getFiles(__DIR__.'/src/', 'foo/bar');
        
        $this->assertInstanceOf(File::class, $files[0]);
        $this->assertInstanceOf(File::class, $files[1]);
        $this->assertEquals(2, count($files));
    }
    
    public function testGetFilesMethodWithFilesToIgnoreExtensions()
    {
        $dir = new Dir();
        
        $dir->setFilesToIgnoreExtensions(['txt']);
        
        $files = $dir->getFiles(__DIR__.'/src/');
        
        $this->assertInstanceOf(File::class, $files[0]);
        $this->assertEquals(1, count($files));
        
        $dir->setFilesToIgnoreExtensions(['json', 'txt']);
        
        $files = $dir->getFiles(__DIR__.'/src/');
        
        $this->assertEquals(0, count($files));
    }
    
    public function testGetFilesMethodWithFilesToIgnoreExtensionsAndFormatIsNotAdded()
    {
        // this does not check the mime type only the extension
        $dir = new Dir(true, false);
        
        $dir->setFilesToIgnoreExtensions(['txt']);
        
        $files = $dir->getFiles(__DIR__.'/src/');
        
        $this->assertInstanceOf(File::class, $files[0]);
        $this->assertEquals(2, count($files));
    }    
    
    public function testGetFoldersAllMethod()
    {
        $dir = new Dir();
        
        $folders = $dir->getFoldersAll(__DIR__.'/src/');
        
        $this->assertInstanceOf(Folder::class, $folders[1]);
        
        $this->assertSame(1, $folders[1]->id());
        $this->assertSame(0, $folders[1]->parentId());
        $this->assertSame(0, $folders[1]->level());
        $this->assertSame('bar', $folders[1]->folderPath());
        
        $this->assertSame(2, $folders[2]->id());
        $this->assertSame(1, $folders[2]->parentId());
        $this->assertSame(1, $folders[2]->level());
        $this->assertSame('bar/wee', $folders[2]->folderPath());
        
        $this->assertSame(4, $folders[4]->id());
        $this->assertSame(3, $folders[4]->parentId());
        $this->assertSame(2, $folders[4]->level());
        $this->assertSame('bar/zoo/sub', $folders[4]->folderPath());
        
        $this->assertSame(6, count($folders));
    }
    
    public function testGetFoldersAllMethodWithFoldersToIgnore()
    {
        $dir = new Dir();
        
        $dir->setFoldersToIgnore(['bar/zoo']);
        
        $folders = $dir->getFoldersAll(__DIR__.'/src/');
        
        $this->assertSame(4, count($folders));
    }

    public function testGetFoldersMethod()
    {
        $dir = new Dir();
        
        $folders = $dir->getFolders(__DIR__.'/src/');
        
        $this->assertInstanceOf(Folder::class, $folders[1]);
        
        $this->assertSame(1, $folders[1]->id());
        $this->assertSame(0, $folders[1]->parentId());
        $this->assertSame(0, $folders[1]->level());
        $this->assertSame('bar', $folders[1]->folderPath());
        
        $this->assertSame(2, $folders[2]->id());
        $this->assertSame(0, $folders[2]->parentId());
        $this->assertSame(0, $folders[2]->level());
        $this->assertSame('foo', $folders[2]->folderPath());
        
        $this->assertSame(3, $folders[3]->id());
        $this->assertSame(0, $folders[3]->parentId());
        $this->assertSame(0, $folders[3]->level());
        $this->assertSame('tmp', $folders[3]->folderPath());
        
        $this->assertSame(3, count($folders));
    }

    public function testCreateMethod()
    {
        $dir = new Dir();
        
        $created = $dir->create(__DIR__.'/src-tmp/');
        
        $this->assertTrue($created);
        $this->assertTrue($dir->isDir(__DIR__.'/src-tmp/'));
        
        $dir->delete(__DIR__.'/src-tmp/');
    }
    
    public function testCreateMethodRecursive()
    {
        $dir = new Dir();
        
        $created = $dir->create(__DIR__.'/src-tmp/foo/bar/', recursive: true);
        
        $this->assertTrue($created);
        $this->assertTrue($dir->isDir(__DIR__.'/src-tmp/foo/bar/'));
        
        $dir->delete(__DIR__.'/src-tmp/foo/bar/');
    }    
    
    public function testCreateMethodFailsAsNotRecursive()
    {
        $dir = new Dir();
        
        $created = $dir->create(__DIR__.'/src-rec/foo/bar');
        
        $this->assertFalse($created);
    }   
    
    public function testRenameMethod()
    {
        $dir = new Dir();
        
        $created = $dir->create(__DIR__.'/src-tmp/', mode: 0755);
        $renamed = $dir->rename(__DIR__.'/src-tmp/', 'src-tmp-new');
        
        $this->assertTrue($renamed);
        $this->assertFalse($dir->isDir(__DIR__.'/src-tmp/'));
        $this->assertTrue($dir->isDir(__DIR__.'/src-tmp-new/'));
        
        $dir->delete(__DIR__.'/src-tmp-new/');
    }
    
    public function testRenameMethodFailsIfDirDoesNotExist()
    {
        $dir = new Dir();
        
        $renamed = $dir->rename(__DIR__.'/src-tmp/foo/bar/', 'new');
        
        $this->assertFalse($renamed);
    }
    
    public function testCopyMethod()
    {
        $dir = new Dir();

        $copy = $dir->copy(dir: __DIR__.'/src/', destination: __DIR__.'/src-tmp-copy/new/');
        
        $this->assertTrue($copy);
        $this->assertTrue($dir->isDir(__DIR__.'/src-tmp-copy/new/bar/wee'));
        $this->assertTrue($dir->isFile(__DIR__.'/src-tmp-copy/new/foo/image.jpg'));
        $this->assertTrue($dir->isFile(__DIR__.'/src-tmp-copy/new/flowers.txt'));

        $dir->delete(__DIR__.'/src-tmp-copy/');
    }
    
    public function testCopyMethodFailsIfDirDoesNotExist()
    {
        $dir = new Dir();

        $copy = $dir->copy(dir: __DIR__.'/src/not-exist/', destination: __DIR__.'/src-tmp-copy/new/');
        
        $this->assertFalse($copy);
    }

    public function testCopyMethodWithFilesToIgnore()
    {
        $dir = new Dir();
        
        $dir->setFilesToIgnore(['foo/image.jpg']);
        
        $copy = $dir->copy(dir: __DIR__.'/src/', destination: __DIR__.'/src-tmp-copy/new/');
        
        $this->assertTrue($copy);
        $this->assertFalse($dir->isFile(__DIR__.'/src-tmp-copy/new/foo/image.jpg'));
        $this->assertTrue($dir->isFile(__DIR__.'/src-tmp-copy/new/flowers.txt'));

        $dir->delete(__DIR__.'/src-tmp-copy/');
    }
    
    public function testCopyMethodWithFilesToIgnoreExtensions()
    {
        $dir = new Dir();
        
        $dir->setFilesToIgnoreExtensions(['txt']);
        
        $copy = $dir->copy(dir: __DIR__.'/src/', destination: __DIR__.'/src-tmp-copy/new/');
        
        $this->assertTrue($copy);
        $this->assertTrue($dir->isFile(__DIR__.'/src-tmp-copy/new/foo/image.jpg'));
        $this->assertFalse($dir->isFile(__DIR__.'/src-tmp-copy/new/flowers.txt'));

        $dir->delete(__DIR__.'/src-tmp-copy/');
    }

    public function testCopyMethodWithFoldersToIgnore()
    {
        $dir = new Dir();
        
        $dir->setFoldersToIgnore(['bar/zoo']);
        
        $copy = $dir->copy(dir: __DIR__.'/src/', destination: __DIR__.'/src-tmp-copy/new/');
        
        $this->assertTrue($copy);
        $this->assertFalse($dir->isDir(__DIR__.'/src-tmp-copy/new/bar/zoo'));

        $dir->delete(__DIR__.'/src-tmp-copy/');
    }
    
    public function testDeleteMethod()
    {
        $dir = new Dir();
        
        $copy = $dir->copy(dir: __DIR__.'/src/', destination: __DIR__.'/src-tmp-copy/new/');

        $this->assertTrue($dir->delete(__DIR__.'/src-tmp-copy/'));
    }
    
    public function testDeleteMethodFails()
    {
        $dir = new Dir();

        $this->assertFalse($dir->delete(__DIR__.'/src-to-delete/'));
    }    
}