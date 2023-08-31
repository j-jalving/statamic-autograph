<?php

namespace JJalving\Autograph;

use Illuminate\Support\Facades\Blade;
use Statamic\Entries\Entry;
use Statamic\Facades\Antlers;
use Statamic\Facades\Cascade;
use Statamic\Facades\File;
use Statamic\Facades\Folder;
use Statamic\Facades\User;

class Autograph
{
  public static function getUsers(): mixed
  {
    $collection = config('statamic.autograph.user_collection');
    if ($collection) {
      return Entry::query()->where("collection", $collection)->get();
    }
    // No collection given, return cp users list
    return User::all();
  }


  public static function getUser(string $id): mixed
  {
    $collection = config('statamic.autograph.user_collection');
    if ($collection) {
      return Entry::find($id);
    }
    // No collection given, return cp user
    return User::find($id);
  }

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

  public static function getDisplayNameFromPath(string $path): string
  {
    // Get filename from path
    $parts = explode('/', $path);
    $filename = end($parts);
    $filename = Autograph::removeExtensions($filename);
    return $filename;
  }

  public static function removeExtensions(string $inputString): string
  {
    $pattern = '/\.(antlers|blade)?\.(html|php)?$/';
    return preg_replace($pattern, '', $inputString);
  }

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

  public static function removeDoubleSpaces(string $string): string
  {
    return preg_replace('/\s+/', ' ', $string);
  }
}
