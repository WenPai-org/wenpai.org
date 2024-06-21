<?php
/**
 * Class WPfanyi_Import
 *
 * Core class
 *
 * @package wpfanyi-import
 */
class WPfanyi_Import {

    /**
     * @var string Translation package import method. value:"file" or "url"
     *
     * @since 1.0.0
     */
    private $trans_import_method = '';

    /**
     * @var string Translation package type. value:"plugin" or "theme"
     *
     * @since 1.0.0
     */
    private $trans_type = '';

    /**
     * @var array Translation package information uploaded by users
     *
     * @since 1.0.0
     */
    private $trans_zip = array();

    /**
     * @var string Translation package URL
     *
     * @since 1.0.0
     */
    private $trans_url = '';

    public function __construct() {
        /** Register menu */
        add_action(is_multisite() ? 'network_admin_menu' : 'admin_menu', function () {
            add_submenu_page(
                is_multisite() ? 'index.php' : 'tools.php',
                __('import translation', 'wpfanyi-import'),
                __('import translation', 'wpfanyi-import'),
                is_multisite() ? 'manage_network_options' : 'manage_options',
                'wpfanyi_import',
                array($this, 'wpfanyi_import_page')
            );
        });

        add_filter(sprintf('%splugin_action_links_%s', is_multisite() ? 'network_admin_' : '', WPF_BASE_NAME), function ($links) {
            return array_merge(
                [sprintf('<a href="%s">%s</a>', is_multisite() ? network_admin_url('index.php?page=wpfanyi_import') : admin_url('tools.php?page=wpfanyi_import'), __('Import', 'wpfanyi-import'))],
                $links
            );
        });

        add_action('admin_enqueue_scripts', array($this, 'register_css_and_js'));
    }

    /**
     * Output and process translation import form
     *
     * @since 1.0.0
     */
    public function wpfanyi_import_page() {
        /** If it is a post request, the form value is processed */
        if (isset($_SERVER['REQUEST_METHOD']) && 'POST' === strtoupper($_SERVER['REQUEST_METHOD'])) {
            $this->trans_import_method = sanitize_text_field($_POST['trans_import_method']);
            $this->trans_type = sanitize_text_field($_POST['trans_type']);
            $this->trans_zip = array(
                'name'      => sanitize_text_field(@$_FILES['trans_zip']['name']),
                'type'      => sanitize_text_field(@$_FILES['trans_zip']['type']),
                'tmp_name'  => sanitize_text_field(@$_FILES['trans_zip']['tmp_name']),
            );
            $this->trans_url = esc_url($_POST['trans_url']);

            if ($this->data_verify()) {
                if ($this->import_trans()) {
                    $this->success_msg(__('Translation imported successfully!', 'wpfanyi-import'));
                }
            }
        }

        require_once 'page.php';
    }

    /**
     * Register CSS and JS for forms
     *
     * @since 1.0.0
     *
     * @param $page string Current page
     */
    public function register_css_and_js($page) {
        if ('tools_page_wpfanyi_import' !== $page && 'index_page_wpfanyi_import' !== $page) {
            return;
        }

        wp_enqueue_script('sisyphus', WPF_DIR_URL . 'assets/js/sisyphus.min.js', array(), WPF_VERSION);
        wp_enqueue_script('wpf', WPF_DIR_URL . 'assets/js/wpf.js', array('jquery', 'sisyphus'), WPF_VERSION, true);
        wp_enqueue_style('wpf-style', WPF_DIR_URL . 'assets/css/wpf-style.css', array(), WPF_VERSION);
    }

