<?php
defined('_JEXEC') or die;

// Nạp CSS & JS
$doc = JFactory::getDocument();
$base = JUri::base(true) . '/modules/mod_ordermanager';
$doc->addStyleSheet($base . '/media/css/style.css');
$doc->addScript($base . '/media/js/script.js');

// Gọi helper (nếu cần sau này)
require_once __DIR__ . '/helper.php';

// Hiển thị giao diện
require JModuleHelper::getLayoutPath('mod_ordermanager');
