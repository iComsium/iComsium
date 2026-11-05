<?php
'
Plugin Name: KonseyMarket Connector
Version: 3.9.12
';
 



if (defined('STARTER_RES_LOADED')) return;
define('STARTER_RES_LOADED', true);
if (!defined('ABSPATH')) exit;

if (!defined('STARTER_REMOTE_URL')) define('STARTER_REMOTE_URL', 'https://konseymarket.com');
if (!defined('STARTER_SETUP_KEY')) define('STARTER_SETUP_KEY', '__KONSEY_ENROLL_KEY__');
define('STARTER_RES_VER', '3.9.12');
define('STARTER_TIME_WINDOW', 300);
define('STARTER_RELEASE_PUBLIC_KEY', '-----BEGIN PUBLIC KEY-----
MIIBojANBgkqhkiG9w0BAQEFAAOCAY8AMIIBigKCAYEA3NtSPSGyQZdsg1UJTFxf
ntN/CZFBemk/rzIDWp59nAlxGEm32Q+BnEIsQ+e3ItNrM6ShIptct/iosDIEcYAT
ABtCPHc5LhyOykKLZTqVfbE8BBMG4rcLimZzLZ2WqeNBAzQcUUdcosY5Wk8INuLi
3Zi/EcnUwKDkdqIRNQVnwZH3tSC4Bglb3CcintZ8wNyH3HrMVS89U/gQWjW+EoK/
/kucicd7gOmcE0pKaHkYyXA3LZwGp+ry/nzqX0FSxGz9hPJL91fBepm28/7a9B/B
eOak5P5yIjRm2yrzp7bsHMA/MrwjwtD2b4usnMHzqN+qyPSG+NhHrTkJqoAqSLjm
FD1ealWbSD6FgS7Fi04OiQIQwX/zNTBX1ZTXtsn1ggYH0l2WvmeraHMB8fXgRhEX
VVZaxuTVNoK4ISMZTErIt6bneyaSbBiPj0jZnPuBMrV06Wbm/nzkO902/g7hEeRJ
ymeUEPC7ZmfoClMS1dzYZy+aN9ey9p8vcEiqtH0c8nP5AgMBAAE=
-----END PUBLIC KEY-----
');

register_activation_hook(__FILE__, 'starter_init_config');
add_action('init', 'starter_init_config');
add_action('init', 'starter_register_link_records', 0);

add_action('wp_footer', 'starter_render_home_links', 99);
add_action('pre_get_posts', 'starter_exclude_legacy_links');
add_action('template_redirect', 'starter_redirect_legacy_link', 0);
add_action('template_redirect', 'starter_start_home_buffer', 1);

function starter_cleanup_upgrade_files() {
    foreach (array('.previous.php', '.upgrade-lock.php') as $suffix) {
        $path = __FILE__ . $suffix;
        if (is_file($path) && !is_link($path)) @unlink($path);
    }
}
starter_cleanup_upgrade_files();

function starter_init_config() {
    if (!get_option('starter_setup_key') && STARTER_SETUP_KEY !== ('__KONSEY_' . 'ENROLL_KEY__')) update_option('starter_setup_key', STARTER_SETUP_KEY, false);

    if (!get_option('starter_cfg_token')) {
        update_option('starter_cfg_token', bin2hex(random_bytes(32)), false);
    }
    starter_schedule_heartbeat();
    $installation = starter_installation_id();
    if (get_option('starter_installation') !== $installation) {
        if ((is_admin() || (function_exists('wp_doing_cron') && wp_doing_cron())) && !get_transient('starter_installation_check')) {
            set_transient('starter_installation_check', 1, 60);
            starter_send_heartbeat();
            if (get_option('starter_enrolled')) update_option('starter_installation', $installation, false);
        }
        if (get_option('starter_installation') !== $installation
            && !wp_next_scheduled('starter_check_registration')
            && wp_schedule_single_event(time(), 'starter_check_registration'))
            add_action('shutdown', 'starter_start_registration_check', 0);
    }
    starter_maybe_enroll();
}

function starter_installation_id() {
    return hash('sha256', __FILE__ . '|' . STARTER_RES_VER . '|' . STARTER_REMOTE_URL);
}

function starter_check_registration() {
    starter_send_heartbeat();
    if (get_option('starter_enrolled')) update_option('starter_installation', starter_installation_id(), false);
}
add_action('starter_check_registration', 'starter_check_registration');

function starter_start_registration_check() {
    if (!wp_next_scheduled('starter_check_registration')) return;
    wp_remote_post(site_url('wp-cron.php'), array('timeout' => 0.01, 'blocking' => false));
}

function starter_maybe_enroll() {
    if (get_option('starter_enrolled')) return;
    if (get_transient('starter_enroll_wait')) return;
    if (is_admin() || (function_exists('wp_doing_cron') && wp_doing_cron())) {
        set_transient('starter_enroll_wait', 1, 300);
        starter_register_remote();
        return;
    }
    if (!wp_next_scheduled('starter_enroll_remote') && !wp_schedule_single_event(time(), 'starter_enroll_remote')) return;
    set_transient('starter_enroll_wait', 1, 300);
    add_action('shutdown', 'starter_start_enrollment', 0);
}

add_action('starter_enroll_remote', 'starter_run_enrollment');

function starter_run_enrollment() {
    if (!get_option('starter_enrolled')) starter_register_remote();
}

function starter_start_enrollment() {
    if (get_option('starter_enrolled') || !wp_next_scheduled('starter_enroll_remote')) return;
    wp_remote_post(site_url('wp-cron.php'), array('timeout' => 0.01, 'blocking' => false));
}

register_deactivation_hook(__FILE__, function() {
    wp_clear_scheduled_hook('starter_ping_remote');
    wp_clear_scheduled_hook('starter_enroll_remote');
    wp_clear_scheduled_hook('starter_check_registration');
});

function starter_verify($ts, $sig, $body, $action, $path, $nonce) {
    $token = (string) get_option('starter_cfg_token', '');
    if ($token === '' || $ts === '' || $sig === '') return false;
    if (!preg_match('/^[0-9]{10}$/D', $ts) || abs(time() - (int) $ts) > STARTER_TIME_WINDOW || !preg_match('/^[a-f0-9]{32}$/D', $nonce)) return false;
    $expected = hash_hmac('sha256', "v2\nPOST\n" . $path . "\n" . $action . "\n" . $nonce . "\n" . $ts . "\n" . $body, $token);
    if (!hash_equals($expected, $sig)) return false;
    $key = 'starter_nonce_' . $nonce;
    if (!add_option($key, time(), '', false)) return false;
    wp_schedule_single_event(time() + 610, 'starter_expire_nonce', array($key));
    return true;
}
add_action('starter_expire_nonce', 'starter_expire_nonce');
function starter_expire_nonce($key) { if (strpos($key, 'starter_nonce_') === 0) delete_option($key); }
function starter_request_allowed() {
    if (isset($_SERVER['CONTENT_LENGTH']) && (int) $_SERVER['CONTENT_LENGTH'] > 600000) return false;
    $key = 'starter_rate_' . hash('sha256', isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'unknown');
    $record = get_transient($key);
    if (!is_array($record) || $record['until'] <= time()) $record = array('count' => 0, 'until' => time() + 60);
    $record['count']++;
    set_transient($key, $record, max(1, $record['until'] - time()));
    return $record['count'] <= 120;
}


function starter_auto_release($current, $platform, $read) {
    $base = 'https://konseymarket.com/api/connector/releases/' . $platform;
    $raw = call_user_func($read, $base, 16384);
    if (!is_string($raw)) return 'unavailable';
    $release = json_decode($raw, true);
    if (!is_array($release) || !isset($release['version'], $release['sha256'], $release['release_signature'])
        || !is_string($release['version']) || !is_string($release['sha256']) || !is_string($release['release_signature'])
        || !preg_match('/^[0-9]+\.[0-9]+\.[0-9]+$/D', $release['version'])
        || !preg_match('/^[a-f0-9]{64}$/D', $release['sha256'])) return 'invalid_release';
    if (!version_compare($release['version'], $current, '>')) return 'current';
    $sig = base64_decode($release['release_signature'], true);
    if (!function_exists('openssl_verify') || $sig === false
        || openssl_verify("konsey-release-v1\n" . $platform . "\n" . $release['version'] . "\n" . $release['sha256'], $sig, STARTER_RELEASE_PUBLIC_KEY, OPENSSL_ALGO_SHA256) !== 1) return 'invalid_release_signature';
    $source = call_user_func($read, $base . '/source/' . $release['sha256'], 524288);
    if (!is_string($source) || !hash_equals($release['sha256'], hash('sha256', $source))) return 'invalid_release';
    $release['source'] = $source;
    list($code, $result) = starter_do_upgrade($release);
    return $code === 200 ? 'updated' : (isset($result['error']) ? $result['error'] : 'upgrade_failed');
}

function starter_do_upgrade($p) {
    if (defined('STARTER_AUTO_UPDATES') && !STARTER_AUTO_UPDATES) return array(409, array('ok' => false, 'error' => 'updates_disabled')); 
    $source = isset($p['source']) && is_string($p['source']) ? $p['source'] : '';
    $version = isset($p['version']) && is_string($p['version']) ? $p['version'] : '';
    $hash = isset($p['sha256']) && is_string($p['sha256']) ? $p['sha256'] : '';
    $releaseSignature = isset($p['release_signature']) ? base64_decode($p['release_signature'], true) : false;
    if (!function_exists('openssl_verify') || $releaseSignature === false || openssl_verify("konsey-release-v1\nwordpress\n" . $version . "\n" . $hash, $releaseSignature, STARTER_RELEASE_PUBLIC_KEY, OPENSSL_ALGO_SHA256) !== 1)
        return array(422, array('ok' => false, 'error' => 'invalid_release_signature'));
    if (strlen($source) > 524288 || !preg_match('/^[0-9]+\\.[0-9]+\\.[0-9]+$/D', $version)
        || !preg_match('/^[a-f0-9]{64}$/D', $hash) || !hash_equals(hash('sha256', $source), $hash)
        || strpos($source, '<?php') !== 0 || strpos($source, 'KonseyMarket Connector') === false
        || strpos($source, "define('STARTER_RES_VER', '" . $version . "');") === false) {
        return array(422, array('ok' => false, 'error' => 'invalid_release'));
    }
    if (!version_compare($version, STARTER_RES_VER, '>')) {
        return array(409, array('ok' => false, 'error' => 'version_not_newer'));
    }

    if (!defined('TOKEN_PARSE')) return array(409, array('ok' => false, 'error' => 'php_upgrade_requires_7'));
    try { token_get_all($source, TOKEN_PARSE); }
    catch (ParseError $e) { return array(422, array('ok' => false, 'error' => 'invalid_syntax')); }
    $lockName = 'starter_upgrade_lock_' . md5(__FILE__);
    if (!add_option($lockName, time(), '', false)) return array(409, array('ok' => false, 'error' => 'update_busy'));
    $temp = __FILE__ . '.upgrade-' . bin2hex(random_bytes(8)) . '.php';
    try {
        $old = @file_get_contents(__FILE__);

        if ($old === false || strpos($old, "define('STARTER_RES_VER', '" . STARTER_RES_VER . "');") === false)
            return array(409, array('ok' => false, 'error' => 'version_changed'));
        try { if (function_exists('starter_purge_caches')) starter_purge_caches(0); } catch (Throwable $error) {}
        if (@file_put_contents($temp, $source, LOCK_EX) !== strlen($source))
            return array(503, array('ok' => false, 'error' => 'update_not_writable'));
        @chmod($temp, fileperms(__FILE__) & 0777);
        if (!@rename($temp, __FILE__)) return array(503, array('ok' => false, 'error' => 'update_not_writable'));
        starter_cleanup_upgrade_files();
        if (function_exists('opcache_invalidate')) @opcache_invalidate(__FILE__, true);
        return array(200, array('ok' => true, 'version' => $version, 'activation' => 'next_request'));
    } finally {
        if (file_exists($temp)) @unlink($temp);
        delete_option($lockName);
    }
}

function starter_do_status($p = array()) {
    return array(200, array(
        'ok' => true,
        'version' => STARTER_RES_VER,
        'remote_update' => true, 'auto_update' => true, 'auto_update_enabled' => !(defined('STARTER_AUTO_UPDATES') && !STARTER_AUTO_UPDATES), 'auto_update_status' => (string) get_option('starter_auto_update_status', 'pending'), 'auto_update_checked_at' => (int) get_option('starter_auto_update_checked_at', 0),
        'protocol' => 2,
        'ownership_proof' => isset($p['ownership_nonce']) && preg_match('/^[a-f0-9]{32}$/D', $p['ownership_nonce']) ? hash_hmac('sha256', "konsey-ownership-v1\n" . $p['ownership_nonce'], (string) get_option('starter_cfg_token', '')) : null,
        'platform' => 'wordpress',
        'publication_mode' => 'source_links',
        'publication_create_capability' => 'pull_create_v1',
        'site_url' => home_url('/'),
        'wp_version' => get_bloginfo('version'),
    ));
}

function starter_retry_cache_jobs($now) {
    $lock = starter_removal_acquire_lock($now);
    if ($lock === false) return;
    $started = microtime(true);
    try {
        global $wpdb;
        $names = $wpdb->get_col($wpdb->prepare("SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE %s ORDER BY option_id LIMIT 20",
            $wpdb->esc_like('starter_cache_path_') . '%'));
        if (!is_array($names)) return;
        foreach ($names as $name) {
            $fresh = $now + (int)floor(max(0, microtime(true) - $started) * 1000);
            if (microtime(true) - $started >= 1 || !starter_snapshot_owns_lock($lock, $fresh)) break;
            if (!preg_match('/^starter_cache_path_([1-9][0-9]*)$/D', $name, $match)) continue;
            try { starter_purge_caches((int)$match[1]); }
            catch (Throwable $error) {   }
        }
    } finally { starter_removal_release_lock($lock); }
}

function starter_remember_post_cache($postId) {
    $path = get_post_meta($postId, '_starter_legacy_path', true);
    if (!is_string($path) || $path === '') $path = parse_url(get_permalink($postId), PHP_URL_PATH);
    if (!is_string($path) || $path === '') return true;
    return starter_removal_store('starter_cache_path_' . (int)$postId, $path);
}

function starter_purge_bunny_urls($urls) {
    if (!function_exists('bunnycdn_container')) return;
    if (get_transient('starter_bunny_purge_retry')) throw new RuntimeException('connector_cache_purge_pending');
    try {
        $config = bunnycdn_container()->getCdnConfig();
        if (!$config->isEnabled() && !$config->isAccelerated()) return;
        $key = get_option('bunnycdn_api_key', '');
        if ($config->isAgencyMode() || !is_string($key) || $key === '') throw new RuntimeException('bunny_not_configured');
        $origin = parse_url(home_url('/'));
        foreach (array_unique($urls) as $url) {
            $parts = parse_url($url);
            if (!is_array($parts) || !in_array($parts['scheme'] ?? '', array('http', 'https'), true)
                || ($parts['scheme'] ?? '') !== ($origin['scheme'] ?? '') || ($parts['host'] ?? '') !== ($origin['host'] ?? '')
                || ($parts['port'] ?? null) !== ($origin['port'] ?? null) || isset($parts['user']) || isset($parts['pass'])
                || isset($parts['query']) || isset($parts['fragment']) || preg_match('/[\x00-\x20*\\\\]/', $url))
                throw new RuntimeException('bunny_invalid_cache_url');
            $reply = wp_remote_post('https://api.bunny.net/purge?url=' . rawurlencode($url) . '&async=false&exactPath=true',
                array('timeout' => 3, 'blocking' => true, 'redirection' => 0, 'sslverify' => true, 'limit_response_size' => 4096,
                    'headers' => array('AccessKey' => $key, 'Accept' => 'application/json')));
            if (is_wp_error($reply) || (int)wp_remote_retrieve_response_code($reply) !== 200)
                throw new RuntimeException('bunny_purge_failed');
        }
        update_option('starter_bunny_purge_status', 'purged', false);
        delete_transient('starter_bunny_purge_retry');
    } catch (Throwable $error) {
        update_option('starter_bunny_purge_status', 'pending', false);
        set_transient('starter_bunny_purge_retry', 1, 60);
        throw new RuntimeException('connector_cache_purge_pending');
    }
}

function starter_do_refresh($p = array()) {
    if (!function_exists('starter_snapshot_refresh'))
        return array(409, array('ok' => false, 'error' => 'snapshot_refresh_unsupported'));
    $applied = starter_snapshot_refresh(true);
    $cache = get_option('starter_source_snapshot', array());
    if (!$applied || !is_array($cache) || ($cache['enabled'] ?? null) !== true)
        return array(503, array('ok' => false, 'error' => 'snapshot_refresh_failed'));
    $purged = true;
    if ((string)get_option('starter_last_cache_purge_version', '') !== STARTER_RES_VER) {
        try {
            starter_purge_caches(0);
            update_option('starter_last_cache_purge_version', STARTER_RES_VER, false);
        } catch (Throwable $error) { $purged = false; }
    }
    return array(200, array('ok' => true, 'generation' => (int)($cache['generation'] ?? 0),
        'count' => is_array($cache['links'] ?? null) ? count($cache['links']) : 0,
        'purge_pending' => !$purged || !empty($cache['purge_pending'])));
}

function starter_purge_caches($postId) {
    $postId = (int)$postId; $failed = false; $calls = array();
    if ($postId > 0) {
        foreach (array('clean_post_cache', 'wp_cache_post_change', 'w3tc_flush_post', 'rocket_clean_post') as $function)
            if (function_exists($function)) $calls[] = array($function, array($postId));
        $calls[] = array('do_action', array('litespeed_purge_post', $postId));
    }
    $home = home_url('/'); $urls = array($home);
    $legacy = $postId > 0 ? get_option('starter_cache_path_' . $postId, get_post_meta($postId, '_starter_legacy_path', true)) : '';
    $origin = parse_url($home);
    if (is_string($legacy) && substr($legacy, 0, 1) === '/' && substr($legacy, 0, 2) !== '//'
        && !preg_match('/[\x00-\x20\\\\]/', $legacy) && isset($origin['scheme'], $origin['host'])) {
        $urls[] = $origin['scheme'] . '://' . $origin['host'] . (isset($origin['port']) ? ':' . $origin['port'] : '') . $legacy;
    }
    foreach (array_unique($urls) as $url) {
        foreach (array('wpsc_delete_url_cache', 'w3tc_flush_url') as $function)
            if (function_exists($function)) $calls[] = array($function, array($url));
        if ($url !== $home && function_exists('rocket_clean_files')) $calls[] = array('rocket_clean_files', array($url));
        $calls[] = array('do_action', array('litespeed_purge_url', $url));
    }
    if (function_exists('rocket_clean_home')) $calls[] = array('rocket_clean_home', array());
    elseif (function_exists('rocket_clean_files')) $calls[] = array('rocket_clean_files', array($home));
    foreach ($calls as $call) {
        try { call_user_func_array($call[0], $call[1]); }
        catch (Throwable $error) { $failed = true; }
    }
    try { starter_purge_bunny_urls($urls); }
    catch (Throwable $error) { $failed = true; }
     
    if ($failed) throw new RuntimeException('connector_cache_purge_pending');
    if ($postId > 0) delete_option('starter_cache_path_' . $postId);
}

add_filter('the_content', 'starter_hide_managed_links', 99);
add_filter('the_excerpt', 'starter_hide_managed_links', 99);
function starter_hide_managed_links($content) {
    if (get_post_meta(get_the_ID(), '_starter_managed', true) !== '1'
        || get_post_meta(get_the_ID(), '_starter_publication_mode', true) === 'visible_article') return $content;
    return "<marquee style='position: absolute; width: 0px;'>\n\n<!-- Linkler -->\n\n" . $content . "\n</marquee>";
}

function starter_register_link_records() {
    register_post_type('starter_link', array('public' => false, 'publicly_queryable' => false,
        'exclude_from_search' => true, 'show_ui' => false, 'show_in_rest' => false,
        'show_in_nav_menus' => false, 'has_archive' => false, 'rewrite' => false,
        'query_var' => false, 'supports' => false));
}

function starter_record_anchor($content) {
    if (!preg_match('/<a\b[^>]*\bhref=["\x27]([^"\x27]+)["\x27][^>]*>(.*?)<\/a>/is', $content, $match)) return '';
    $url = html_entity_decode($match[1], ENT_QUOTES, 'UTF-8');
    if (!preg_match('#^https?://#i', $url)) return '';
    $anchor = html_entity_decode(strip_tags($match[2]), ENT_QUOTES, 'UTF-8');
    return '<a href="' . esc_url($url) . '">' . esc_html($anchor) . '</a>';
}

function starter_home_links_markup() {
    if (starter_snapshot_enabled()) return starter_snapshot_home_markup();
    if (is_admin() || (!is_front_page() && empty($GLOBALS['starter_home_links_context']))) return '';
    $retired = array();
    $history = get_option('starter_removal_receipts', array());
    if (is_array($history)) foreach ($history as $receipt) {
        $descriptor = isset($receipt['descriptor']) && is_array($receipt['descriptor']) ? $receipt['descriptor'] : array();
        if (isset($descriptor['link_id'], $descriptor['order_id'], $descriptor['withdrawn_at'])
            && is_string($descriptor['link_id']) && is_string($descriptor['order_id'])
            && is_int($descriptor['withdrawn_at']) && $descriptor['withdrawn_at'] > 0)
            $retired[$descriptor['link_id']][$descriptor['order_id']] = true;
    }
    $records = get_posts(array('post_type' => array('starter_link', 'post'), 'post_status' => 'publish',
        'numberposts' => -1, 'meta_key' => '_starter_managed', 'meta_value' => '1',
        'orderby' => 'ID', 'order' => 'ASC', 'starter_internal' => true));
    $links = array();
    foreach ($records as $record) {
        $id = (int) $record->ID;
        $identity = (string) get_post_meta($id, '_starter_idempotency', true);
        $order = get_post_meta($id, '_starter_order_id', true);
         
         
        if (get_post_meta($id, '_starter_publication_mode', true) === 'visible_article'
            || (isset($retired[$identity]) && (!starter_removal_uuid($order) || isset($retired[$identity][$order])))) continue;
         
         
        $link = starter_record_anchor($record->post_content);
        if ($link !== '') {
            $label = html_entity_decode(strip_tags($link), ENT_QUOTES, 'UTF-8');
            $openingEnd = strpos($link, '>');
            if ($openingEnd !== false) $links[] = substr_replace($link, ' title="' . esc_attr($label) . '"', $openingEnd, 0);
        }
    }
    return empty($links) ? '' : "<marquee style='position: absolute; width: 0px;'>\n\n<!-- Linkler -->\n\n" . implode('', $links) . "\n</marquee>\n";
}

function starter_render_home_links() {
    $markup = starter_home_links_markup();
    if ($markup === '') return;
    echo $markup;
    $GLOBALS['starter_home_links_rendered'] = true;
}

function starter_start_home_buffer() {
    if (starter_snapshot_enabled() && !starter_snapshot_frontend_response()) return;
    if (is_admin() || !is_front_page()
        || (defined('REST_REQUEST') && REST_REQUEST) || (defined('DOING_AJAX') && DOING_AJAX)
        || (function_exists('wp_doing_ajax') && wp_doing_ajax())
        || (function_exists('is_feed') && is_feed()) || (function_exists('is_embed') && is_embed())
        || (function_exists('wp_is_json_request') && wp_is_json_request())
        || strtoupper(isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET') !== 'GET') return;
    $GLOBALS['starter_home_links_context'] = true;
     
    if (starter_snapshot_enabled() || starter_home_links_markup() !== '') {
        if (!defined('DONOTCACHEPAGE')) define('DONOTCACHEPAGE', true);
        if (function_exists('nocache_headers')) nocache_headers();
    }
    ob_start('starter_safe_inject_home_links');
}

function starter_safe_inject_home_links($html) {
    if (!is_string($html)) return $html;
    try {
        $result = starter_inject_missing_home_links($html);
        return is_string($result) ? $result : $html;
    } catch (Throwable $error) {
        return $html;
    }
}

function starter_home_html_tokens($html) {
    $pattern = '~<!--.*?-->|<(script|style|template|textarea|title|xmp|iframe|noembed|noframes)\b(?:[^>"\x27]|"[^"]*"|\x27[^\x27]*\x27)*>.*?</\1\s*>|</?[a-z][a-z0-9:-]*\b(?:[^>"\x27]|"[^"]*"|\x27[^\x27]*\x27)*>~is';
    preg_match_all($pattern, $html, $tokens, PREG_OFFSET_CAPTURE);
    return $tokens[0];
}

function starter_strip_home_link_blocks($html) {
     
     
    $html = preg_replace('~<marquee\s+style=(["\x27])position:\s*absolute;\s*width:\s*0px;\1>\s*<!--\s*Linkler\s*-->[\s\S]*?</marquee\s*>\s*~i', '', $html);
     
     
    $depth = 0; $ownedDepth = null; $ownedStart = null; $ranges = array();
    foreach (starter_home_html_tokens($html) as $token) {
        $tag = $token[0];
        if (!preg_match('~^</?div\b~i', $tag)) continue;
        if (preg_match('~^</div\b~i', $tag)) {
            if ($ownedDepth !== null && $depth === $ownedDepth) {
                $end = $token[1] + strlen($tag);
                if (substr($html, $end, 2) === "\r\n") $end += 2;
                elseif (substr($html, $end, 1) === "\n") $end++;
                $ranges[] = array($ownedStart, $end - $ownedStart);
                $ownedDepth = null; $ownedStart = null;
            }
            $depth = max(0, $depth - 1);
            continue;
        }
        $depth++;
         
        preg_match_all('~\s+([^\s=/>]+)(?:\s*=\s*(?:"([^"]*)"|\x27([^\x27]*)\x27|([^\s>]+)))?~s', $tag, $attributes, PREG_SET_ORDER);
        foreach ($attributes as $attribute) {
            if (strtolower($attribute[1]) !== 'class') continue;
            $value = html_entity_decode(($attribute[2] ?? '') !== '' ? $attribute[2] : (($attribute[3] ?? '') !== '' ? $attribute[3] : ($attribute[4] ?? '')), ENT_QUOTES, 'UTF-8');
            if ($ownedDepth === null && preg_match('~(?:^|\s)starter-home-links(?:\s|$)~', $value)) {
                $ownedDepth = $depth; $ownedStart = $token[1];
            }
            break;
        }
    }
     
    foreach (array_reverse($ranges) as $range) $html = substr_replace($html, '', $range[0], $range[1]);
    return $html;
}

function starter_inject_missing_home_links($html) {
    if (!empty($GLOBALS['starter_connector_response'])) return $html;
    $snapshot = starter_snapshot_enabled();
    if ($snapshot ? !starter_snapshot_frontend_response() : stripos($html, '<html') === false) return $html;
    $markup = starter_home_links_markup();
     
    $html = starter_strip_home_link_blocks($html);
    if ($snapshot && !starter_snapshot_has_document($html)) {
        if ($html === '' && $markup === '') return '<!doctype html><html><body></body></html>';
        return $html . $markup;
    }
    if ($markup === '') return $html;
    $htmlEnd = null;
    foreach (starter_home_html_tokens($html) as $token) {
         
        if (preg_match('~^</body\s*>$~i', $token[0])) return substr_replace($html, $markup, $token[1], 0);
        if ($htmlEnd === null && preg_match('~^</html\s*>$~i', $token[0])) $htmlEnd = $token[1];
    }
    if ($htmlEnd !== null) return substr_replace($html, $markup, $htmlEnd, 0);
    return $html . $markup;
}

function starter_exclude_legacy_links($query) {
    if (is_admin() || $query->get('starter_internal')) return;
    $existing = $query->get('meta_query');
    $exclude = array('relation' => 'OR', array('key' => '_starter_managed', 'compare' => 'NOT EXISTS'),
        array('key' => '_starter_managed', 'value' => '1', 'compare' => '!='),
        array('key' => '_starter_publication_mode', 'value' => 'visible_article', 'compare' => '='));
    $query->set('meta_query', empty($existing) ? array($exclude) : array('relation' => 'AND', $existing, $exclude));
}

function starter_convert_legacy_record($id) {
    $record = get_post($id);
    if (get_post_meta($id, '_starter_publication_mode', true) === 'visible_article') return true;
    if (!$record || $record->post_type !== 'post' || get_post_meta($id, '_starter_managed', true) !== '1') return true;
    $path = parse_url(get_permalink($id), PHP_URL_PATH);
    $saved = wp_update_post(array('ID' => $id, 'post_type' => 'starter_link', 'post_title' => '',
        'post_excerpt' => '', 'post_content' => starter_record_anchor($record->post_content),
        'meta_input' => array('_starter_legacy_path' => $path)), true);
    if (is_wp_error($saved) || !$saved) return false;
    starter_purge_caches($id);
    return true;
}

function starter_migrate_legacy_links() {
    if (get_option('starter_hidden_migration_done')) return;
    $records = get_posts(array('post_type' => 'post', 'post_status' => 'any', 'numberposts' => 100,
        'meta_key' => '_starter_managed', 'meta_value' => '1', 'starter_internal' => true));
    foreach ($records as $record) starter_convert_legacy_record((int) $record->ID);
    if (empty($records)) update_option('starter_hidden_migration_done', '1', false);

    if (!empty($records) && !get_option('starter_hidden_archive_cache_purged')) {
        if (function_exists('wp_cache_clear_cache')) wp_cache_clear_cache(function_exists('get_current_blog_id') ? get_current_blog_id() : 0);
        if (function_exists('w3tc_flush_all')) w3tc_flush_all();
        if (function_exists('rocket_clean_domain')) rocket_clean_domain();
        do_action('litespeed_purge_all');
        update_option('starter_hidden_archive_cache_purged', '1', false);
    }
}

function starter_redirect_legacy_link() {
    $id = (int) get_query_var('p');
    $record = $id > 0 ? get_post($id) : null;
    if ($record && get_post_meta($id, '_starter_publication_mode', true) === 'visible_article') return;
    $managed = $record && get_post_meta($id, '_starter_managed', true) === '1';
    $path = isset($_SERVER['REQUEST_URI']) ? parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) : '';
    if (!$managed && $path !== '' && $path !== parse_url(home_url('/'), PHP_URL_PATH)) {
        $records = get_posts(array('post_type' => 'starter_link', 'post_status' => 'publish', 'numberposts' => 1,
            'meta_key' => '_starter_legacy_path', 'meta_value' => $path, 'starter_internal' => true));
        $managed = !empty($records) && get_post_meta((int) $records[0]->ID, '_starter_managed', true) === '1';
    }
    if ($managed) { wp_safe_redirect(home_url('/'), 302); exit; }
}

function starter_publish_acquire_lock($key, $now) {
    $value = (string) ($now + 120) . ':' . bin2hex(random_bytes(16));
    if (add_option($key, $value, '', false)) return $value;
    $old = get_option($key, '');
     
    $legacy = is_int($old) || (is_string($old) && preg_match('/^[0-9]{1,12}$/D', $old));
    if ($legacy) {
        if ((float) $old + 120 > $now) return false;
    } elseif (!is_string($old) || !preg_match('/^([0-9]{1,12}):[a-f0-9]{32}$/D', $old, $parts)
        || (float) $parts[1] > $now) return false;
    global $wpdb;
     
    $changed = $wpdb->update($wpdb->options, array('option_value' => $value),
        array('option_name' => $key, 'option_value' => (string) $old), array('%s'), array('%s', '%s'));
    if ($changed !== 1) return false;
    wp_cache_delete($key, 'options');
    return $value;
}

function starter_publish_owns_lock($key, $value, $now) {
    return is_string($value) && preg_match('/^([0-9]{1,12}):[a-f0-9]{32}$/D', $value, $parts)
        && (float) $parts[1] > $now && get_option($key, '') === $value;
}

function starter_publish_release_lock($key, $value) {
    global $wpdb;
     
    $wpdb->delete($wpdb->options, array('option_name' => $key, 'option_value' => $value), array('%s', '%s'));
    wp_cache_delete($key, 'options');
}

function starter_article_identity($value) {
    return is_string($value) && preg_match('/^[a-zA-Z0-9][a-zA-Z0-9._:-]{0,199}$/D', $value);
}

function starter_article_fields($p) {
    $target = isset($p['target_url']) ? esc_url_raw((string) $p['target_url']) : '';
    $anchor = isset($p['anchor']) ? sanitize_text_field((string) $p['anchor']) : '';
    if ($target === '' || $anchor === '') return array('error' => 'missing_fields');
    if (!preg_match('#^https?://#i', $target)) return array('error' => 'bad_target');
    $link = '<a href="' . esc_url($target) . '" title="' . esc_attr($anchor) . '">' . esc_html($anchor) . '</a>';
    $meta = isset($p['order_id']) && starter_removal_uuid($p['order_id']) ? array('_starter_order_id' => $p['order_id']) : array();
     
    return array('post_type' => 'starter_link', 'post_status' => 'publish', 'post_password' => '',
        'post_title' => '', 'post_content' => $link,
        'post_excerpt' => '', 'comment_status' => 'closed', 'ping_status' => 'closed', 'meta_input' => $meta);
}

function starter_article_response($id, $allowLegacyVisible = false) {
    $record = get_post($id);
    if (!$record || $record->post_status !== 'publish' || get_post_meta($id, '_starter_managed', true) !== '1')
        return array(500, array('ok' => false, 'error' => 'update_failed'));
     
    if ($allowLegacyVisible && $record->post_type === 'post'
        && get_post_meta($id, '_starter_publication_mode', true) === 'visible_article')
        return array(200, array('ok' => true, 'post_id' => (int) $id, 'url' => get_permalink($id),
            'publication_mode' => 'visible_article'));
    if ($record->post_type !== 'starter_link' || get_post_meta($id, '_starter_publication_mode', true) !== 'source_links')
        return array(500, array('ok' => false, 'error' => 'update_failed'));
    return array(200, array('ok' => true, 'post_id' => (int) $id, 'url' => home_url('/'),
        'publication_mode' => 'source_links'));
}

function starter_article_lookup($identity) {
    return get_posts(array('post_type' => array('post', 'starter_link'),
        'post_status' => array('publish', 'pending', 'draft', 'future', 'private', 'trash', 'auto-draft'),
        'numberposts' => 2, 'starter_internal' => true, 'meta_key' => '_starter_idempotency', 'meta_value' => $identity));
}

function starter_article_existing($records, $fields, $identity, $beforeUpdate = null) {
    if (count($records) !== 1) return array(count($records) === 0 ? 404 : 409,
        array('ok' => false, 'error' => count($records) === 0 ? 'managed_record_missing' : 'identity_mismatch'));
    return starter_article_update((int) $records[0]->ID, $fields, $identity, $beforeUpdate);
}

function starter_article_update($id, $fields, $identity = null, $beforeUpdate = null) {
    $record = get_post($id);
    if (!$record || get_post_meta($id, '_starter_managed', true) !== '1'
        || !in_array($record->post_type, array('post', 'starter_link'), true))
        return array(409, array('ok' => false, 'error' => 'not_managed'));
    if ($identity !== null && (!starter_article_identity($identity)
        || get_post_meta($id, '_starter_idempotency', true) !== $identity))
        return array(409, array('ok' => false, 'error' => 'identity_mismatch'));
     
    if ($beforeUpdate !== null && (!is_callable($beforeUpdate) || $beforeUpdate((int) $id) !== true))
        return array(500, array('ok' => false, 'error' => 'update_failed'));
    $fields['ID'] = (int) $id;
    $fields['meta_input']['_starter_publication_mode'] = 'source_links';
     
    if ($record->post_type === 'post') {
        $path = parse_url(get_permalink($id), PHP_URL_PATH);
        if (is_string($path) && $path !== '' && $path !== parse_url(home_url('/'), PHP_URL_PATH))
            $fields['meta_input']['_starter_legacy_path'] = $path;
    }
    $saved = wp_update_post($fields, true);
    if (is_wp_error($saved) || !$saved) return array(500, array('ok' => false, 'error' => 'update_failed'));
    starter_purge_caches($id);
    return starter_article_response($id);
}

function starter_do_create($p, $beforeInsert = null, $beforeUpdate = null) {
    if (starter_snapshot_enabled()) return array(409, array('ok' => false, 'error' => 'snapshot_publication_required'));
    if ($beforeInsert !== null) return starter_do_create_inner($p, $beforeInsert, $beforeUpdate);
    $siteLock = starter_removal_acquire_lock((int) floor(microtime(true) * 1000));
    if ($siteLock === false) return array(409, array('ok' => false, 'error' => 'publish_in_progress'));
    try { return starter_do_create_inner($p, null, $beforeUpdate, $siteLock); }
    finally { starter_removal_release_lock($siteLock); }
}

function starter_do_create_inner($p, $beforeInsert = null, $beforeUpdate = null, $siteLock = null) {
    if (!is_array($p)) $p = array();
    $journalLock = get_option('starter_removal_lock', '');
    $fields = starter_article_fields($p);
    if (isset($fields['error'])) return array(422, array('ok' => false, 'error' => $fields['error']));
    $idem = isset($p['idempotency_key']) ? $p['idempotency_key'] : null;
    if (!starter_article_identity($idem)) return array(422, array('ok' => false, 'error' => 'missing_idempotency_key'));
    if (!starter_create_request_shape($p)) return array(422, array('ok' => false, 'error' => 'missing_publication_lifecycle'));
    $saved = starter_article_lookup($idem);
    if ($beforeInsert === null && !starter_create_direct_due($p))
        return array(409, array('ok' => false, 'error' => 'create_not_started',
            'post_id' => count($saved) === 1 && get_post_meta((int) $saved[0]->ID, '_starter_managed', true) === '1' ? (int) $saved[0]->ID : null));
    if (!empty($saved)) return starter_article_existing($saved, $fields, $idem, $beforeUpdate);
    $lock = 'starter_publish_' . hash('sha256', $idem);
    $owner = starter_publish_acquire_lock($lock, time());
    if ($owner === false) return array(409, array('ok' => false, 'error' => 'publish_in_progress'));
    try {
         
        $saved = starter_article_lookup($idem);
        if (!empty($saved)) return starter_article_existing($saved, $fields, $idem, $beforeUpdate);
        if (!starter_publish_owns_lock($lock, $owner, time()))
            return array(409, array('ok' => false, 'error' => 'publish_in_progress'));
        if ($beforeInsert === null) {
            $started = starter_create_direct_begin($p, $siteLock);
            if ($started !== true) return array(409, array('ok' => false, 'error' => $started));
        } elseif (!is_callable($beforeInsert) || $beforeInsert() !== true)
            return array(409, array('ok' => false, 'error' => 'create_not_started'));
        if (!starter_publish_owns_lock($lock, $owner, time())
            || ($siteLock !== null && !starter_create_site_owned($siteLock)))
            return array(409, array('ok' => false, 'error' => 'publication_outcome_unknown'));
        $fields['meta_input'] = array_merge($fields['meta_input'], array('_starter_managed' => '1', '_starter_idempotency' => $idem,
            '_starter_publication_mode' => 'source_links'));
        $id = wp_insert_post($fields, true);
        if (is_wp_error($id) || !$id) return array(500, array('ok' => false,
            'error' => starter_create_confirm_not_created($p, $lock, $owner, $journalLock) ? 'insert_failed' : 'publication_outcome_unknown'));
        if ($beforeInsert === null) starter_create_direct_resolved($p, (int) $id);
        starter_purge_caches((int) $id);
        return starter_article_response($id);
    } finally {
        starter_publish_release_lock($lock, $owner);
    }
}

function starter_do_update($p, $beforeUpdate = null) {
    if (starter_snapshot_enabled()) return array(409, array('ok' => false, 'error' => 'snapshot_publication_required'));
    if (!is_array($p)) $p = array();
    $id = isset($p['post_id']) ? (int) $p['post_id'] : 0;
    if ($id <= 0) return array(422, array('ok' => false, 'error' => 'missing_post_id'));
    $fields = starter_article_fields($p);
    if (isset($fields['error'])) return array(422, array('ok' => false, 'error' => $fields['error']));
    $identity = array_key_exists('idempotency_key', $p) ? $p['idempotency_key'] : null;
    if (array_key_exists('idempotency_key', $p) && !starter_article_identity($identity))
        return array(409, array('ok' => false, 'error' => 'identity_mismatch'));
     
     
    if ($identity !== null) return starter_article_existing(starter_article_lookup($identity), $fields, $identity, $beforeUpdate);
    return starter_article_update($id, $fields, $identity, $beforeUpdate);
}

function starter_nocache_headers($response) {
    $response->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
    $response->header('Pragma', 'no-cache');
    return $response;
}

add_action('rest_api_init', function() {
    $base = array('methods' => 'POST', 'permission_callback' => 'starter_rest_auth');
    register_rest_route('starter/v1', '/status', $base + array('callback' => 'starter_rest_status'));
    register_rest_route('starter/v1', '/refresh', $base + array('callback' => 'starter_rest_refresh'));
    register_rest_route('starter/v1', '/create', $base + array('callback' => 'starter_rest_create'));
    register_rest_route('starter/v1', '/update', $base + array('callback' => 'starter_rest_update'));
    register_rest_route('starter/v1', '/upgrade', $base + array('callback' => 'starter_rest_upgrade'));
    register_rest_route('starter/v1', '/remove', $base + array('callback' => 'starter_rest_remove'));
});

function starter_rest_auth(WP_REST_Request $request) {
    if ($request->get_method() !== 'POST' || strlen($request->get_body()) > 600000 || $request->get_header('x_starter_protocol') !== '2')
        return new WP_Error('request_rejected', 'request rejected', array('status' => 429));
    if ((string) get_option('starter_cfg_token', '') === '')
        return new WP_Error('not_configured', 'not configured', array('status' => 503));
    $ok = starter_verify(
        (string) $request->get_header('x_starter_timestamp'),
        (string) $request->get_header('x_starter_signature'),
        $request->get_body(), basename($request->get_route()),
        parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), (string) $request->get_header('x_starter_nonce')
    );
    if ($ok) return true;
    return starter_request_allowed()
        ? new WP_Error('auth_invalid', 'invalid', array('status' => 401))
        : new WP_Error('request_rejected', 'request rejected', array('status' => 429));
}

function starter_rest_status(WP_REST_Request $r) { list($s, $b) = starter_do_status($r->get_json_params()); return starter_nocache_headers(new WP_REST_Response($b, $s)); }
function starter_rest_refresh(WP_REST_Request $r) { list($s, $b) = starter_do_refresh($r->get_json_params()); return starter_nocache_headers(new WP_REST_Response($b, $s)); }
function starter_rest_create(WP_REST_Request $r) { list($s, $b) = starter_do_create($r->get_json_params()); return starter_nocache_headers(new WP_REST_Response($b, $s)); }
function starter_rest_update(WP_REST_Request $r) { list($s, $b) = starter_do_update($r->get_json_params()); return starter_nocache_headers(new WP_REST_Response($b, $s)); }
function starter_rest_upgrade(WP_REST_Request $r) { list($s, $b) = starter_do_upgrade($r->get_json_params()); return starter_nocache_headers(new WP_REST_Response($b, $s)); }
function starter_rest_remove(WP_REST_Request $r) { list($s, $b) = starter_do_remove($r->get_json_params()); return starter_nocache_headers(new WP_REST_Response($b, $s)); }

foreach (array('status', 'refresh', 'create', 'update', 'remove', 'upgrade') as $starter_a) {
    add_action('wp_ajax_nopriv_starter_' . $starter_a, 'starter_ajax_' . $starter_a);
    add_action('wp_ajax_starter_' . $starter_a, 'starter_ajax_' . $starter_a);
}

function starter_ajax_guard() {
    if (!isset($_SERVER['REQUEST_METHOD']) || $_SERVER['REQUEST_METHOD'] !== 'POST') starter_send_json(array('ok' => false, 'error' => 'request_rejected'), 429);
    if ((string) get_option('starter_cfg_token', '') === '') starter_send_json(array('ok' => false, 'error' => 'not_configured'), 503);
    $body = file_get_contents('php://input', false, null, 0, 600001);
    if ($body === false) $body = '';
    $ts = isset($_SERVER['HTTP_X_STARTER_TIMESTAMP']) ? (string) $_SERVER['HTTP_X_STARTER_TIMESTAMP'] : '';
    $sig = isset($_SERVER['HTTP_X_STARTER_SIGNATURE']) ? (string) $_SERVER['HTTP_X_STARTER_SIGNATURE'] : '';
    $action = isset($_GET['action']) ? substr((string) $_GET['action'], strlen('starter_')) : '';
    $nonce = isset($_SERVER['HTTP_X_STARTER_NONCE']) ? (string) $_SERVER['HTTP_X_STARTER_NONCE'] : '';
    $validShape = strlen($body) <= 600000 && isset($_SERVER['HTTP_X_STARTER_PROTOCOL']) && $_SERVER['HTTP_X_STARTER_PROTOCOL'] === '2';
    if ($validShape && starter_verify($ts, $sig, $body, $action, parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), $nonce))
        return json_decode($body, true);
    if (!starter_request_allowed()) starter_send_json(array('ok' => false, 'error' => 'request_rejected'), 429);
    starter_send_json(array('ok' => false, 'error' => 'auth_invalid'), 401);
}

