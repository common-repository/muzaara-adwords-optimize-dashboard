<div class="wrap">
	<?php require_once( __DIR__ . "/header.php" );?>
	<div style="text-align: center; margin-bottom: 10px; margin-top: 10%;">
		<img src="<?php echo MUZAARA[ "assets_url" ]?>images/logo-icon.png" height="70" />
	</div>
	<div class="muzaara is-linked">
		<h2><?php _e( "Welcome to Muzaara", "muzaara-gads" )?></h2>
		<h5>Monitor your Ad account</h5>
		<!-- <p>
			<?php _e( "Select Google Ads Account", "muzaara-gads" )?></p>
		</p> -->
		<p>
			<?php _e( "Click on any account below to select Customer account", "muzaara-gads" )?>
		</p>
		<?php if( !$this->gapi->errno ):?>
			<?php if( $customers ):?>
				<ul class="account-listing">
					<?php foreach( $customers["linked"] as $customer ):?>
						<?php $client_id = $customers[ "is_ads_account" ] ? $customer->getId() : /* $customer->getClientCustomerId()*/ null;?>
						<li 
							data-id="<?php echo $client_id?>" 
							data-timezone="<?php echo esc_html( $customers["entries"][$client_id]->getTimeZone() )?>" 
							data-currency="<?php echo esc_html( $customers["entries"][$client_id]->getCurrencyCode() )?>" 
							data-name="<?php echo esc_html( $customers["entries"][$client_id]->getDescriptiveName() )?>"
							class="<?php echo $current_customer_id == $client_id ? 'is-selected' : ''?>">
							<?php echo esc_html( $customers["entries"][$client_id]->getDescriptiveName() )?> - <?php echo $client_id?>
						</li>
					<?php endforeach;?>
				</ul>
			<?php else:?>
				<div><?php _e( "No account found", "muzaara-gads" )?></div>
			<?php endif;?>
			<div style="text-align: left; margin-top: 20px;" id="muzaara-settings">
				<div>
					<input type="checkbox" value="1" name="show_in_dashboard" id="show_in_dashboard" <?php echo @$this->config["show_in_dashboard"] ? "checked" : ""?> /> &nbsp; <label for="show_in_dashboard"><?php _e( "Show on Dashboard", "muzaara-gads" )?></label>
				</div>
			</div>
		<?php else:?>
			<div class="api-error">
				<span class="icon-alert-triangle"></span>
				<h4><?php echo $this->gapi->error?></h4>
				<small><em><?php _e( "Please contact our support", "muzaara-gads" )?></em></small>
			</div>
		<?php endif;?>
	</div>
	<?php if( !$this->gapi->errno ):?>
		<div style="text-align: center; margin-top: 20px;">
			<button class="muzaara-button" id="muzaara_save">
				<?php _e( "Save", "muzaara-gads" )?>
			</button>
		</div>

		<div class="account-summary <?php echo ($current_customer_id)? 'show' : ''?>">
			<h3 class="title"><?php _e( "Manage Accounts", "muzaara-gads" )?></h3>
			<div>
				<img src="<?php echo MUZAARA[ "assets_url" ]?>images/Ads_logo_horizontal.png" width="300" />
			</div>

			<ul class="linked-accounts">
				<li>
					<strong><?php _e( "Selected Google Account:", "muzaara-gads" )?></strong>
					<span class="data"><?php echo $account_data[ "email" ]?></span>
				</li>
				<li>
					<strong><?php _e( "Selected Google Ads Account:", "muzaara-gads" )?></strong>
					<span class="data"><?php printf( "<span id='account_name'>%s</span> (<span id='account_id'>%s</span>)", $current_customer[ "name" ], $current_customer["id"] )?></span>
				</li>
			</ul>

			<div class="text-center">
				<button class="muzaara-button" id="muzaara_edit"><?php _e( "Edit", "muzaara-gads" )?></button><br />
				<a href="" id="muzaara-unlink" class="delete error"><?php _e( "Unlink Account", "muzaara-gads" )?></a>
			</div>
		</div>
	<?php endif;?>
	<?php require_once( __DIR__ . "/footer.php" )?>
</div>
