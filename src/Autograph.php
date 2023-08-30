<?php

namespace JJalving\Autograph;

use Statamic\Facades\Antlers;
use Statamic\Facades\Cascade;
use Statamic\Entries\Entry;
use Statamic\Facades\File;
use Statamic\Facades\Folder;
use Statamic\Facades\User;

class Autograph
{
    public static function getUsers()
    {
      $collection = config('statamic.autograph.user_collection');
      if($collection) {
        return Entry::query()->where("collection", $collection)->get();
      }
      // No collection given, return cp users list
      return User::all();
    }
  

    public static function getUser($id)
    {
      $collection = config('statamic.autograph.user_collection');
      if($collection) {
        return Entry::find($id);
      }
      // No collection given, return cp user
      return User::find($id);
    }  
  
    public static function getTemplates()
    {
      $folder = config('statamic.autograph.templates_folder');
  
      $templates = [];
  
      foreach (Folder::disk('resources')->getFilesByTypeRecursively($folder, 'html') as $path) {
        // Add to templates array
        $templates[] = [
          'path' => $path,
          'label' => Autograph::getDisplayNameFromPath($path)
        ];
      }
  
      return $templates;
    }
  
    public static function getDisplayNameFromPath($path) {
      // Get filename from path
      $parts = explode('/', $path);
      $filename = end($parts);
      // Remove .antlers.html
      $filename = preg_replace('/\.antlers\.html$/', '', $filename);
      // Remove .html
      $filename = preg_replace('/\.html$/', '', $filename);
      return $filename;
    }

    public static function getParsedTemplate($path, $variables)
    {
      // Load the template file
      $file = File::disk('resources')->get($path);
      // Get cascade data
      $cascade = Cascade::instance()->hydrate()->toArray();
      // Return parsed template
      return (string) Antlers::parse(
        $file, 
        array_merge(
          $cascade,
          $variables
        )
      );
    }
  
    public static function removeDoubleSpaces($string) {
      return preg_replace('/\s+/', ' ', $string);
    }
  
}