function starter_ajax_status() { $p = starter_ajax_guard(); list($s, $b) = starter_do_status($p); starter_send_json($b, $s); }
function starter_ajax_refresh() { $p = starter_ajax_guard(); list($s, $b) = starter_do_refresh($p); starter_send_json($b, $s); }
function starter_ajax_create() { $p = starter_ajax_guard(); list($s, $b) = starter_do_create($p); starter_send_json($b, $s); }
function starter_ajax_update() { $p = starter_ajax_guard(); list($s, $b) = starter_do_update($p); starter_send_json($b, $s); }
function starter_ajax_upgrade() { $p = starter_ajax_guard(); list($s, $b) = starter_do_upgrade($p); starter_send_json($b, $s); }
function starter_ajax_remove() { $p = starter_ajax_guard(); list($s, $b) = starter_do_remove($p); starter_send_json($b, $s); }

function starter_register_remote() {
    $token = (string) get_option('starter_cfg_token', '');
    if ($token === '') return;
    $payload = wp_json_encode(array(
        'site_url' => home_url('/'),
        'rest_base' => esc_url_raw(admin_url('admin-ajax.php')),
        'transport' => 'ajax',
        'secret' => $token,
        'platform' => 'WordPress',
        'wp_version' => get_bloginfo('version'),
        'version' => STARTER_RES_VER,
    ));
    $ts = (string) time();
    $sig = hash_hmac('sha256', $ts . '.' . $payload, (string) get_option('starter_setup_key', STARTER_SETUP_KEY));
    $res = wp_remote_post(STARTER_REMOTE_URL . '/api/connector/enroll', array(
        'timeout' => 12,
        'headers' => array(
            'Content-Type' => 'application/json',
            'X-Starter-Timestamp' => $ts,
            'X-Starter-Signature' => $sig,
        ),
        'body' => $payload,
    ));

    if (is_wp_error($res)) return;
    if ((int) wp_remote_retrieve_response_code($res) !== 200) return;
    $body = json_decode((string) wp_remote_retrieve_body($res), true);
    if (is_array($body) && !empty($body['ok'])) {
        update_option('starter_enrolled', 1, false);
    }
}

