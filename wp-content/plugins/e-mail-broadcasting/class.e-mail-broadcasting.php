<?php


class EMailBroadcasting
{

	private $options = array();
	private $options_changed = FALSE;

	function __construct()
	{
		$this->options = get_option('e-mail-broadcasting', array());
  	}

	function register_template()
	{
		register_post_type('e-mail-broadcasting', array(
			'label' => __('Templates'),
			'public' => FALSE,
			'query_var' => FALSE,
			'can_export' => TRUE,
			'supports' => array('title', 'editor'),
			));
	}

	function __return_emailbroadcasting_html() { return 'text/html'; }
	function __return_emailbroadcasting_utf8() { return 'UTF-8'; }
	function __return_emailbroadcasting_from() { return $this->option('from'); }
	function __return_emailbroadcasting_from_name() { return $this->option('from_name'); }

	function mail_setup()
	{
		add_filter('wp_mail_content_type',	array($this, '__return_emailbroadcasting_html'));
		add_filter('wp_mail_charset',		array($this, '__return_emailbroadcasting_utf8'));
		add_filter('wp_mail_from',			array($this, '__return_emailbroadcasting_from'));
		add_filter('wp_mail_from_name',		array($this, '__return_emailbroadcasting_from_name'));
	}


	function is_session()
	{
		return (bool)($this->option('session_hash'));
	}


	function session_start()
	{
		$hash = $this->option('sess_hash');

		if (!(is_numeric($hash) && $hash > 0))
		{
			global $wpdb;
			$hash = time();
			$this->option('session_hash', $hash);
			$this->option('session_time', time()-(intval($this->option('interval'))*60));
			$querystr = 'UPDATE `'.$wpdb->prefix.'emailbroadcasting` SET `sess` = \''.$wpdb->escape($hash).'\';';
			$wpdb->query($querystr);
			$this->session_send(intval($this->option('send_immed')));
			return $hash;
		}
		return FALSE;
	}


	function session_close()
	{
		global $wpdb;
		$this->option('session_hash', '');
		$this->option('session_time', '');
		$querystr = 'UPDATE `'.$wpdb->prefix.'emailbroadcasting` SET `sess` = \'0\';';
		$wpdb->query($querystr);
		return TRUE;
	}

	function session_send($forced_start = FALSE)
	{

		$hash = (int)$this->option('session_hash');
		$time = (int)$this->option('session_time');
		$interval = (float)$this->option('interval');

		if (!(is_numeric($interval) && $interval > 1))
		{
			$interval = 1;
			$this->option('interval', $interval);
		}

		if ($forced_start === TRUE || ($hash && $time && ($time+($interval*60) <= time())))
		{
			global $wpdb;

			$template = $this->template($this->option('template'));

			if (!$template)
			{
				$this->session_close();
				return FALSE;
			}

			$limit = $this->option('limit');

			if (!(is_numeric($limit) && $limit > 1))
			{
				$limit = 100;
				$this->option('limit', $limit);
			}

			$querystr = 'SELECT `id`,`email` FROM `'.$wpdb->prefix.'emailbroadcasting`  WHERE NOT `deleted` AND `sess` = \''.$wpdb->escape($hash).'\' ORDER BY `id` ASC'.($limit?' LIMIT '.$limit:'');

			$rows = $wpdb->get_results($querystr, ARRAY_A);

			if ($rows)
			{
				$this->option('session_time', time());
				$ids = array();
				$this->mail_setup();

				foreach($rows as $email)
				{
					array_push($ids, (int)$email['id']);
					wp_mail($email['email'], $template->post_title, $template->post_content);
				}

				$ids_c = count($ids);

				if ($ids_c)
				{
					$querystr = 'UPDATE `'.$wpdb->prefix.'emailbroadcasting` SET `sess` = \'0\', `recieved` = `recieved` + 1 WHERE `id` IN ('.implode(',',$ids).') LIMIT '.$ids_c;
					$wpdb->query($querystr);
				}
				return $ids_c;
			}
			else
			{
				// There is no more emails in the quee, closing the active session
				$this->session_close();
			}
		}
	}

	function session_send_test()
	{

		$template = $this->template($this->option('template'));

		if (!$template)
		{
			$this->session_close();
			return FALSE;
		}

		$this->mail_setup();

		$emails = $this->option('test_list');

		if (is_scalar($emails))
			$emails = preg_split('/[\s,;]+/', $emails, -1, PREG_SPLIT_NO_EMPTY);

		$c = 0;

		foreach($emails as $email)
		{
			if (is_email($email))
			{
				wp_mail($email, $template->post_title, $template->post_content);
				$c++;
			}
		}

		return $c;
	}