    /**
     * Verify the data submitted by the user
     *
     * @since 1.0.0
     *
     * @return bool true on success or false on failure.
     */
    private function data_verify() {
        if (!current_user_can('install_plugins') || !(isset($_POST['_wpnonce']) &&
            wp_verify_nonce($_POST['_wpnonce'], 'wpfanyi-import-nonce'))) {
            $this->error_msg(__('You don\'t have the authority to do that', 'wpfanyi-import'));

            return false;
        }

        if ('plugin' !== $this->trans_type && 'theme' !== $this->trans_type && 'auto' !== $this->trans_type) {
            $this->error_msg(__('Unexpected translation package type', 'wpfanyi-import'));

            return false;
        }

        if ('file' === $this->trans_import_method) {
            if (empty($this->trans_zip['name'])) {
                $this->error_msg(__('Translation package not selected', 'wpfanyi-import'));

                return false;
            }
            if ('application/x-zip-compressed' !== @$this->trans_zip['type'] && 'application/zip' !== @$this->trans_zip['type']) {
                $this->error_msg(__('The translation package should be in ZIP format', 'wpfanyi-import'));

                return false;
            }
        } elseif ('url' === $this->trans_import_method) {
            $pattern="#(http|https)://(.*\.)?.*\..*#i";
            if (!preg_match($pattern, $this->trans_url)) {
                $this->error_msg(__('Invalid URL format', 'wpfanyi-import'));

                return false;
            }
        } else {
            $this->error_msg(__('Parameter error: unknown translation package import method', 'wpfanyi-import'));

            return false;
        }

        return true;
    }

    /**
     * Handling translation package import
     *
     * @since 1.0.0
     *
     * @return bool true on success or false on failure.
     */
    private function import_trans() {
        $trans_zip_file = 'file' === $this->trans_import_method ? @$this->trans_zip['tmp_name'] : download_url($this->trans_url, $timeout = 1000);
        if(!file_exists($trans_zip_file) && filesize($trans_zip_file) > 0) {
            if ('file' === $this->trans_import_method) {
                $this->error_msg(__('Translation package upload failed, please check whether the file system permissions are normal', 'wpfanyi-import'));
            } else {
                $this->error_msg(__('Translation package acquisition failed, please check whether the URL is valid', 'wpfanyi-import'));
            }

            return false;
        }

        $trans_tmp_dir = $this->unzip($trans_zip_file);
        if (!$trans_tmp_dir) {
            return false;
        }

        $trans_types = [];
        if ('auto' === $this->trans_type) {
            $trans_types = $this->get_trans_type($trans_tmp_dir);
            if (is_wp_error($trans_types)) {
                $this->error_msg($trans_types->get_error_message());

                return false;
            }
        } else {
            $trans_types[$this->trans_type] = '';
        }

        foreach ($trans_types as $trans_type => $trans_dir) {
            $trans_files = [];
            foreach (scandir("{$trans_tmp_dir}{$trans_dir}") as $filename) {
                $trans_files[] = "{$trans_tmp_dir}{$trans_dir}/{$filename}";
            }

            $trans_files = $this->prepare_trans_file($trans_files);
            if (is_wp_error($trans_files)) {
                $this->error_msg($trans_files->get_error_message());

                return false;
            }

            $res = $this->save_to_trans_dir($trans_files, $trans_type);
            if (is_wp_error($res)) {
                $this->error_msg($res->get_error_message());

                return false;
            }
        }

        /** Try to delete the temporary file after all operations */
        @unlink($trans_zip_file);

        return true;
    }

    /**
     * Get translation type according to directory structure
     *
     * @param string $dir Directory to check
     *
     * @return WP_Error|array The translation types contained in the directory and their corresponding directories, such as ['plugin '= >'plugins']
     */
    private function get_trans_type($dir) {
        $data = [];

        foreach (scandir($dir) as $item) {
            if ('plugin' === $item || 'plugins' === $item) {
                $data['plugin'] = "/{$item}";
            } elseif ('theme' === $item || 'themes' === $item) {
                $data['theme'] = "/{$item}";
            }
        }

        if (empty($data)) {
            return new WP_Error('wpf_type_identification_failed', __('No translation package was successfully identified.', 'wpfanyi-import'));
        }

        return $data;
    }

