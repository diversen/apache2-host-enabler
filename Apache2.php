<?php

namespace Diversen;

class Apache2 {


    private static $logDir = 'logs';
    private static $htdocsDir = 'htdocs';
    /**
     * Creates logs in cwd
     */
    private static function createLogs($hostname) {
        $cwd = getcwd();

        $log_dir = "$cwd/" . self::$logDir;
        $htdocs_dir = "$cwd/" . self::$htdocsDir;

        self::execCommand("mkdir -p $log_dir");
        self::execCommand("mkdir -p $htdocs_dir");

        $test_file = <<<EOF
<?php 

echo "hello world from $hostname";
EOF;

        if (!file_exists($htdocs_dir . "/index.php")) {
            file_put_contents($htdocs_dir . "/index.php", $test_file);
        }
        
        $access_log = $log_dir ."/access.log";
        $error_log = $log_dir . "/error.log";
        
        touch($access_log);
        touch($error_log);
    }

    /**
     * Get apache2 configuration by servername and scheme (http or)
     */
    private static function getA2Conf($SERVER_NAME, $https = true) {
        $current_dir = getcwd();
        $DOCUMENT_ROOT = $current_dir . "/" . self::$htdocsDir;
        $APACHE_LOG_ROOT = $current_dir . "/" . self::$logDir;

        $apache_str = self::getConf($SERVER_NAME, $DOCUMENT_ROOT, $APACHE_LOG_ROOT);
 
        return $apache_str;
    }

    /**
     * 
     */
    public static function enableSite($hostname, $options = []) {

        if (isset($options['htdocs'])) {
            self::$htdocsDir = $options['htdocs'];
        }
        
        // Need root
        self::needRoot();

        // Create logs
        self::createLogs($hostname);
        
        // Get configuration
        $apache2_conf = self::getA2Conf($hostname, $options);

        // Create tmp host 
        $tmp_file = "/tmp/$hostname";

        // Put contents in tmp file
        file_put_contents($tmp_file, $apache2_conf);
        
        // Real apache2 conf file
        $apache2_conf_file = "/etc/apache2/sites-available/$hostname.conf";;

        // Create real apache2 conf file
        self::execCommand("cp -f /tmp/$hostname $apache2_conf_file");
        
        // Enable host
        self::execCommand("a2ensite $hostname");

        // Add host to /etc/hosts
        $hosts_file_str = file_get_contents("/etc/hosts");
        
        //Host string ot add to /etc/hosts
        $host_str = "127.0.0.1\t$hostname\n";

        // Add if host is not is /etc/hosts
        if (!strstr($hosts_file_str, $host_str)) {
            $new_hosts_file_str = $host_str . $hosts_file_str;
            file_put_contents("/tmp/hosts", $new_hosts_file_str);
            self::execCommand("cp -f /tmp/hosts /etc/hosts");
        }

        // Reload configuration
        self::execCommand("/etc/init.d/apache2 reload");

        // Chown to user running the sudo command
        $user = get_current_user();
        self::execCommand("chown -R $user:$user .");
    }

    /**
     * Check if user is root
     */
    public static function isRoot() {
        if (!function_exists('posix_getuid')){
            return true;
        }
        if (0 == posix_getuid()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check if user is root
     */
    public static function needRoot($str = '') {

        $output = '';
        $output.= "Current command needs to be run as root. E.g. with sudo: ";
        if (!empty($str)) {
            $output.="\nsudo $str";
        }

        if (!self::isRoot()) {
            echo $output . PHP_EOL;
            exit(128);
        }
        return 0;
    }

    public static function execCommand($command) {
        passthru($command);
    }


    /**
     * Method that disable an apache2 site
     * 
     * @param array $options only options is $options[sitename] 
     */
    public static function disableSite($hostname) {

        self::needRoot();

        $apache2_conf_file = "/etc/apache2/sites-available/$hostname.conf";
        $ret = self::execCommand("a2dissite $hostname");
        if ($ret) {
            return false;
        }

        $ret = self::execCommand("rm -f $apache2_conf_file");

        // create new hosts file and reload server
        $host_file_str = '';
        $hosts_file_str = file("/etc/hosts");

        $host_search = "127.0.0.1\t$hostname\n";
        foreach ($hosts_file_str as $key => $val) {
            if (strstr($val, $host_search)) {
                continue;
            } else {
                $host_file_str.=$val;
            }
        }
        file_put_contents("/tmp/hosts", $host_file_str);

        self::execCommand("cp -f /tmp/hosts /etc/hosts");
        self::execCommand("/etc/init.d/apache2 reload");
    }

    /**
     * Gets the running version of apache2
     * 
     * @return string $version e.g. 2.4.6
     */
    public static function getVersion() {
        exec('apache2 -v', $ary, $ret);
        $line = $ary[0];
        $ary = explode(':', $line);
        $ary[1];
        $ary = explode('/', $ary[1]);
        preg_match("/\d+(\.\d+)+/", $ary[1], $matches);
        return $matches[0];
    }

    /**
     * Get apache2 configuration as a string for http
     * 
     * @param string $SERVER_NAME
     * @param string $DOCUMENT_ROOT
     * @param string $APACHE_LOG_ROOT
     * @return string $conf a apache2 conf file
     */
    public static function getConf($SERVER_NAME, $DOCUMENT_ROOT, $APACHE_LOG_ROOT) {

        return <<<EOD
<VirtualHost *:80>
    ServerAdmin webmaster@example.com
    ServerName  {$SERVER_NAME}

    # Indexes + Directory Root.
    DirectoryIndex index.php
    DocumentRoot {$DOCUMENT_ROOT}
  
    <Directory {$DOCUMENT_ROOT}>
        Options FollowSymLinks
        AllowOverride All
        Require all granted
        RewriteEngine on
        RewriteBase /
        RewriteCond %{REQUEST_FILENAME} !-f
        RewriteCond %{REQUEST_FILENAME} !-d
        RewriteRule ^(.*)$ index.php?q=$1 [L,QSA]
    </Directory>

<Files *.inc>
deny from all
</Files>

<Files info.php>
deny from all
allow from 127.0.0.1
</Files>

<Files *.sql>
deny from all
</Files>

<Files *.ini>
deny from all
</Files>
   
    ErrorLog  {$APACHE_LOG_ROOT}/error.log
    CustomLog {$APACHE_LOG_ROOT}/access.log combined
</VirtualHost>
EOD;
    }
}

