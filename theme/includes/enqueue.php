<?php

const THEME_VERSION = '1.0.0';

function load_stylesheets() {
  wp_register_style('style', get_template_directory_uri() . '/assets/css/style.css', array(), THEME_VERSION);
  wp_enqueue_style('style');
}

add_action('wp_enqueue_scripts', 'load_stylesheets');