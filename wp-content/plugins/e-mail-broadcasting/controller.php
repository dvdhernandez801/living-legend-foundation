<?php

class EmailBroadcastingController
{
	private $EMailBroadcasting	= NULL;

	private $_tab = 'publish';
	private $_paged = 0;
	private $_template = 0;

	public function __construct()
	{
	}

	public function bootstrap()
	{

		$this->EMailBroadcasting = new EMailBroadcasting;

		$this->check_session();

		$this->EMailBroadcasting->register_template();

		if (isset($_POST['e-mail-broadcasting-mail']) && isset($_POST['e-mail-broadcasting-action']))
		{
			$this->ajax(array($_POST['e-mail-broadcasting-action'], $_POST['e-mail-broadcasting-mail']));
		}
		elseif (isset($_POST['e-mail-broadcasting-ajax-del']))
		{
			$this->ajax(array('del', $_POST['e-mail-broadcasting-ajax-del']));
		}

		// This won't work while using wp_redirect()
		//add_action('shutdown', array($this->EMailBroadcasting, 'save_options')); // normal destructor won't work corectly, i don't know why?!
		add_action('admin_menu', array($this, 'admin_menu'));
		add_filter('plugin_action_links', array($this, 'action_links'), 10, 2);
		add_action('widgets_init', array($this, 'register_widgets'));

		$this->_tab = isset($_GET['tab']) && is_scalar($_GET['tab']) ? $_GET['tab'] : 'publish';
		$this->_paged = isset($_GET['paged']) && is_numeric($_GET['paged']) && $_GET['paged'] > 0 ? $_GET['paged'] : 1;
		$this->_template = isset($_GET['template']) && is_numeric($_GET['template']) && $_GET['template'] > 0 ? $_GET['template'] : 0;


		if ($this->_tab)
		{
			wp_enqueue_script('post');
			wp_enqueue_script('postbox');
			wp_enqueue_script('thickbox');
			wp_enqueue_style('thickbox');
		}

		// load all assets that needs for good interface working
		// must be init before outputing
		$this->tab_model();
	}

	function register_widgets()
	{
		register_widget('EMailBroadcasting_Widget');
	}

	public function url($params = '')
	{
		return admin_url('tools.php?page='.URL_E_MAIL_BROADCASTING.($params?'&'.$params:''));
	}

	public function admin_menu()
	{
		$page = add_submenu_page('tools.php', __('E-Mail Broadcasting', URL_E_MAIL_BROADCASTING), __('E-Mail Broadcasting', URL_E_MAIL_BROADCASTING), 'activate_plugins', URL_E_MAIL_BROADCASTING, array(&$this, 'tab'));
	}

	function action_links($links, $file)
	{
		if ($file == PATH_E_MAIL_BROADCASTING)
		{
			array_push($links, '<a href="'.$this->url('tab=settings').'">'.__('Settings').'</a>');
		}
		return $links;
	}

	public function message($params = array())
	{
		$messageid = array_shift($params);
		$param = array_shift($params);
		unset($params);

		$messages = array(
			1 => sprintf(_n('%s email added.', '%s emails added.', $param, URL_E_MAIL_BROADCASTING), $param),
			2 => sprintf(_n('%s email imported successfull.', '%s emails imported successfull.', $param, URL_E_MAIL_BROADCASTING), $param),
			3 => __('Import unsuccessfull. File is not correct csv type', URL_E_MAIL_BROADCASTING),
			4 => sprintf(_n('%s email deleted.', '%s emails deleted.', $param, URL_E_MAIL_BROADCASTING), $param),
			5 => __('You try to delete all emails in the list, however this is imposible by the security reasons. Try if you really want this try with wildcard &quot;<em>*?</em>&quot;', URL_E_MAIL_BROADCASTING),
			6 => __('Settings saved', URL_E_MAIL_BROADCASTING),
			10 => __('Template saved', URL_E_MAIL_BROADCASTING),
			11 => sprintf(_n('Test sent to %s email', 'Test sent to %s emails', $param, URL_E_MAIL_BROADCASTING), $param),
			12 => __('Session starting...', URL_E_MAIL_BROADCASTING),
			13 => __('Template deleted.', URL_E_MAIL_BROADCASTING),
			14 => __('Must select list to send the mails.', URL_E_MAIL_BROADCASTING));

		if (isset($messages[$messageid]))
			echo '<div id="message" class="updated"><p>'.$messages[$messageid].'</p></div>';
	}


