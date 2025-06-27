<?php

get_header();

while ( have_posts() ) : the_post(); ?>
    <article class="post-content container">
        <h1 class="post-title"><?php the_title(); ?></h1>
        <div class="post-meta">
            <span class="post-date"><?php the_date(); ?></span>
        </div>
        <?php the_content(); ?>
    </article>
<?php endwhile;

get_footer();
