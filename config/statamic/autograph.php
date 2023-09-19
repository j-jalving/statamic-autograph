<?php

return [
    /*
      |--------------------------------------------------------------------------
      | Templates folder
      |--------------------------------------------------------------------------
      |
      | The path to the folder containing the e-mail signature template Antlers 
      | files.
      |
      */

  "templates_folder" => "views/autograph",

    /*
      |--------------------------------------------------------------------------
      | User Collection
      |--------------------------------------------------------------------------
      |
      | This value determines what collection will be used for the user data. If 
      | not set, the control panel accounts list will be used.
      |
      */

    "user_collection" => null,

    /*
      |--------------------------------------------------------------------------
      | Allow No User
      |--------------------------------------------------------------------------
      |
      | This value determines if a signature can be generated without a selected 
      | user.
      |
      */

    "allow_empty_user" => false,

    /*
      |--------------------------------------------------------------------------
      | User Formatter
      |--------------------------------------------------------------------------
      |
      | This value allows you to provide a custom user formatter class that
      | implements the UserFormatter interface. This is used to display the
      | list of users in the user select field.
      |
      */

      "user_formatter" => \JJalving\Autograph\CustomUserFormatter::class,

];
