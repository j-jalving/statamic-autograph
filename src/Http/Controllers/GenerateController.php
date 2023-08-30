<?php

namespace JJalving\Autograph\Http\Controllers;

use Illuminate\Http\Request;
use Statamic\Http\Controllers\CP\CpController;
use Statamic\Facades\User;
use JJalving\Autograph\Autograph;

class GenerateController extends CpController
{
  public function index(Request $request)
  {
    abort_unless(User::current()->can('generate signatures'), 403);

    $allowEmptyUser = config('statamic.autograph.allow_empty_user');

    $codeSnippet = "";

    // Check if the form was posted
    if ($request->isMethod('post')) {
      // Create validation rules
      $rules = [
        'template_path' => ['required'],
      ];
      // // Only validate user when it can't be empty
      if (!$allowEmptyUser) {
          $rules['user_id'] = ['required'];
      }     
      // Validate request
      $request->validate($rules);
      // Create variables array to pass to the template
      $variables = [];
      if ($request->user_id) {
        $variables['autograph'] = Autograph::getUser($request->user_id);
      }
      // Check if a template file was given
      $codeSnippet = Autograph::getParsedTemplate($request->template_path, $variables);
      $codeSnippet = Autograph::removeDoubleSpaces($codeSnippet);
    }

    // Get the list of users
    $users = Autograph::getUsers();

    // Get the list of templates
    $templates = Autograph::getTemplates();

    // Remember old form input
    $request->flash();

    // Get user format
    $userFormatter = config('statamic.autograph.user_formatter');

    // Return view
    return view('statamic-autograph::generate', [
      'users' => $users,
      'allow_empty_user' => $allowEmptyUser,
      'templates' => $templates,
      'code_snippet' => $codeSnippet,
      'user_formatter' => $userFormatter
    ]);
  }
}