	function session_info()
	{
		$hash = $this->option('session_hash');
		$time = $this->option('session_time');
		$limit = $this->option('limit');
		$interval = $this->option('interval');

		global $wpdb;

		$querystr = 'SELECT
						(SELECT count(*) FROM `'.$wpdb->prefix.'emailbroadcasting` WHERE sess = \''.$wpdb->escape($hash).'\') Remaining,
						(SELECT count(*) FROM `'.$wpdb->prefix.'emailbroadcasting` WHERE sess <> \''.$wpdb->escape($hash).'\') Elapsed
					LIMIT 1;';

		$result = $wpdb->get_results($querystr, ARRAY_N);
		$result = array_shift($result);
		$remaining = array_shift($result);
		$elapsed = array_shift($result);
		unset($result, $querystr);

		return array(
			'SESS' => $hash,
			'TIME_START' => $time,
			'TIME_END' => $remaining ? ($time+((ceil($remaining/$limit)*$interval)*60)) : time(),
			'TIME_NEXT' => $remaining ? ($time+$interval*60) : time(),
			'LIMIT' => $limit,
			'INTERVAL' => $interval,
			'REMAINING' => $remaining,
			'ELAPSED' => $elapsed,
			'TOTAL' => ($remaining+$elapsed),
			'PROGRESS' => floor($remaining
							? ($elapsed ? $elapsed/($remaining+$elapsed)*100 : 0)
							: 100)
			);
	}


	function import()
	{
		if (isset($_FILES['list']['tmp_name']) && $_FILES['list']['tmp_name']
			&& is_uploaded_file($_FILES['list']['tmp_name']) && is_readable($_FILES['list']['tmp_name'])
			&& isset($_FILES['list']['error']) && $_FILES['list']['error'] == 0
			&& isset($_FILES['list']['size']) && $_FILES['list']['size'] > 0)
		{
			$handle = fopen($_FILES['list']['tmp_name'], 'r');
			$chunk = fread($handle, 512);
			fclose($handle);
			clearstatcache();

			$c = 0;

			// let check if file is binary or not
			if (!(0 OR substr_count($chunk, "^ -~", (double)("^\r\n"))/512 > 0.3 OR substr_count($chunk, "\x00") > 0))
			{
				$c = $this->add(file_get_contents($_FILES['list']['tmp_name']));
			}
			unset($chunk);

			return $c;
		}

		return FALSE;

	}

	function import_from_comments()
	{
		global $wpdb;

		$querystr = '
			INSERT INTO `'.$wpdb->prefix.'emailbroadcasting` (`email`, `sess`, `added`)
			(
				SELECT DISTINCT
					`comment_author_email`, \'0\', NOW()
				FROM `'.$wpdb->prefix.'comments`
				WHERE `comment_author_email` NOT IN (SELECT DISTINCT `email` FROM `'.$wpdb->prefix.'emailbroadcasting`)
			)';

		return $wpdb->query($querystr);
	}

	function list_get($offset = 100, $limit = 100)
	{
		global $wpdb;

		$a_limit = '';

		if (is_numeric($limit) && $limit > 0)
			$a_limit = $limit;

		if (is_numeric($offset) && $offset >= 0)
			$a_limit = $offset . ($a_limit ? ',' . $a_limit : '');

		$querystr = 'SELECT * FROM `'.$wpdb->prefix.'emailbroadcasting`  WHERE NOT `deleted` ORDER BY `email` ASC '.($a_limit?' LIMIT '.$a_limit : '').';';

		return $wpdb->get_results($querystr, ARRAY_A);
	}

	function list_search($filter = '')
	{
		global $wpdb;

		$filter = $wpdb->escape($filter);
		$filter = strtr($filter, array('*' => '%', '?' => '_'));

		$querystr = 'SELECT * FROM `'.$wpdb->prefix.'emailbroadcasting` WHERE NOT `deleted` AND `email` '.($filter{0}==='!'?' NOT ':'').' LIKE \''.ltrim($filter,'! ').'\' ORDER BY `email` ASC;';

		return $wpdb->get_results($querystr, ARRAY_A);
	}

	function list_history($what = 'subscribed', $limit = 20)
	{
		global $wpdb;

		$limit = (int)$limit;

		if ($what == 'unsubscribed')
		{
			$querystr = 'SELECT * FROM `'.$wpdb->prefix.'emailbroadcasting` WHERE `deleted` ORDER BY `deleted` DESC LIMIT '.$limit.';';
		}
		elseif ($what == 'subscribed')
		{
			$querystr = 'SELECT * FROM `'.$wpdb->prefix.'emailbroadcasting` WHERE NOT `deleted` ORDER BY `added` LIMIT '.$limit.';';
		}
		else
		{
			return array();
		}
		return $wpdb->get_results($querystr, ARRAY_A);
	}

	function add($emails = '')
	{
		if (!is_scalar($emails))
			return 0;

		global $wpdb;
		$emails = preg_split('#[\\\/\s\,\;\|\:\"\']+#', $emails, -1, PREG_SPLIT_NO_EMPTY);

		$c = 0;
		foreach($emails as $email)
		{
			$email = trim($email, '\'"`.[]<>@#$!%^&*(){}|+=-/\ ');
			if (is_email($email))
			{
				$querystr = 'INSERT INTO `'.$wpdb->prefix.'emailbroadcasting` (`email`, `sess`, `added`) VALUES(\''.$wpdb->escape($email).'\', \'0\', NOW());';
				$c += (int)$wpdb->query($querystr);
			}
		}
		return $c;
	}


	function delete($emails = '')
	{
		if (!is_scalar($emails))
			return FALSE;

		global $wpdb;

		if (is_numeric($emails) && $emails > 0)
		{
			$querystr = 'DELETE FROM `'.$wpdb->prefix.'emailbroadcasting` WHERE `id` = '.$wpdb->escape($emails).' LIMIT 1;';
			return $wpdb->query($querystr);
		}
		elseif ((strpos($emails, '*') !== FALSE) || (strpos($emails, '?') !== FALSE))
		{
			$emails = $wpdb->escape($emails);
			$emails = strtr($emails, array('*' => '%', '?' => '_'));
			$querystr = 'DELETE FROM `'.$wpdb->prefix.'emailbroadcasting` WHERE `email` LIKE \''.$emails.'\';';
			return $wpdb->query($querystr);
		}
		else
		{
			$emails = preg_split('#[\s,;]+#', $emails, -1, PREG_SPLIT_NO_EMPTY);

			$querystr_i = array();

			foreach($emails as $email)
			{
				if (is_email($email))
				{
					array_push($querystr_i, '\''.$wpdb->escape(trim($email)).'\'');
				}
			}

			unset($emails);

			$c = count($querystr_i);

			if ($c)
			{
				$querystr = 'DELETE FROM `'.$wpdb->prefix.'emailbroadcasting` WHERE `email` IN ('.implode(', ', $querystr_i).') LIMIT '.$c.';';
				unset($querystr_i);
				return $wpdb->query($querystr);
			}
		}
		return FALSE;
	}

	function list_length()
	{
		static $t = NULL;
		if ($t === NULL)
		{
			global $wpdb;
			$querystr = 'SELECT COUNT(*) FROM `'.$wpdb->prefix.'emailbroadcasting`  WHERE NOT `deleted`';
			$t = $wpdb->get_col($querystr);
			$t = (int)array_shift($t);
		}
		return $t;
	}

	function template($id = 0, $data = NULL)
	{


		if ($data === NULL)
		{
			return get_post($id);
		}
		elseif ($data === 'delete')
		{
			if ($this->option('template') == $id)
				$this->option('template', 0);

			return wp_delete_post($id, TRUE);
		}
		elseif (is_array($data) && $data)
		{
			$template = array(
					'comment_status' => 'closed',
					'ping_status' => 'closed',
					'post_name' => '',
					'post_content' => '',
					'post_status' => 'draft',
					'post_title' => '',
					'post_type' => 'e-mail-broadcasting');
			$template = array_merge($template, $data);
			if ($id)
				$template['ID'] = $id;
			$id = wp_insert_post($template);
			return $id;
		}
		return NULL;
	}

	function template_list()
	{
		global $wpdb;
		$querystr = 'SELECT ID, post_name, post_title FROM '.$wpdb->posts.' WHERE post_type = \'e-mail-broadcasting\' ORDER BY post_date DESC, post_name ASC, post_title ASC';
		return $wpdb->get_results($querystr, OBJECT);
	}

	function option($name = NULL, $value = NULL)
	{
		if ($value === NULL)
		{
			return isset($this->options[$name])
					? $this->options[$name]
					: NULL;
		}
		else
		{
			$this->options[$name] = $value;

			$this->options_changed = TRUE;

			// @todo: this have be made only once if the options are changed, however with the shutdown action there are case (in wp_redirect()) that are not triggered the shutdown action
			update_option('e-mail-broadcasting', $this->options);

			return $value;
		}
	}

}

class EMailBroadcastingInit
{
	private static $EMailBroadcasting	= NULL;

	static function on_uninstall()
	{
		self::$EMailBroadcasting = new EMailBroadcasting;

		// Delete the options
		delete_option('e-mail-broadcasting');

		// Delete all templates
		foreach (self::$EMailBroadcasting->template_list() as $template)
			wp_delete_post($template->ID, TRUE);

		// Drop the database;
		global $wpdb;
		$querystr = 'DROP TABLE IF EXISTS `'.$wpdb->prefix.'emailbroadcasting`';
		$wpdb->query($querystr);

		return TRUE;
	}

	static function on_activate()
	{
		self::$EMailBroadcasting = new EMailBroadcasting;

		// Setting the default option values
		$options = array(
			'session_hash' => 0,
			'session_time' => 0,
			'limit' => 100,
			'from' => get_bloginfo('admin_email'),
			'from_name' => get_bloginfo('admin_email_name'),
			'display_limit' => 300,
			'cron_run' => 'admins',
			'cron_hash' => md5(time()),
			'send_immed' => 1,
			'export_delimeter' => '\n',
			'template' => 0,
			'test_list' => get_bloginfo('admin_email')
		);
		add_option('e-mail-broadcasting', $options, '', 'yes');

		// Create the database
		global $wpdb;
		$querystr = '
			CREATE TABLE IF NOT EXISTS `'.$wpdb->prefix.'emailbroadcasting`
			(
				`id` INT(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
				`email` VARCHAR(128) NOT NULL DEFAULT \'\' UNIQUE,
				`sess` INT(10) NOT NULL DEFAULT \'0\',
				`added` DATE NOT NULL DEFAULT \'0000-00-00\',
				`recieved` INT(10) NOT NULL DEFAULT \'0\',
				`list` INT(10) UNSIGNED NOT NULL DEFAULT \'1\',
				`deleted` DATE NOT NULL default \'0000-00-00\'
			)  AUTO_INCREMENT=1 DEFAULT CHARSET=UTF8;';
		$wpdb->query($querystr);

		// Migrate old settings
		if (self::$EMailBroadcasting->option('version') &&
				0.2 > floatval(self::$EMailBroadcasting->option('version')))
		{
			// Convert old settings
			self::$EMailBroadcasting->template(0, array(
				'post_title' => get_option('emailbroadcasting_last_title'),
				'post_name' => get_option('emailbroadcasting_last_title'),
				'post_content' => get_option('emailbroadcasting_last_text')));
			self::$EMailBroadcasting->option('test_list', get_option('emailbroadcasting_last_test_list'));
			self::$EMailBroadcasting->option('from', get_option('emailbroadcasting_from'));
			self::$EMailBroadcasting->option('from_name', get_option('emailbroadcasting_from_name'));
			self::$EMailBroadcasting->option('cron_run', 'admins');
			self::$EMailBroadcasting->option('cron_hash', get_option('emailbroadcasting_triggerhash'));
			self::$EMailBroadcasting->option('version', 0.2);

			// Removing old settings from the DB
			delete_option('emailbroadcasting_session_sess');
			delete_option('emailbroadcasting_session_time');
			delete_option('emailbroadcasting_settings_limit');
			delete_option('emailbroadcasting_settings_interval');
			delete_option('emailbroadcasting_settings_from');
			delete_option('emailbroadcasting_settings_from_name');
			delete_option('emailbroadcasting_settings_display_limit');
			delete_option('emailbroadcasting_settings_admin_only');
			delete_option('emailbroadcasting_settings_triggerhash');
			delete_option('emailbroadcasting_version');

			// Modify the DB
			$querystr = '
				ALTER TABLE `'.$wpdb->prefix.'emailbroadcasting`
				ADD COLUMN `list` INT(10) UNSIGNED NOT NULL DEFAULT \'1\',
				ADD COLUMN `deleted` DATE NOT NULL DEFAULT \'0000-00-00\'';
			$wpdb->query($querystr);
		}
	}

	static function on_deactivate()
	{
		self::$EMailBroadcasting = new EMailBroadcasting;

		self::$EMailBroadcasting->session_close();
	}
}
