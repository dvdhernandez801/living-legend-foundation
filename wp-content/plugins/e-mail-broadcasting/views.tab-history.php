<?php $i=0;?>
<div style="float:left;width:40%;margin-left:5%;">
	<h3><?php _e('Last subscribed users')?></h3>
	<table class="widefat">
		<thead>
			<tr>
				<th>&nbsp;</th>
				<th>
					<?php _e('E-mail address')?>
				</th>
				<th style="width:120px;">
					<?php _e('Date')?>
				</th>
			</tr>
		</thead>
		<tbody id="e-mail-broadcasting-tablelist">
			<?php foreach($this->EMailBroadcasting->list_history('subscribed', 30) as $email):?>
			<tr>
				<td><?php echo ++$i?>
				<td><?php echo $email['email']?></td>
				<td><?php echo $email['added']?></td>
			</tr>
			<?php endforeach?>
		</tbody>
	</table>
</div>

<?php $i=0;?>
<div style="float:left;width:40%;margin-left:5%;">
	<h3><?php _e('Last unsubscribed users')?></h3>
	<table class="widefat">
		<thead>
			<tr>
				<th>&nbsp;</th>
				<th>
					<?php _e('E-mail address')?>
				</th>
				<th style="width:120px;">
					<?php _e('Date')?>
				</th>
			</tr>
		</thead>
		<tbody id="e-mail-broadcasting-tablelist">
			<?php foreach($this->EMailBroadcasting->list_history('unsubscribed', 30) as $email):?>
			<tr>
				<td><?php echo ++$i?>
				<td><?php echo $email['email']?></td>
				<td><?php echo $email['deleted']?></td>
			</tr>
			<?php endforeach?>
		</tbody>
	</table>
</div>
