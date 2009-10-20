<div class="wrap">
	<h2><?php _e('Dashboard Last News','dashboard-last-news'); ?></h2>
	<br/>
	<div id="fragment-1">
		<br />
		<div style='margin: 0 10px 0 10px'>
			<form id='settform' name='settform' action='' method='post'>
				<input type='hidden' id='formname' name='formname' value='settform' />
				<table class='form-table'>
					<tbody>
						<tr valign='top'>
							<th scope='row' style='width:400px;'>
								<b><?php _e('How many Last News dashboard widgets do you want ?','dashboard-last-news'); ?></b>
							</th>
							<td> 
								<select id="dashboard-last-news-widget-count" name="dashboard-last-news-widget-count">
<?php
for ( $i = 1; $i <= 5; $i = $i + 1 )
									echo "<option value='$i'" . ( $dashboard_last_news_widget_count == $i ? " selected='selected'" : '' ) . ">$i</option>";
?>
								</select>
							</td>
						</tr>
					</tbody>
				</table>
				<p class='submit'>
					<input type='submit' name='Submit' value='<?php  _e('Update','dashboard-last-news'); ?>' />
				</p>
			</form>
		</div>
		<br/>
	</div>
</div>
