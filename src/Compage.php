<?php
/*
Plugin Name: Compage
Plugin URI: https://github.com/Moobin/Compage
Description: Framework base para construcción de plugins y templates de Wordpress.
Author: Moobin
Version: 0.2.1
Author URI: http://moobin.net/
License: MIT
*/

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

/**
 * @package Compage
 * @version 0.2
 */

namespace Compage;

require "Essentials/PluggableType.php";
require "Essentials/Pluggable.php";
require "Essentials/Context.php";
require "Component/ComponentType.php";
require "Component/Component.php";
require "Component/Controller.php";
require "Component/Entity.php";
require "Component/Hook.php";
require "Component/View.php";
require "Extensions/Notification/BaseNotification.php";
require "Extensions/Notification/Notification.php";
require "Extensions/CustomPostType/CustomPostType.php";
require "Extensions/CustomTaxonomy/CustomTaxonomy.php";
require "Extensions/CustomTaxonomy/TaxonomyController.php";
require "Extensions/Shortcode/Shortcode.php";
require "Plugin/Plugin.php";
require "Theme/Theme.php";
require "Theme/Controllers/InitializeController.php";