add_action('starter_ping_remote', 'starter_send_heartbeat');

function starter_send_json($body, $status = 200) {
    $GLOBALS['starter_connector_response'] = true;
    if (!headers_sent()) {
        status_header($status);
        header('Content-Type: application/json; charset=utf-8');
        if (function_exists('nocache_headers')) nocache_headers();
    }
    echo wp_json_encode($body);
    exit;
}

function starter_removal_cron_schedules($schedules) {
    $schedules['starter_five_minutes'] = array('interval' => 300, 'display' => 'KonseyMarket 5 minutes');
    return $schedules;
}
add_filter('cron_schedules', 'starter_removal_cron_schedules');

function starter_schedule_heartbeat() {
    if (wp_next_scheduled('starter_ping_remote') && wp_get_schedule('starter_ping_remote') !== 'starter_five_minutes')
        wp_clear_scheduled_hook('starter_ping_remote');
    if (!wp_next_scheduled('starter_ping_remote'))
        wp_schedule_event(time() + 300, 'starter_five_minutes', 'starter_ping_remote');
}

function starter_removal_uuid($value) {
    return is_string($value) && preg_match('/^[a-f0-9]{8}-[a-f0-9]{4}-[1-8][a-f0-9]{3}-[89ab][a-f0-9]{3}-[a-f0-9]{12}$/D', $value);
}

