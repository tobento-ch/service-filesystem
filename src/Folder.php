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
 * Folder
 */
class Folder
{
    /**
     * Create a new Folder
     *
     * @param string $name
     * @param string $dir
     * @param int $id
     * @param int $parentId
     * @param int $level
     * @param string $folderPath
     */    
    public function __construct(
        protected string $name,
        protected string $dir,
        protected int $id,
        protected int $parentId,
        protected int $level,
        protected string $folderPath,
    ){}
    
    /**
     * Get the folder name.
     *
     * @return string
     */    
    public function name(): string
    {
        return $this->name;
    }

    /**
     * Return an instance with the specified name.
     *
     * @param string $name
     * @return static
     */    
    public function withName(string $name): static
    {
		$new = clone $this;
		$new->name = $name;
        return $new;
    }
    
    /**
     * Get the folder dir.
     *
     * @return string
     */    
    public function dir(): string
    {
        return $this->dir;
    }

    /**
     * Get the folder id.
     *
     * @return int
     */    
    public function id(): int
    {
        return $this->id;
    }

    /**
     * Get the folder parent id.
     *
     * @return int
     */    
    public function parentId(): int
    {
        return $this->parentId;
    }
    
    /**
     * Get the folder level.
     *
     * @return int
     */    
    public function level(): int
    {
        return $this->level;
    }
    
    /**
     * Get the folder path.
     *
     * @return string
     */    
    public function folderPath(): string
    {
        return $this->folderPath;
    }
    
    /**
     * Return an instance with the specified folder path.
     *
     * @param string $folderPath
     * @return static
     */    
    public function withFolderPath(string $folderPath): static
    {
		$new = clone $this;
		$new->folderPath = $folderPath;
        return $new;
    }    
}