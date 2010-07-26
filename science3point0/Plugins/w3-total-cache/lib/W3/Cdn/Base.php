<?php

/**
 * W3 CDN Base class
 */

if (!defined('W3TC_CDN_RESULT_HALT')) {
    define('W3TC_CDN_RESULT_HALT', -1);
}

if (!defined('W3TC_CDN_RESULT_ERROR')) {
    define('W3TC_CDN_RESULT_ERROR', 0);
}

if (!defined('W3TC_CDN_RESULT_OK')) {
    define('W3TC_CDN_RESULT_OK', 1);
}

/**
 * Class W3_Cdn_Base
 */
class W3_Cdn_Base
{
    /**
     * CDN Configuration
     *
     * @var array
     */
    var $_config = array();
    
    /**
     * Cache config
     * @var array
     */
    var $cache_config = array();
    
    /**
     * PHP5 Constructor
     *
     * @param array $config
     */
    function __construct($config)
    {
        $this->_config = $config;
    }
    
    /**
     * PHP4 Constructor
     *
     * @param array $config
     */
    function W3_Cdn_Base($config)
    {
        $this->__construct($config);
    }
    
    /**
     * Upload files to CDN
     *
     * @param array $files
     * @param array $results
     * @param boolean $force_rewrite
     * @return boolean
     */
    function upload($files, &$results, $force_rewrite = false)
    {
        $results = $this->get_results($files, W3TC_CDN_RESULT_HALT, 'Not implemented.');
        
        return false;
    }
    
    /**
     * Delete files from CDN
     *
     * @param array $files
     * @param array $results
     * @return boolean
     */
    function delete($files, &$results)
    {
        $results = $this->get_results($files, W3TC_CDN_RESULT_HALT, 'Not implemented.');
        
        return false;
    }
    
    /**
     * Purges URLs from CDN
     * 
     * @param array $files
     * @param array $results
     * @return boolean
     */
    function purge($files, &$results)
    {
        return $this->upload($files, $results, true);
    }
    