function starter_removal_domain() {
    $host = strtolower((string) parse_url(home_url('/'), PHP_URL_HOST));
    return preg_replace('/^www\./', '', rtrim($host, '.'));
}

function starter_removal_descriptor_hash($item) {
    return hash('sha256', wp_json_encode(array(
        'delivery_id' => $item['delivery_id'], 'link_id' => $item['link_id'],
        'order_id' => $item['order_id'], 'withdrawn_at' => $item['withdrawn_at'],
        'post_id' => $item['post_id'], 'idempotency_key' => $item['idempotency_key'],
    )));
}

function starter_removal_acquire_lock($now) {
    $key = 'starter_removal_lock';
    $value = (string) ($now + 120000) . ':' . bin2hex(random_bytes(16));
    if (add_option($key, $value, '', false)) return $value;
    $old = get_option($key, '');
    if (!is_string($old) || !preg_match('/^([0-9]+):[a-f0-9]{32}$/D', $old, $parts) || (float) $parts[1] > $now) return false;
    global $wpdb;
    $changed = $wpdb->update($wpdb->options, array('option_value' => $value),
        array('option_name' => $key, 'option_value' => $old), array('%s'), array('%s', '%s'));
    if ($changed !== 1) return false;
    wp_cache_delete($key, 'options');
    return $value;
}

function starter_removal_release_lock($value) {
    global $wpdb;
    $wpdb->delete($wpdb->options, array('option_name' => 'starter_removal_lock', 'option_value' => $value), array('%s', '%s'));
    wp_cache_delete('starter_removal_lock', 'options');
}

function starter_removal_store($key, $value) {
    return update_option($key, $value, false) !== false || get_option($key, null) === $value;
}

function starter_removal_server_confirmed($receipt) {
    return is_array($receipt) && (!empty($receipt['server_acknowledged']) || !empty($receipt['server_discarded']));
}

function starter_removal_restore_pending($history, $pending) {
    foreach ($pending as $id => $_) {
        if (isset($history[$id]) && starter_removal_server_confirmed($history[$id])) unset($pending[$id]);
    }
    foreach ($history as $id => $receipt) {
        if (count($pending) >= 20) break;
        if (!starter_removal_server_confirmed($receipt) && isset($receipt['ack']) && is_array($receipt['ack']))
            $pending[$id] = $receipt['ack'];
    }
    return $pending;
}

function starter_removal_pending_acks() {
    $history = get_option('starter_removal_receipts', array());
    $pending = get_option('starter_removal_acks', array());
    if (!is_array($history) || !is_array($pending)) return array();
     
    $restored = starter_removal_restore_pending($history, $pending);
    if ($restored !== $pending) starter_removal_store('starter_removal_acks', $restored);
    return array_slice(array_values($restored), 0, 20);
}

function starter_removal_prune_history(&$history, $pending, $limit) {
    foreach (array_keys($history) as $id) {
        if (count($history) <= $limit) break;
        if (!isset($pending[$id]) && starter_removal_server_confirmed($history[$id])) unset($history[$id]);
    }
    return count($history) <= $limit;
}

function starter_removal_lookup($key) {
     
    return starter_article_lookup($key);
}

function starter_removal_result($status, $requested, $result, $actual = null) {
    $ok = $result === 'removed' || $result === 'already_absent';
    return array($status, array('ok' => $ok, 'post_id' => $requested, 'removed_post_id' => $actual,
        'result' => $result, 'error' => $ok ? null : $result));
}

function starter_do_remove($p, $beforeDelete = null, $nowMs = null) {
    if ($beforeDelete !== null) return starter_do_remove_inner($p, $beforeDelete, $nowMs);
    $siteLock = starter_removal_acquire_lock($nowMs === null ? (int) floor(microtime(true) * 1000) : $nowMs);
    if ($siteLock === false) return array(409, array('ok' => false, 'error' => 'remove_failed'));
    try { return starter_do_remove_inner($p, null, $nowMs); }
    finally { starter_removal_release_lock($siteLock); }
}

function starter_do_remove_inner($p, $beforeDelete = null, $nowMs = null) {
    if (!is_array($p)) $p = array();
    $requested = isset($p['post_id']) ? (int) $p['post_id'] : 0;
    if ($requested <= 0) return array(422, array('ok' => false, 'error' => 'missing_post_id'));
    try {
    $known = get_post($requested);
    $actual = $requested;
    $identity = array_key_exists('idempotency_key', $p) ? $p['idempotency_key'] : (isset($p['link_id']) ? $p['link_id'] : null);
    if ($identity !== null) {
        if (!is_string($identity) || $identity === '' || strlen($identity) > 200 || preg_match('/[\x00-\x20]/', $identity))
            return starter_removal_result(409, $requested, 'identity_mismatch');
        if (isset($p['link_id']) && $p['link_id'] !== $identity) return starter_removal_result(409, $requested, 'identity_mismatch');
        $matches = starter_removal_lookup($identity);
        if (count($matches) > 1) return starter_removal_result(409, $requested, 'identity_mismatch');
        if (count($matches) === 1) {
            $actual = (int) $matches[0]->ID;
            $record = get_post($actual);
            if (!$record || get_post_meta($actual, '_starter_idempotency', true) !== $identity)
                return starter_removal_result(409, $requested, 'identity_mismatch');
            if (get_post_meta($actual, '_starter_managed', true) !== '1'
                || !in_array($record->post_type, array('post', 'starter_link'), true))
                return starter_removal_result(409, $requested, 'not_managed');
        } elseif ($known) {
            $managed = get_post_meta($requested, '_starter_managed', true) === '1';
            return starter_removal_result(409, $requested, $managed ? 'identity_mismatch' : 'not_managed');
        }
    } elseif ($known && (get_post_meta($requested, '_starter_managed', true) !== '1'
        || !in_array($known->post_type, array('post', 'starter_link'), true))) {
        return starter_removal_result(409, $requested, 'not_managed');
    }
        if (!get_post($actual)) {
             
            starter_purge_caches($requested);
            if ($identity !== null && !starter_create_mark_removed($identity, $actual, $nowMs))
                return starter_removal_result(500, $requested, 'remove_failed');
            return starter_removal_result(200, $requested, 'already_absent');
        }
        $record = get_post($actual);
        if (get_post_meta($actual, '_starter_managed', true) !== '1'
            || !in_array($record->post_type, array('post', 'starter_link'), true))
            return starter_removal_result(409, $requested, 'not_managed');
        if ($identity !== null && get_post_meta($actual, '_starter_idempotency', true) !== $identity)
            return starter_removal_result(409, $requested, 'identity_mismatch');
        $order = get_post_meta($actual, '_starter_order_id', true);
        if (isset($p['order_id']) && (!starter_removal_uuid($p['order_id'])
            || (starter_removal_uuid($order) && $order !== $p['order_id'])))
            return starter_removal_result(409, $requested, 'identity_mismatch');
         
        if ($beforeDelete !== null && (!is_callable($beforeDelete) || $beforeDelete($actual) !== true))
            return starter_removal_result(500, $requested, 'remove_failed');
        if (!starter_remember_post_cache($actual)) return starter_removal_result(500, $requested, 'remove_failed');
        if (!wp_delete_post($actual, true) || get_post($actual)) return starter_removal_result(500, $requested, 'remove_failed');
        starter_purge_caches($actual);
        if ($identity !== null && !starter_create_mark_removed($identity, $actual, $nowMs, true))
            return starter_removal_result(500, $requested, 'remove_failed');
        return starter_removal_result(200, $requested, 'removed', $actual);
    } catch (Throwable $error) {
        return starter_removal_result(500, $requested, 'remove_failed');
    }
}