	public function ajax($parameters = array())
	{
		if (!is_array($parameters))
			$parameters = array();

		$action = array_shift($parameters);
		$param	= array_shift($parameters);

		switch($action)
		{

			case 'subscribe':
				return (is_email($param) && $this->EMailBroadcasting->add($param));

			case 'unsubscribe':
				return (is_email($param) && $this->EMailBroadcasting->delete($param));

			case 'tbody':
				if (!is_admin()) break;
				$this->tbody($param);
				exit;

			case 'del':
				if (!is_admin()) break;
				echo $this->EMailBroadcasting->delete($param);
				exit;

			case 'export':
				if (!is_admin()) break;

				header('Content-Disposition: attachment; filename="e-mail-broadcasting.'.$this->EMailBroadcasting->list_length().'.list.txt"', TRUE);
				header('Content-Transfer-Encoding: binary', TRUE);
				header('Content-Description: File Transfer');
				header('Cache-control: private', TRUE);

				$param = strtr($param, array('\r' => "\r", '\n' => "\n", '\t' => "\t", '\0' => "\0", '\v' => "\v", '\e' => "\e"));

				foreach($this->EMailBroadcasting->list_get(0,0) as $email)
				{
					echo $email['email'].$param;
				}
				exit;
		}
	}

	public function tab()
	{

		echo '
		<style>
			#e-mail-broadcasting-le-0 td { font-size: 1.2em; color: #aaa; text-align: center; padding: 14px; font-weight: bolder; }
			.e-mail-broadcasting-col-del { width: 16px; }
			.e-mail-broadcasting-col-del a { background: transparent url(images/xit.gif) 100px 50% no-repeat; float: left; width: 10px; height: 16px; }
			tr:hover .e-mail-broadcasting-col-del a { display: block; background-position: 0 50%; }
			tr:hover .e-mail-broadcasting-col-del a:hover { display: block; background-position: 100% 50%; }
			#icon-e-mail-broadcasting { background:transparent url('.WP_PLUGIN_URL.'/'.URL_E_MAIL_BROADCASTING.'/email-icon.png) 50% 50% no-repeat; }
		</style>
		<div class="wrap">
			'.  get_screen_icon(URL_E_MAIL_BROADCASTING).'
			<h2 class="nav-tab-wrapper">
				<span>'.__('E-Mail Broadcasting', URL_E_MAIL_BROADCASTING).' &nbsp; </span>
				<a href="'.$this->url().'" class="nav-tab'.($this->_tab=='publish'?' nav-tab-active':'').'">'.__('Publish').'</a>
				<a href="'.$this->url('tab=settings').'" class="nav-tab'.($this->_tab=='settings'?' nav-tab-active':'').'">'.__('Settings').' & '.__('List').'</a>
				<a href="'.$this->url('tab=history').'" class="nav-tab'.($this->_tab=='history'?' nav-tab-active':'').'">'.__('History').'</a>
			</h2>';

		if (isset($_GET['msg']))
		{
			echo $this->message(explode(',',$_GET['msg']));
		}
		else
		{
			echo '<div class="clear">&nbsp;</div>';
		}

		if ($this->EMailBroadcasting->is_session())
		{
			$info = $this->EMailBroadcasting->session_info();

			echo '<script>setTimeout("location.reload()", '.(($info['TIME_NEXT']-time()+3)*1000).');</script>';
			echo '<p><strong>'.__('Start').':</strong> '. date_i18n('Y-m-d H:i:s', $info['TIME_START']).'</p>';
			echo '<p><strong>'.__('Next send').':</strong> '. date_i18n('Y-m-d H:i:s', $info['TIME_NEXT']).'</p>';
			echo '<p><strong>'.__('End').':</strong> '. date_i18n('Y-m-d H:i:s', $info['TIME_END']).'</p>';
			echo '<p><strong>'.__('Limit').':</strong> '. $info['LIMIT'].'</p>';
			echo '<p><strong>'.__('Interval').':</strong> '. $info['INTERVAL'].'m. </p>';
			echo '<p><strong>'.__('Elapsed').':</strong> '. $info['ELAPSED'].'</p>';
			echo '<p><strong>'.__('Remaining').':</strong> '. $info['REMAINING'].'</p>';
			echo '<p><strong>'.__('Total').':</strong> '. $info['TOTAL'].'</p>';
			if ($info['PROGRESS'])
			{
				echo '<div style="border:1px solid #ddd;background:#eee url(images/white-grad-active.png);padding:1px;height:24px;width:200px;overflow:hidden;border-radius:3px;"><div style="background:#777 url(images/fav-vs.png);width:'.($info['PROGRESS']*2).'px;height:21px;float:left;border-radius:2px;text-shadow:1px 1px 0 #333;font-size:14px;padding:3px 4px 0 4px;text-align:center;color:#eee;">'.$info['PROGRESS'].'%</div></div>';
			}
			echo '<p>&nbsp;</p><p class="submitbox"><a href="'.$this->url('stop-sending='.time()).'" class="submitdelete deletion">Stop sending now</a></p>';
		}
		else
		{
			if ($this->_tab === 'settings')
			{
				$display_limit = $this->EMailBroadcasting->option('display_limit');
				$display_limit = $display_limit ? $display_limit : 100;
				$total_pages = ceil($this->EMailBroadcasting->list_length()/$display_limit);
				$search = isset($_GET['s']) && is_scalar($_GET['s']) ? $_GET['s'] : NULL;
				include 'views.tab-settings.php';
			}
			elseif ($this->_tab === 'history')
			{
				include 'views.tab-history.php';
			}
			else
			{
				$template = NULL;

				if (!($template = $this->EMailBroadcasting->template($this->_template)))
				{
					$template = (object)array('post_title' => '', 'post_content' => '', 'post_name' => '');
					$this->_template = NULL;
				}

				$template_list = $this->EMailBroadcasting->template_list();

				include 'views.tab-publish.php';
			}
		}
		echo '
		</div>';
	}

