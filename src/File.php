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
use JsonException;
use Throwable;

/**
 * File
 */
class File
{
    /**
     * @var bool If the file must exist.
     */
    protected bool $mustExist = true;    

    /**
     * @var null|bool If the file is a file.
     */
    protected ?bool $isFile = null;
        
    /**
     * @var string The dirname. /home/www/dir/
     */
    protected string $dirname = '';

    /**
     * @var string The basename. file.txt
     */
    protected string $basename;

    /**
     * @var string The filename. file
     */
    protected string $filename;

    /**
     * @var null|string The extension such as jpg, txt
     */
    protected ?string $extension = null;
    
    /**
     * @var string The folder path. img/sub/
     */
    protected string $folderPath = '';

    /**
     * @var null|array The image size, [width, height].
     */
    protected null|array $imageSize = null;

    /**
     * @var null|array The exif data.
     */
    protected null|array $exifData = null;
    
    /**
     * @var null|string The mime type.
     */
    protected ?string $mimeType = null;
                    
    /**
     * Create a new File
     *
     * @param string $file The file /home/www/file.txt
     */    
    final public function __construct(
        protected string $file
    ){
        $pathParts = pathinfo($this->file);
        $this->dirname = $pathParts['dirname'] ?? '';
        $this->dirname = ($this->dirname === '.') ? '' : $this->dirname.'/';
        $this->basename = $pathParts['basename'] ?? '';
        $this->filename = $pathParts['filename'] ?? '';
        $this->extension = isset($pathParts['extension']) ? strtolower($pathParts['extension']) : null;
    }
    
    /**
     * Set if the file must exist.
     * new File('zip:///file.zip#image.png');
     *
     * @param bool $mustExist
     * @return $this
     */    
    public function mustExist(bool $mustExist)
    {
        $this->mustExist = $mustExist;
        return $this;
    }
        
    /**
     * Get the file.
     *
     * @return string
     */    
    public function getFile(): string
    {
        return $this->file;
    }
                
    /**
     * Gets the dirname.
     *
     * @return string
     */    
    public function getDirname(): string
    {
        return $this->dirname;
    }

    /**
     * Return an instance with the specified dirname.
     *
     * @param string $dirname The dirname.
     * @return static
     */    
    public function withDirname(string $dirname): static
    {    
        $file = rtrim($dirname, '/').'/'.$this->filename;
        
        if ($this->extension !== null) {
            $file .= '.'.$this->extension;
        }
        
        return new static($file);
    }    

    /**
     * Gets the basename.
     *
     * @return string
     */    
    public function getBasename(): string
    {
        return $this->basename;
    }

    /**
     * Gets the filename.
     *
     * @return string
     */    
    public function getFilename(): string
    {
        return $this->filename;
    }

    /**
     * Gets the extension such as jpg, txt. Note getMimeType() is more reliable and secure to check.
     *
     * @return string
     */    
    public function getExtension(): string
    {
        return $this->extension === null ? '' : $this->extension;
    }
    
    /**
     * Return an instance with the specified filename.
     *
     * @param string $filename The filename
     * @return static
     */    
    public function withFilename(string $filename): static
    {    
        $file = $this->dirname.$filename;
        
        if ($this->extension !== null) {
            $file .= '.'.$this->extension;
        }
        
        return new static($file);
    }

    /**
     * Return an instance with an unique filename.
     *
     * @return static
     */
    public function withUniqueFilename(): static
    {
        if ($this->isFile()) {
            $file = $this->withFilename($this->generateFilename($this->filename));
            return $file->withUniqueFilename();
        }
        
        return clone $this;
    }

    /**
     * Return an instance with a folder path.
     *
     * @param string $folderPath The folder path. 'img/test'
     * @return static
     */    
    public function withFolderPath(string $folderPath): static
    {
        $new = clone $this;
        $new->folderPath = $folderPath;
        return $new;
    }

