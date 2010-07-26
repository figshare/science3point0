<?php

/**
 * W3 Object Cache object
 */
class W3_ObjectCache
{
    /**
     * Internal cache array
     * 
     * @var array
     */
    var $cache = array();
    
    /**
     * Array of global groups
     * 
     * @var array
     */
    var $global_groups = array();
    
    /**
     * List of non-persistent groups
     *
     * @var array
     */
    var $nonpersistent_groups = array();
    
    /**
     * Total count of calls
     *
     * @var integer
     */
    var $cache_total = 0;
    
    /**
     * Cache hits count
     * 
     * @var integer
     */
    var $cache_hits = 0;
    
    /**
     * Cache misses count
     *
     * @var integer
     */
    var $cache_misses = 0;
    
    /**
     * Store debug information of w3tc using
     *
     * @var array
     */
    var $debug_info = array();
    
    /**
     * Config
     *
     * @var W3_Config
     */
    var $_config = null;
    
    /**
     * Caching flag
     * 
     * @var boolean
     */
    var $_caching = false;
    
    /**
     * Cache reject reason
     * 
     * @var string
     */
    var $_cache_reject_reason = '';
    
    /**
     * Lifetime
     *
     * @var integer
     */
    var $_lifetime = null;
    
    /**
     * Debug flag
     * 
     * @var boolean
     */
    var $_debug = false;
    
    /**
     * PHP5 style constructor
     */
    function __construct()
    {
        require_once W3TC_LIB_W3_DIR . '/Config.php';
        
        $this->_config = & W3_Config::instance();
        $this->_lifetime = $this->_config->get_integer('objectcache.lifetime');
        $this->_debug = $this->_config->get_boolean('objectcache.debug');
        
        $this->global_groups = $this->_config->get_array('objectcache.groups.global');
        $this->nonpersistent_groups = $this->_config->get_array('objectcache.groups.nonpersistent');
        
        $this->_caching = $this->_can_cache();
        
        $GLOBALS['_wp_using_ext_object_cache'] = $this->_caching;
        
        if ($this->_can_ob()) {
            ob_start(array(
                &$this, 
                'ob_callback'
            ));
        }
    }
    
    /**
     * PHP4 style constructor
     */
    function W3_ObjectCache()
    {
        $this->__construct();
    }
    
    /**
     * Returns onject instance
     *
     * @return W3_ObjectCache
     */
    function &instance()
    {
        static $instances = array();
        
        if (!isset($instances[0])) {
            $class = __CLASS__;
            $instances[0] = & new $class();
        }
        
        return $instances[0];
    }
    
    /**
     * Get from the cache
     * 
     * @param string $id
     * @param string $group
     * @return mixed
     */
    function get($id, $group = 'default')
    {
        $key = $this->_get_cache_key($id, $group);
        
        $caching = true;
        $internal = true;
        $reason = '';
        
        if (isset($this->cache[$key])) {
            $value = $this->cache[$key];
        } else {
            $caching = $this->_can_cache2($id, $group, $reason);
            $internal = false;
            
            if ($caching) {
                $cache = & $this->_get_cache();
                $value = $cache->get($key);
            } else {
                $value = false;
            }
            
            $this->cache[$key] = $value;
        }
        
        if ($value === null) {
            $value = false;
        }
        
        $this->cache_total++;
        
        if ($value !== false) {
            $cached = true;
            $this->cache_hits++;
        } else {
            $cached = false;
            $this->cache_misses++;
        }
        
        /**
         * Add debug info
         */
        if ($this->_debug) {
            if (!$group) {
                $group = 'default';
            }
            
            $this->debug_info[] = array(
                'id' => $id, 
                'group' => $group, 
                'caching' => $caching, 
                'reason' => $reason, 
                'cached' => $cached, 
                'internal' => $internal
            );
        }
        
        return $value;
    }
    
    /**
     * Set to the cache
     * 
     * @param string $id
     * @param mixed $data
     * @param string $group
     * @param integer $expire
     * @return boolean
     */
    function set($id, $data, $group = 'default', $expire = 0)
    {
        $key = $this->_get_cache_key($id, $group);
        
        if (is_object($data)) {
            $data = wp_clone($data);
        }
        
        if (isset($this->cache[$key]) && $this->cache[$key] === $data) {
            return true;
        }
        
        $this->cache[$key] = $data;
        
        $reason = '';
        
        if ($this->_can_cache2($id, $group, $reason)) {
            $cache = & $this->_get_cache();
            
            return $cache->set($key, $data, ($expire ? $expire : $this->_lifetime));
        }
        
        return true;
    }
    