	public function tbody($page_or_text = 0, $is_filter = FALSE)
	{
		if ($is_filter)
		{
			$list = $this->EMailBroadcasting->list_search($page_or_text);
		}
		else
		{
			$page = intval($page_or_text);
			if ($page <= 0)
				$page = 1;
			$page--;

			$limit = $this->EMailBroadcasting->option('display_limit');
			if (!$limit) $limit = 100;
			$offset = floor($page*$limit);
			$list = $this->EMailBroadcasting->list_get($offset, $limit);
		}

		$i = 0;
		foreach($list as $email)
		{
			echo '<tr id="e-mail-broadcasting-le-'.$email['id'].'" '.($i%2===0?' class="alternate"':'').'>';
			echo '<td class="e-mail-broadcasting-col-del"><a href="javascript:emailbroadcasting_del('.$email['id'].')" title="'.__('Delete').'"> </a></td>';
			echo '<td>'.$email['email'].'</td>';
			echo '<td class="e-mail-broadcasting-col-recieved">'.$email['recieved'].'</td>';
			echo '<td class="e-mail-broadcasting-col-added">'.$email['added'].'</td>';
			echo '</tr>';
			$i++;
		}
		if ($i < 1)
		{
			echo '
				<tr id="e-mail-broadcasting-le-0">
					<td colspan="4">
						'.__('No results found.').'
					</td>
				</tr>';
		}
		return $i;
	}


