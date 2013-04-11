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
use Compage\Component\ComponentType;
use Compage\Essentials\Pluggable;

class InitializeController extends Controller {

  public function __construct($theme) {
    parent::__construct($theme);

    $theme->loadAll(ComponentType::Controller)
      ->loadAll(ComponentType::Shortcode)
      ->loadAll(ComponentType::CustomTaxonomy)
      ->loadAll(ComponentType::CustomPostType);

    $this->hook(function($locale) use ($theme) {
      if (isset($_COOKIE["pll_language"])) {
        return $_COOKIE["pll_language"];
      }
      return $locale;
    })->toFilter("locale");

    $this->hook(function() use ($theme) {
      foreach ($theme->get("features") as $feature => $settings) {
        if ($feature == "shortcode-in-widgets") {
          add_filter("widget_text", "do_shortcode");
        } else {
          if (isset($settings) || count($settings) > 0) {
            add_theme_support($feature, $settings);
          } else {
            add_theme_support($feature);
          }
        }
      }

      foreach ($theme->get("locales") as $language => $path) {
        load_theme_textdomain('eurofrits', dirname($path));
      }
    })->toAction("after_setup_theme");

    $this->hook(function() use ($theme) {
      foreach ($theme->get("stylesheets") as $uniqid => $stylesheet) {
        wp_register_style(
          $uniqid,
          $stylesheet["source"],
          array(),
          time(),
          $stylesheet["media"]
        );
        wp_enqueue_style($uniqid);
      }

      foreach ($theme->get("scripts") as $uniqid => $script) {
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

    $this->hook(function() use ($theme) {
      foreach ($theme->get("menus") as $menu_id => $menu) {
        register_nav_menu($menu_id, $menu["caption"]);
      }

      foreach ($theme->get("customTaxonomies") as $tax) {
        $tax->register();
      }

      foreach ($theme->get("customPostTypes") as $cpt) {
        $cpt->register();
      }
    })->toAction("init");

    $this->hook(function() use ($theme) {
      foreach ($theme->get("sidebars") as $sidebar) {
        register_sidebar($sidebar);
      }

      foreach ($theme->get("widgets") as $widget) {
        register_widget($widget);
      }

      foreach ($theme->get("shortcodes") as $shortcode) {
        add_shortcode($shortcode->getKeyword(), array($shortcode, "render"));
      }
    })->toAction("widgets_init");

  }

}