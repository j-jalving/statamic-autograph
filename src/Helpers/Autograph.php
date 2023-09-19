<?php

namespace JJalving\Autograph\Helpers;

use Illuminate\Support\Facades\Blade;
use Statamic\Entries\Entry;
use Statamic\Data\DataCollection;
use Statamic\Facades\Antlers;
use Statamic\Facades\Cascade;
use Statamic\Facades\File;
use Statamic\Facades\Folder;
use Statamic\Facades\User;

class Autograph
{
  /**
   * Get all users from the collection set up in the config, or if no collection has been set, all 
   * control panel users will be returned.
   *
   * @return mixed
   */
  public static function getUsers(): mixed
  {
    $collection = config('statamic.autograph.user_collection');
    if ($collection) {
      return Entry::query()->where("collection", $collection)->get();
    }
    // No collection given, return cp users list
    return User::all();
  }

  /**
   * Get a specific user by id from the collection set up in the config, or if no collection has
   * been set, from the control panel users list.
   *
   * @param string $id
   * @return mixed
   */
  public static function getUser(string $id): mixed
  {
    $collection = config('statamic.autograph.user_collection');
    if ($collection) {
      return Entry::find($id);
    }
    // No collection given, return cp user
    return User::find($id);
  }

  /**
   * Get all the Blade and Antlers file from the templates folder
   *
   * @return array
   */
  public static function getTemplates(): array
  {
    $folder = config('statamic.autograph.templates_folder');
    $templates = [];
    // Get antlers template files
    foreach (Folder::disk('resources')->getFilesByTypeRecursively($folder, 'html') as $path) {
      // Add path to templates array
      $templates[$path] = [
        'type' => 'antlers',
        'label' => Autograph::getDisplayNameFromPath($path)
      ];
    }
    // Get blade template files
    foreach (Folder::disk('resources')->getFilesByTypeRecursively($folder, 'php') as $path) {
      // Add path to templates array
      $templates[$path] = [
        'type' => 'blade',
        'label' => Autograph::getDisplayNameFromPath($path)
      ];
    }

    return $templates;
  }

  /**
   * Get the display name from the given path (returns the filename without extension)
   *
   * @param string $path
   * @return string
   */
  public static function getDisplayNameFromPath(string $path): string
  {
    // Get filename from path
    $parts = explode('/', $path);
    $filename = end($parts);
    $filename = Autograph::removeExtensions($filename);
    return $filename;
  }

  /**
   * Remove .antlers.html and .blade.php extensions from a given path
   *
   * @param string $path
   * @return string
   */
  public static function removeExtensions(string $path): string
  {
    $pattern = '/\.(antlers|blade)?\.(html|php)?$/';
    return preg_replace($pattern, '', $path);
  }

  /**
   * Parse a Blade or Antlers template to HTML code
   *
   * @param string $path
   * @param string $type
   * @param array $variables
   * @return string
   */
  public static function getParsedTemplate(string $path, string $type, array $variables): string
  {
    // Load the template file
    $file = File::disk('resources')->get($path);
    // Get cascade data
    $cascade = Cascade::instance()->hydrate()->toArray();
    // Merge data
    $data = array_merge(
      $cascade,
      $variables
    );
    // Return parsed template
    if ($type === 'blade') {
      // Parse blade file
      return Blade::render($file, $data);
    } else {
      // Parse Antlers file
      return Antlers::parse($file, $data);
    }
  }

  /**
   * Get a minified version of the given HTML string
   *
   * @param string $html
   * @return string
   */
  public static function minifyHtml(string $html): string
  {
    return Minify::minifyHtml($html);
  }
}
