<?php
declare(strict_types=1);

namespace n0nag0n;

class Environment_Check extends \Prefab {

	protected $html_check = '&#10003;';
	protected $html_x = '&#10007;';

	protected $is_windows_os;

	public function __construct() {
		$fw = \Base::instance();
		$fw->route('GET /environment-check', '\n0nag0n\Environment_Check->indexAction');

		$this->is_windows_os = false;
		if(strcasecmp(substr(PHP_OS, 0, 3), 'WIN') == 0){
			$this->is_windows_os = true;
		}
	}

	public function indexAction(\Base $fw): void {

		$php_configs = $this->getPhpConfigs();
		$project_configs = $this->getProjectConfigs();
		$host_configs = $this->getHostConfigs();
		$db_configs = $this->getDbConfigs();

		$fw->set('db_configs', $db_configs);
		$fw->set('host_configs', $host_configs);
		$fw->set('project_configs', $project_configs);
		$fw->set('php_configs', $php_configs);
		$old_ui = $fw->UI;
		$fw->UI = __DIR__.'/../ui/';
		echo \Template::instance()->render('index.htm');
		$fw->UI = $old_ui;
	}

	protected function getPhpConfigs(): array {
		$php_configs = [];

		$php_configs[] = [
			'title' => 'PHP Version',
			'value' => phpversion(),
			'class' => (PHP_VERSION_ID < 70000 ? 'danger' : (PHP_VERSION_ID > 70300 ? 'success' : 'warning')),
			'note' => (PHP_VERSION_ID < 70000 ? 'Your PHP version is less than PHP 7. You should consider upgrading to at least PHP 7 if not PHP 7.3' : (PHP_VERSION_ID > 70300 ? '' : 'You should consider upgrading to at least PHP 7.3')),
		];

		$is_utf8 = ini_get('default_charset') === 'UTF-8';
		$php_configs[] = [
			'title' => 'UTF-8 Charset',
			'value' => ($is_utf8 ? $this->html_check : $this->html_x),
			'class' => ($is_utf8 ? 'success' : 'warning'),
			'note' => ($is_utf8 === false ? 'UTF-8 is the current standard to be used when handling data for multiple languages and regions.' : ''),
		];

		$has_json = extension_loaded('json');
		$php_configs[] = [
			'title' => 'JSON Extension',
			'value' => ($has_json ? $this->html_check : $this->html_x),
			'class' => ($has_json ? 'success' : 'warning'),
			'note' => ($has_json === false ? 'JSON is now such a common format of communication, it is important to make sure your PHP install has this loaded.' : ''),
		];

		$has_mbstring = extension_loaded('mbstring');
		$php_configs[] = [
			'title' => 'Mbstring Extension',
			'value' => ($has_mbstring ? $this->html_check : $this->html_x),
			'class' => ($has_mbstring ? 'success' : 'warning'),
			'note' => ($has_mbstring === false ? 'Mbstring is an important extension if you plan on having multiple languages in your app.' : ''),
		];

		$has_openssl = extension_loaded('openssl');
		$php_configs[] = [
			'title' => 'openssl Extension',
			'value' => ($has_openssl ? $this->html_check : $this->html_x),
			'class' => ($has_openssl ? 'success' : 'warning'),
			'note' => ($has_openssl === false ? 'OpenSSL is critical in many applications for securely making requests across the web.' : ''),
		];

		$has_pdo = extension_loaded('pdo');
		$php_configs[] = [
			'title' => 'PDO Database Extension',
			'value' => ($has_pdo ? $this->html_check : $this->html_x),
			'class' => ($has_pdo ? 'success' : 'warning'),
			'note' => ($has_pdo === false ? 'PDO is the standard used across PHP projects.' : ''),
		];

		$has_mysqlnd = extension_loaded('mysqlnd');
		$php_configs[] = [
			'title' => 'MySQL Native Driver Extension',
			'value' => ($has_mysqlnd ? $this->html_check : $this->html_x),
			'class' => ($has_mysqlnd ? 'success' : 'warning'),
			'note' => ($has_mysqlnd === false ? 'MySQL Native Driver is the latest version that has the most features that are usable with PDO.' : ''),
		];

		$has_sqlite3 = extension_loaded('sqlite3');
		$php_configs[] = [
			'title' => 'SQLite3 Extension',
			'value' => ($has_sqlite3 ? $this->html_check : $this->html_x),
			'class' => ($has_sqlite3 ? 'success' : 'warning'),
			'note' => ($has_sqlite3 === false ? 'If you are using SQLite3 as your database, you should have this extension installed.' : ''),
		];

		$has_igbinary = extension_loaded('igbinary');
		$php_configs[] = [
			'title' => 'igbinary Extension',
			'value' => ($has_igbinary ? $this->html_check : $this->html_x),
			'class' => ($has_igbinary ? 'success' : 'warning'),
			'note' => ($has_igbinary === false ? 'igbinary is a PHP extension that allows data to be serealized quickly and with a smaller binary footprint. Fat-Free will automatically use this if this extension is installed.' : 'Fat-Free will automatically use this if this extension is installed.'),
		];

		$has_curl = extension_loaded('curl');
		$php_configs[] = [
			'title' => 'cURL Extension',
			'value' => ($has_curl ? $this->html_check : $this->html_x),
			'class' => ($has_curl ? 'success' : 'warning'),
			'note' => ($has_curl === false ? 'cURL is used in many extensions and within the Fat-Free Web module.' : ''),
		];

		return $php_configs;
	}

