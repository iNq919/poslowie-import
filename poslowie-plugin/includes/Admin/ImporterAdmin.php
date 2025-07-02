<?php

namespace App\ImporterAdmin;

use App\Importer\Importer;

class ImporterAdmin
{
    private Importer $importer;

    public function __construct()
    {
        if (!is_admin()) {
            return;
        }

        $this->importer = new Importer();

        if (!function_exists('acf_add_local_field_group')) {
            add_action('admin_notices', [$this, 'acf_missing_notice']);
            return;
        }

        add_action('init', [$this, 'register_acf_fields']);
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('wp_ajax_dd_sync_poslowie', [$this, 'handle_manual_sync']);
        add_action('wp_ajax_dd_stop_sync_poslowie', [$this, 'handle_stop_sync']);
        add_action('admin_init', fn() => delete_option('poslowie_import_stop'));
    }

    public function acf_missing_notice(): void
    {
        echo '<div class="notice notice-error"><p><strong>Posłowie Import:</strong> Brakuje wtyczki ACF.</p></div>';
    }

    public function register_acf_fields(): void
    {
        $fields = [];

        foreach (array_keys(Importer::FIELD_MAP) as $field_name) {
            $fields[] = [
                'key'   => 'field_' . $field_name,
                'label' => ucwords(str_replace('_', ' ', $field_name)),
                'name'  => $field_name,
                'type'  => match ($field_name) {
                    'email' => 'email',
                    'birth_date' => 'date_picker',
                    'photo_url' => 'url',
                    default => 'text',
                },
            ];
        }

        acf_add_local_field_group([
            'key' => 'group_mp_details',
            'title' => 'MP Details',
            'fields' => $fields,
            'location' => [[['param' => 'post_type', 'operator' => '==', 'value' => Importer::CPT]]],
        ]);
    }

    public function add_admin_menu(): void
    {
        add_menu_page(
            'Posłowie Import',
            'Posłowie Import',
            'manage_options',
            'mps-importer',
            [$this, 'admin_page'],
            'dashicons-groups',
            30
        );
    }

    public function admin_page(): void
    {
        $count = wp_count_posts(Importer::CPT)->publish;
        ?>
        <div class="wrap">
            <h1>Posłowie Import</h1>
            <p>
                <label for="mp-batch-size">Liczba posłów do pobrania (-1 = wszyscy): </label>
                <input type="number" id="mp-batch-size" value="-1" min="-1" step="1" style="width:80px">
            </p>
            <p>
                <button id="mp-sync" class="button button-primary">Synchronizuj</button>
                <button id="mp-stop" class="button button-secondary" disabled>Zatrzymaj</button>
            </p>
            <div id="mp-sync-result" style="margin-top:10px;"></div>
            <h2>Lista posłów (<?php echo $count; ?>)</h2>
            <table class="widefat striped">
                <thead><tr><th>ID</th><th>Zdjęcie</th><th>Imię</th><th>Nazwisko</th><th>Partia</th><th>Region</th></tr></thead>
                <tbody>
                <?php
                $query = new \WP_Query(['post_type' => Importer::CPT, 'posts_per_page' => -1]);
                while ($query->have_posts()) {
                    $query->the_post();
                    printf(
                        '<tr><td>%d</td><td><img src="%s" width="60"></td><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>',
                        get_the_ID(),
                        esc_url(get_field('photo_url')),
                        esc_html(get_field('first_name')),
                        esc_html(get_field('last_name')),
                        esc_html(get_field('club')),
                        esc_html(get_field('district_name'))
                    );
                }
                wp_reset_postdata();
                ?>
                </tbody>
            </table>
        </div>
        <script>
        (function () {
            const syncBtn = document.getElementById('mp-sync');
            const stopBtn = document.getElementById('mp-stop');
            const resultDiv = document.getElementById('mp-sync-result');
            let controller = null;

            syncBtn.onclick = () => {
                const batchSize = document.getElementById('mp-batch-size').value;
                syncBtn.disabled = true;
                stopBtn.disabled = false;
                resultDiv.textContent = 'Synchronizacja...';

                controller = new AbortController();

                fetch(ajaxurl, {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: new URLSearchParams({
                        action: 'dd_sync_poslowie',
                        nonce: '<?php echo wp_create_nonce("sync_poslowie_nonce"); ?>',
                        batch_size: batchSize
                    }),
                    signal: controller.signal
                })
                .then(r => r.json())
                .then(data => {
                    resultDiv.textContent = data.success ? data.data : 'Błąd: ' + data.data;
                    syncBtn.disabled = false;
                    stopBtn.disabled = true;
                })
                .catch(error => {
                    resultDiv.textContent = error.name === 'AbortError' ? 'Synchronizacja przerwana.' : 'Wystąpił błąd.';
                    syncBtn.disabled = false;
                    stopBtn.disabled = true;
                });
            };

            stopBtn.onclick = () => {
                controller?.abort();
                fetch(ajaxurl, {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: new URLSearchParams({action: 'dd_stop_sync_poslowie'})
                });
            };
        })();
        </script>
        <?php
    }

    public function handle_manual_sync(): void
    {
        check_ajax_referer('sync_poslowie_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
        }

        update_option('poslowie_import_stop', false);
        $batch_size = isset($_POST['batch_size']) ? intval($_POST['batch_size']) : -1;
        wp_send_json_success($this->importer->sync_poslowie($batch_size));
    }

    public function handle_stop_sync(): void
    {
        update_option('poslowie_import_stop', true);
        wp_send_json_success('Przerwano synchronizację');
    }
}