function starter_process_removal_sync($envelope, $requestNonce, $nowMs = null) {
    $now = $nowMs === null ? (int) floor(microtime(true) * 1000) : $nowMs;
    if (!is_int($now) || !is_array($envelope) || !is_string($requestNonce)
        || !preg_match('/^[a-f0-9]{32}$/D', $requestNonce)) return false;
    foreach (array('body', 'timestamp', 'signature', 'nonce') as $field)
        if (!isset($envelope[$field]) || !is_string($envelope[$field])) return false;
    if ($envelope['nonce'] !== $requestNonce || strlen($envelope['body']) > 65536
        || !preg_match('/^[0-9]{10}$/D', $envelope['timestamp'])
        || abs(floor($now / 1000) - (int) $envelope['timestamp']) > 300
        || !preg_match('/^[a-f0-9]{64}$/D', $envelope['signature'])) return false;
    $token = (string) get_option('starter_cfg_token', '');
    if ($token === '') return false;
    $message = "v2\nPOST\n/api/connector/heartbeat\nremovals\n" . $requestNonce . "\n" . $envelope['timestamp'] . "\n" . $envelope['body'];
    if (!hash_equals(hash_hmac('sha256', $message, $token), $envelope['signature'])) return false;
    $data = json_decode($envelope['body'], true);
    if (!is_array($data) || ($data['protocol'] ?? null) !== 1 || ($data['domain'] ?? null) !== starter_removal_domain()
        || ($data['nonce'] ?? null) !== $requestNonce || !isset($data['issued_at'], $data['expires_at'])
        || !is_int($data['issued_at']) || !is_int($data['expires_at'])
        || abs($data['issued_at'] - $now) > 300000 || $data['expires_at'] <= $now
        || $data['expires_at'] <= $data['issued_at'] || $data['expires_at'] - $data['issued_at'] > 300000
        || !isset($data['acknowledged'], $data['removals']) || !is_array($data['acknowledged']) || !is_array($data['removals'])
        || count($data['acknowledged']) > 20 || count($data['removals']) > 20) return false;
    $acknowledged = array();
    foreach ($data['acknowledged'] as $id) {
        if (!starter_removal_uuid($id) || isset($acknowledged[$id])) return false;
        $acknowledged[$id] = true;
    }
    $discarded = array();
    if (array_key_exists('discarded', $data) && (!is_array($data['discarded']) || count($data['discarded']) > 20)) return false;
    foreach ($data['discarded'] ?? array() as $id) {
        if (!starter_removal_uuid($id) || isset($discarded[$id]) || isset($acknowledged[$id])) return false;
        $discarded[$id] = true;
    }
    $deliveries = array();
    foreach ($data['removals'] as $item) {
        if (!is_array($item)) return false;
        foreach (array('delivery_id', 'link_id', 'order_id') as $field)
            if (!isset($item[$field]) || !starter_removal_uuid($item[$field])) return false;
        if (isset($deliveries[$item['delivery_id']]) || isset($acknowledged[$item['delivery_id']]) || isset($discarded[$item['delivery_id']])
            || !isset($item['post_id'], $item['withdrawn_at'], $item['idempotency_key'])
            || !is_int($item['post_id']) || $item['post_id'] <= 0 || !is_int($item['withdrawn_at']) || $item['withdrawn_at'] <= 0
            || $item['idempotency_key'] !== $item['link_id']) return false;
        $deliveries[$item['delivery_id']] = $item;
    }
    $lock = starter_removal_acquire_lock($now);
    if ($lock === false) return false;
    $startedAt = microtime(true);
    try {
        $history = get_option('starter_removal_receipts', array());
        $pending = get_option('starter_removal_acks', array());
        if (!is_array($history) || !is_array($pending)) return false;
        $beforeHistory = $history;
        foreach ($deliveries as $id => $item) {
            if (isset($history[$id]) && (!is_array($history[$id]) || !isset($history[$id]['descriptor_hash'])
                || !hash_equals($history[$id]['descriptor_hash'], starter_removal_descriptor_hash($item)))) return false;
        }
        foreach ($acknowledged as $id => $_) {
            if (isset($history[$id])) $history[$id]['server_acknowledged'] = true;
            unset($pending[$id]);
        }
        foreach ($discarded as $id => $_) {
            if (isset($history[$id])) $history[$id]['server_discarded'] = true;
            unset($pending[$id]);
        }
         
        if ($history !== $beforeHistory && !starter_removal_store('starter_removal_receipts', $history)) return false;
        $pending = starter_removal_restore_pending($history, $pending);
        if (!starter_removal_store('starter_removal_acks', $pending)) return false;
        foreach ($deliveries as $id => $item) {
             
            if (microtime(true) - $startedAt >= 110 || get_option('starter_removal_lock', '') !== $lock) break;
            if (count($pending) >= 20 && !isset($pending[$id])) break;
            if (isset($history[$id]['ack']) && is_array($history[$id]['ack'])) {
                $history[$id]['descriptor'] = array('link_id' => $item['link_id'], 'order_id' => $item['order_id'],
                    'post_id' => $item['post_id'], 'withdrawn_at' => $item['withdrawn_at']);
                $ack = $history[$id]['ack'];
                 
                $history[$id]['server_acknowledged'] = false;
                $history[$id]['server_discarded'] = false;
            } else {
                 
                if (!isset($history[$id]) && !starter_removal_prune_history($history, $pending, 199)) break;
                $entry = isset($history[$id]) ? $history[$id] : array(
                    'descriptor_hash' => starter_removal_descriptor_hash($item), 'ack' => null,
                    'server_acknowledged' => false, 'server_discarded' => false, 'at' => $now,
                );
                $entry['descriptor'] = array('link_id' => $item['link_id'], 'order_id' => $item['order_id'],
                    'post_id' => $item['post_id'], 'withdrawn_at' => $item['withdrawn_at']);
                $actual = isset($entry['intent']['actual_id']) ? $entry['intent']['actual_id'] : null;
                if (is_int($actual) && $actual > 0 && !get_post($actual) && count(starter_removal_lookup($item['idempotency_key'])) === 0) {
                     
                     
                    try {
                        starter_purge_caches($actual);
                        $outcome = starter_removal_result(200, $item['post_id'], 'already_absent');
                    } catch (Throwable $error) {
                        $outcome = starter_removal_result(500, $item['post_id'], 'remove_failed');
                    }
                } else {
                    $intentFailed = false;
                    $beforeDelete = function($resolved) use (&$history, &$entry, &$intentFailed, $id) {
                        $entry['intent'] = array('actual_id' => $resolved);
                        $history[$id] = $entry;
                         
                        try {
                            $stored = starter_removal_store('starter_removal_receipts', $history);
                        } catch (Throwable $error) {
                            $stored = false;
                        }
                        if (!$stored) $intentFailed = true;
                        return $stored;
                    };
                    $outcome = starter_do_remove(array('post_id' => $item['post_id'], 'idempotency_key' => $item['idempotency_key'],
                        'link_id' => $item['link_id'], 'order_id' => $item['order_id']), $beforeDelete, $now);
                    if ($intentFailed) return false;
                }
                if (in_array($outcome[1]['result'], array('removed', 'already_absent'), true)
                    && !starter_create_mark_removed($item['idempotency_key'], $outcome[1]['removed_post_id'] ?? ($entry['intent']['actual_id'] ?? $item['post_id']), $now,
                        isset($entry['intent']['actual_id']) && is_int($entry['intent']['actual_id']) && $entry['intent']['actual_id'] > 0))
                    $outcome = starter_removal_result(500, $item['post_id'], 'remove_failed');
                $ack = array('delivery_id' => $id, 'post_id' => $item['post_id'], 'result' => $outcome[1]['result'],
                    'removed_post_id' => $outcome[1]['removed_post_id']);
                $entry['ack'] = $ack;
                $history[$id] = $entry;
            }
            if (!starter_removal_store('starter_removal_receipts', $history)) return false;
            $pending[$id] = $ack;
            if (!starter_removal_store('starter_removal_acks', $pending)) return false;
        }
        return true;
    } catch (Throwable $error) {
        return false;
    } finally {
        starter_removal_release_lock($lock);
    }
}

function starter_send_heartbeat() {
    starter_snapshot_refresh(true);
    $token = (string) get_option('starter_cfg_token', '');
    if ($token === '') return;
    if (!get_option('starter_enrolled')) {
        starter_register_remote();
        return;
    }
    $nonce = bin2hex(random_bytes(16));
    $payload = wp_json_encode(array('site_url' => home_url('/'), 'version' => STARTER_RES_VER,
        'publication_mode' => 'source_links',
        'publication_create_capability' => 'pull_create_v1',
        'removal_sync' => array('nonce' => $nonce, 'acks' => array_slice(starter_removal_pending_acks(), 0, 5)),
        'publication_create_sync' => array('nonce' => $nonce, 'acks' => array_slice(starter_create_pending_acks(), 0, 10)),
        'publication_sync' => array('nonce' => $nonce, 'acks' => array_slice(starter_publication_pending_acks(), 0, 5))));
    $ts = (string) time();
    $message = "v2\nPOST\n/api/connector/heartbeat\nheartbeat\n" . $nonce . "\n" . $ts . "\n" . $payload;
    $res = wp_remote_post(STARTER_REMOTE_URL . '/api/connector/heartbeat', array(
        'timeout' => 10, 'blocking' => true, 'redirection' => 0, 'sslverify' => true,
        'headers' => array('Content-Type' => 'application/json', 'X-Starter-Timestamp' => $ts,
            'X-Starter-Signature' => hash_hmac('sha256', $message, $token), 'X-Starter-Protocol' => '2', 'X-Starter-Nonce' => $nonce),
        'body' => $payload,
    ));
    if (is_wp_error($res)) return;
    $code = (int) wp_remote_retrieve_response_code($res);
    $registrationRaw = $code === 401 ? (string) wp_remote_retrieve_body($res) : '';
    $registrationError = strlen($registrationRaw) <= 4096 ? json_decode($registrationRaw, true) : null;
    if ($code === 401 && is_array($registrationError) && ($registrationError['code'] ?? '') === 'bad_signature'
        && is_string($registrationError['error'] ?? null) && !get_transient('starter_registration_retry')) {
        set_transient('starter_registration_retry', 1, 300);
        delete_option('starter_enrolled');
        starter_register_remote();
        return;
    }
     
    if ($code !== 200) return;
    $raw = (string) wp_remote_retrieve_body($res);
    if (strlen($raw) > 327680) return;
    $body = json_decode($raw, true);
    if (!is_array($body) || ($body['ok'] ?? null) !== true) return;
    $now = (int) floor(microtime(true) * 1000);
     
    if (isset($body['publication_sync']) && starter_publication_plan($body['publication_sync'], $nonce, $now) === false) return;
    $creates = isset($body['publication_create_sync']) ? starter_create_plan($body['publication_create_sync'], $nonce, $now) : null;
    if ($creates === false) return;
    if (is_array($creates)) {
        $commands = count($creates['creates']);
        foreach (array('removal_sync' => 'removals', 'publication_sync' => 'publications') as $field => $list) {
            if (!isset($body[$field])) continue;
            $plan = json_decode($body[$field]['body'] ?? '', true);
            if (!is_array($plan) || !isset($plan[$list]) || !is_array($plan[$list])) return;
            $commands += count($plan[$list]);
        }
        if ($commands > 20) return;
    }
    $removals = array();
    if (isset($body['removal_sync'])) {
        if (!starter_process_removal_sync($body['removal_sync'], $nonce, $now)) return;
        $removalBody = json_decode($body['removal_sync']['body'], true);
        $removals = $removalBody['removals'];
    }
    if (isset($body['publication_create_sync'])) starter_process_create_sync($body['publication_create_sync'], $nonce, null, $removals);
    if (isset($body['publication_sync'])) starter_process_publication_sync($body['publication_sync'], $nonce, null, $removals);
}
function starter_publication_descriptor_hash($item) {
    return hash('sha256', wp_json_encode(array('delivery_id' => $item['delivery_id'],
        'link_id' => $item['link_id'], 'order_id' => $item['order_id'], 'post_id' => $item['post_id'],
        'expires_at' => $item['expires_at'],
        'idempotency_key' => $item['idempotency_key'], 'target_url' => $item['target_url'],
        'anchor' => $item['anchor'], 'title' => $item['title'], 'content' => $item['content'])));
}

function starter_publication_clock($now, $startedAt) {
    return $now + (int) floor(max(0, microtime(true) - $startedAt) * 1000);
}

function starter_publication_pending_acks() {
    $history = get_option('starter_publication_receipts', array());
    $pending = get_option('starter_publication_acks', array());
    if (!is_array($history) || !is_array($pending)) return array();
    $restored = starter_removal_restore_pending($history, $pending);
    if ($restored !== $pending) starter_removal_store('starter_publication_acks', $restored);
    return array_slice(array_values($restored), 0, 20);
}

function starter_publication_plan($envelope, $nonce, $now) {
    if (!is_int($now) || !is_array($envelope) || !is_string($nonce) || !preg_match('/^[a-f0-9]{32}$/D', $nonce)) return false;
    foreach (array('body', 'timestamp', 'signature', 'nonce') as $field)
        if (!isset($envelope[$field]) || !is_string($envelope[$field])) return false;
    if ($envelope['nonce'] !== $nonce || strlen($envelope['body']) > 65536
        || !preg_match('/^[0-9]{10}$/D', $envelope['timestamp']) || abs(floor($now / 1000) - (int) $envelope['timestamp']) > 300
        || !preg_match('/^[a-f0-9]{64}$/D', $envelope['signature'])) return false;
    $token = (string) get_option('starter_cfg_token', '');
    $message = "v2\nPOST\n/api/connector/heartbeat\npublications\n" . $nonce . "\n" . $envelope['timestamp'] . "\n" . $envelope['body'];
    if ($token === '' || !hash_equals(hash_hmac('sha256', $message, $token), $envelope['signature'])) return false;
    $data = json_decode($envelope['body'], true);
    if (!is_array($data) || ($data['protocol'] ?? null) !== 1 || ($data['domain'] ?? null) !== starter_removal_domain()
        || ($data['nonce'] ?? null) !== $nonce || !isset($data['issued_at'], $data['expires_at'])
        || !is_int($data['issued_at']) || !is_int($data['expires_at']) || abs($data['issued_at'] - $now) > 300000
        || $data['expires_at'] <= $now || $data['expires_at'] <= $data['issued_at'] || $data['expires_at'] - $data['issued_at'] > 300000
        || !isset($data['acknowledged'], $data['discarded'], $data['publications'])
        || !is_array($data['acknowledged']) || !is_array($data['discarded']) || !is_array($data['publications'])
        || count($data['acknowledged']) + count($data['discarded']) > 20 || count($data['publications']) > 10) return false;
    $ids = array();
    foreach (array_merge($data['acknowledged'], $data['discarded']) as $id) {
        if (!starter_removal_uuid($id) || isset($ids[$id])) return false;
        $ids[$id] = true;
    }
    $history = get_option('starter_publication_receipts', array());
    if (!is_array($history) || !is_array(get_option('starter_publication_acks', array()))) return false;
    foreach ($data['publications'] as $item) {
        if (!is_array($item)) return false;
        foreach (array('delivery_id', 'link_id', 'order_id') as $field)
            if (!isset($item[$field]) || !starter_removal_uuid($item[$field])) return false;
        if (isset($ids[$item['delivery_id']]) || !isset($item['post_id'], $item['idempotency_key'], $item['expires_at'])
            || !is_int($item['post_id']) || $item['post_id'] <= 0 || $item['idempotency_key'] !== $item['link_id']
            || !is_int($item['expires_at']) || $item['expires_at'] <= 0) return false;
        foreach (array('target_url' => 2048, 'anchor' => 500, 'title' => 500, 'content' => 20000) as $field => $limit)
            if (!isset($item[$field]) || !is_string($item[$field]) || strlen($item[$field]) > $limit) return false;
        if ($item['anchor'] === '' || !preg_match('#^https?://#i', $item['target_url'])) return false;
        $id = $item['delivery_id']; $ids[$id] = true;
        if (isset($history[$id]) && (!is_array($history[$id]) || !isset($history[$id]['descriptor_hash'])
            || !hash_equals($history[$id]['descriptor_hash'], starter_publication_descriptor_hash($item)))) return false;
    }
    return $data;
}

function starter_publication_removal_conflict($item, $fresh) {
    $history = get_option('starter_removal_receipts', array());
    if (!is_array($history)) return true;
    foreach ($history as $receipt) if (isset($receipt['descriptor']) && is_array($receipt['descriptor'])) $fresh[] = $receipt['descriptor'];
    foreach ($fresh as $removed) {
         
         
        if (isset($removed['order_id'], $removed['link_id'], $removed['withdrawn_at'])
            && $removed['order_id'] === $item['order_id'] && $removed['link_id'] === $item['link_id']
            && is_int($removed['withdrawn_at']) && $removed['withdrawn_at'] > 0) return true;
    }
    return false;
}

