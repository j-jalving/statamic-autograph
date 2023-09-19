<?php

namespace JJalving\Autograph\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Statamic\Http\Controllers\CP\CpController;
use Statamic\Facades\User;
use JJalving\Autograph\Helpers\Autograph;

class IndexController extends CpController
{
  public function index(Request $request): View
  {
    abort_unless(User::current()->can('generate signatures'), 403);

    // Get allow empty user config value
    $allowEmptyUser = config('statamic.autograph.allow_empty_user');

    // Get user formatter config value
    $userFormatter = config('statamic.autograph.user_formatter');

    // Get the list of users to populate the form with
    $users = Autograph::getUsers();

    // Get the list of templates to populate the form with
    $templates = Autograph::getTemplates();

    // Code snippet will be filled with form data if posted
    $codeSnippet = "";

    // Check if the form was posted
    if ($request->isMethod('post')) {

      // Create validation rules
      $rules = [
        'template_path' => ['required'],
      ];

      // Only validate user when it can't be empty
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

      // Get template details
      $template = $templates[$request->template_path];

      // Check if a template file was given
      $codeSnippet = Autograph::getParsedTemplate($request->template_path, $template['type'], $variables);
      $codeSnippet = Autograph::minifyHtml($codeSnippet);
    }

    // Remember old form input
    $request->flash();

    // Return view
    return view('statamic-autograph::index', [
      'users' => $users,
      'allow_empty_user' => $allowEmptyUser,
      'templates' => $templates,
      'code_snippet' => $codeSnippet,
      'user_formatter' => $userFormatter
    ]);
  }
}