    /**
     * Delete from the cache
     * 
     * @param string $id
     * @param string $group
     * @return boolean
     */
    function delete($id, $group = 'default')
    {
        $key = $this->_get_cache_key($id, $group);
        
        unset($this->cache[$key]);
        
        $reason = '';
        
        if ($this->_can_cache2($id, $group, $reason)) {
            $cache = & $this->_get_cache();
            
            return $cache->delete($key);
        }
        
        return true;
    }
    
    /**
     * Add to the cache
     * 
     * @param string $id
     * @param mixed $data
     * @param string $group
     * @param integer $expire
     * @return boolean
     */
    function add($id, $data, $group = 'default', $expire = 0)
    {
        if ($this->get($id, $group) !== false) {
            return false;
        }
        
        return $this->set($id, $data, $group, $expire);
    }
    
    /**
     * Replace in the cache
     * 
     * @param string $id
     * @param mixed $data
     * @param string $group
     * @param integer $expire
     * @return boolean
     */
    function replace($id, $data, $group = 'default', $expire = 0)
    {
        if ($this->get($id, $group) === false) {
            return false;
        }
        
        return $this->set($id, $data, $group, $expire);
    }
    
    /**
     * Flush cache
     * 
     * @return boolean
     */
    function flush()
    {
        $this->cache = array();
        
        $cache = & $this->_get_cache();
        
        return $cache->flush();
    }
    
    /**
     * Add global groups
     * 
     * @param array $groups
     * @return void
     */
    function add_global_groups($groups)
    {
        if (!is_array($groups)) {
            $groups = (array) $groups;
        }
        
        $this->global_groups = array_merge($this->global_groups, $groups);
        $this->global_groups = array_unique($this->global_groups);
    }
    
    /**
     * Add non-persistent groups
     * 
     * @param array $groups
     * @return void
     */
    function add_nonpersistent_groups($groups)
    {
        if (!is_array($groups)) {
            $groups = (array) $groups;
        }
        
        $this->nonpersistent_groups = array_merge($this->nonpersistent_groups, $groups);
        $this->nonpersistent_groups = array_unique($this->nonpersistent_groups);
    }
    
    /**
     * Output buffering callback
     *
     * @param string $buffer
     * @return string
     */
    function ob_callback(&$buffer)
    {
        if ($buffer != '' && w3_is_xml($buffer)) {
            $buffer .= "\r\n\r\n" . $this->_get_debug_info();
        }
        
        return $buffer;
    }
    
    /**
     * Returns cache key
     * 
     * @param string $id
     * @param string $group
     * @return string
     */
    function _get_cache_key($id, $group = 'default')
    {
        if (!$group) {
            $group = 'default';
        }
        
        $host = w3_get_host();
        
        if (!in_array($group, $this->global_groups)) {
            $prefix = w3_get_blogname();
        } else {
            $prefix = '';
        }
        
        $key = sprintf('w3tc_%s_object_%s', md5($host), md5($prefix . $group . $id));
        
        /**
         * Allow to modify cache key by W3TC plugins
         */
        $key = w3tc_do_action('w3tc_objectcache_cache_key', $key);
        
        return $key;
    }
    
    /**
     * Returns cache object
     *
     * @return W3_Cache_Base
     */
    function &_get_cache()
    {
        static $cache = array();
        
        if (!isset($cache[0])) {
            $engine = $this->_config->get_string('objectcache.engine');
            
            switch ($engine) {
                case 'memcached':
                    $engineConfig = array(
                        'servers' => $this->_config->get_array('objectcache.memcached.servers'), 
                        'persistant' => $this->_config->get_boolean('objectcache.memcached.persistant')
                    );
                    break;
                
                case 'file':
                    $engineConfig = array(
                        'cache_dir' => W3TC_CACHE_FILE_OBJECTCACHE_DIR, 
                        'locking' => $this->_config->get_boolean('objectcache.file.locking')
                    );
                    break;
                
                default:
                    $engineConfig = array();
            }
            
            require_once W3TC_LIB_W3_DIR . '/Cache.php';
            $cache[0] = & W3_Cache::instance($engine, $engineConfig);
        }
        
        return $cache[0];
    }
    
    /**
     * Check if caching allowed on init
     * 
     * @return boolean
     */
    function _can_cache()
    {
        /**
         * Skip if disabled
         */
        if (!$this->_config->get_boolean('objectcache.enabled')) {
            $this->_cache_reject_reason = 'object caching is disabled';
            
            return false;
        }
        
        /**
         * Check for DONOTCACHCEOBJECT constant
         */
        if (defined('DONOTCACHCEOBJECT') && DONOTCACHCEOBJECT) {
            $this->_cache_reject_reason = 'DONOTCACHCEOBJECT constant is defined';
            
            return false;
        }
        
        /**
         * Skip if admin
         */
        if ($this->_config->get_boolean('objectcache.reject.admin') && defined('WP_ADMIN')) {
            $this->_cache_reject_reason = 'wp-admin';
            
            return false;
        }
        
        /**
         * Check request URI
         */
        if (!$this->_check_request_uri()) {
            $this->_cache_reject_reason = 'request URI is rejected';
            
            return false;
        }
        
        return true;
    }
    
