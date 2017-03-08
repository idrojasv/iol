<div class="wrap about-wrap mailster-welcome-wrap">

	<h1><?php printf( esc_html__( 'Welcome to %s', 'mailster' ), 'Mailster 2.2' ); ?></h1>

	<div class="about-text">
		<?php esc_html_e( 'Easily create, send and track your Newsletter Campaigns', 'mailster' ); ?><br>
	</div>

	<div class="mailster-badge"><?php printf( esc_html__( 'Version %s', 'mailster' ), MAILSTER_VERSION ); ?></div>

	<h2 class="nav-tab-wrapper">
		<a href="edit.php?post_type=newsletter&page=mailster_welcome" class="nav-tab nav-tab-active"><?php esc_html_e( 'What\'s New', 'mailster' ); ?></a>
		<?php if ( current_user_can( 'mailster_manage_templates' ) ) : ?>
		<a href="edit.php?post_type=newsletter&page=mailster_templates&more" class="nav-tab"><?php esc_html_e( 'Templates', 'mailster' ); ?></a>
		<?php endif; ?>
		<?php if ( current_user_can( 'mailster_manage_addons' ) ) : ?>
		<a href="edit.php?post_type=newsletter&page=mailster_addons" class="nav-tab"><?php esc_html_e( 'Add Ons', 'mailster' ); ?></a>
		<?php endif; ?>

	</h2>

<?php if ( get_transient( '_mailster_mymail' ) ) : ?>

		<div class="feature-section one-col">
			<h2>Mailster is the new MyMail!</h2>

			<p>Providing our customer with the best email marketing software possible to increase their interaction and grow their business has always been our main goal. To serve you even better in the future we are changing some important things on how we manage and help our customers. Delivering outstanding support is key to us. For that reason we’ve worked hard and are now introducing our new support and license center.</p>
			<p>From now on you can manage all your Mailster licenses from your account and check the status of your support tickets. By giving our customers a dedicated place where they can reach out to us and get helped quickly we will increase our efficiency and be able to respond even faster to your questions.</p>
			<p>As our team and customer base is growing we try to expand every aspect of our business with many great new features for Mailster in the pipeline as well as improving existing functionality.</p>

			<p>Thanks for being a loyal Mailster (MyMail) customer. We are looking forward continuing to provide you with the last email marketing software you’ll ever have to buy!</p>

			<p>Your Mailster Team</p>

			<div class="mailster-transition">
				<div class="mailster-transition-box">
					<img src="https://mailster.github.io/welcome/My.svg" class="mailster-transition-my" width="162">
					<img src="https://mailster.github.io/welcome/Mail.svg" class="mailster-transition-mail" width="222">
					<img src="https://mailster.github.io/welcome/ster.svg" class="mailster-transition-ster" width="211">
				</div>
			</div>
		</div>

