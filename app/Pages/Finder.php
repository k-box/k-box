<?php

namespace KBox\Pages;

use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

/**
 * Page Finder.
 *
 * List and query the defined pages
 */
class Finder
{

    /**
     * The storage disk name
     *
     * @var string
     */
    protected $disk;

    /**
     * the base path inside the storage disk
     *
     * @var string
     */
    protected $basePath;

    /**
     * Collection of all pages
     *
     * @var \Illuminate\Support\Collection
     */
    protected $pages;

    /**
     * The page model
     *
     * @var PageModel
     */
    protected $model;

    /**
     * Create a new Eloquent query builder instance.
     *
     * @param  PageModel  $model
     * @param  string  $disk
     * @param  string  $basePath
     * @return void
     */
    public function __construct($model, $disk, $basePath)
    {
        $this->model = $model;
        $this->disk = $disk;
        $this->basePath = $basePath;
    }

    protected function loadPagesFromStorage()
    {
        if (! $this->pages) {
            $this->pages = collect(Storage::disk($this->disk)->files($this->basePath))->map(function ($file) {
                if (Str::endsWith($file, 'md')) {
                    return $this->model->newInstanceFromFile($file);
                }
            })->filter();
        }
    }

    /**
     * Add a where clause on the page identifier.
     *
     * @param  mixed  $id
     * @return $this
     */
    public function whereKey($id)
    {
        return $this->where($this->model->getQualifiedKeyName(), $id);
    }
    
    public function whereLanguage($language)
    {
        return $this->where($this->model->getQualifiedLanguageName(), $language);
    }
    
    public function where($key, $value)
    {
        $this->loadPagesFromStorage();
        
        $this->pages = $this->pages->where($key, '=', $value);

        return $this;
    }

    /**
     * Find a model by its primary key.
     *
     * @param  mixed  $id
     * @param  array  $columns
     * @return PageModel|\Illuminate\Support\Collection|static[]|static|null
     */
    public function find($id, $language = null)
    {
        if ($language) {
            return $this->whereKey($id)->whereLanguage($language)->get()->first();
        }

        return $this->whereKey($id)->get();
    }

    /**
     * Retrieve all the pages.
     *
     * @param  array  $columns
     * @return \Illuminate\Support\Collection|static[]
     */
    public function get()
    {
        $this->loadPagesFromStorage();

        return collect($this->pages);
    }
}
