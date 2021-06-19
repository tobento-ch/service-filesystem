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
 * Dir
 */
class Dir implements FileFormatsInterface
{
    use FileFormats;

    /**
     * @var array The files to ignore.
     */
    protected array $filesToIgnore = [];

    /**
     * @var array The files to ignore by its extension.
     */
    protected array $filesToIgnoreExtensions = [];
    
    /**
     * @var array The folders to ignore.
     */
    protected array $foldersToIgnore = [];
            
    /**
     * @var array the folders.
     */
    protected array $folders = [];

    /**
     * @var int the current folder id to set unique folder id.
     */
    protected int $currentFolderId = 0;

    /**
     * Create a new Dir
     *
     * @param bool $withDefaults
     * @param bool $withDefaultFormats
     */    
    public function __construct(
        bool $withDefaults = true,
        bool $withDefaultFormats = true,
    )
    {
        if ($withDefaults) {
            $this->setFilesToIgnore(['*.DS_Store', '*_notes', '*Thumbs.db']);
            $this->setFoldersToIgnore(['*_notes']);
        }
        
        if ($withDefaultFormats) {
            $this->addDefaultFormats();
        }
    }
                
    /**
     * Sets the files which get ignored.
     *
     * @param array $filesToIgnore The files. ['.', '..', '.DS_Store', '_notes', 'Thumbs.db']
     * @return void
     */    
    public function setFilesToIgnore(array $filesToIgnore): void
    {
        $this->filesToIgnore = array_merge(['*.', '*..'], $filesToIgnore);
    }

    /**
     * Sets the files which get ignored by the files extension.
     *
     * @param array $filesToIgnoreExtensions The extensions. ['htaccess', 'txt']
     * @return void
     */    
    public function setFilesToIgnoreExtensions(array $filesToIgnoreExtensions): void
    {
        $this->filesToIgnoreExtensions = $filesToIgnoreExtensions;
    }
    
    /**
     * Sets the folders which get ignored.
     *
     * @param array $foldersToIgnore The files. ['img', 'misc/sub']
     * @return void
     */    
    public function setFoldersToIgnore(array $foldersToIgnore): void
    {
        $this->foldersToIgnore = $foldersToIgnore;
    }

    /**
     * Gets the files from the directory set.
     *
     * @param string $directory The directory.
     * @param string $folderPath The folder path. 'img/sub/'
     * @param array $extensions Get only those files by extensions ['jpe', 'png']. If empty all are fetched.
     * @return array The files.
     */    
    public function getFiles(string $directory, string $folderPath = '', array $extensions = []): array
    {
        if (!$this->isDir($directory)) {
            
            return [];
        }
        
        $filesTmp = scandir($directory);
        $files = [];

        // group files to ignore by wildcards.
        list($filesToIgnoreByPath, $filesToIgnoreAllPaths) = $this->groupWildcards($this->filesToIgnore);
        
        // go through each tmp files.                    
        foreach($filesTmp as $file){
            
            // ingore files all paths.
            if (in_array($file, $filesToIgnoreAllPaths)) {
                continue;
            }

            // ignore files by path.
            $filePath = (empty($folderPath)) ? '' : rtrim($folderPath, '/').'/';
            
            if (in_array($filePath.$file, $filesToIgnoreByPath)) {
                continue;
            }
                        
            $fileObj = new File($directory.$file);
            
            if ($fileObj->isFile()) {
                
                $filesToIgnoreMimeTypes = $this->getMimeTypes($this->filesToIgnoreExtensions, 2);
                
                if (in_array($fileObj->getMimeType(2), $filesToIgnoreMimeTypes)) {
                    continue;
                }

                if (in_array($fileObj->getExtension(), $this->filesToIgnoreExtensions)) {
                    continue;
                }
                                
                // get only extensions set
                if (!empty($extensions)) {
                    
                    $extensionsMimeTypes = $this->getMimeTypes($extensions, 2);
                    
                    if (!in_array($fileObj->getMimeType(2), $extensionsMimeTypes)) {
                        continue;
                    }

                    if (!in_array($fileObj->getExtension(), $extensions)) {
                        continue;
                    }                                
                }
                
                $files[] = $fileObj->withFolderPath($folderPath);
            }
        }
        
        return $files;                
    }

