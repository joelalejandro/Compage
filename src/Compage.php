<?php
/*
Plugin Name: Compage
Plugin URI: https://github.com/Moobin/Compage
Description: Framework base para construcción de plugins de Wordpress.
Author: Moobin
Version: 0.1.0
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
 * @version 0.1.0
 */

namespace Compage;

require "Core/Plugin/Plugin.php";
require "Core/Context.php";
require "Core/Plugin/PluginComponent.php";
require "Hook/PluginHook.php";
require "Model/PluginModel.php";
require "Template/PluginTemplate.php";
require "Controller/PluginController.php";
require "Notifications/BaseNotification.php";
require "Notifications/Notification.php";