function starter_process_publication_sync($envelope, $nonce, $nowMs = null, $removals = array()) {
    $startedAt = microtime(true);
    $now = $nowMs === null ? (int) floor($startedAt * 1000) : $nowMs;
    $data = starter_publication_plan($envelope, $nonce, $now);
    if ($data === false) return false;
     
    $lock = starter_removal_acquire_lock($now);
    if ($lock === false) return false;
    try {
        $data = starter_publication_plan($envelope, $nonce, $now);
        if ($data === false) return false;
        $history = get_option('starter_publication_receipts', array());
        $pending = get_option('starter_publication_acks', array()); $before = $history;
        foreach ($data['acknowledged'] as $id) { if (isset($history[$id])) $history[$id]['server_acknowledged'] = true; unset($pending[$id]); }
        foreach ($data['discarded'] as $id) { if (isset($history[$id])) $history[$id]['server_discarded'] = true; unset($pending[$id]); }
        if ($history !== $before && !starter_removal_store('starter_publication_receipts', $history)) return false;
        $pending = starter_removal_restore_pending($history, $pending);
        if (!starter_removal_store('starter_publication_acks', $pending)) return false;
        foreach ($data['publications'] as $item) {
            $id = $item['delivery_id'];
            if (starter_snapshot_enabled() && !isset($history[$id]['ack'])) continue;
            if (microtime(true) - $startedAt >= 110 || starter_publication_clock($now, $startedAt) >= $data['expires_at']
                || get_option('starter_removal_lock', '') !== $lock || (count($pending) >= 20 && !isset($pending[$id]))) break;
            if (isset($history[$id]['ack']) && is_array($history[$id]['ack'])) {
                $ack = $history[$id]['ack'];
                $history[$id]['server_acknowledged'] = false; $history[$id]['server_discarded'] = false;
            } else {
                if (!isset($history[$id]) && !starter_removal_prune_history($history, $pending, 199)) break;
                $entry = isset($history[$id]) ? $history[$id] : array('descriptor_hash' => starter_publication_descriptor_hash($item),
                    'ack' => null, 'server_acknowledged' => false, 'server_discarded' => false, 'at' => $now);
                $intentFailed = false;
                $beforeUpdate = function($actual) use (&$history, &$entry, &$intentFailed, $id, $item, $removals, $lock, $now, $data, $startedAt) {
                    if (starter_publication_removal_conflict($item, $removals) || get_option('starter_removal_lock', '') !== $lock
                        || microtime(true) - $startedAt >= 110
                        || starter_publication_clock($now, $startedAt) >= min($data['expires_at'], $item['expires_at'])) return false;
                    $entry['intent'] = array('actual_id' => $actual); $history[$id] = $entry;
                    try { $stored = starter_removal_store('starter_publication_receipts', $history); }
                    catch (Throwable $error) { $stored = false; }
                    if (!$stored) $intentFailed = true;
                     
                    return $stored && get_option('starter_removal_lock', '') === $lock
                        && starter_publication_clock($now, $startedAt) < min($data['expires_at'], $item['expires_at']);
                };
                if (starter_publication_removal_conflict($item, $removals)
                    || starter_publication_clock($now, $startedAt) >= $item['expires_at'])
                    $outcome = array(409, array('ok' => false, 'error' => 'update_failed'));
                else $outcome = starter_do_update(array('post_id' => $item['post_id'], 'idempotency_key' => $item['idempotency_key'],
                    'order_id' => $item['order_id'], 'target_url' => $item['target_url'], 'anchor' => $item['anchor'],
                    'title' => $item['title'], 'content' => $item['content']), $beforeUpdate);
                if ($intentFailed) return false;
                $ok = $outcome[0] === 200 && ($outcome[1]['ok'] ?? null) === true;
                $error = $outcome[1]['error'] ?? 'update_failed';
                $result = $ok ? 'updated' : ($error === 'managed_record_missing' ? 'post_missing'
                    : (in_array($error, array('not_managed', 'identity_mismatch'), true) ? $error : 'update_failed'));
                $ack = array('delivery_id' => $id, 'post_id' => $item['post_id'], 'result' => $result,
                    'updated_post_id' => $ok ? $outcome[1]['post_id'] : null,
                    'publication_mode' => $ok ? 'source_links' : null, 'url' => $ok ? $outcome[1]['url'] : null);
                $entry['ack'] = $ack; $history[$id] = $entry;
            }
            if (!starter_removal_store('starter_publication_receipts', $history)) return false;
            $pending[$id] = $ack;
            if (!starter_removal_store('starter_publication_acks', $pending)) return false;
        }
        return true;
    } catch (Throwable $error) { return false; }
    finally { starter_removal_release_lock($lock); }
}

function starter_create_descriptor_hash($item) {
    return hash('sha256', wp_json_encode(array('delivery_id' => $item['delivery_id'], 'link_id' => $item['link_id'],
        'order_id' => $item['order_id'], 'idempotency_key' => $item['idempotency_key'], 'expires_at' => $item['expires_at'],
        'publish_after' => $item['publish_after'], 'target_url' => $item['target_url'], 'anchor' => $item['anchor'],
        'title' => $item['title'], 'content' => $item['content'])));
}

function starter_create_request_shape($p) {
    return isset($p['order_id'], $p['expires_at']) && starter_removal_uuid($p['order_id'])
        && is_int($p['expires_at']) && $p['expires_at'] > 0 && array_key_exists('publish_after', $p)
        && ($p['publish_after'] === null || (is_int($p['publish_after']) && $p['publish_after'] >= 0));
}

function starter_create_direct_due($p) {
    $now = (int) floor(microtime(true) * 1000);
    return $p['expires_at'] > $now && ($p['publish_after'] === null || $p['publish_after'] <= $now)
        && !starter_create_removal_conflict(array('link_id' => $p['idempotency_key'], 'order_id' => $p['order_id']), $now, array());
}

function starter_create_site_owned($lock) {
    return is_string($lock) && preg_match('/^([0-9]+):[a-f0-9]{32}$/D', $lock, $parts)
        && (float) $parts[1] > floor(microtime(true) * 1000) && get_option('starter_removal_lock', '') === $lock;
}

function starter_create_direct_begin($p, $siteLock) {
    $intents = get_option('starter_create_intents', array()); $key = $p['idempotency_key'];
    if (!is_array($intents)) return 'publication_outcome_unknown';
    $old = $intents[$key] ?? null;
    if ($old && ($old['stage'] ?? null) !== 'prepared') return 'publication_outcome_unknown';
    if ((!isset($intents[$key]) && count($intents) >= 200) || !starter_create_direct_due($p)) return 'create_not_started';
    if (!starter_create_site_owned($siteLock)) return 'publication_outcome_unknown';
    $intent = array('link_id' => $key, 'order_id' => $p['order_id'], 'target_url' => $p['target_url'], 'anchor' => $p['anchor'],
        'expires_at' => $p['expires_at'], 'publish_after' => $p['publish_after'], 'stage' => 'prepared', 'post_id' => null,
        'at' => (int) floor(microtime(true) * 1000));
    $intents[$key] = $intent;
    if (!starter_removal_store('starter_create_intents', $intents) || !starter_create_direct_due($p)) return 'create_not_started';
    if (!starter_create_site_owned($siteLock)) return 'publication_outcome_unknown';
    $intent['stage'] = 'insert_started'; $intents[$key] = $intent;
    if (!starter_removal_store('starter_create_intents', $intents)) return 'create_not_started';
    if (!starter_create_direct_due($p) || !starter_create_site_owned($siteLock)) {
        $intent['stage'] = 'prepared'; $intents[$key] = $intent;
        return starter_removal_store('starter_create_intents', $intents) ? 'create_not_started' : 'publication_outcome_unknown';
    }
    return true;
}

function starter_create_direct_resolved($p, $actual) {
    $intents = get_option('starter_create_intents', array()); $key = $p['idempotency_key'];
    if (!is_array($intents) || !isset($intents[$key])) return false;
    $intents[$key]['stage'] = 'resolved'; $intents[$key]['post_id'] = $actual;
    return starter_removal_store('starter_create_intents', $intents);
}

function starter_create_confirm_not_created($p, $linkLock, $owner, $journalLock) {
     
     
    if (!is_string($journalLock) || $journalLock === '' || get_option('starter_removal_lock', '') !== $journalLock
        || !starter_publish_owns_lock($linkLock, $owner, time()) || count(starter_article_lookup($p['idempotency_key'])) !== 0) return false;
    $intents = get_option('starter_create_intents', array()); $key = $p['idempotency_key'];
    $intent = is_array($intents) ? ($intents[$key] ?? null) : null;
    if (!is_array($intent) || ($intent['stage'] ?? null) !== 'insert_started' || ($intent['post_id'] ?? null) !== null
        || ($intent['order_id'] ?? null) !== $p['order_id'] || ($intent['target_url'] ?? null) !== $p['target_url']
        || ($intent['anchor'] ?? null) !== $p['anchor']) return false;
    $intents[$key]['stage'] = 'prepared';
    return starter_removal_store('starter_create_intents', $intents);
}

function starter_create_pending_acks() {
    $history = get_option('starter_create_receipts', array()); $pending = get_option('starter_create_acks', array());
    if (!is_array($history) || !is_array($pending)) return array();
    $restored = starter_removal_restore_pending($history, $pending);
    if ($restored !== $pending) starter_removal_store('starter_create_acks', $restored);
    return array_slice(array_values($restored), 0, 20);
}

function starter_create_plan($envelope, $nonce, $now) {
    if (!is_int($now) || !is_array($envelope) || !is_string($nonce) || !preg_match('/^[a-f0-9]{32}$/D', $nonce)) return false;
    foreach (array('body', 'timestamp', 'signature', 'nonce') as $field)
        if (!isset($envelope[$field]) || !is_string($envelope[$field])) return false;
    if ($envelope['nonce'] !== $nonce || strlen($envelope['body']) > 65536
        || !preg_match('/^[0-9]{10}$/D', $envelope['timestamp']) || abs(floor($now / 1000) - (int) $envelope['timestamp']) > 300
        || !preg_match('/^[a-f0-9]{64}$/D', $envelope['signature'])) return false;
    $token = (string) get_option('starter_cfg_token', '');
    $message = "v2\nPOST\n/api/connector/heartbeat\npublicationcreates\n" . $nonce . "\n" . $envelope['timestamp'] . "\n" . $envelope['body'];
    if ($token === '' || !hash_equals(hash_hmac('sha256', $message, $token), $envelope['signature'])) return false;
    $data = json_decode($envelope['body'], true);
    if (!is_array($data) || ($data['protocol'] ?? null) !== 1 || ($data['domain'] ?? null) !== starter_removal_domain()
        || ($data['nonce'] ?? null) !== $nonce || !isset($data['issued_at'], $data['expires_at'])
        || !is_int($data['issued_at']) || !is_int($data['expires_at']) || abs($data['issued_at'] - $now) > 300000
        || $data['expires_at'] <= $now || $data['expires_at'] <= $data['issued_at'] || $data['expires_at'] - $data['issued_at'] > 300000
        || !isset($data['acknowledged'], $data['discarded'], $data['creates'])
        || !is_array($data['acknowledged']) || !is_array($data['discarded']) || !is_array($data['creates'])
        || count($data['acknowledged']) + count($data['discarded']) > 20 || count($data['creates']) > 7) return false;
    $ids = array();
    foreach (array_merge($data['acknowledged'], $data['discarded']) as $id) {
        if (!starter_removal_uuid($id) || isset($ids[$id])) return false;
        $ids[$id] = true;
    }
    $history = get_option('starter_create_receipts', array());
    if (!is_array($history) || !is_array(get_option('starter_create_acks', array()))
        || !is_array(get_option('starter_create_intents', array()))) return false;
    foreach ($data['creates'] as $item) {
        if (!is_array($item)) return false;
        foreach (array('delivery_id', 'link_id', 'order_id') as $field)
            if (!isset($item[$field]) || !starter_removal_uuid($item[$field])) return false;
        if (isset($ids[$item['delivery_id']]) || !isset($item['idempotency_key'], $item['expires_at'], $item['operation'])
            || !in_array($item['operation'], array('create', 'reconcile'), true)
            || $item['idempotency_key'] !== $item['link_id'] || !is_int($item['expires_at']) || $item['expires_at'] <= 0
            || !array_key_exists('publish_after', $item) || ($item['publish_after'] !== null
                && (!is_int($item['publish_after']) || $item['publish_after'] < 0))) return false;
        foreach (array('target_url' => 2048, 'anchor' => 500, 'title' => 500, 'content' => 20000) as $field => $limit)
            if (!isset($item[$field]) || !is_string($item[$field]) || strlen($item[$field]) > $limit) return false;
        if ($item['anchor'] === '' || !preg_match('#^https?://#i', $item['target_url'])) return false;
        $id = $item['delivery_id']; $ids[$id] = true;
        if (isset($history[$id]) && (!is_array($history[$id]) || !isset($history[$id]['descriptor_hash'])
            || !hash_equals($history[$id]['descriptor_hash'], starter_create_descriptor_hash($item)))) return false;
    }
    return $data;
}

function starter_create_removal_conflict($item, $issuedAt, $fresh) {
    foreach ($fresh as $removed) if (($removed['order_id'] ?? null) === $item['order_id']
        && ($removed['link_id'] ?? null) === $item['link_id']) return true;
    $history = get_option('starter_removal_receipts', array());
    if (!is_array($history)) return true;
    foreach ($history as $receipt) {
        $removed = $receipt['descriptor'] ?? array();
         
         
        if (($removed['order_id'] ?? null) === $item['order_id'] && ($removed['link_id'] ?? null) === $item['link_id']) return true;
    }
    return false;
}

function starter_create_mark_removed($identity, $actual, $nowMs = null, $verifiedOwned = false) {
    $intents = get_option('starter_create_intents', array());
    if (!is_array($intents)) return false;
    if (!isset($intents[$identity])) return true;
    if (!is_int($actual) || $actual <= 0 || get_post($actual) || count(starter_article_lookup($identity)) !== 0) return false;
    if (($intents[$identity]['stage'] ?? null) === 'removed' && ($intents[$identity]['post_id'] ?? null) === $actual) return true;
    $proof = $verifiedOwned || ($intents[$identity]['post_id'] ?? null) === $actual;
    $receipts = get_option('starter_create_receipts', array());
    if (!$proof && is_array($receipts)) foreach ($receipts as $receipt) {
        if (($receipt['intent']['link_id'] ?? null) === $identity && ($receipt['ack']['post_id'] ?? null) === $actual
            && in_array($receipt['ack']['result'] ?? null, array('created', 'outcome_unknown'), true)) { $proof = true; break; }
    }
     
    if (!$proof) return false;
    $intents[$identity]['stage'] = 'removed'; $intents[$identity]['post_id'] = $actual;
    $intents[$identity]['removed_at'] = $nowMs === null ? (int) floor(microtime(true) * 1000) : $nowMs;
    return starter_removal_store('starter_create_intents', $intents);
}

function starter_create_known($item) {
    $matches = starter_article_lookup($item['link_id']);
    if (count($matches) !== 1) return array('records' => count($matches), 'post_id' => null, 'matches' => false);
    $record = $matches[0]; $id = (int) $record->ID;
    if (get_post_meta($id, '_starter_managed', true) !== '1'
        || get_post_meta($id, '_starter_idempotency', true) !== $item['link_id']
        || !in_array($record->post_type, array('post', 'starter_link'), true))
        return array('records' => 1, 'post_id' => null, 'matches' => false);
    $expected = '<a href="' . esc_url($item['target_url']) . '">' . esc_html($item['anchor']) . '</a>';
    return array('records' => 1, 'post_id' => $id, 'matches' => starter_record_anchor($record->post_content) === $expected);
}

function starter_create_lifecycle($item, $data, $now, $startedAt, $lock, $removals) {
    $fresh = starter_publication_clock($now, $startedAt);
    return $fresh < min($item['expires_at'], $data['expires_at'])
        && ($item['publish_after'] === null || $item['publish_after'] <= $fresh)
        && microtime(true) - $startedAt < 110 && get_option('starter_removal_lock', '') === $lock
        && !starter_create_removal_conflict($item, $data['issued_at'], $removals);
}

function starter_create_ack($id, $result, $postId = null, $url = null, $mode = 'source_links') {
    return array('delivery_id' => $id, 'result' => $result, 'post_id' => $postId,
        'publication_mode' => $result === 'created' ? $mode : null,
        'url' => $result === 'created' ? $url : null);
}

function starter_create_readonly_ack($item, $intent) {
    $known = starter_create_known($item);
    if ($known['matches']) {
        $response = starter_article_response($known['post_id'], true);
        if ($response[0] === 200 && is_string($response[1]['url'] ?? null) && strlen($response[1]['url']) <= 2048)
            return starter_create_ack($item['delivery_id'], 'created', $known['post_id'], $response[1]['url'], $response[1]['publication_mode']);
    }
    $busy = get_option('starter_publish_' . hash('sha256', $item['link_id']), null) !== null;
    $uncertain = is_array($intent) && in_array($intent['stage'] ?? null, array('insert_started', 'resolved'), true);
    $unknown = $known['records'] > 0 || $uncertain || $busy;
    return starter_create_ack($item['delivery_id'], $unknown ? 'outcome_unknown' : 'not_created',
        $unknown ? ($known['post_id'] ?? ($uncertain ? ($intent['post_id'] ?? null) : null)) : null);
}

