<?php

function emailbroadcasting_shortcode($atts = array())
{
	$first = strtoupper(array_shift($atts));
	if ('UNSUBSCRIBE' == $first)
	{
		return '
		<form method="post">
			<input class="e-mail-broadcasting" name="e-mail-broadcasting-mail" type="email" value="" placeholder="" />
			<button class="e-mail-broadcasting" type="submit" name="e-mail-broadcasting-action" value="unsubscribe">'.__('Unsubscribe', URL_E_MAIL_BROADCASTING).'</button>
		</form>';
	}

	else
	{
		return '
		<form method="post">
			<input class="e-mail-broadcasting" name="e-mail-broadcasting-mail" type="email" value="" placeholder="" />
			<button class="e-mail-broadcasting" type="submit" name="e-mail-broadcasting-action" value="subscribe">'.__('Subscribe', URL_E_MAIL_BROADCASTING).'</button>
		</form>';
	}
}
add_shortcode('emailbroadcasting', 'emailbroadcasting_shortcode');



class EMailBroadcasting_Widget extends WP_Widget
{
	function EMailBroadcasting_Widget()
	{
		$widget_ops = array('classname' => 'e-mail-broadcasting', 'description' => __('Allow user to subscribe/unsubscribe in the lists.', URL_E_MAIL_BROADCASTING));
		$this->WP_Widget('EMailBroadcasting_Widget', 'E-Mail Broadcasting', $widget_ops);
	}

	function form($instance)
	{
		$instance = wp_parse_args((array)$instance, array('title' => '', 'text' => '', 'action' => 'subscribe'));
		$title = $instance['title'];
		$text = $instance['text'];
		$action = $instance['action'];
		echo '
		<p>
			<label for="'.$this->get_field_id('title').'">
				'.__('Title').'
				<input class="widefat" id="'.$this->get_field_id('title').'" name="'.$this->get_field_name('title').'" type="text" value="'.esc_attr($title).'" />
			</label>
		</p>
		<p>
			<label for="'.$this->get_field_id('text').'">
				'.__('Description').'
				<textarea class="widefat" id="'.$this->get_field_id('text').'" name="'.$this->get_field_name('text').'">'.esc_attr($text).'</textarea>
			</label>
		</p>
		<p>
			<label for="'.$this->get_field_id('action').'">
				'.__('Action').'
			</label>
			<select id="'.$this->get_field_id('action').'" name="'.$this->get_field_name('action').'">
				<option value="subscribe" '.selected('subscribe', $action, 0).'>'.__('Subscribe', URL_E_MAIL_BROADCASTING).'</option>
				<option value="unsubscribe" '.selected('unsubscribe', $action, 0).'>'.__('Unsubscribe', URL_E_MAIL_BROADCASTING).'</option>
			</select>
		</p>
		';
	}

	function update($new_instance, $old_instance)
	{
		$instance = $old_instance;
		$instance['title'] = $new_instance['title'];
		$instance['text'] = $new_instance['text'];
		$instance['action'] = $new_instance['action'];
		return $instance;
	}

	function widget($args, $instance)
	{
		extract($args, EXTR_SKIP);

		$title = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
		$text = apply_filters('widget_text', $instance['text'], $instance );

		echo $before_widget;

		if (!empty($title))
		{
			echo $before_title . $title . $after_title;
		}

		echo emailbroadcasting_shortcode(array($instance['action']));

		if (!empty($text))
		{
			echo '<p>&nbsp;</p><p>'.wpautop($text) .'</p>';
		}

		echo $after_widget;
	}
}
