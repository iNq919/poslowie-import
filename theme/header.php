<!DOCTYPE html>
<html <?php language_attributes(); ?>>
    <head>
        <meta charset="<?php bloginfo('charset'); ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="profile" href="http://gmpg.org/xfn/11">
        <title><?php wp_title('-', true, 'right'); ?></title>
        <?php wp_head(); ?>
    </head>
    <body <?php body_class(); ?>>
    <div class="mobile-container">
        <div class="right">
            <div class="hamburger-toggle-container">
                <div class="hamburger-toggle">
                    <div class="hamburger-toggle-bar"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="menu-container">
        <div class="container">
            <div class="menu">
                <nav class="main-navigation">
                <?php
                    wp_nav_menu(
                        array(
                            'theme_location' => 'main-menu',
                            'menu_id' => 'main-menu',
                            'container' => false,
                            'menu_class' => 'main-menu',
                            'link_after' => '<span class="dropdown-icon"><svg width="10" height="6" viewBox="0 0 10 6" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9 1L5 5L1 1" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/></svg></span>',
                        )
                    );
                ?>

                </nav>
            </div>
        </div>
    </div>