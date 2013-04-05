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

namespace Compage\Essentials;

abstract class Context {

  static private $pluggables = array();

  public static function registerPluggable($pluggable) {
    self::$pluggables[] = $pluggable;
  }

  public static function get($name) {
    $p = null;

    foreach (self::$pluggables as $pluggable) {
      if ($pluggable->getName() == $name) {
        $p = $pluggable;
        break;
      }
    }
    return $p;    
  }

  public static function getPlugins() {
    $plugins = array();
    foreach (self::$pluggables as $pluggable) {
      if ($pluggable->getType() == PluggableType::Plugin) {
        $plugins[] = $pluggable;
      }
    }
    return $plugins;
  }

  public static function getThemes() {
    $themes = array();
    foreach (self::$pluggables as $pluggable) {
      if ($pluggable->getType() == PluggableType::Theme) {
        $themes[] = $pluggable;
      }
    }
    return $themes;
  }
  
}