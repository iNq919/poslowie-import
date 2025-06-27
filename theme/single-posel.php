<?php get_header(); ?>

<main id="main-content" class="single container">
  <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
    <nav class="back-nav">
      <a class="back-link" href="<?php echo esc_url(get_post_type_archive_link('posel')); ?>">
        ← Powrót do listy posłów
      </a>
    </nav>

    <article class="profile">
      <header class="profile-header">
        <h1 class="profile-name">
          <?php echo esc_html(get_field('first_name') . ' ' . get_field('last_name')); ?>
        </h1>
        <?php if ($club = get_field('club')) : ?>
          <p class="profile-club"><?php echo esc_html($club); ?></p>
        <?php endif; ?>
      </header>

      <div class="profile-content">
        <?php if ($photo = get_field('photo_url')) : ?>
          <div class="profile-image">
            <img src="<?php echo esc_url($photo); ?>" alt="<?php the_title_attribute(); ?>">
          </div>
        <?php endif; ?>

        <div class="profile-info">
          <dl class="details-list">
            <?php
              $fields = [
                'first_name'       => 'Imię',
                'last_name'        => 'Nazwisko',
                'email'            => 'Email',
                'voivodeship'      => 'Województwo',
                'birth_date'       => 'Data urodzenia',
                'birth_location'   => 'Miejsce urodzenia',
                'district_name'    => 'Region',
                'district_number'  => 'Numer regionu',
                'education_level'  => 'Wykształcenie',
                'votes'            => 'Liczba głosów',
                'profession'       => 'Zawód',
              ];

              foreach ($fields as $field_key => $label) {
                $value = get_field($field_key);
                if (empty($value)) continue;

                echo '<dt>' . esc_html($label) . ':</dt>';

                if ($field_key === 'email') {
                  echo '<dd><a href="mailto:' . esc_attr($value) . '" class="email-link">' . esc_html($value) . '</a></dd>';
                } else {
                  echo '<dd>' . esc_html($value) . '</dd>';
                }
              }
            ?>
          </dl>
        </div>
      </div>
    </article>

  <?php endwhile; else : ?>
    <p class="single-empty">Brak informacji do wyświetlenia</p>
  <?php endif; ?>
</main>

<?php get_footer(); ?>
