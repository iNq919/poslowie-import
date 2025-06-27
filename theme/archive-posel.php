<?php get_header(); ?>

<main id="main-content" class="archive container">
  <h1 class="archive-title">Lista Posłów</h1>

  <section class="archive-grid">
    <?php if (have_posts()) : ?>
      <?php while (have_posts()) : the_post(); ?>
        <article class="card">
          <a href="<?php the_permalink(); ?>" class="card-link" aria-label="<?php the_title_attribute(); ?>">
            <?php if ($photo = get_field('photo_url')) : ?>
              <div class="card-image" style="background-image: url('<?php echo esc_url($photo); ?>');"
                   role="img" aria-label="Zdjęcie posła"></div>
            <?php endif; ?>

            <div class="card-body">
              <?php
                $fields = [
                  'first_name'     => '',
                  'last_name'      => '',
                  'club'           => 'card-club',
                  'district_name'  => 'card-district',
                ];

                $full_name = trim(get_field('first_name') . ' ' . get_field('last_name'));
                echo '<h2 class="card-name">' . esc_html($full_name) . '</h2>';

                foreach ($fields as $field => $class) {
                  if (in_array($field, ['first_name', 'last_name'], true)) {
                    continue;
                  }

                  $value = get_field($field);
                  if (!empty($value)) {
                    echo '<p class="' . esc_attr($class) . '">' . esc_html($value) . '</p>';
                  }
                }
              ?>
            </div>
          </a>
        </article>
      <?php endwhile; ?>
    <?php else : ?>
      <p class="archive-empty">Brak posłów do wyświetlenia.</p>
    <?php endif; ?>
  </section>

  <nav class="pagination" aria-label="Paginacja">
    <?php
      echo paginate_links([
        'prev_text' => '← Poprzednia',
        'next_text' => 'Następna →',
        'type'      => 'list',
      ]);
    ?>
  </nav>
</main>

<?php get_footer(); ?>