    /**
     * Gets the folders from the directory set.
     *
     * @param string $directory The directory.
     * @return array The folders.
     */    
    public function getFolders(string $directory): array
    {        
        $folders = [];
        
        foreach($this->fetchDirs($directory) as $index => $dir)
        {
            $index++;
            
            $folders[$index] = new Folder(
                name: basename($dir),
                dir: rtrim($dir, '/').'/', // normalize
                id: $index,
                parentId: 0,
                level: 0,
                folderPath: basename($dir),
            );         
        }
        
        return $folders;
    }

    /**
     * Gets the folders with its children from the directory set.
     *
     * @param string $directory The directory.
     * @return array The folders.
     */    
    public function getFoldersAll(string $directory): array
    {
        $this->currentFolderId = 0; // reset
        
        return $this->fetchFoldersAll($directory, $directory);
    }
    
    /**
     * Check if it a file.
     *
     * @param string $file The file.
     * @param bool True on success, false on failure.
     */    
    public function isFile(string $file): bool
    {
        return is_file($file);
    }
    
    /**
     * Checks if the directory or file exist.
     *
     * @param string $dirOrFile The directory or file.
     * @param bool True exist else false.
     */    
    public function has(string $dirOrFile): bool
    {
        return file_exists($dirOrFile);
    }

    /**
     * Creates a new direcotry.
     *
     * @param string $directory The directory.
     * @param int $mode The mode such as 0755.
     * @param bool $recursive If recursive.
     * @param resource $context (Not yet set available.)
     * @return bool True on success, false on failure.
     */    
    public function create(string $directory, int $mode = 0600, bool $recursive = false, $context = null): bool
    {
        if ($this->isDir($directory))
        {
            return true;
        }
        
        if ($recursive === false)
        {
            $directory = rtrim($directory, '/');
            $dirArr = explode('/', $directory);
            array_pop($dirArr);
            $parentDir = implode('/', $dirArr);

            if (! $this->isDir($parentDir)) {
                return false;
            }
        }
        
        return mkdir($directory, $mode, $recursive);
    }    
    
    /**
     * Deletes a dir or file.
     *
     * @param string $dirOrFile The directory or file.
     * @return bool True on success, false on failure..
     */    
    public function delete(string $dirOrFile): bool
    {
        if ($this->isDir($dirOrFile)) {
        
            return $this->deleteDir($dirOrFile);
            
        } else {
            
            $fileObj = new File($dirOrFile);
            if ($fileObj->isFile()) {
                
                return unlink($fileObj->getFile());
            }
        }
        
        return false;
    }

    /**
     * Renames a directory.
     * http://php.net/manual/en/function.rename.php
     *
     * @param string $dir The directory. dir/folder/oldname
     * @param string $name The new name. newname
     * @param bool True on success, false on failure.
     */    
    public function rename(string $dir, string $name): bool
    {
        $dir = rtrim($dir, '/');
        
        if (! $this->isDir($dir)) {
            return false;
        }
        
        // Build the new direcotry.
        $dirArr = explode('/', $dir);
        array_pop($dirArr); // delete old name
        $newDir = implode('/', $dirArr).'/'.$name;
        
        return @rename($dir, $newDir) === true;
    }
    
    /**
     * Copies a directory and files with all subdirs.
     *
     * @param string $dir The directory to copy.
     * @param string $destination The destination where to copy the directory.
     * @param bool True on success, false on failure.
     */    
    public function copy(string $dir, string $destination): bool
    {
        $dirRoot = rtrim($dir, '/').'/';
        $destination = rtrim($destination, '/').'/';
        
        if (! $this->isDir($dirRoot)) {
            return false;
        }
        
        if (! $this->has($destination)) {
            $this->create($destination, 0755, true);
        }
                        
        foreach($this->getFoldersAll($dirRoot) as $folder) {
            
            if (! $this->has($destination.$folder->folderPath())) {
                
                if (! $this->create($destination.$folder->folderPath(), 0755, true)) {
                    
                    continue;
                }
            }
            
            $files = $this->getFiles($folder->dir().'/', $folder->folderPath());
            
            foreach($files as $file) {
                
                $file->copy($destination.$folder->folderPath().'/'.$file->getBasename());
            }    
        }
        
        $parentFiles = $this->getFiles($dirRoot);
        
        foreach($parentFiles as $parentFile) {
            
            $parentFile->copy($destination.$parentFile->getBasename());    
        }
        
        return true;
    }

