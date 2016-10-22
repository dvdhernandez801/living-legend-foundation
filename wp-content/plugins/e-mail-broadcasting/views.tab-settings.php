<div class="alignright" style="width:25%;">
	<div class="tablenav top">&nbsp;</div>
	<table class="widefat widget">
		<thead>
			<tr>
				<th>
					<?php _e('List')?>
				</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>
					<form method="post">
						<p><strong><?php _e('Add')?></strong></p>
						<input name="email" type="text" value="" placeholder="<?php _e('E-mail address')?>" autocomplete="off" style="width:200px;" />
						<button class="button alignright" type="submit" name="#add"><?php _e('Add')?></button>
					</form>
				</td>
			</tr>
			<tr>
				<td>
					<form method="post" enctype="multipart/form-data">
						<p><strong><?php _e('Import')?></strong></p>
						<p><em>Import from csv file (, ; : or spaces)</em></p>
						<p>
							<input name="list" type="file" />
							<button class="button" type="submit" name="#import"><?php _e('Import')?></button>
							<button class="button" type="submit" name="#import-from-comments"><?php _e('Import from Comments')?></button>
						</p>
					</form>
				</td>
			</tr>
			<tr>
				<td>
					<form method="post" target="_blank">
						<p><strong><?php _e('Export')?></strong></p>
						<p>
							<label for="export_delimeter"><?php _e('Delimeter')?>:</label>
							<input name="export_delimeter" type="text" id="export_delimeter" value="<?php echo esc_attr($this->EMailBroadcasting->option('export_delimeter'))?>" placeholder="<?php esc_attr_e('Export file delimeter')?>" />
						</p>
						<p>
							<button class="button" type="submit" name="#export" value="export"><?php _e('Export')?></button>
						</p>
					</form>
				</td>
			</tr>
			<tr>
				<td>
					<form method="post">
						<p><strong><?php _e('Delete')?></strong></p>
						<p><em>Wildcard to match the emails you want to delete</em></p>
						<p>
							<input name="wildcard" type="text" value="" placeholder="john.?.*@yahoo.com" style="width:165px;" />
							<button class="button alignright submitdelete" type="submit" name="#del"><?php _e('Delete')?></button>
						</p>
					</form>
				</td>
			</tr>
		</tbody>
	</table>

	<table class="widefat widget">
		<thead>
			<tr>
				<th>
					<?php _e('Settings')?>
				</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>
					<form method="post">
						<p>
							<label for="from"><?php _e('From')?>:</label>
							<input name="from" type="text" id="from" value="<?php echo esc_attr($this->EMailBroadcasting->option('from'))?>" placeholder="<?php esc_attr_e('E-mail address')?>" />
						</p>
						<p>
							<label for="from_name"><?php _e('From Name')?>:</label>
							<input name="from_name" type="text" id="from_name" value="<?php echo esc_attr($this->EMailBroadcasting->option('from_name'))?>" placeholder="<?php esc_attr_e('E-mail sender name')?>" />
						</p>
						<p>&nbsp;</p>
						<p>
							<input name="limit" type="number" id="limit" value="<?php echo esc_attr($this->EMailBroadcasting->option('limit'))?>" min="1" max="9999" size="3" />
							<label for="limit"><?php _e('mails')?></label>
							&nbsp; /
							<input name="interval" type="number" id="interval" value="<?php echo esc_attr($this->EMailBroadcasting->option('interval'))?>" min="1" max="9999" size="3" />
							<label for="interval"><?php printf(__('%s min'), '')?></label>
						</p>
						<p>
							<input type="checkbox" name="send_immed" value="1" id="send_immed" <?php checked(1, intval($this->EMailBroadcasting->option('send_immed')))?> />
							<label for="send_immed"><?php _e('Start sending immediately')?></label>
						</p>
						<p>
						<p>&nbsp;</p>
						<p>
							<label for="cron_run"><?php _e('Crontab can be triggered by')?></label>
							<select name="cron_run">
								<option value="users" <?php selected('users', $this->EMailBroadcasting->option('cron_run'))?>><?php echo _e('Users')?></option>
								<option value="admins" <?php selected('admins', $this->EMailBroadcasting->option('cron_run'))?>><?php echo _x('Administrator', 'User role')?></option>
								<option value="hash" <?php selected('hash', $this->EMailBroadcasting->option('cron_run'))?>><?php echo _e('URL')?></option>
							</select>
						</p>
						<p>
							<label for="cron_hash"><strong><?php _e('URL')?>:</strong></label>
						</p>
						<p>
							<?php echo site_url('?e-mail-broadcasting-trigger=')?>
							<input name="cron_hash" type="text" id="cron_hash" value="<?php echo esc_attr($this->EMailBroadcasting->option('cron_hash'))?>" />
						</p>
						<p>
							<button class="button-primary alignright" type="submit" name="#settings"><?php _e('Save')?></button>
						</p>
					</form>
				</td>
			</tr>
		</tbody>
	</table>
