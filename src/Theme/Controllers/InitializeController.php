<?php
/*
Copyright (c) 2013 Joel A. Villarreal Bertoldi

Permission is hereby granted, free of charge, to any
person obtaining a copy of this software and associated
documentation files (the "Software"), to deal in the
Software without restriction, including without limitation
the rights to use, copy, modify, merge, publish,
distribute, sublicense, and/or sell copies of the
Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice
shall be included in all copies or substantial portions of
the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY
KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE
WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR
PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS
OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR
OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/

namespace Compage\Theme\Controllers;

use Compage\Component\Controller;
use Compage\Essentials\Pluggable;

class InitializeController extends Controller {

  public function __construct(Theme $theme) {
    parent::__construct($theme);

    $this->hook(function() (use $theme) {
      foreach ($theme->features as $feature => $settings) {
        if ($feature == "shortcode-in-widgets") {
          add_filter("widget_text", "do_shortcode");
          continue;
        }

        add_theme_support($features, $settings);
      }
    })->toAction("after_setup_theme");

    $this->hook(function() (use $theme) {
      foreach ($theme->stylesheets as $uniqid => $stylesheet) {
        wp_register_style(
          $uniqid,
          $stylesheet["source"],
          array(),
          time(),
          $stylesheet["media"]
        );
        wp_enqueue_style($uniqid);
      }

      foreach ($theme->scripts as $uniqid => $script) {
        wp_register_script(
          $uniqid,
          $script["source"],
          array(),
          time(),
          $script["footer"]
        );
        wp_enqueue_script($uniqid);
      }
    })->toAction("wp_enqueue_scripts");

    $this->hook(function() (use $theme) {
      foreach ($theme->menus as $menu_id => $menu_caption) {
        register_nav_menu($menu_id, $menu_caption);
      }
    })->toAction("init");

    $this->hook(function() (use $theme) {
      foreach ($theme->sidebars as $sidebar) {
        register_sidebar($sidebar);
      }

      foreach ($theme->widgets as $widget) {
        register_widget($widget);
      }
    })->toAction("widgets_init");

  }

}