    /**
     * Fetches the dirs from the directory set.
     *
     * @param string $directory The directory.
     * @return array<int, string> The folders.
     */    
    protected function fetchDirs(string $directory): array
    {
        $dirs = glob($directory . '*', GLOB_ONLYDIR);
        return is_array($dirs) ? $dirs : [];
    }    

    /**
     * Fetches the folders with its children from the directory set.
     *
     * @param string $directory The directory.
     * @param string $rootDirectory The root directory.
     * @param int $parentId The parent id.
     * @param int $level The level depth.
     * @return array The folders.
     */    
    protected function fetchFoldersAll(string $directory, string $rootDirectory, int $parentId = 0, int $level = 0): array
    {
        $rootDirLen = strlen($rootDirectory);
        
        foreach($this->fetchDirs($directory) as $folderDir) {

            $folderPath = (substr($folderDir, 0, $rootDirLen) === $rootDirectory) ? substr($folderDir, $rootDirLen) : '';
            
            // ignore folders.
            list($foldersToIgnore, $foldersToIgnoreAll) = $this->groupWildcards($this->foldersToIgnore);

            if (in_array($folderPath, $foldersToIgnore)) {
                continue;
            }
            
            if (in_array(basename($folderPath), $foldersToIgnoreAll)) {
                continue;
            }
            
            $this->currentFolderId++;
            
            $this->folders[$this->currentFolderId] = new Folder(
                name: basename($folderDir),
                dir: rtrim($folderDir, '/').'/', // normalize
                id: $this->currentFolderId,
                parentId: $parentId,
                level: $level,
                folderPath: $folderPath,
            );
            
            $this->fetchFoldersAll($folderDir.'/', $rootDirectory, $this->currentFolderId, $level+1);
        }
        
        return $this->folders;
    }
    
    /**
     * Deletes a directory recursive.
     *
     * @param string $dir The directory.
     * @param bool True on success, false on failure.
     */    
    protected function deleteDir(string $dir): bool
    {
        $files = array_diff(scandir($dir), array('.','..'));
        
        foreach ($files as $file) {
            
            $path = $dir.'/'.$file;
            
            (is_dir($path) && !is_link($dir)) ? $this->deleteDir($path) : unlink($path);
        }
        
        return rmdir($dir);
    }
    
    /**
     * Check if it a direcotry.
     *
     * @param string $directory The directory.
     * @return bool True on success, false on failure.
     */    
    public function isDir(string $directory): bool
    {
        return is_dir($directory);
    }

    /**
     * Check if it a direcotry is empty.
     *
     * @param string $directory The directory.
     * @return bool True if empty, otherwise false.
     */    
    public function isEmpty(string $directory): bool
    {
        return (count(glob($directory . '*')) === 0) ? true : false;
    }    

    /**
     * Check if the direcotry is writable.
     *
     * @param string $directory The directory.
     * @return bool True on success, false on failure.
     */    
    public function isWritable(string $directory): bool
    {
        return is_writable($directory);
    }

    /**
     * Groups names with wildcards.
     *
     * @param array $names Any list with names. ['_notes', '*filename.jpg']
     * @param array The grouped names. [[names], [names_wildcards]]
     */    
    protected function groupWildcards(array $names): array
    {
        $cleanNames = [];
        $wildcardNames = [];
        
        foreach($names as $name){
            if (substr($name, 0, 1) === '*') {
                if (strpos($name, '/') === false) {
                    $wildcardNames[] = substr($name, 1);
                } else {
                    $cleanNames[] = substr($name, 1);
                }
            } else {
                $cleanNames[] = $name;
            }
        }
            
        return [$cleanNames, $wildcardNames];
    }    
}