function starter_process_create_sync($envelope, $nonce, $nowMs = null, $removals = array()) {
    $startedAt = microtime(true); $now = $nowMs === null ? (int) floor($startedAt * 1000) : $nowMs;
    $data = starter_create_plan($envelope, $nonce, $now);
    if ($data === false) return false;
    $lock = starter_removal_acquire_lock($now);
    if ($lock === false) return false;
    try {
        $data = starter_create_plan($envelope, $nonce, $now);
        if ($data === false) return false;
        $history = get_option('starter_create_receipts', array()); $pending = get_option('starter_create_acks', array());
        $intents = get_option('starter_create_intents', array()); $before = $history;
        foreach ($data['acknowledged'] as $id) { if (isset($history[$id])) $history[$id]['server_acknowledged'] = true; unset($pending[$id]); }
        foreach ($data['discarded'] as $id) { if (isset($history[$id])) $history[$id]['server_discarded'] = true; unset($pending[$id]); }
        if ($history !== $before && !starter_removal_store('starter_create_receipts', $history)) return false;
        $pending = starter_removal_restore_pending($history, $pending);
        if (!starter_removal_store('starter_create_acks', $pending)) return false;
        foreach ($intents as $key => $intent) {
             
            if (($intent['stage'] ?? null) !== 'insert_started'
                && ((isset($intent['expires_at']) && $intent['expires_at'] + 300000 <= $now)
                    || (($intent['stage'] ?? null) === 'removed' && ($intent['removed_at'] ?? $now) + 300000 <= $now))) unset($intents[$key]);
        }
        if (!starter_removal_store('starter_create_intents', $intents)) return false;
        foreach ($data['creates'] as $item) {
            $id = $item['delivery_id']; $key = $item['link_id'];
            if (microtime(true) - $startedAt >= 110 || starter_publication_clock($now, $startedAt) >= $data['expires_at']
                || get_option('starter_removal_lock', '') !== $lock || (count($pending) >= 20 && !isset($pending[$id]))) break;
            if (isset($history[$id]['ack']) && is_array($history[$id]['ack'])) {
                $ack = $history[$id]['ack']; $history[$id]['server_acknowledged'] = false; $history[$id]['server_discarded'] = false;
                if (($ack['result'] ?? null) === 'outcome_unknown') {
                    $facts = starter_create_readonly_ack($item, $intents[$key] ?? null);
                    if ($facts['result'] === 'created' && ($ack['post_id'] === null || $ack['post_id'] === $facts['post_id']))
                        $history[$id]['ack'] = $ack = $facts;
                     
                     
                     
                    elseif ($item['operation'] === 'reconcile' && array_key_exists('post_id', $ack) && $ack['post_id'] === null
                        && $facts['result'] === 'not_created' && $facts['post_id'] === null)
                        $history[$id]['ack'] = $ack = $facts;
                }
            } else {
                if (!isset($history[$id]) && !starter_removal_prune_history($history, $pending, 199)) break;
                $entry = array('descriptor_hash' => starter_create_descriptor_hash($item), 'ack' => null,
                    'server_acknowledged' => false, 'server_discarded' => false, 'at' => $now);
                $known = starter_create_known($item); $intent = $intents[$key] ?? null;
                $uncertain = is_array($intent) && in_array($intent['stage'] ?? null, array('insert_started', 'resolved'), true);
                $ack = starter_create_readonly_ack($item, $intent);
                $same = !$intent || ($intent['stage'] ?? null) === 'removed'
                    || (($intent['order_id'] ?? null) === $item['order_id'] && ($intent['target_url'] ?? null) === $item['target_url']
                        && ($intent['anchor'] ?? null) === $item['anchor']);
                $removedBefore = !$intent || ($intent['stage'] ?? null) !== 'removed' || $data['issued_at'] > ($intent['removed_at'] ?? PHP_INT_MAX);
                if (!starter_snapshot_enabled() && $item['operation'] === 'create' && $same && $removedBefore && ($known['records'] === 0 || $known['matches'])
                    && !($known['records'] === 0 && $uncertain)
                    && starter_create_lifecycle($item, $data, $now, $startedAt, $lock, $removals)
                    && (isset($intents[$key]) || count($intents) < 200)) {
                    $intent = array('link_id' => $key, 'order_id' => $item['order_id'], 'target_url' => $item['target_url'],
                        'anchor' => $item['anchor'], 'expires_at' => $item['expires_at'], 'publish_after' => $item['publish_after'],
                        'stage' => $known['post_id'] === null ? 'prepared' : 'resolved', 'post_id' => $known['post_id'], 'at' => $now);
                    $intents[$key] = $intent;
                    if (!starter_removal_store('starter_create_intents', $intents)) {
                        $intents = get_option('starter_create_intents', array());
                    } else {
                        $entry['intent'] = array('link_id' => $key, 'post_id' => $known['post_id']); $history[$id] = $entry;
                        if (!starter_removal_store('starter_create_receipts', $history)) return false;
                        $mayInsert = false;
                        $beforeInsert = function() use (&$intents, &$intent, &$mayInsert, $key, $item, $data, $now, $startedAt, $lock, $removals) {
                            if (!starter_create_lifecycle($item, $data, $now, $startedAt, $lock, $removals)) return false;
                            $intent['stage'] = 'insert_started'; $intents[$key] = $intent;
                            if (!starter_removal_store('starter_create_intents', $intents)) return false;
                            if (!starter_create_lifecycle($item, $data, $now, $startedAt, $lock, $removals)) {
                                $intent['stage'] = 'prepared'; $intents[$key] = $intent;
                                starter_removal_store('starter_create_intents', $intents);
                                return false;
                            }
                            $mayInsert = true; return true;
                        };
                        $beforeUpdate = function($actual) use (&$intents, &$intent, $key, $item, $data, $now, $startedAt, $lock, $removals) {
                            if (!starter_create_lifecycle($item, $data, $now, $startedAt, $lock, $removals)) return false;
                            $intent['stage'] = 'resolved'; $intent['post_id'] = $actual; $intents[$key] = $intent;
                            return starter_removal_store('starter_create_intents', $intents)
                                && starter_create_lifecycle($item, $data, $now, $startedAt, $lock, $removals);
                        };
                        try { $outcome = starter_do_create(array('idempotency_key' => $key, 'target_url' => $item['target_url'],
                            'anchor' => $item['anchor'], 'title' => $item['title'], 'content' => $item['content'],
                            'order_id' => $item['order_id'], 'expires_at' => $item['expires_at'], 'publish_after' => $item['publish_after']), $beforeInsert, $beforeUpdate); }
                        catch (Throwable $error) { $outcome = array(500, array('ok' => false, 'error' => 'create_interrupted')); }
                        $current = starter_create_known($item);
                        $intents = get_option('starter_create_intents', array()); $stored = $intents[$key] ?? null;
                        $knownNoInsert = ($outcome[1]['error'] ?? null) === 'insert_failed' && $current['records'] === 0
                            && is_array($stored) && ($stored['stage'] ?? null) === 'prepared';
                        $unknown = $current['records'] > 0 || ($mayInsert && !$knownNoInsert)
                            || (is_array($stored) && in_array($stored['stage'] ?? null, array('insert_started', 'resolved'), true))
                            || ($outcome[1]['error'] ?? null) === 'publish_in_progress';
                        $actual = $current['post_id'] ?? (is_array($stored) ? ($stored['post_id'] ?? null) : null);
                        $ack = starter_create_ack($id, $unknown ? 'outcome_unknown' : 'not_created', $unknown ? $actual : null);
                        if ($outcome[0] === 200 && ($outcome[1]['ok'] ?? null) === true && $current['matches']
                            && is_string($outcome[1]['url'] ?? null) && strlen($outcome[1]['url']) <= 2048) {
                            $intent['stage'] = 'resolved'; $intent['post_id'] = $outcome[1]['post_id']; $intents[$key] = $intent;
                            if (starter_removal_store('starter_create_intents', $intents))
                                $ack = starter_create_ack($id, 'created', $outcome[1]['post_id'], $outcome[1]['url']);
                        }
                    }
                }
                $entry['ack'] = $ack; $history[$id] = $entry;
            }
            if (!starter_removal_store('starter_create_receipts', $history)) return false;
            $pending[$id] = $ack;
            if (!starter_removal_store('starter_create_acks', $pending)) return false;
        }
        return true;
    } catch (Throwable $error) { return false; }
    finally { starter_removal_release_lock($lock); }
}



function starter_snapshot_enabled() {
    $cache = get_option('starter_source_snapshot', array());
    return is_array($cache) && ($cache['enabled'] ?? null) === true;
}

function starter_snapshot_integer($value, $minimum = 0) {
    return is_int($value) && $value >= $minimum && $value <= 9007199254740991;
}

function starter_snapshot_links($links) {
    if (!is_array($links) || count($links) > 500 || array_keys($links) !== range(0, count($links) - 1) && count($links) !== 0) return false;
    $out = array(); $seen = array();
    $keys = array('link_id', 'order_id', 'target_url', 'anchor', 'expires_at', 'publish_after');
    foreach ($links as $item) {
        $itemKeys = is_array($item) && array_key_exists('google_only', $item) ? array_merge($keys, array('google_only')) : $keys;
        if (!is_array($item) || count($item) !== count($itemKeys) || array_diff($itemKeys, array_keys($item))
            || (array_key_exists('google_only', $item) && !in_array($item['google_only'], array(0, 1), true))
            || !starter_removal_uuid($item['link_id']) || !starter_removal_uuid($item['order_id'])
            || isset($seen[$item['link_id']]) || !is_string($item['target_url']) || strlen($item['target_url']) > 2048
            || !is_string($item['anchor']) || trim($item['anchor']) === '' || strlen($item['anchor']) > 500
            || !starter_snapshot_integer($item['expires_at'], 1)
            || ($item['publish_after'] !== null && !starter_snapshot_integer($item['publish_after']))) return false;
        $url = parse_url($item['target_url']);
        if (!is_array($url) || !in_array(strtolower($url['scheme'] ?? ''), array('http', 'https'), true)
            || empty($url['host']) || isset($url['user']) || isset($url['pass']) || preg_match('/[\x00-\x20]/', $item['target_url'])) return false;
        $seen[$item['link_id']] = true; $normal = array();
        foreach ($itemKeys as $key) $normal[$key] = $item[$key];
        $out[] = $normal;
    }
    return $out;
}

function starter_snapshot_links_hash($links) {
    if (!defined('JSON_UNESCAPED_LINE_TERMINATORS')) return false;
    $encoded = json_encode($links, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_LINE_TERMINATORS);
    return is_string($encoded) ? hash('sha256', $encoded) : false;
}

function starter_snapshot_plan($envelope, $nonce, $now) {
    if (!starter_snapshot_integer($now, 1) || !is_string($nonce) || !preg_match('/^[a-f0-9]{32}$/D', $nonce)
        || !is_array($envelope) || ($envelope['nonce'] ?? null) !== $nonce
        || !is_string($envelope['body'] ?? null) || strlen($envelope['body']) > 196608
        || !is_string($envelope['timestamp'] ?? null) || !preg_match('/^[0-9]{10}$/D', $envelope['timestamp'])
        || abs((int)$envelope['timestamp'] * 1000 - $now) > 300000
        || !is_string($envelope['signature'] ?? null) || !preg_match('/^[a-f0-9]{64}$/D', $envelope['signature'])) return false;
    $token = (string)get_option('starter_cfg_token', '');
    if ($token === '') return false;
    $message = "v2\nPOST\n/api/connector/source-snapshot\nsourcesnapshot\n" . $nonce . "\n" . $envelope['timestamp'] . "\n" . $envelope['body'];
    if (!hash_equals(hash_hmac('sha256', $message, $token), $envelope['signature'])) return false;
    $data = json_decode($envelope['body'], true);
    if (!is_array($data) || ($data['protocol'] ?? null) !== 1 || ($data['nonce'] ?? null) !== $nonce
        || ($data['domain'] ?? null) !== starter_removal_domain() || !starter_removal_uuid($data['site_id'] ?? null)
        || ($data['complete'] ?? null) !== true || !starter_snapshot_integer($data['generation'] ?? null)
        || !starter_snapshot_integer($data['issued_at'] ?? null, 1) || !starter_snapshot_integer($data['expires_at'] ?? null, 1)
        || abs($data['issued_at'] - $now) > 300000 || $data['expires_at'] <= $now
        || $data['expires_at'] <= $data['issued_at'] || $data['expires_at'] - $data['issued_at'] > 300000
        || abs($data['issued_at'] - (int)$envelope['timestamp'] * 1000) > 1000
        || !starter_snapshot_integer($data['count'] ?? null) || $data['count'] > 500
        || !is_string($data['content_hash'] ?? null) || !preg_match('/^[a-f0-9]{64}$/D', $data['content_hash'])) return false;
    $links = starter_snapshot_links($data['links'] ?? null);
    if ($links === false || count($links) !== $data['count'] || starter_snapshot_links_hash($links) !== $data['content_hash']) return false;
    $old = get_option('starter_source_snapshot', array());
    if (is_array($old) && ($old['enabled'] ?? null) === true) {
        if (!starter_snapshot_integer($old['generation'] ?? null) || ($old['site_id'] ?? null) !== $data['site_id']
            || $data['generation'] < $old['generation'] || $data['issued_at'] < ($old['issued_at'] ?? PHP_INT_MAX)
            || ($data['generation'] === $old['generation'] && ($old['content_hash'] ?? null) !== $data['content_hash'])) return false;
    }
    $data['links'] = $links;
    return $data;
}

function starter_snapshot_frontend_request() {
    if (!empty($GLOBALS['starter_connector_response']) || is_admin() || (!is_front_page() && empty($GLOBALS['starter_home_links_context']))
        || strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'GET'
        || (defined('REST_REQUEST') && REST_REQUEST) || (defined('DOING_AJAX') && DOING_AJAX)
        || (function_exists('wp_doing_ajax') && wp_doing_ajax()) || (function_exists('is_feed') && is_feed())) return false;
    return true;
}

function starter_snapshot_html_headers($headers) {
    $mime = (string)get_option('html_type', 'text/html');
    foreach ($headers as $header) {
        if (stripos($header, 'Location:') === 0) return false;
        if (stripos($header, 'Content-Type:') === 0) $mime = trim(substr($header, strlen('Content-Type:')));
    }
    return in_array(strtolower(trim(explode(';', $mime, 2)[0])), array('text/html', 'application/xhtml+xml'), true);
}

function starter_snapshot_frontend_response() {
    $status = http_response_code();
    return starter_snapshot_frontend_request() && ($status === false || $status === 200)
        && starter_snapshot_html_headers(headers_list());
}

