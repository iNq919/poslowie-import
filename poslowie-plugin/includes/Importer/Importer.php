<?php

namespace App\Importer;

class Importer
{
    public const CPT = 'posel';
    public const API_URL = 'https://api.sejm.gov.pl/sejm/term10/MP';

    public const FIELD_MAP = [
        'first_name'      => 'firstName',
        'last_name'       => 'lastName',
        'email'           => 'email',
        'club'            => 'club',
        'voivodeship'     => 'voivodeship',
        'birth_date'      => 'birthDate',
        'birth_location'  => 'birthLocation',
        'district_name'   => 'districtName',
        'district_number' => 'districtNum',
        'education_level' => 'educationLevel',
        'votes'           => 'numberOfVotes',
        'profession'      => 'profession',
        'photo_url'       => null,
    ];

    private Logger $logger;

    public function __construct()
    {
        if (!is_admin()) {
            return;
        }

        $this->logger = new Logger();

        add_action('plugins_loaded', [$this, 'register_cpt']);
    }

    public function register_cpt(): void
    {
        if (post_type_exists(self::CPT)) {
            return;
        }

        register_post_type(self::CPT, [
            'labels' => [
                'name' => 'Posłowie',
                'singular_name' => 'Poseł',
            ],
            'public' => true,
            'has_archive' => true,
            'rewrite' => ['slug' => 'posel'],
            'supports' => ['title'],
            'show_in_rest' => true,
        ]);

        $this->logger->log('CPT posel zarejestrowany');
    }

    public function sync_poslowie(int $batchSize = -1): string
    {
        if (!post_type_exists(self::CPT)) {
            $this->logger->log('Błąd: CPT nie istnieje.');
            return 'CPT nie istnieje.';
        }

        $response = wp_remote_get(self::API_URL);
        if (is_wp_error($response)) {
            $this->logger->log('Błąd pobierania danych z API: ' . $response->get_error_message());
            return 'Błąd: ' . $response->get_error_message();
        }

        $body = wp_remote_retrieve_body($response);
        $list = json_decode($body, true);
        if (!is_array($list)) {
            $this->logger->log('Błędny JSON z API.');
            return 'Błędny JSON.';
        }

        $count = 0;

        foreach ($list as $mp_data) {
            if (get_option('poslowie_import_stop')) {
                $this->logger->log('Synchronizacja zatrzymana przez użytkownika.');
                return 'Synchronizacja została zatrzymana.';
            }

            if ($batchSize !== -1 && $count >= $batchSize) {
                break;
            }

            $prepared = $this->prepare_post_data($mp_data);
            $existing = get_page_by_title($prepared['title'], OBJECT, self::CPT);

            if ($existing) {
                if ($this->post_meta_equal($existing->ID, $prepared['meta'])) {
                    continue;
                }

                wp_update_post(['ID' => $existing->ID, 'post_title' => $prepared['title']]);
                foreach ($prepared['meta'] as $key => $value) {
                    update_post_meta($existing->ID, $key, $value);
                }
                $this->logger->log("Zaktualizowano posła: {$prepared['title']}");
            } else {
                $post_id = wp_insert_post([
                    'post_title' => $prepared['title'],
                    'post_type' => self::CPT,
                    'post_status' => 'publish',
                ]);
                foreach ($prepared['meta'] as $key => $value) {
                    update_post_meta($post_id, $key, $value);
                }
                $this->logger->log("Dodano nowego posła: {$prepared['title']}");
            }

            $count++;
        }

        return "Zakończono. Zaimportowano $count posłów.";
    }

    private function post_meta_equal(int $post_id, array $data): bool
    {
        foreach ($data as $key => $value) {
            if (get_post_meta($post_id, $key, true) !== (string)$value) {
                return false;
            }
        }
        return true;
    }

    private function prepare_post_data(array $mp_data): array
    {
        $meta = [];

        foreach (self::FIELD_MAP as $meta_key => $api_key) {
            $meta[$meta_key] = $api_key
                ? ($mp_data[$api_key] ?? '')
                : self::API_URL . "/{$mp_data['id']}/photo";
        }

        $title = trim(($mp_data['firstName'] ?? '') . ' ' . ($mp_data['lastName'] ?? ''));

        return [
            'title' => $title,
            'meta' => $meta,
        ];
    }
}