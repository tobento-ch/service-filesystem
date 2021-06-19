<?php
error_reporting( -1 );
ini_set('display_errors', '1');

use Tobento\Service\Filesystem\File;
use Tobento\Service\Filesystem\Dir;
use Nyholm\Psr7\Factory\Psr17Factory;

require __DIR__ . '/../vendor/autoload.php';

$file = new File(__DIR__.'/src/foo/image.jpg');

$psr17Factory = new Psr17Factory();
$response = $psr17Factory->createResponse(200);

/*$downloadResponse = $file->downloadResponse($response, $psr17Factory);
echo '<pre>';
print_r($downloadResponse);*/

/*$fileResponse = $file->fileResponse($response, $psr17Factory);
echo '<pre>';
print_r($fileResponse);*/

$dir = new Dir(withDefaultFormats: false);

$dir->addFormat('jpeg', 'image/jpeg');
$dir->addFormat('csv', 'text/csv');

// Returns first found mime type or null if not found any.
echo '<pre>';
print_r($dir->getMimeTypes(formats: ['jpeg', 'csv'], part: 2));

$dir = new Dir(true, false);


$dir->setFilesToIgnoreExtensions(['txt', 'json']);

//$dir->setFilesToIgnore(['.DS_Store', '*_notes', 'Thumbs.db', 'flowers.txt']);


//print_r($dir->getFiles(__DIR__.'/../tests/src/'));

//$dir->setFilesToIgnoreExtensions(['json', 'txt']);

echo '<pre>';
print_r($dir->getFiles(__DIR__.'/../tests/src/'));


//$dir->setFoldersToIgnore(['bar/zoo']);

//print_r($dir->getFoldersAll(__DIR__.'/../tests/src/'));

//print_r($dir->getFolders(__DIR__.'/../tests/src/'));

//$created = $dir->create(__DIR__.'/../tests/src-tmp/foo/bar/');

//$dir->setFilesToIgnore(['foo/image.jpg']);
        
//$copy = $dir->copy(dir: __DIR__.'/src/', destination: __DIR__.'/src-tmp-copy/new/');


//var_dump($dir->rename(__DIR__.'/../tests/src-tmp/foo/bar/', 'new'));

$file = new File(__DIR__.'/src/foo/image.jpg');

//var_dump($file->getExifData());

//$file = new File(__DIR__.'/src/flowers.txt');

//var_dump($file->getExtension());

//var_dump($file->getMimeType());

//var_dump($file->getFile());
?>
<!DOCTYPE html>
<html lang="de">
    
    <head>
        <title>Filesystem</title>
    </head>
    
    <body>
        <h1>Filesystem</h1>    
        
    </body>
</html>