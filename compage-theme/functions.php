<?php
namespace Moobin\Wordpress\Themes\CompageTheme;

use Compage\Theme\Theme;
use Compage\Component\ComponentType;

class CompageTheme extends Theme {
  
  public function __construct($base_file) {

    parent::__construct($base_file);
    $this->setFullyQualifiedName(__NAMESPACE__)
         ->addScript("demo.js")
         ->addMenu("compage-menu", "Menú principal")
         ->addFeature("custom-background")
         ->addSidebar(array(
            "name" => "Homepage Post Aside Widget Area",
            "id" => "expert-theme-aside-post-widgets",
            "description" => "Widgets placed here will appear to the right of the latest post on the homepage.",
            "before_widget" => "<div class='wrapper'>",
            "after_widget" => "</div>",
            "before_title" => "",
            "after_title" => ""
          ))    
         ->loadAll(ComponentType::Controller)
         ->initialize();

  }

}

Theme::activate("CompageTheme", __FILE__);