    /**
     * Check if caching allowed runtime
     *
     * @param string $id
     * @param string $group
     * @return boolean
     */
    function _can_cache2($id, $group, &$cache_reject_reason)
    {
        /**
         * Skip if disabled
         */
        if (!$this->_caching) {
            $cache_reject_reason = $this->_cache_reject_reason;
            
            return false;
        }
        
        /**
         * Check for DONOTCACHCEOBJECT constant
         */
        if (defined('DONOTCACHCEOBJECT') && DONOTCACHCEOBJECT) {
            $cache_reject_reason = 'DONOTCACHCEOBJECT constant is defined';
            
            return false;
        }
        
        /**
         * Skip if key in non-persistent group
         */
        if (!$group) {
            $group = 'default';
        }
        
        if (in_array($group, $this->nonpersistent_groups)) {
            $cache_reject_reason = 'non-persistent group';
            
            return false;
        }
        
        return true;
    }
    
    /**
     * Check request URI
     *
     * @return boolean
     */
    function _check_request_uri()
    {
        $auto_reject_uri = array(
            'wp-login', 
            'wp-register'
        );
        
        foreach ($auto_reject_uri as $uri) {
            if (strstr($_SERVER['REQUEST_URI'], $uri) !== false) {
                return false;
            }
        }
        
        $reject_uri = $this->_config->get_array('objectcache.reject.uri');
        
        foreach ($reject_uri as $expr) {
            $expr = trim($expr);
            if ($expr != '' && preg_match('~' . $expr . '~i', $_SERVER['REQUEST_URI'])) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Check if we can start OB
     * 
     * @return boolean
     */
    function _can_ob()
    {
        /**
         * Object cache should be enabled
         */
        if (!$this->_config->get_boolean('objectcache.enabled')) {
            return false;
        }
        
        /**
         * Debug should be enabled
         */
        if (!$this->_config->get_boolean('objectcache.debug')) {
            return false;
        }
        
        /**
         * Skip if admin
         */
        if (defined('WP_ADMIN')) {
            return false;
        }
        
        /**
         * Skip if doint AJAX
         */
        if (defined('DOING_AJAX')) {
            return false;
        }
        
        /**
         * Skip if doing cron
         */
        if (defined('DOING_CRON')) {
            return false;
        }
        
        /**
         * Skip if APP request
         */
        if (defined('APP_REQUEST')) {
            return false;
        }
        
        /**
         * Skip if XMLRPC request
         */
        if (defined('XMLRPC_REQUEST')) {
            return false;
        }
        
        /**
         * Check for WPMU's and WP's 3.0 short init
         */
        if (defined('SHORTINIT') && SHORTINIT) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Returns debug info
     * 
     * @return string
     */
    function _get_debug_info()
    {
        $debug_info = "<!-- W3 Total Cache: Object Cache debug info:\r\n";
        $debug_info .= sprintf("%s%s\r\n", str_pad('Engine: ', 20), w3_get_engine_name($this->_config->get_string('objectcache.engine')));
        $debug_info .= sprintf("%s%d\r\n", str_pad('Total calls: ', 20), $this->cache_total);
        $debug_info .= sprintf("%s%d\r\n", str_pad('Cache hits: ', 20), $this->cache_hits);
        $debug_info .= sprintf("%s%d\r\n", str_pad('Cache misses: ', 20), $this->cache_misses);
        
        $debug_info .= "W3TC Object Cache info:\r\n";
        $debug_info .= sprintf("%s | %s | %s | %s | %s\r\n", str_pad('#', 5, ' ', STR_PAD_LEFT), str_pad('Caching (Reject reason)', 30, ' ', STR_PAD_BOTH), str_pad('Status', 15, ' ', STR_PAD_BOTH), str_pad('Source', 15, ' ', STR_PAD_BOTH), 'ID:Group');
        
        foreach ($this->debug_info as $index => $debug) {
            $debug_info .= sprintf("%s | %s | %s | %s | %s\r\n", str_pad($index + 1, 5, ' ', STR_PAD_LEFT), str_pad(($debug['caching'] ? 'enabled' : sprintf('disabled (%s)', $debug['reason'])), 30, ' ', STR_PAD_BOTH), str_pad(($debug['cached'] ? 'cached' : 'not cached'), 15, ' ', STR_PAD_BOTH), str_pad(($debug['internal'] ? 'internal' : 'persistent'), 15, ' ', STR_PAD_BOTH), sprintf('%s:%s', $debug['id'], $debug['group']));
        }
        
        $debug_info .= '-->';
        
        return $debug_info;
    }
}