    /**
     * unzip the zip to the PHP temporary directory
     *
     * @since 1.1.0
     *
     * @param string $zip_filename zip file
     *
     * @return false|string tmp dir on success or false on failure.
     */
    private function unzip($zip_filename) {
        $tmp_dir = get_temp_dir();
        if (empty($tmp_dir)) {
            $this->error_msg(__('The PHP temporary directory was not recognized', 'wpfanyi-import'));

            return false;
        }

        for ($i = 0; $i < 10; $i++) {
            $rand_dir = md5(rand());

            if (!file_exists($rand_dir)) {
                if (!mkdir($tmp_dir . $rand_dir, 0775)) {
                    $this->error_msg(__('PHP temporary directory is not writable.', 'wpfanyi-import'));

                    return false;
                }

                $tmp_dir .= $rand_dir;
                break;
            }
        }

        if (!class_exists('ZipArchive')) {
            $this->error_msg(__('This server doesn‘t support the decompression of Zip archives. Please contact the service provider of this server to enable the zip extension module function of PHP.', 'wpfanyi-import'));

            return false;
        }

        $zip = new ZipArchive;
        $res = $zip->open($zip_filename);
        if (!$res) {
            $this->error_msg(__('Failed to parse the Zip package. The Zip package may be damaged', 'wpfanyi-import'));
        }

        $zip->extractTo($tmp_dir);
        $zip->close();

        return $tmp_dir;
    }

    /**
     * Save the file to the WordPress translation directory
     *
     * @since 1.1.0
     *
     * @param array $files Files list
     * @param string $trans_type Translation Type
     *
     * @return WP_Error|bool
     */
    private function save_to_trans_dir($files, $trans_type) {
        $wp_trans_store_dir = WP_CONTENT_DIR . "/languages/{$trans_type}s/";

        if (!is_writable($wp_trans_store_dir)) {
            if (file_exists($wp_trans_store_dir)) {
                /** dir exist but it is not writable */

                /* translators: %s: Translation storage directory */
                return new WP_Error('wpf_file_permission_error', sprintf(__('The translation storage directory of this WordPress is not writable：%s', 'wpfanyi-import'), $wp_trans_store_dir));
            } else {
                /** translation store directory does not exist */

                if (!mkdir($wp_trans_store_dir, 0775, true)) {
                    return new WP_Error('wpf_file_permission_error', __('WordPress translation storage directory does not exist and an error occurred when trying to create it. Please refer to PHP warning output for specific error information.', 'wpfanyi-import'));
                }
            }
        }

        foreach ($files as $file) {
            copy($file, $wp_trans_store_dir.basename($file));
        }

        return true;
    }

    /**
     * Prepare to translate files and filter out other irrelevant files
     *
     * @since 1.1.0
     *
     * @param array $files Files list
     *
     * @return WP_Error|array translate files list on success.
     */
    private function prepare_trans_file($files) {
        $trans_files = [];

        foreach ($files as $k => $file) {
            $pattern="#.*\.(mo|po)#i";
            if (preg_match($pattern, $file)) {
                if(!file_exists($file) && filesize($file) > 0) {
                    continue;
                }

                $trans_files[] = $file;
            }
        }

        $exist_mo = false;
        foreach ($trans_files as $k => $file) {
            $pattern="#.*\.mo#i";
            if (preg_match($pattern, $file)) {
                $exist_mo = true;
            }
        }

        if (!$exist_mo) {
            return new WP_Error('wpf_trans_not_found', __('No valid translation was found.', 'wpfanyi-import'));
        }

        return $trans_files;
    }

    /**
     * Print success message
     *
     * @since 1.0.0
     *
     * @param string $msg Message
     */
    private function success_msg($msg) {
        echo "<div id='message' class='updated notice'><p>{$msg}</p></div>";
    }

    /**
     * Print fail message
     *
     * @since 1.0.0
     *
     * @param string $msg Message
     */
    private function error_msg($msg) {
        echo "<div id='message' class='updated error'><p>{$msg}</p></div>";
    }

}