	private function tab_model()
	{

		if ($this->EMailBroadcasting->is_session())
			return FALSE;

		// Template deleting
		if (isset($_GET['delete-template']) && is_numeric($_GET['delete-template']))
		{
			$this->EMailBroadcasting->template($_GET['delete-template'], 'delete');
			wp_redirect($this->url('tab=publish&msg=13'));
		}

		// Saving template
		// Sending test mail
		// Sending real list mails
		elseif (isset($_POST['title']) && isset($_POST['content']) && isset($_POST['name']))
		{
			$_POST['title'] = trim(stripslashes($_POST['title']));
			$_POST['content'] = trim(stripslashes($_POST['content']));
			$_POST['name'] = $_POST['name'] ? trim(stripslashes($_POST['name'])) : $_POST['title'];

			$this->_template = $this->EMailBroadcasting->template(
													(($this->_template && is_numeric($this->_template) && $this->_template > 0)?$this->_template:0),
													array(
														'post_title' => (string)$_POST['title'],
														'post_content' => (string)$_POST['content'],
														'post_name' => (string)$_POST['name']));
			if (isset($_POST['test_list']))
				$this->EMailBroadcasting->option('test_list', $_POST['test_list']);

			if (isset($_POST['#send']))
			{
				$this->EMailBroadcasting->option('template', $this->_template);
				if (isset($_POST['send_to']))
				{
					if ($_POST['send_to'] === 'test')
					{
						$sent = $this->EMailBroadcasting->session_send_test();
						wp_redirect($this->url('tab=publish&msg=11,'.$sent));

					}
					elseif ($_POST['send_to'] === 'list')
					{
						$this->EMailBroadcasting->session_start();
						wp_redirect($this->url('tab=publish&msg=12'));
					}
				}
				else
				{
					wp_redirect($this->url('tab=publish&template='.$this->_template.'&msg=14'));
				}
			}
			elseif (isset($_POST['#save_template']))
			{
				wp_redirect($this->url('tab=publish&template='.$this->_template.'&msg=10'));
			}
		}

		// Add new email
		if (isset($_POST['#add']))
		{
			if (isset($_POST['email']))
			{
				$added = $this->EMailBroadcasting->add($_POST['email']);
				wp_redirect($this->url('tab=settings&msg=1,'.$added));
			}
		}

		// Import emails from file
		elseif (isset($_POST['#import']))
		{
			$imported = $this->EMailBroadcasting->import();
			wp_redirect($this->url('tab=settings&msg=2,'.$imported));
		}

		// Import commenter's emails
		elseif (isset($_POST['#import-from-comments']))
		{
			$imported =  $this->EMailBroadcasting->import_from_comments();
			wp_redirect($this->url('tab=settings&msg=2,'.$imported));
		}

		// Export to file
		elseif (isset($_POST['#export']) && isset($_POST['export_delimeter']))
		{
			$this->EMailBroadcasting->option('export_delimeter', trim($_POST['export_delimeter']));
			$this->ajax(array('export', $this->EMailBroadcasting->option('export_delimeter')));
		}

		// Delete mails
		elseif (isset($_POST['#del']) && isset($_POST['wildcard']))
		{
			$_POST['wildcard'] = trim($_POST['wildcard']);
			if ($_POST['wildcard'] == '*')
			{
				wp_redirect($this->url('tab=settings&msg=5'));
			}
			else
			{
				$deleted = $this->EMailBroadcasting->delete($_POST['wildcard']);
				wp_redirect($this->url('tab=settings&msg=4,'.$deleted));
			}
		}

		elseif (isset($_POST['#settings']))
		{
			$optlist = array('from', 'from_name', 'interval', 'limit', 'cron_run', 'cron_hash', 'send_immed');

			foreach($optlist as $key)
				$this->EMailBroadcasting->option($key, (isset($_POST[$key]) ? $_POST[$key] : ''));

			wp_redirect($this->url('tab=settings&msg=6'));
		}

		elseif (isset($_POST['display_limit']))
		{
			$this->EMailBroadcasting->option('display_limit', $_POST['display_limit']);
			wp_redirect($this->url('tab=settings'));
		}

	}

	public function check_session()
	{
		if ($this->EMailBroadcasting->is_session())
		{
			$cron_run = $this->EMailBroadcasting->option('cron_run');
			if ($cron_run === 'users')
			{
				$this->EMailBroadcasting->session_send();
				// have to be invisible for enduser
			}
			elseif ($cron_run === 'admins' && is_admin())
			{
				if (isset($_GET['stop-sending']))
				{
					$this->EMailBroadcasting->session_close();
					wp_redirect($this->url('tab='.$this->_tab));
				}
				else
				{
					$this->EMailBroadcasting->session_send();
					// May be have to set some data, dunno ;)
				}
			}
			elseif ($cron_run === 'hash' && $_GET['e-mail-broadcasting'] === $this->EMailBroadcasting->option('cron_hash')
					&& $_GET['e-mail-broadcasting'])
			{
				$this->EMailBroadcasting->session_send();

				// There is no sense to continue
				exit;
			}
			return TRUE;
		}

		return FALSE;
	}

}