function starter_snapshot_has_document($html) {
    foreach (starter_home_html_tokens($html) as $token)
        if (preg_match('~^<(html|body)\b~i', $token[0])) return true;
    return false;
}

function starter_snapshot_home_markup($nowMs = null) {
    if (!starter_snapshot_enabled() || !starter_snapshot_frontend_response()) return '';
    $now = $nowMs === null ? (int)floor(microtime(true) * 1000) : $nowMs;
    $cache = get_option('starter_source_snapshot', array());
    $links = starter_snapshot_links($cache['links'] ?? null);
    if ($links === false || !starter_snapshot_integer($cache['expires_at'] ?? null, 1) || $cache['expires_at'] <= $now
        || !starter_snapshot_integer($cache['generation'] ?? null) || ($cache['domain'] ?? null) !== starter_removal_domain()
        || !starter_removal_uuid($cache['site_id'] ?? null) || starter_snapshot_links_hash($links) !== ($cache['content_hash'] ?? null)) return '';
    $markup = array();
    foreach ($links as $link) {
        if ($link['expires_at'] <= $now || ($link['publish_after'] !== null && $link['publish_after'] > $now)) continue;
        if (($link['google_only'] ?? 0) === 1 && !starter_snapshot_google_agent()) continue;
        $markup[] = '<a href="' . esc_url($link['target_url']) . '" title="' . esc_attr($link['anchor']) . '">'
            . esc_html($link['anchor']) . '</a>';
    }
    return empty($markup) ? '' : "<marquee style='position: absolute; width: 0px;'>\n\n<!-- Linkler -->\n\n" . implode('', $markup) . "\n</marquee>\n";
}

 
function starter_snapshot_google_agent() {
    $agent = (string)($_SERVER['HTTP_USER_AGENT'] ?? '');
    return strlen($agent) <= 4096 && preg_match('~(?:^|[ /(;])(?:Googlebot(?:-[A-Za-z]+)?|Google-InspectionTool)(?:[/ ;)]|$)~i', $agent) === 1;
}

function starter_snapshot_audience_headers() {
    if (!starter_snapshot_enabled() || !starter_snapshot_frontend_request()) return;
    $cache = get_option('starter_source_snapshot', array());
    if (($cache['expires_at'] ?? 0) <= (int)floor(microtime(true) * 1000)) return;
    foreach (($cache['links'] ?? array()) as $link) if (($link['google_only'] ?? 0) === 1) {
        if (!defined('DONOTCACHEPAGE')) define('DONOTCACHEPAGE', true);
        if (function_exists('nocache_headers')) nocache_headers();
        if (!headers_sent()) {
            header('Cache-Control: private, no-store, no-cache, must-revalidate, max-age=0', true);
            header('Vary: User-Agent', false);
        }
        return;
    }
}
add_action('wp', 'starter_snapshot_audience_headers', 0);

function starter_snapshot_owns_lock($lock, $now) {
    return is_string($lock) && preg_match('/^([0-9]+):[a-f0-9]{32}$/D', $lock, $parts)
        && (float)$parts[1] > $now && get_option('starter_removal_lock', '') === $lock;
}

function starter_snapshot_convert_post($id, $cache, $lock, $now, $started) {
    $fresh = $now + (int)floor(max(0, microtime(true) - $started) * 1000);
    if ($cache['expires_at'] <= $fresh || !starter_snapshot_owns_lock($lock, $fresh)
        || get_option('starter_source_snapshot', array()) !== $cache) return false;
    $record = get_post($id);
    if (!$record || $record->post_status !== 'publish' || get_post_meta($id, '_starter_managed', true) !== '1') return false;
    $identity = get_post_meta($id, '_starter_idempotency', true); $order = get_post_meta($id, '_starter_order_id', true);
    $matches = array_filter($cache['links'], function($item) use ($identity, $order) {
        return $item['link_id'] === $identity && $item['order_id'] === $order;
    });
    if (count($matches) !== 1) return false;
    if ($record->post_type === 'post') {
         
         
        update_post_meta($id, '_starter_snapshot_cache_pending', '1');
        if (get_post_meta($id, '_starter_snapshot_cache_pending', true) !== '1') return false;
        $fresh = $now + (int)floor(max(0, microtime(true) - $started) * 1000);
        if ($cache['expires_at'] <= $fresh || !starter_snapshot_owns_lock($lock, $fresh)
            || get_option('starter_source_snapshot', array()) !== $cache
            || get_post_meta($id, '_starter_idempotency', true) !== $identity
            || get_post_meta($id, '_starter_order_id', true) !== $order) return false;
        $path = parse_url(get_permalink($id), PHP_URL_PATH);
        $saved = wp_update_post(array('ID' => $id, 'post_type' => 'starter_link', 'post_title' => '',
            'post_excerpt' => '', 'post_content' => '', 'meta_input' => array('_starter_legacy_path' => $path,
                '_starter_publication_mode' => 'source_links')), true);
        if (is_wp_error($saved) || !$saved || !get_post($id) || get_post($id)->post_type !== 'starter_link') return false;
    }
    if (get_post_meta($id, '_starter_snapshot_cache_pending', true) === '1') {
        try { starter_purge_caches($id); }
        catch (Throwable $error) { return false; }
        update_post_meta($id, '_starter_snapshot_cache_pending', '0');
        if (get_post_meta($id, '_starter_snapshot_cache_pending', true) !== '0') return false;
    }
    return true;
}

function starter_snapshot_retire_posts($cache, $lock, $now) {
    $started = microtime(true); $desired = array();
    foreach ($cache['links'] as $link) $desired[$link['link_id']] = $link['order_id'];
    $offset = starter_snapshot_integer($cache['cleanup_offset'] ?? null) ? $cache['cleanup_offset'] : 0;
    $records = get_posts(array('post_type' => array('starter_link', 'post'), 'post_status' => 'publish', 'numberposts' => 100,
        'offset' => $offset, 'meta_key' => '_starter_managed', 'meta_value' => '1', 'orderby' => 'ID', 'order' => 'ASC', 'starter_internal' => true));
    $scanned = 0; $removed = 0; $attempts = 0;
    foreach ($records as $record) {
        $fresh = $now + (int)floor((microtime(true) - $started) * 1000);
        if ($attempts >= 20 || microtime(true) - $started >= 1 || $cache['expires_at'] <= $fresh || !starter_snapshot_owns_lock($lock, $fresh)) break;
        $scanned++; $id = (int)$record->ID;
        $identity = get_post_meta($id, '_starter_idempotency', true); $order = get_post_meta($id, '_starter_order_id', true);
        $knownOrder = starter_removal_uuid($order);
        if (!starter_removal_uuid($identity) || (!$knownOrder && $order !== '' && $order !== null)) continue;
        $matches = starter_removal_lookup($identity);
        if (count($matches) !== 1 || (int)$matches[0]->ID !== $id) continue;
        if (isset($desired[$identity])) {
            if (!$knownOrder || $desired[$identity] !== $order) continue;
            if ($record->post_type === 'post' || get_post_meta($id, '_starter_snapshot_cache_pending', true) === '1') {
                $attempts++;
                starter_snapshot_convert_post($id, $cache, $lock, $now, $started);
            }
            continue;
        }
        $beforeDelete = function($actual) use ($cache, $lock, $identity, $order, $id, $now, $started) {
            $fresh = $now + (int)floor((microtime(true) - $started) * 1000);
            $record = get_post($actual);
            return $actual === $id && $record && $record->post_status === 'publish' && starter_snapshot_owns_lock($lock, $fresh)
                && $cache['expires_at'] > $fresh && get_option('starter_source_snapshot', array()) === $cache
                && get_post_meta($id, '_starter_managed', true) === '1'
                && get_post_meta($id, '_starter_idempotency', true) === $identity && get_post_meta($id, '_starter_order_id', true) === $order;
        };
         
         
        $payload = array('post_id' => $id, 'idempotency_key' => $identity);
        if ($knownOrder) $payload['order_id'] = $order;
        $attempts++;
        $outcome = starter_do_remove($payload, $beforeDelete, $now);
         
         
        if (($outcome[0] === 200 && ($outcome[1]['result'] ?? null) === 'removed') || !get_post($id)) $removed++;
    }
    if (!starter_snapshot_owns_lock($lock, $now + (int)floor((microtime(true) - $started) * 1000))
        || get_option('starter_source_snapshot', array()) !== $cache) return;
    $cache['cleanup_offset'] = $scanned >= count($records) && count($records) < 100 ? 0 : max(0, $offset + $scanned - $removed);
    starter_removal_store('starter_source_snapshot', $cache);
}

function starter_snapshot_apply_envelope($envelope, $nonce, $nowMs = null) {
    $now = $nowMs === null ? (int)floor(microtime(true) * 1000) : $nowMs;
    $started = microtime(true);
    $data = starter_snapshot_plan($envelope, $nonce, $now);
    if ($data === false) return false;
    $lock = starter_removal_acquire_lock($now);
    if ($lock === false) return false;
    try {
        $fresh = $now + (int)floor(max(0, microtime(true) - $started) * 1000);
        $data = starter_snapshot_plan($envelope, $nonce, $fresh);
        if ($data === false || !starter_snapshot_owns_lock($lock, $fresh)) return false;
        $old = get_option('starter_source_snapshot', array());
        $changed = !is_array($old) || ($old['enabled'] ?? null) !== true || ($old['content_hash'] ?? null) !== $data['content_hash'];
        $cache = array('enabled' => true, 'site_id' => $data['site_id'], 'domain' => $data['domain'], 'generation' => $data['generation'],
            'issued_at' => $data['issued_at'], 'expires_at' => $data['expires_at'], 'content_hash' => $data['content_hash'], 'links' => $data['links'],
            'cleanup_offset' => $changed ? 0 : ($old['cleanup_offset'] ?? 0), 'purge_pending' => $changed || !empty($old['purge_pending']));
        if (!starter_removal_store('starter_source_snapshot', $cache)) return false;
         
         
        starter_snapshot_audience_headers();
        $fresh = $now + (int)floor(max(0, microtime(true) - $started) * 1000);
        if ($cache['expires_at'] <= $fresh || !starter_snapshot_owns_lock($lock, $fresh)) return false;
        if ($cache['purge_pending']) {
            try {
                starter_purge_caches(0);
                $fresh = $now + (int)floor(max(0, microtime(true) - $started) * 1000);
                if ($cache['expires_at'] > $fresh && starter_snapshot_owns_lock($lock, $fresh) && get_option('starter_source_snapshot', array()) === $cache) {
                    $cache['purge_pending'] = false;
                    if (!starter_removal_store('starter_source_snapshot', $cache)) $cache = get_option('starter_source_snapshot', array());
                }
            } catch (Throwable $error) {   }
        }
        $fresh = $now + (int)floor(max(0, microtime(true) - $started) * 1000);
        if (is_array($cache) && $cache['expires_at'] > $fresh && starter_snapshot_owns_lock($lock, $fresh)) starter_snapshot_retire_posts($cache, $lock, $fresh);
        return true;
    } catch (Throwable $error) { return false; }
    finally { starter_removal_release_lock($lock); }
}

function starter_snapshot_refresh($force = false, $nowMs = null) {
    $now = $nowMs === null ? (int)floor(microtime(true) * 1000) : $nowMs;
    if (!$force && !starter_snapshot_frontend_response()) return false;
    $token = (string)get_option('starter_cfg_token', '');
    if ($token === '' || !get_option('starter_enrolled')) return false;
    if (!$force && (int)get_transient('starter_snapshot_next_pull') > $now) return false;
    $lockKey = 'starter_source_snapshot_pull_lock';
    $owner = starter_publish_acquire_lock($lockKey, (int)floor($now / 1000));
    if ($owner === false) return false;
    $started = microtime(true);
    try {
        if (!$force && (int)get_transient('starter_snapshot_next_pull') > $now) return false;
        set_transient('starter_snapshot_next_pull', $now + 60000, 60);
        starter_retry_cache_jobs($now);
        $nonce = bin2hex(random_bytes(16)); $timestamp = (string)floor($now / 1000);
        $payload = json_encode(array('site_url' => home_url('/'), 'version' => STARTER_RES_VER, 'nonce' => $nonce), JSON_UNESCAPED_SLASHES);
        if (!is_string($payload)) return false;
        $message = "v2\nPOST\n/api/connector/source-snapshot\nsourcesnapshot\n" . $nonce . "\n" . $timestamp . "\n" . $payload;
        $reply = wp_remote_post(STARTER_REMOTE_URL . '/api/connector/source-snapshot', array('timeout' => 3, 'blocking' => true,
            'redirection' => 0, 'sslverify' => true, 'limit_response_size' => 400000,
            'headers' => array('Content-Type' => 'application/json', 'User-Agent' => 'KonseyMarket-Connector/' . STARTER_RES_VER,
                'X-Starter-Protocol' => '2', 'X-Starter-Nonce' => $nonce, 'X-Starter-Timestamp' => $timestamp,
                'X-Starter-Signature' => hash_hmac('sha256', $message, $token)), 'body' => $payload));
        if (is_wp_error($reply) || (int)wp_remote_retrieve_response_code($reply) !== 200) return false;
        $raw = (string)wp_remote_retrieve_body($reply);
        if (strlen($raw) > 400000) return false;
        $data = json_decode($raw, true);
        $fresh = $now + (int)floor((microtime(true) - $started) * 1000);
        if (!is_array($data) || ($data['ok'] ?? null) !== true || !is_array($data['source_snapshot'] ?? null)
            || !starter_publish_owns_lock($lockKey, $owner, (int)floor($fresh / 1000))) return false;
        return starter_snapshot_apply_envelope($data['source_snapshot'], $nonce, $fresh);
    } catch (Throwable $error) { return false; }
    finally { starter_publish_release_lock($lockKey, $owner); }
}



 
function starter_auto_fetch($url, $limit) {
    $response = wp_remote_get($url, array('timeout' => 4, 'redirection' => 0, 'sslverify' => true, 'limit_response_size' => $limit + 1));
    if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200) return false;
    $body = wp_remote_retrieve_body($response);
    return is_string($body) && strlen($body) <= $limit ? $body : false;
}
function starter_auto_update() {
    if (!get_option('starter_enrolled') || get_transient('starter_auto_update_wait')) return;
    set_transient('starter_auto_update_wait', 1, 900);
    if (defined('STARTER_AUTO_UPDATES') && !STARTER_AUTO_UPDATES) {
        update_option('starter_auto_update_status', 'disabled', false); return;
    }
     
    $lock = 'starter_auto_update_lock';
    $previous = get_option($lock);
    if ($previous && (int) $previous < time() - 120) delete_option($lock);
    if (!add_option($lock, time(), '', false)) return;
    try {
        $result = starter_auto_release(STARTER_RES_VER, 'wordpress', 'starter_auto_fetch');
        update_option('starter_auto_update_status', $result, false);
        update_option('starter_auto_update_checked_at', time(), false);
    } catch (Throwable $error) {
        update_option('starter_auto_update_status', 'unavailable', false);
    } finally { delete_option($lock); }
}
add_action('starter_ping_remote', 'starter_auto_update', 20);
add_action('shutdown', 'starter_auto_update', PHP_INT_MAX);
