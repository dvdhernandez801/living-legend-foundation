<div id="poststuff">
	<form method="post">
		<div class="alignright" style="width:25%;">
			<table class="widefat widget submitbox">
				<thead>
					<tr>
						<th>
							<?php _e('Publish')?>
						</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>
							<p>
								<input id="emailbroadcasting_send_to_list" type="radio" name="send_to" value="list" <?php echo checked($_POST['send_to'], 'list')?> />
								<label for="emailbroadcasting_send_to_list"><?php echo __('List').' ('.$this->EMailBroadcasting->list_length().')'?>
							</p>
							<p>
								<input id="emailbroadcasting_send_to_test" type="radio" name="send_to" value="test" <?php echo checked($_POST['send_to'], 'test')?> />
								<label for="emailbroadcasting_send_to_test"><?php _e('Make test, using the mails from the text box', URL_E_MAIL_BROADCASTING)?></label>
							</p>
							<p>
								<textarea id="the_list" name="test_list" style="width:100%;height:80px;resize:vertical;" placeholder="<?php _e('Test emails', URL_E_MAIL_BROADCASTING)?>"><?php echo esc_textarea($this->EMailBroadcasting->option('test_list'))?></textarea>
								<script>
								//<![CDATA[
									function enable_disable_test_list() {  jQuery('#the_list').attr('disabled', !(jQuery('#emailbroadcasting_send_to_test').is(':checked'))); }
									window.onload=function()
									{
										enable_disable_test_list();
										jQuery('#emailbroadcasting_send_to_list, #emailbroadcasting_send_to_test').live('blur, change, click', enable_disable_test_list);
									}
								//]]>
								</script>
							</p>
							<p>
								<button class="button-primary alignright" type="submit" name="#send" value="<?php echo wp_create_nonce('emailbroadcasting-send')?>"><?php _e('send e-mail')?></button>
							</p>
							<div class="clear">&nbsp;</div>
						</td>
					</tr>
				</tbody>
			</table>

			<table class="widefat widget submitbox">
				<thead>
					<tr>
						<th>
							<?php _e('Templates')?>
						</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>
							<p>
								<input type="text" name="name" value="<?php echo $template->post_name?>" placeholder="<?php _e('Name')?>" title="<?php _e('Name')?>" />
								<button class="button" type="submit" name="#save_template"><?php _e('Save')?></button>
								<?php if($this->_template):?>
								<a class="submitdelete deletion" href="<?php echo $this->url('tab=publish&delete-template='.$template->ID)?>"><?php _e('Delete')?></a>
								<?php endif?>
							</p>
						</td>
					</tr>
					<?php if($template_list):?>
					<tr>
						<td>
							<?php if($this->_template):?>
							<p>
								<a class="button" href="<?php echo $this->url('tab=publish&template=0')?>"><?php _e('New')?></a>
							</p>
							<?php endif?>
							<div style="max-height:300px;overflow:auto;">
								<ul>
									<?php foreach($template_list as $tpl):?>
									<li>
									<?php if ($this->EMailBroadcasting->option('template') == $tpl->ID) echo '*'?>
									<?php if ($this->_template==$tpl->ID):?>
										<?php echo $tpl->post_name?$tpl->post_name:$tpl->post_title?>
									<?php else:?>
										<a href="<?php echo $this->url('tab=publish&template='.$tpl->ID)?>">
											<?php echo $tpl->post_name?$tpl->post_name:$tpl->post_title?>
										</a>
									<?php endif?>
									</li>
									<?php endforeach?>
								</ul>
							</div>
						</td>
					</tr>
					<?php endif?>
				</tbody>
			</table>
		</div>
		<div id="post-body" style="margin-right:27%;">
			<div id="post-body-content">
				<div id="titlediv">
					<div id="titlewrap">
						<input type="text" name="title" size="30" tabindex="1" value="<?php echo esc_attr_e($template->post_title)?>" id="title" autocomplete="off" placeholder="<?php _e( 'Enter title here')?>" />
					</div>
				</div>
				<div id="postdivrich" class="postarea">
					<?php
						// since version 3.3
						if (function_exists('wp_editor'))
						{
							wp_editor($template->post_content, 'content');
						}
						// for versions < 3.3
						else
						{
							echo '<style>#wp-fullscreen-save, #wp_fs_image { display: none !important; }</style>';
							the_editor($template->post_content, 'content', 'title');
						}
					?>
					<table id="post-status-info" cellspacing="0">
						<tbody>
							<tr>
								<td id="wp-word-count">
									<?php printf( __( 'Word count: %s' ), '<span class="word-count">0</span>' ); ?>
								</td>
								<?php
									//<td class="autosave-info">
									//	<span class="autosave-message">&nbsp;</span>
									//	<span id="last-edit"><%php _e('Template')%></span>
									//	<select id="emailbroadcasting-template">
									//		<option></option>
									//	</select>
									//</td>
								?>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</form>
</div>