    /**
     * Test CDN server
     *
     * @param string $error
     * @return boolean
     */
    function test(&$error)
    {
        if (!$this->_test_domains($error)) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Create bucket / container for some CDN engines
     * 
     * @param string $container_id
     * @param string $error
     * @return boolean
     */
    function create_container(&$container_id, &$error)
    {
        $error = 'Not implemented.';
        
        return false;
    }
    
    /**
     * Returns first domain
     * 
     * @return string
     */
    function get_domain()
    {
        $domains = $this->get_domains();
        
        if (count($domains)) {
            return current($domains);
        }
        
        return false;
    }
    
    /**
     * Returns array of CDN domains
     *
     * @return array
     */
    function get_domains()
    {
        return array();
    }
    
    /**
     * Returns via string
     *
     * @return string
     */
    function get_via()
    {
        $domain = $this->get_domain();
        
        if ($domain) {
            return $domain;
        }
        
        return 'N/A';
    }
    
    /**
     * Formats object URL
     *
     * @param string $path
     * @return string
     */
    function format_url($path)
    {
        $domains = $this->get_domains();
        $count = count($domains);
        
        if ($count) {
            switch (true) {
                /**
                 * Reserved CSS
                 */
                case (isset($domains[0]) && $this->_is_css($path)):
                    $host = $domains[0];
                    break;
                
                /**
                 * Reserved JS in head
                 */
                case (isset($domains[1]) && $this->_is_js($path)):
                    $host = $domains[1];
                    break;
                
                /**
                 * Reserved JS after body
                 */
                case (isset($domains[2]) && $this->_is_js_body($path)):
                    $host = $domains[2];
                    break;
                
                /**
                 * Reserved JS before /body
                 */
                case (isset($domains[3]) && $this->_is_js_footer($path)):
                    $host = $domains[3];
                    break;
                
                default:
                    if ($count > 4) {
                        $host = $this->_get_host(array_slice($domains, 4), $path);
                    } else {
                        $host = $this->_get_host($domains, $path);
                    }
            }
            
            $url = sprintf('%s://%s/%s', (w3_is_https() ? 'https' : 'http'), $host, $path);
            
            return $url;
        }
        
        return false;
    }
    
    /**
     * Returns results
     *
     * @param array $files
     * @param integer $result
     * @param string $error
     * @return array
     */
    function get_results($files, $result = W3TC_CDN_RESULT_OK, $error = 'OK')
    {
        $results = array();
        
        foreach ($files as $local_path => $remote_path) {
            $results[] = $this->get_result($local_path, $remote_path, $result, $error);
        }
        
        return $results;
    }
    
    /**
     * Returns file process result
     *
     * @param string $local_path
     * @param string $remote_path
     * @param integer $result
     * @param string $error
     * @return array
     */
    function get_result($local_path, $remote_path, $result = W3TC_CDN_RESULT_OK, $error = 'OK')
    {
        return array(
            'local_path' => $local_path, 
            'remote_path' => $remote_path, 
            'result' => $result, 
            'error' => $error
        );
    }
    
    /**
     * Returns headers for file
     * 
     * @param string $file
     * @return array
     */
    function get_headers($file)
    {
        $mime_type = w3_get_mime_type($file);
        $last_modified = time();
        
        $headers = array(
            'Content-Type' => $mime_type, 
            'Last-Modified' => w3_http_date($last_modified)
        );
        
        if (isset($this->cache_config[$mime_type])) {
            if ($this->cache_config[$mime_type]['etag']) {
                $headers['Etag'] = @md5_file($file);
            }
            
            if ($this->cache_config[$mime_type]['w3tc']) {
                $headers['X-Powered-By'] = W3TC_POWERED_BY;
            }
            
            if ($this->cache_config[$mime_type]['lifetime']) {
                $headers['Expires'] = w3_http_date($last_modified + $this->cache_config[$mime_type]['lifetime']);
            }
            
            switch ($this->cache_config[$mime_type]['cache_control']) {
                case 'cache':
                    $headers = array_merge($headers, array(
                        'Pragma' => 'public', 
                        'Cache-Control' => 'public'
                    ));
                    break;
                
                case 'cache_validation':
                    $headers = array_merge($headers, array(
                        'Pragma' => 'public', 
                        'Cache-Control' => 'public, must-revalidate, proxy-revalidate'
                    ));
                    break;
                
                case 'cache_noproxy':
                    $headers = array_merge($headers, array(
                        'Pragma' => 'public', 
                        'Cache-Control' => 'public, must-revalidate'
                    ));
                    break;
                
                case 'cache_maxage':
                    $headers = array_merge($headers, array(
                        'Pragma' => 'public', 
                        'Cache-Control' => 'max-age=' . $this->cache_config[$mime_type]['lifetime'] . ', public, must-revalidate, proxy-revalidate'
                    ));
                    break;
                
                case 'no_cache':
                    $headers = array_merge($headers, array(
                        'Pragma' => 'no-cache', 
                        'Cache-Control' => 'max-age=0, private, no-store, no-cache, must-revalidate'
                    ));
                    break;
            }
        }
        
        return $headers;
    }
    
    /**
     * Use gzip compression only for text-based files
     * 
     * @param string $file
     * @return boolean
     */
    function may_gzip($file)
    {
        /**
         * Remove query string
         */
        $file = preg_replace('~\?.*$~', '', $file);
        
        /**
         * Check by file extension
         */
        if (preg_match('~\.(ico|js|css|xml|xsd|xsl|svg|htm|html|txt)$~i', $file)) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Test domains
     * 
     * @param string $error
     * @return boolean
     */
    function _test_domains(&$error)
    {
        $domains = $this->get_domains();
        
        if (!count($domains)) {
            $error = 'Empty domains / CNAMEs list.';
            
            return false;
        
        }
        
        foreach ($domains as $domain) {
            if ($domain && gethostbyname($domain) === $domain) {
                $error = sprintf('Unable to resolve domain: %s.', $domain);
                
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Check if css file
     * 
     * @param string $path
     * @return boolean
     */
    function _is_css($path)
    {
        return preg_match('~[a-z0-9\-_]+\.include\.[0-9]+\.css$~', $path);
    }
    
    /**
     * Check if JS file in heeader
     * 
     * @param string $path
     * @return boolean
     */
    function _is_js($path)
    {
        return preg_match('~[a-z0-9\-_]+\.include(-nb)?\.[0-9]+\.js$~', $path);
    }
    
    /**
     * Check if JS file after body
     * 
     * @param string $path
     * @return boolean
     */
    function _is_js_body($path)
    {
        return preg_match('~[a-z0-9\-_]+\.include-body(-nb)?\.[0-9]+\.js$~', $path);
    }
    
    /**
     * Check if JS file before /body
     * 
     * @param string $path
     * @return boolean
     */
    function _is_js_footer($path)
    {
        return preg_match('~[a-z0-9\-_]+\.include-footer(-nb)?\.[0-9]+\.js$~', $path);
    }
    
    /**
     * Returns host for path
     * 
     * @param array $domains
     * @param string $path
     * @return string
     */
    function _get_host($domains, $path)
    {
        $count = count($domains);
        
        if ($count) {
            /**
             * Use for equal URLs same host to allow caching by browser
             */
            $hash = $this->_get_hash($path);
            $host = $domains[$hash % $count];
            
            return $host;
        }
        
        return false;
    }
    
    /**
     * Returns integer hash for key
     * 
     * @param string $key
     * @return integer
     */
    function _get_hash($key)
    {
        $hash = abs(crc32($key));
        
        return $hash;
    }
}