<?php endif; ?>

		<div class="feature-section one-col">
			<h2>Dashboard</h2>
			<p>From now on you have a dashboard page to quickly access all your important task as well as get a glimpse on several statistics.</p>
			<img src="https://mailster.github.io/welcome/dashboard.jpg" width="1050" height="740">
			<div class="return-to-dashboard align-center"><a href="admin.php?page=mailster_dashboard">Check out the Dashboard</a></div>
		</div>

		<div class="feature-section two-col">
			<div class="col">
				<div class="media-container">
					<img src="https://mailster.github.io/welcome/setup_wizard.jpg" width="505" height="284">
				</div>
				<h3>Setup Wizard</h3>
				<p>Getting started with Mailster was never as easy as this. Go through the Wizard and start sending emails to grow your business.</p>
				<div class="return-to-dashboard"><a href="edit.php?post_type=newsletter&page=mailster_setup">Start Wizard</a></div>
			</div>
			<div class="col">
				<div class="media-container">
					<img src="https://mailster.github.io/welcome/editor_buttons.jpg" width="505" height="284">
				</div>
				<h3>Editor Button</h3>
				<p>Quickly add tags, buttons and other shortcodes to any post or page via the tinymce editor.</p>
				<div class="return-to-dashboard"></div>
			</div>
			<div class="col">
				<div class="media-container">
					<img src="https://mailster.github.io/welcome/attachments.jpg" width="505" height="284">
				</div>
				<h3>Attachments</h3>
				<p>From now on you can include attachments into your email newsletter for an even better interaction and service with your subscribers.</p>
				<div class="return-to-dashboard"></div>
			</div>
			<div class="col">
				<div class="media-container">
					<video loop muted preload="auto" autoplay="" src="https://mailster.github.io/welcome/bulk_actions.mp4" poster="https://mailster.github.io/welcome/bulk_actions.gif">
						<source src="https://mailster.github.io/welcome/bulk_actions.mp4" type="video/mp4">
					</video>
				</div>
				<h3>Bulk Actions</h3>
				<p>Thanks to our smart engineering from now on you can execute bulk actions for your whole list of subscribers not only on a per page basis.</p>
				<div class="return-to-dashboard"><a href="edit.php?post_type=newsletter&page=mailster_subscribers">Goto Subscribers</a></div>
			</div>
		</div>

		<div class="feature-section two-col">
			<div class="col">
				<div class="media-container">
					<img src="https://mailster.github.io/welcome/template_settings.jpg" width="505" height="284">
				</div>
				<h3>Template Settings</h3>
				<p>Set a logo for your template without the need to touch any code.</p>
				<div class="return-to-dashboard"><a href="edit.php?post_type=newsletter&page=mailster_settings#template">Goto Template Settings</a></div>
			</div>
			<div class="col">
				<div class="media-container">
					<img src="https://mailster.github.io/welcome/updated_templates.jpg" width="505" height="284">
				</div>
				<h3>Updated Template</h3>
				<p>We’ve updated our mail templates to work with the latest version of popular email clients.</p>
				<div class="return-to-dashboard"><a href="edit.php?post_type=newsletter&page=mailster_templates">Goto Templates</a></div>
			</div>
			<div class="col">
				<div class="media-container">
					<img src="https://mailster.github.io/welcome/manage_settings.jpg" width="505" height="284">
				</div>
				<h3>Manage Settings</h3>
				<p>From now on you can export and import settings with a click of a button which makes moving your installation or recreating your Mailster settings super simple.</p>
				<div class="return-to-dashboard"><a href="edit.php?post_type=newsletter&page=mailster_settings#manage-settings">Goto Manage Settings</a></div>
			</div>
			<div class="col">
			</div>
		</div>


		<div class="changelog">
			<h2>Under the Hood</h2>

			<div class="feature-section under-the-hood three-col">
				<div class="col">
					<h4>Notice Capabilities</h4>
					<p>Notifications are now shown to the targeted users.</p>
				</div>
				<div class="col">
					<h4>IMAP Bounces</h4>
					<p>IMAP servers are now supported in addition to POP3.</p>
				</div>
				<div class="col">
					<h4>Custom Template Screenshot</h4>
					<p>A screenshot.jpg file can now be used for the screenshots in template folders.</p>
				</div>
				<div class="col">
					<h4>Settings moved</h4>
					<p>Due to high demand of our customers we’ve moved the setting page into the plugin menu.</p>
				</div>
				<div class="col">
					<h4>WordPress 3.8+ Support</h4>
					<p>With this release Mailster will no longer support WordPress versions below 3.8.</p>
				</div>
				<div class="col">
					<h4></h4>
					<p></p>
				</div>
			</div>

		</div>
		<div class="clear"></div>

		<div class="return-to-dashboard">
			<a href="admin.php?page=mailster_dashboard">Back to Dashboard</a>
		</div>

<div class="clear"></div>



<div id="ajax-response"></div>
<br class="clear">
</div>