	protected function getProjectConfigs(): array {
		$fw = \Base::instance();
		$project_configs = [];

		$standard_version = intval(str_pad(str_replace('.', '0', \Base::VERSION), 5, '0'));
		$is_good_version = $standard_version > 30700;
		$is_bad_version = $standard_version < 30600;
		$project_configs[] = [
			'title' => 'Framework Version',
			'value' => \Base::VERSION,
			'class' => ($is_bad_version ? 'danger' : ($is_good_version ? 'success' : 'warning')),
			'note' => ($is_bad_version ? 'Your framework version is less than 3.6. You should consider upgrading to the latest version to take advantage of the latest features and security fixes.' : ($is_good_version ? '' : 'You should consider upgrading to the latest version.')),
		];

		$tmp_is_writable = is_writable($fw->TEMP);
		$project_configs[] = [
			'title' => 'Tmp Directory Writable',
			'value' => ($tmp_is_writable ? $this->html_check : $this->html_x),
			'class' => ($tmp_is_writable ? 'success' : 'warning'),
			'note' => ($tmp_is_writable === false ? 'The TEMP directory in Fat-Free needs to be writable or you will not be able to have cached files and data saved there.' : ''),
		];

		if($fw->LOGS) {
			$logs_is_writable = is_writable($fw->LOGS);
			$project_configs[] = [
				'title' => 'Logs Directory Writable',
				'value' => ($logs_is_writable ? $this->html_check : $this->html_x),
				'class' => ($logs_is_writable ? 'success' : 'warning'),
				'note' => ($logs_is_writable === false ? 'The LOGS directory in Fat-Free needs to be writable or logs will not be able to be written to there.' : ''),
			];
		}

		return $project_configs;
	}

	protected function getHostConfigs(): array {
		$host_configs = [];
		
		if($this->is_windows_os === false) {
			$host_configs[] = [
				'title' => 'Linux Kernel Version',
				'value' => exec('uname -r'),
				'class' => 'success',
				'note' => '',
			];

			$host_configs[] = [
				'title' => 'Total CPUs',
				'value' => exec("cat /proc/cpuinfo | grep processor | wc -l"),
				'class' => 'success',
				'note' => '',
			];

			$host_configs[] = [
				'title' => 'Total RAM',
				'value' => round(exec("cat /proc/meminfo | grep MemTotal | tr -s ' ' | cut -d ' ' -f 2") / 1024).' MB',
				'class' => 'success',
				'note' => '',
			];
		}

		$is_https = $_SERVER['HTTPS'] === 'on';
		$host_configs[] = [
			'title' => 'HTTPS Secure Protocol',
			'value' => ($is_https ? $this->html_check : $this->html_x),
			'class' => ($is_https ? 'success' : 'warning'),
			'note' => ($is_https === false ? 'Now that SSL certs are easy and free to get (LetsEncrypt for example), it is important that you serve your site with an SSL certificate installed. This also has benefits with SEO.' : ''),
		];

		return $host_configs;
	}

	protected function getDbConfigs(): array {
		$db_configs = [];
		$has_mysql = $this->hasCliCommandInstalled('mysql');
		if($has_mysql === true) {
			$db_configs[] = [
				'title' => 'MySQL Version',
				'value' => exec(($this->is_windows_os ? 'mysql.exe' : 'mysql').' --version'),
				'class' => 'success',
				'note' => '',
			];
		}

		return $db_configs;
	}

	protected function hasCliCommandInstalled(string $app): bool {
		$arg_safe = escapeshellarg(($this->is_windows_os ? $app.'.exe' : $app));
		if($this->is_windows_os) {
			return !empty(exec('where.exe '.$arg_safe));
		} else {
			return !empty(exec('command -v '.$arg_safe));
		}
	}
}