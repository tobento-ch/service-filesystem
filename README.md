# Filesystem Service

The Filesystem Service provides abstraction layers for dealing with directories and files.

## Table of Contents

- [Getting started](#getting-started)
	- [Requirements](#requirements)
	- [Highlights](#highlights)
- [Documentation](#documentation)
	- [Files](#files)
		- [File](#file)
		- [Json File](#json-file)
        - [Pdf File](#pdf-file)
	- [Dir](#dir)
    - [File Formats](#file-formats)
- [Credits](#credits)
___

# Getting started

Add the latest version of the Filesystem service project running this command.

```
composer require tobento/service-filesystem
```

## Requirements

- PHP 8.0 or greater

## Highlights

- Framework-agnostic, will work with any project
- Decoupled design

# Documentation

## Files

Dealing with different files.

### File

#### Check if it is a file:

```php
use Tobento\Service\Filesystem\File;

$file = new File('home/public/src/foo/image.jpg');

var_dump($file->isFile()); // bool(true)

$file = new File('home/public/src/foo');

var_dump($file->isFile()); // bool(false)
```

#### Check for specific file extension:

```php
use Tobento\Service\Filesystem\File;

$file = new File('home/public/src/foo/image.jpg');

var_dump($file->isExtension(['jpeg', 'tiff'])); // bool(true)

var_dump($file->isImage(['jpeg', 'gif'])); // bool(true)

var_dump($file->isHtmlImage()); // bool(true)

// $file->isHtmlImage() is same as:
$file->isImage(['gif', 'jpeg', 'png', 'webp']);
```

#### File information:

```php
use Tobento\Service\Filesystem\File;

$file = new File('home/public/src/foo/image.jpg');

var_dump($file->getFile()); // string(30) "/home/public/src/foo/image.jpg"

var_dump($file->getDirname()); // string(21) "/home/public/src/foo/"

var_dump($file->getBasename()); // string(9) "image.jpg"

var_dump($file->getFilename()); // string(5) "image"

var_dump($file->getExtension()); // string(3) "jpg"

var_dump($file->getFolderPath()); // string(0) ""

var_dump($file->getDateModified()); // string(22) "09. June 2021 08:52:28"

var_dump($file->getDateModified('F d Y H:i:s.')); // string(22) "June 09 2021 08:52:28."

var_dump($file->getDateUpdated()); // string(22) "09. June 2021 08:52:28"

var_dump($file->getDateUpdated('F d Y H:i:s.')); // string(22) "June 09 2021 08:52:28."

// size in bytes
var_dump($file->getSize()); // int(34221)

// human readable sizes
var_dump($file->size()); // string(8) "33.42 KB"

// image size data
var_dump($file->getImageSize());
// array(7) { [0]=> int(500) [1]=> int(375) [2]=> int(2) [3]=> string(24) "width="500" height="375"" ["bits"]=> int(8) ["channels"]=> int(3) ["mime"]=> string(10) "image/jpeg" 

var_dump($file->getImageSize(1)); // int(375)

// get exif data if exist
var_dump($file->getExifData());
// array(7) { ["FileName"]=> string(9) "image.jpg" ["FileDateTime"]=> int(1623403678) ["FileSize"]=> int(34221) ["FileType"]=> int(2) ["MimeType"]=> string(10) "image/jpeg" ["SectionsFound"]=> string(0) "" ["COMPUTED"]=> array(4) { ["html"]=> string(24) "width="500" height="375"" ["Height"]=> int(375) ["Width"]=> int(500) ["IsColor"]=> int(1) } } 

// readable and writable
var_dump($file->isReadable()); // bool(true)

var_dump($file->isWritable()); // bool(true)

// mime type
var_dump($file->getMimeType()); // string(10) "image/jpeg"

var_dump($file->getMimeType(1)); // string(5) "image"

var_dump($file->getMimeType(2)); // string(4) "jpeg"
```

#### File name manipulation:

These methods return always an new instance.

```php
use Tobento\Service\Filesystem\File;

$file = new File('home/public/src/foo/image.jpg');

$newFile = $file->withDirname('home/public/src/bar/');

$newFile = $file->withFilename('new-image');

// Defining a folder path might be useful for certain cases.
$newFile = $file->withFolderPath('foo/bar');

// Generating unique filename.
$file = $file->withUniqueFilename();

var_dump($file->getBasename()); // string(18) "image-20317715.jpg"

var_dump($file->isFile()); // bool(false)
```

#### File copy, move, rename and delete operations:

These methods return a new instance on success, otherwise null, except for delete operation.

```php
use Tobento\Service\Filesystem\File;

$file = new File('home/public/src/foo/image.jpg');

$copiedFile = $file->copy('home/public/src/bar/name.jpg');

$movedFile = $file->move('home/public/src/bar/name.jpg');

$renamedFile = $movedFile->rename('new-name');

var_dump($movedFile->delete()); // bool(true)
```

#### Check if file is within a specified directory:

```php
use Tobento\Service\Filesystem\File;

$file = new File('home/public/src/foo/image.jpg');

var_dump($file->isWithinDir('home/public/src'));
// bool(true)

var_dump($file->isWithinDir('home/public/src/foo/'));
// bool(true)

var_dump($file->isWithinDir('home/public/src/bar/'));
// bool(false)
```

#### File responses

```php
use Tobento\Service\Filesystem\File;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;

$file = new File('home/public/src/foo/image.jpg');

$downloadResponse = $file->downloadResponse($response, $streamFactory);

$fileResponse = $file->fileResponse($response, $streamFactory);
```

### Json File

Providing the following additional methods.

```php
use Tobento\Service\Filesystem\JsonFile;

$file = new JsonFile('home/public/src/foo/data.json');

var_dump($file->isJson()); // bool(true)

$array = $file->toArray();

$jsonString = $file->getContent();
```

### Pdf File

Providing the following additional methods.

```php
use Tobento\Service\Filesystem\PdfFile;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;

$file = new Pdf('home/public/src/foo/data.pdf');

var_dump($file->isPdf()); // bool(true)

// Content example
$file = new PdfFile('document.pdf');

var_dump($file->isPdf()); // bool(false)

// add content, this will return a new instance.
$file = $file->content($pdfEngine->createPdfString('document.pdf'));

$string = $file->getContent();

$fileResponse = $file->fileResponse($response, $streamFactory);
```

## Dir

#### Dir information.

```php
use Tobento\Service\Filesystem\Dir;

$dir = new Dir();

var_dump($dir->isDir('home/public/src/foo')); // bool(true)

var_dump($dir->isDir('home/public/src/foo/')); // bool(true)

var_dump($dir->isFile('home/public/src/foo/image.jpg')); // bool(true)

var_dump($dir->isWritable(__DIR__.'/src/foo')); // bool(true)

// Check if directory or file exists.
var_dump($dir->has(__DIR__.'/src/foo')); // bool(true)

var_dump($dir->has(__DIR__.'/src/foo/')); // bool(true)

var_dump($dir->has(__DIR__.'/src/foo/image.jpg')); // bool(true)

// Check if a directory is empty.
var_dump($dir->isEmpty(__DIR__.'/src/foo/')); // bool(false)
```

#### Get files from a directory

```php
use Tobento\Service\Filesystem\Dir;
use Tobento\Service\Filesystem\File;

$dir = new Dir();

$dir->setFilesToIgnore(['.DS_Store', '*_notes', 'Thumbs.db', 'folder/subdir/image.png']); // use wildcard * for all

$dir->setFilesToIgnoreExtensions(['htaccess', 'txt']);

$files = $dir->getFiles('home/public/media/path/folder/');

// define a folder path, might be useful for certain use cases.
$files = $dir->getFiles('home/public/media/path/folder/', 'path/folder/');

// only get png and jpeg files.
$files = $dir->getFiles('home/public/media/path/folder/', '', ['png', 'jpeg']);

foreach($files as $file)
{
    var_dump($file instanceof File); // bool(true)
}
```

#### Get folders from a directory

```php
use Tobento\Service\Filesystem\Dir;
use Tobento\Service\Filesystem\Folder;

$dir = new Dir();

$dir->setFoldersToIgnore(['img/firm', 'misc/test', '*_notes']); // use wildcard * for all

// get all with subfolders.
$folders = $dir->getFoldersAll('home/public/media/');

// get only first level folders. Note: $dir->setFoldersToIgnore() has no influence.
$folders = $dir->getFolders('home/public/media/');

foreach($folders as $folder)
{
    var_dump($folder instanceof Folder); // bool(true)
    
    // Get folder information
    $name = $folder->name();
    $dir = $folder->dir();
    $id = $folder->id();
    $parentId = $folder->parentId();
    $level = $folder->level();
    $folderPath = $folder->folderPath();
    
    // Manipulate, returns a new instance.
    $folder = $folder->withName('-'.$folder->name());
    $folder = $folder->withFolderPath('bar/foo');
}
```

#### Create or rename a directory

```php
use Tobento\Service\Filesystem\Dir;

$dir = new Dir();

var_dump($dir->create('home/public/media/new/', mode: 0755, recursive: true)); // bool(true)

var_dump($dir->rename('home/public/media/old/', 'new')); // bool(true)
```

#### Copy a directory

This will copy subfolders and all files too.

```php
use Tobento\Service\Filesystem\Dir;

$dir = new Dir();

$dir->setFilesToIgnore(['subdir/image.png']); // starting from dir set.

$dir->setFilesToIgnoreExtensions(['txt']);

$dir->setFoldersToIgnore(['sub/foo']); // starting from dir set.

var_dump($dir->copy(dir: 'home/public/media/foo/', destination: 'home/public/media/bar/')); // bool(true)
```

#### Delete directories or file

```php
use Tobento\Service\Filesystem\Dir;

$dir = new Dir();

// Careful: this will delete all subfolders and all files.

var_dump($dir->delete('www/public/media/dir_to_delete/')); // bool(true)

// This method will delete files too.
var_dump($dir->delete('www/public/media/file_to_delete.jpg')); // bool(true)
```

#### A note on ignoring files

> :warning: **If the given format is not added by the addFormat() method, see File Formats below for detail, files will only be checked by its extension and not by its mime type.**

```php
use Tobento\Service\Filesystem\Dir;

$dir = new Dir(withDefaultFormats: false);

// would only check txt files by its extension as withDefaultFormats is set to false.
$dir->setFilesToIgnoreExtensions(['txt']);
```

The formats added by default are:

```php
trait FileFormats
{   
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
}
```

## File Formats

The FileFormatsInterface has the following methods:

#### Add a format

```php
use Tobento\Service\Filesystem\Dir;
use Tobento\Service\Filesystem\FileFormatsInterface;

$dir = new Dir();
var_dump($dir instanceof FileFormatsInterface); // bool(true)

$dir->addFormat(format: 'jpeg', mimeType: 'image/jpeg');
$dir->addFormat('jpg', 'image/jpeg');
$dir->addFormat('csv', 'text/csv');
$dir->addFormat('csv', 'text/plain');
```

#### Merge formats

```php
use Tobento\Service\Filesystem\Dir;

$dir = new Dir();
$dir->mergeFormats([
    'jpeg' => ['image/jpeg'],
    'csv' => ['text/x-csv'],
    'gif' => ['image/gif'],
]);
```

#### Get Format

```php
use Tobento\Service\Filesystem\Dir;

$dir = new Dir(withDefaultFormats: false);

$dir->addFormat('jpeg', 'image/jpeg');
$dir->addFormat('jpg', 'image/jpeg');

// Returns first found format or null if not found any.
var_dump($dir->getFormat(mimeType: 'image/jpeg'));
// string(4) "jpeg"
```

#### Get Formats

```php
use Tobento\Service\Filesystem\Dir;

$dir = new Dir(withDefaultFormats: false);

$dir->addFormat('jpeg', 'image/jpeg');
$dir->addFormat('jpg', 'image/jpeg');
$dir->addFormat('csv', 'text/csv');
$dir->addFormat('csv', 'text/plain');

$formats = $dir->getFormats();

/*
Array
(
    [jpeg] => Array
        (
            [0] => image/jpeg
        )

    [jpg] => Array
        (
            [0] => image/jpeg
        )

    [csv] => Array
        (
            [0] => text/csv
            [1] => text/plain
        )

)
*/
```

#### Get Mime Type

```php
use Tobento\Service\Filesystem\Dir;

$dir = new Dir(withDefaultFormats: false);

$dir->addFormat('jpeg', 'image/jpeg');
$dir->addFormat('jpg', 'image/jpeg');

// Returns first found mime type or null if not found any.
var_dump($dir->getMimeType(format: 'jpg'));
// string(10) "image/jpeg" 

// Return part one
var_dump($dir->getMimeType('jpg', part: 1));
string(5) "image"

// Return part two
var_dump($dir->getMimeType('jpg', 2));
string(4) "jpeg"
```

#### Get Mime Types

```php
use Tobento\Service\Filesystem\Dir;

$dir = new Dir(withDefaultFormats: false);

$dir->addFormat('jpeg', 'image/jpeg');
$dir->addFormat('csv', 'text/csv');

$mimeTypes = $dir->getMimeTypes(formats: ['jpeg', 'csv']);

/*
Array
(
    [0] => image/jpeg
    [1] => text/csv
)
*/

// part one only
$mimeTypes = $dir->getMimeTypes(formats: ['jpeg', 'csv'], part: 1);

/*
Array
(
    [0] => image
    [1] => text
)
*/

// part two only
$mimeTypes = $dir->getMimeTypes(formats: ['jpeg', 'csv'], part: 2);

/*
Array
(
    [0] => jpeg
    [1] => csv
)
*/
```

# Credits

- [Tobias Strub](https://www.tobento.ch)
- [All Contributors](../../contributors)