</div>

<div id="" style="width:73%;overflow:hidden;">

	<div class="tablenav top">

		<div class="alignleft actions">
			<form action="<?php echo $this->url()?> method="get">
				<input name="page" value="<?php echo URL_E_MAIL_BROADCASTING?>" type="hidden" />
				<input name="tab" value="settings" type="hidden" />

				<p class="search-box">
					<label class="screen-reader-text" for="emailbroadcasting-borwser-search">
						<?php _e('Filter')?>
					</label>
					<input type="text" id="emailbroadcasting-borwser-search" name="s" value="<?php echo esc_attr($search)?>" title="<?php esc_attr_e('Allowed wildcards * and ?', URL_E_MAIL_BROADCASTING)?>"  placeholder="<?php _e('Filter')?>" style="width:200px;" />
					<input type="submit" name="" id="search-submit" class="button" value="<?php echo esc_attr__('Search')?>" />
					<?php if ($search):?>
					<a href="<?php echo $this->url('tab=settings')?>" class="button"><?php _e('Clear')?></a>
					<?php endif?>
				</p>

			</form>
		</div>

		<div class="tablenav-pages">

			<?php if($search):?>

			<?php else:?>

				<span class="displaying-num">
					<?php printf(_n('1 item', '%s items', 34), '<span id="e-mail-broadcasting-list-length">'.number_format_i18n($this->EMailBroadcasting->list_length()).'</span>')?>
				</span>

				<?php if($this->_paged > 1):?>
				<a class="prev-page" title="<?php echo esc_attr__('Go to the previous page')?>" href="<?php echo $this->url('tab=settings&paged='.($this->_paged-1))?>">&lsaquo;</a>
				<?php else:?>
				<a class="prev-page disabled" title="<?php echo esc_attr__('Go to the previous page')?>" href="javascript:;">&lsaquo;</a>
				<?php endif?>

				<span class="paging-input">
					<?php printf(_x( '%1$s of %2$s', 'paging'),
							$this->_paged,
							$total_pages)?>
				</span>

				<?php if($this->_paged < $total_pages):?>
				<a class="next-page" title="<?php echo esc_attr__('Go to the next page')?>" href="<?php echo $this->url('tab=settings&paged='.($this->_paged+1))?>">&rsaquo;</a>
				<?php else:?>
				<a class="next-page disabled" title="<?php echo esc_attr__('Go to the next page')?>" href="javascript:;">&rsaquo;</a>
				<?php endif?>

			<?php endif?>

			<br class="clear">
		</div>

	</div>

	<table class="wp-list-table widefat">
		<thead>
			<tr>
				<th>&nbsp;</th>
				<th>
					<?php _e('E-mail address')?>
				</th>
				<th style="width:120px;" class="e-mail-broadcasting-col-recieved">
					<?php _e('Recieved e-mails')?>
				</th>
				<th style="width:130px;"  class="e-mail-broadcasting-col-added">
					<?php _ex('Date Created', 'revisions column name')?>
				</th>
			</tr>
		</thead>
		<tbody id="e-mail-broadcasting-tablelist">
			<?php
			// load first potion
			if ($search)
				$founded_mails = $this->tbody($search, 'search');
			else
				$founded_mails = $this->tbody($this->_paged);
			?>
		</tbody>
	</table>

	<script>
	//<![CDATA[
		function emailbroadcasting_del(id)
		{
			if (!confirm('<?php _e('Are you sure you want to do this?')?>'))
				return false;

			jQuery.post('<?php echo $this->url()?>', { 'e-mail-broadcasting-ajax-del' : id }, function(data)
			{
				if (data)
				{
					jQuery('#e-mail-broadcasting-le-'+id).remove();
					jQuery('#e-mail-broadcasting-list-length').text(jQuery('#e-mail-broadcasting-list-length').text()-1);
				}
			})
		}
	//]]>
	</script>

	<div class="tablenav bottom">
		<?php if($search):?>
		<div class="alignleft actions">
			<?php printf(__('%s emails found from %s in database.'), $founded_mails, $this->EMailBroadcasting->list_length())?>
		</div>
		<?php else:?>
		<div class="alignleft actions">
			<form method="post">
			<label for="display_limit"><?php _ex('Show on screen', 'Screen Options') ?></label>
			<select name="display_limit" id="display_limit" onchange="this.form.submit();">
				<?php foreach(array(30,50,100,200,300,400,500) as $limit):?>
				<option value="<?php echo $limit?>" <?php selected($limit, $display_limit)?>> <?php echo $limit?> </option>
				<?php endforeach?>
			</select>
			</form>
		</div>
		<?php endif?>
	</div>

</div>