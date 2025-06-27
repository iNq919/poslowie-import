<?php
get_header(); ?>

<main class="container">

<div class="archive-header">
    <h1 class="archive-title"><?php the_archive_title(); ?></h1>
    <?php the_archive_description(); ?>
</div>

<?php if ( have_posts() ) : ?>
    <div class="archive-posts">
        <?php while ( have_posts() ) : the_post(); ?>
            <article class="archive-post">
                <h2 class="post-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                <?php the_excerpt(); ?>
                <a href="<?php the_permalink(); ?>" class="read-more">Czytaj więcej</a>
            </article>
        <?php endwhile; ?>
    </div>

    <?php the_posts_pagination(); ?>

<?php else : ?>
    <p class="no-posts">Brak wpisów do wyświetlenia.</p>
<?php endif; ?>

</main>

<?php get_footer(); ?>