    /**
     * Gets the folder path.
     *
     * @param int $trim Start and endling slash '/'.  0 = as set, 1 = 'img/sub', 2 = 'img/sub/', 3 = '/img/sub', 4 = '/img/sub/'
     * @return string The folder path. 'img/sub'
     */    
    public function getFolderPath(int $trim = null): string
    {
        if (empty($this->folderPath)) {
            return $this->folderPath;
        }
        
        switch ($trim) {
            case 0:
                return $this->folderPath;
            case 1:
                return trim($this->folderPath, '/');
            case 2:
                return trim($this->folderPath, '/').'/';
            case 3:
                return '/'.trim($this->folderPath, '/');
            case 4:
                return '/'.trim($this->folderPath, '/').'/';                    
            default:
                return $this->folderPath;
        }    
    }
        
    /**
     * Gets the date modified.
     *
     * @param string $format The format.
     * @return string|null The formatted date or null on failure.
     */    
    public function getDateModified(string $format = 'd. F Y H:i:s'): ?string
    {
        if (!$this->isFile()) {
            return null;
        }
        
        return date($format, filemtime($this->file));
    }

    /**
     * Gets the date updated.
     *
     * @param string $format The format.
     * @return string|null The formatted date or null on failure.
     */    
    public function getDateUpdated(string $format = 'd. F Y H:i:s'): ?string
    {
        if (!$this->isFile()) {
            return null;
        }

        return date($format, filectime($this->file));
    }

    /**
     * Gets the size of the file.
     *
     * @return int The size.
     */    
    public function getSize(): int
    {
        if (!$this->isFile() || $this->mustExist === false) {
            return 0;
        }
        
        return (int) filesize($this->file);
    }

    /**
     * Gets the size in a human readable way.
     *
     * @param null|int $bytes The unit B, KB or MB or if null get filesize.
     * @return string The size.
     */    
    public function size(?int $bytes = null): string
    {
        if (is_null($bytes) && (!$this->isFile() || $this->mustExist === false))
        {
            return '';
        }
        
        $bytes = $bytes ?: (int) filesize($this->file);
            
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];

        for ($i = 0; $bytes > 1024; $i++)
        {
            $bytes /= 1024;
        }

