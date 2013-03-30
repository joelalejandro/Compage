<?php
namespace Moobin\Wordpress\Themes\CompageTheme\Controllers;

use Compage\Component\Controller;

class HomepageController extends Controller {
  
  public function __construct($theme) {
    parent::__construct($theme, "Home Page");
  }

}