        return round($bytes, 2).' '.($units[$i] ?? '');        
    }
    
    /**
     * Gets the image size array or depending on the index set, the value.
     *
        Array
        (
            [0] => 3000
            [1] => 1500
            [2] => 2
            [3] => width="3000" height="1500"
            [bits] => 8
            [channels] => 3
            [mime] => image/jpeg
        )          
     *
     * @param null|int|string $index The image size array index.
     * @return mixed Depending on the index. Null returns array.
     */    
    public function getImageSize(null|int|string $index = null): mixed
    {
        if (!is_null($this->imageSize)) {
            return isset($this->imageSize[$index]) ? $this->imageSize[$index] : null;
        }
        
        if (!$this->isImage(['jpeg', 'png', 'gif', 'webp', 'tiff', 'bmp', 'psd'])) {
            $this->imageSize = [];
            return null;
        }
        
        $this->imageSize = getimagesize($this->file);

        if (is_null($index)) {
            return $this->imageSize;
        }
        
        return isset($this->imageSize[$index]) ? $this->imageSize[$index] : null;
    }

    /**
     * Gets the exif data if they can be read and exist.
     *
     * @return array
     */    
    public function getExifData(): array
    {
        if ($this->exifData === null) {
            
            if ($this->isExtension(['jpeg', 'tiff'])) {
            
                $this->exifData = exif_read_data($this->getFile());
                $this->exifData = !is_array($this->exifData) ? [] : $this->exifData;    
                        
            } else {
            
                $this->exifData = [];
            }
        }
        
        return $this->exifData;
    }
    
    /**
     * Checks if it is an file.
     *
     * @return bool True on success, false on failure.
     */    
    public function isFile(): bool
    {
        if ($this->isFile !== null) {
            return $this->isFile;
        }

        if (empty($this->file)) {
            return $this->isFile = false;
        }
                
        if (is_dir($this->file)) {
            return $this->isFile = false;
        }        

        if (!file_exists($this->file) && $this->mustExist === true) {
            return $this->isFile = false;
        }
        
        return $this->isFile = true;
    }

    /**
     * Checks if it is an file of the extensions defined.
     *
     * @param array $extensions If set it checks if it is of the types set, else all. ['tiff', 'png', 'pdf']
     * @param bool $mimeType True determines based on finfo mime type. False determines based on pathinfo() extension.
     * @return bool True on success, false on failure.
     */    
    public function isExtension(array $extensions = [], bool $mimeType = true): bool
    {
        if (!$this->isFile()) {
            return false;
        }
        
        if ($mimeType === true) {
            $mimeType = $this->getMimeTypePart(2);
            return in_array($mimeType, $extensions);
        }

        return in_array($this->extension, $extensions);
    }
    
    /**
     * Checks if it is readable.
     *
     * @return bool True on success, false on failure.
     */    
    public function isReadable(): bool
    {
        return is_readable($this->file);
    }

    /**
     * Checks if it is writable.
     *
     * @return bool True on success, false on failure.
     */    
    public function isWritable(): bool
    {
        return is_writable($this->file);
    }    

    /**
     * Copies the file and returns the copied file on success.
     *
     * @param string $destination The destination filepath.
     * @return null|static The copied file on success, otherwise null.
     */    
    public function copy(string $destination)
    {
        if (! $this->isFile() || is_dir($destination)) {
            return null;
        }
        
        // create directory if it does not exists.
        $dir = new Dir();
        $destDir = dirname($destination).DIRECTORY_SEPARATOR;

        if (!$dir->has($destDir)) {
            if (!$dir->create($destDir, 0700, true)) {
                return null;                
            }
        }
        
        // copy the file
        $copied = copy($this->getFile(), $destination);
        
        if ($copied) {
            return new static($destination);
        }
        
        return null;
    }

    /**
     * Move the file and returns the moved file on success.
     *
     * @param string $destination The destination filepath.
     * @return null|static The moved file on success, otherwise null.
     */    
    public function move(string $destination): ?static
    {
        if (!is_null($copiedFile = $this->copy($destination))) {
            $this->delete();
            $this->isFile = null;
            return $copiedFile;
        }
        
        return null;
    }
    
    /**
     * Renames the filename.
     *
     * @param string $filename The new name such as 'name' without extension (.jpg e.g.)
     * @return null|static The renamed file on success, otherwise null.
     */    
    public function rename(string $filename): ?static
    {
        if (! $this->isFile()) {
            return null;
        }

        $file = $this->dirname.$filename;
        
        if ($this->extension !== null) {
            $file .= '.'.$this->extension;
        }
                
        $renamed = rename($this->getFile(), $file);
        
        if ($renamed) {
            $this->isFile = null;
            return $this->withFilename($filename);
        }
        
        return null;
    }
        
    /**
     * Deletes the file.
     *
     * @return bool True on success, false on failure.
     */    
    public function delete(): bool
    {
        if (! $this->isFile()) {
            return true;
        }
        
        $this->isFile = null;
        
        return unlink($this->getFile());
    }
            
    /**
     * Checks if it is an image.
     *
     * @param null|array $extensions If set it checks if it is of the extensions set, else all. ['jpg', 'png']
     * @return bool True on success, false on failure.
     */    
    public function isImage(?array $extensions = null): bool
    {
        if ($this->getSize() < 3) {
            return false;
        }

        if (!$this->isFile()) {
            return false;
        }

        if ($extensions !== null) {
            return $this->isExtension($extensions);
        }                
        
        return (@exif_imagetype($this->file) === false) ? false : true;
    }

    /**
     * Checks if it is a html image to render.
     *
     * @return bool True on success, false on failure.
     */    
    public function isHtmlImage(): bool
    {
        return $this->isImage(['gif', 'jpeg', 'png', 'webp']);
    }
    
    /**
     * Gets the mime type. Note: This cannot be 100% trusted.
     *
     * @param int $part If 0 gets full, else  image(part 1 ) / gif(part 2 )
     * @return string The mime type.
     * @psalm-suppress UnusedFunctionCall
     */
    public function getMimeType(int $part = 0): string
    {
        if ($this->mimeType !== null) {
            return ($part === 0) ? $this->mimeType : $this->getMimeTypePart($part);
        }
        
        if (!$this->isFile()) {
            return '';
        }
        
        // get mime type from finfo_file
        $this->mimeType = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $this->file);
        
        // check for json
        if (in_array($this->mimeType, ['text/plain', 'text/html']))
        {
            $content = file_get_contents($this->file);
            
            try {
                json_decode($content, true, 512, JSON_THROW_ON_ERROR);
                $this->mimeType = 'application/json';
            } catch (JsonException $e) {
                // ignore
            }
        }
        
        return ($part === 0) ? $this->mimeType : $this->getMimeTypePart($part);
    }
    
    /**
     * Gets the content.
     *
     * @return string
     */    
    public function getContent(): string
    {
        if (! $this->isFile()) {
            return '';
        }

        try {
            $content = file_get_contents($this->getFile());
            return is_string($content) ? $content : '';
        } catch (Throwable $t) {
            return '';
        }
    }
    
    /**
     * Returns true if the file is within the specified directory, otherwise false.
     *
     * @param string $dir
     * @return bool
     */
    public function isWithinDir(string $dir): bool
    {
        $path = realpath($this->getDirname());
        
        if (!is_string($path)) {
            return false;
        }
        
        // normalize:
        $dir = str_replace('\\', '/', $dir);
        $dir = rtrim($dir, '/');
        $path = str_replace('\\', '/', $path);
        
        return str_starts_with($path, $dir);
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
        
        if (!$this->isFile()) {
            return $response->withStatus(404);
        }
        
        $mimeType = $this->getMimeType(0);

        if (empty($mimeType)) {
            $mimeType = 'application/octet-stream';
        }
        
        // create the stream
        $stream = $streamFactory->createStreamFromFile($this->getFile(), 'rb');
        
        return $response->withHeader('Content-Type', $mimeType)
                        ->withHeader('Content-Disposition', 'attachment; filename='.$this->getBasename())
                        ->withHeader('Content-Length', (string) $this->getSize())
                        ->withBody($stream);
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
        
        return $this->downloadResponse($response, $streamFactory)
            ->withHeader('Content-Disposition', 'inline; filename='.$this->getBasename());
    }    
    
    /**
     * Gets the mime type part set. Note: This cannot be 100% trusted.
     *
     * @param int $part image(part 1 ) / gif(part 2 )
     * @return string The mime type part.
     */    
    protected function getMimeTypePart(int $part): string
    {
        if ($this->mimeType === null) {
            $this->getMimeType();
        }
        
        $part = $part-1;
        $parts = explode('/', $this->mimeType);
        return isset($parts[$part]) ? $parts[$part] : '';
    }

    /**
     * Generates a filename with uniqid() suffix.
     *
     * @param string $filename The filename.
     * @return string The filename with a uniqid() suffix appended.
     */
    protected function generateFilename(string $filename): string
    {
        $suffix = substr(uniqid((string) mt_rand(), true), 0, 8);
        $pos = strrpos($filename, '-');
        
        // check if there is already a suffix.
        if ($pos !== false) {

            $end = substr($filename, $pos+1);
            
            if ($end !== false) {
                
                if (is_numeric($end)) {
                
                    if (strlen($end) === 8) {
                        
                        $filenamePart = substr($filename, 0, $pos);
                        
                        if ($filenamePart !== false) {
                            
                            return $filenamePart.'-'.$suffix;
                        }
                    }
                }
            }
        }
        
        return $filename.'-'.$suffix;
    }    
}