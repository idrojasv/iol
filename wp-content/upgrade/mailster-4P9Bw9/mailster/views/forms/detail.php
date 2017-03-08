<?php

$id = isset( $_GET['ID'] ) ? intval( $_GET['ID'] ) : null;

$currentpage = isset( $_GET['tab'] ) ? $_GET['tab'] : 'structure';

$is_new = isset( $_GET['new'] );

if ( ! $is_new ) {
	if ( ! ( $form = $this->get( $id, true ) ) ) {
		echo '<h2>' . __( 'This form does not exist or has been deleted!', 'mailster' ) . '</h2>';
		return;
	}
} else {

	if ( ! current_user_can( 'mailster_add_forms' ) ) {
		echo '<h2>' . __( 'You don\'t have the right permission to add new forms', 'mailster' ) . '</h2>';
		return;
	}

	$form = $this->get_empty();
	$form->submit = mailster_text( 'submitbutton' );
	$form->fields = array( (object) array(
			'field_id' => 'email',
			'error_msg' => '',
			'name' => mailster_text( 'email' ),
			'required' => true,
		),
	);
	if ( isset( $_POST['mailster_data'] ) ) {
		$form = (object) wp_parse_args( $_POST['mailster_data'], (array) $form );
	}
}
$timeformat = get_option( 'date_format' ) . ' ' . get_option( 'time_format' );
$timeoffset = mailster( 'helper' )->gmt_offset( true );
$customfields = mailster()->get_custom_fields();

$now = time();

$tabindex = 1;

$defaultfields = array(
	'email' => mailster_text( 'email' ),
	'firstname' => mailster_text( 'firstname' ),
	'lastname' => mailster_text( 'lastname' ),
);
if ( $customfields ) {
	foreach ( $customfields as $field => $data ) {
		$defaultfields[ $field ] = $data['name'];
	}
}

?>
<div class="wrap<?php echo ( $is_new ) ? ' new' : '' ?>">
<form id="form_form" action="<?php echo add_query_arg( array( 'ID' => $id ) ) ?>" method="post">
<?php wp_nonce_field( 'mailster_nonce' );?>
<div style="height:0px; width:0px; overflow:hidden;"><input type="submit" name="save" value="1"></div>
<?php if ( $currentpage != 'use' ) : ?>
<p class="alignright">
	<?php if ( ! $is_new && current_user_can( 'mailster_delete_forms' ) ) : ?>
		<input type="submit" name="delete" class="button button-large" value="<?php esc_html_e( 'delete Form', 'mailster' );?>" onclick="return confirm('<?php esc_attr_e( 'Do you really like to remove this form?', 'mailster' );?>');">
	<?php endif; ?>
	<input type="submit" name="save" class="button button-primary" value="<?php esc_html_e( 'Save', 'mailster' );?>">
</p>
<?php endif; ?>
<h1>
<?php
if ( $is_new ) {
	esc_html_e( 'Add new Form', 'mailster' );
} else {
	esc_html_e( 'Edit Form', 'mailster' );
?>
<input type="hidden" id="ID" name="mailster_data[ID]" value="<?php echo intval( $form->ID ) ?>">
<?php if ( current_user_can( 'mailster_add_forms' ) ) : ?>
	<a href="edit.php?post_type=newsletter&page=mailster_forms&new" class="add-new-h2"><?php esc_html_e( 'Add New', 'mailster' );?></a>
<?php endif; ?>
<?php } ?>
<?php if ( ! $is_new ) :  ?>
	<a href="#TB_inline?&width=1200&height=600&inlineId=useitbox" class="add-new-h2" id="use-it"><?php esc_html_e( 'Use it!', 'mailster' );?></a>
<?php endif; ?>
</h1>
<?php if ( ! $is_new ) : ?>
<h1 class="nav-tab-wrapper">

	<a class="nav-tab <?php echo ( 'structure' == $currentpage ) ? 'nav-tab-active' : '' ?>" href="edit.php?post_type=newsletter&page=mailster_forms&ID=<?php echo $id ?>"><?php esc_html_e( 'Fields', 'mailster' ) ?></a>

	<a class="nav-tab <?php echo ( 'design' == $currentpage ) ? 'nav-tab-active' : '' ?>" href="edit.php?post_type=newsletter&page=mailster_forms&ID=<?php echo $id ?>&tab=design"><?php esc_html_e( 'Design', 'mailster' ) ?></a>

	<a class="nav-tab <?php echo ( 'settings' == $currentpage ) ? 'nav-tab-active' : '' ?>" href="edit.php?post_type=newsletter&page=mailster_forms&ID=<?php echo $id ?>&tab=settings"><?php esc_html_e( 'Settings', 'mailster' ) ?></a>

</h1>
<?php endif; ?>
<div id="titlewrap">
	<input type="text" class="widefat" name="mailster_data[name]" size="30" value="<?php echo esc_attr( $form->name ) ?>" id="title" spellcheck="true" autocomplete="off" placeholder="<?php esc_html_e( 'Enter Form Name', 'mailster' );?>">

</div>
<?php if ( 'structure' == $currentpage ) : ?>
<?php if ( ! $is_new ) : ?>

<p class="section-nav"><span class="alignright"><input type="submit" name="design" value="<?php esc_html_e( 'Design', 'mailster' );?> &raquo;" class="button-primary button-small"></span></p>

<?php endif; ?>

<p class="description"><?php esc_html_e( 'Define the structure of your form below. Drag available fields in the left area to add them to your form. Rearrange fields by dragging fields around', 'mailster' ) ?></p>
<div id="form-builder">
	<fieldset id="form-structure">
		<legend><?php esc_html_e( 'Form Fields', 'mailster' ) ?></legend>

		<ul class="form-order sortable">
		<?php foreach ( $form->fields as $field ) : ?>
		<li class="field-<?php echo $field->field_id ?>">
				<label><?php echo $field->name ?></label>
				<div>
				<span class="label"><?php esc_html_e( 'Label', 'mailster' ) ?>:</span>
				<input class="label widefat" type="text" name="mailster_structure[fields][<?php echo $field->field_id ?>]" data-name="mailster_structure[fields][<?php echo $field->field_id ?>]" value="<?php echo esc_attr( $field->name ) ?>" title="<?php esc_html_e( 'define a label for this field', 'mailster' );?>" placeholder="<?php echo $field->name ?>">
					<span class="alignright required-field"><input type="checkbox" name="mailster_structure[required][<?php echo $field->field_id ?>]" data-name="mailster_structure[required][<?php echo $field->field_id ?>]" class="form-order-check-required" value="1" <?php checked( $field->required ) ?> <?php if ( $field->field_id == 'email' ) {	echo ' disabled'; } ?>> <?php esc_html_e( 'required', 'mailster' );?>
						<a class="field-remove" title="<?php esc_html_e( 'remove field', 'mailster' );?>">&#10005;</a>

					</span>
				</div>
				<div>
				<span class="label"><?php esc_html_e( 'Error Message', 'mailster' ) ?>:</span>
				<input class="label widefat error-msg" type="text" name="mailster_structure[error_msg][<?php echo $field->field_id ?>]" data-name="mailster_structure[error_msg][<?php echo $field->field_id ?>]" value="<?php echo esc_attr( $field->error_msg ) ?>" title="<?php esc_html_e( 'define an error message for this field', 'mailster' );?>" placeholder="<?php esc_html_e( 'Error Message (optional)', 'mailster' ) ?>">
				</div>
				</li>
			<?php endforeach; ?>
		</ul>
				<h4><label><?php esc_html_e( 'Button Label', 'mailster' );?>: <input type="text" name="mailster_data[submit]" class="widefat regular-text" value="<?php echo esc_attr( $form->submit ); ?>" placeholder="<?php echo mailster_text( 'submitbutton' ) ?>" ></label></h4>
	</fieldset>

	<fieldset id="form-fields">

	<legend><?php esc_html_e( 'Available Fields', 'mailster' ) ?></legend>

		<ul class="form-order sortable"><?php

		$used = wp_list_pluck( $form->fields, 'field_id' );

		$fields = array_intersect_key( $defaultfields, array_flip( array_keys( array_diff_key( $defaultfields, array_flip( $used ) ) ) ) );

		foreach ( $fields as $field_id => $name ) : ?>

		<li class="field-<?php echo $field_id ?>">
				<label><?php echo $name ?></label>
				<div>
				<span class="label"><?php esc_html_e( 'Label', 'mailster' ) ?>:</span>
				<input class="label widefat" type="text" data-name="mailster_structure[fields][<?php echo $field_id ?>]" value="<?php echo esc_attr( $name ) ?>" title="<?php esc_html_e( 'define a label for this field', 'mailster' );?>" placeholder="<?php echo $name ?>">
					<span class="alignright required-field"><input type="checkbox" data-name="mailster_structure[required][<?php echo $field_id ?>]" class="form-order-check-required" value="1"> <?php esc_html_e( 'required', 'mailster' );?>
					<a class="field-remove" title="<?php esc_html_e( 'remove field', 'mailster' );?>">&#10005;</a>
					</span>
				</div>
				<div>
				<span class="label"><?php esc_html_e( 'Error Message', 'mailster' ) ?>:</span>
				<input class="label widefat error-msg" type="text" name="mailster_structure[error_msg][<?php echo $field_id ?>]" data-name="mailster_structure[error_msg][<?php echo $field_id ?>]" value="" title="<?php esc_html_e( 'define an error message for this field', 'mailster' );?>" placeholder="<?php esc_html_e( 'Error Message (optional)', 'mailster' ) ?>">
				</div>
				</li>
		<?php endforeach; ?>
		</ul>
				<p class="description"><?php printf( __( 'add more custom fields on the %s.', 'mailster' ), '<a href="edit.php?post_type=newsletter&page=mailster_settings#subscribers">' . __( 'Settings Page', 'mailster' ) . '</a>' ) ?></p>

	</fieldset>
</div>

<?php if ( ! $is_new ) : ?>

<p class="section-nav"><span class="alignright"><input type="submit" name="design" value="<?php esc_html_e( 'Design', 'mailster' );?> &raquo;" class="button-primary button-small"></span></p>

<?php endif; ?>

<?php elseif ( 'design' == $currentpage ) : ?>

<?php $style = $form->style; ?>

<p class="section-nav"><span class="alignleft"><input type="submit" name="structure" value="&laquo; <?php esc_html_e( 'back to Fields', 'mailster' );?>" class="button-primary button-small"></span><span class="alignright"><input type="submit" name="settings" value="<?php esc_html_e( 'define the Options', 'mailster' );?> &raquo;" class="button-primary button-small"></span></p>

<div id="form-preview">

	<fieldset id="design">
		<legend><?php esc_html_e( 'Form Design', 'mailster' ) ?></legend>

	<p><label><input type="checkbox" id="themestyle" checked> <?php esc_html_e( 'Include your Theme\'s style.css', 'mailster' ) ?></label></p>
	<p class="description clear"><?php esc_html_e( 'Your form may look different depending on the place you are using it!', 'mailster' ) ?></p>
	<div id="form-design">
		<iframe id="form-design-iframe" width="100%" height="500" allowTransparency="true" frameborder="0" scrolling="no" src="<?php echo $this->url( array( 'id' => $id, 'edit' => wp_create_nonce( 'mailsteriframeform' ), 's' => 1 ) ); ?>"></iframe>
	</div>
	<div id="form-design-options">
	<div class="form-design-options-nav">
		<div class="designnav contextual-help-tabs hide-if-no-js">
		<ul>
			<li><a href="#tab-global" class="nav"><?php esc_html_e( 'Global', 'mailster' ) ?></a></li>
			<li><a href="#tab-buttons" class="nav"><?php esc_html_e( 'Button', 'mailster' ) ?></a></li>
			<li><a href="#tab-fields" class="nav"><?php esc_html_e( 'Fields', 'mailster' ) ?></a></li>
			<li><a href="#tab-messages" class="nav"><?php esc_html_e( 'Info Messages', 'mailster' ) ?></a></li>
		</ul>
		</div>
	</div>
	<div class="form-design-options-tabs">

		<div class="designtab" id="tab-global">
			<ul>
			<li><label><?php esc_html_e( 'Label Color', 'mailster' ) ?>:</label> <input class="color-field" value="<?php $this->_get_style( $style, '.mailster-wrapper label', 'color' );?>" data-selector=".mailster-wrapper label" data-property="color"><a class="add-custom-style button button-small" href="#custom-style"><?php esc_html_e( 'custom style', 'mailster' );?></a></li>
			<li><label><?php esc_html_e( 'Input Text Color', 'mailster' ) ?>:</label> <input class="color-field" value="<?php $this->_get_style( $style, '.mailster-wrapper .input', 'color' );?>" data-selector=".mailster-wrapper .input" data-property="color"><a class="add-custom-style button button-small" href="#custom-style"><?php esc_html_e( 'custom style', 'mailster' );?></a></li>
			<li><label><?php esc_html_e( 'Input Background Color', 'mailster' ) ?>:</label> <input class="color-field" value="<?php $this->_get_style( $style, '.mailster-wrapper .input', 'background-color' );?>" data-selector=".mailster-wrapper .input" data-property="background-color"><a class="add-custom-style button button-small" href="#custom-style"><?php esc_html_e( 'custom style', 'mailster' );?></a></li>
			<li><label><?php esc_html_e( 'Input Focus Color', 'mailster' ) ?>:</label> <input class="color-field" value="<?php $this->_get_style( $style, '.mailster-wrapper .input:focus', 'background-color' );?>" data-selector=".mailster-wrapper .input:focus" data-property="background-color"><a class="add-custom-style button button-small" href="#custom-style"><?php esc_html_e( 'custom style', 'mailster' );?></a></li>
			<li><label><?php esc_html_e( 'Required Asterisk', 'mailster' ) ?>:</label> <input class="color-field" value="<?php $this->_get_style( $style, 'label span.mailster-required', 'color' );?>" data-selector="label span.mailster-required" data-property="color"><a class="add-custom-style button button-small" href="#custom-style"><?php esc_html_e( 'custom style', 'mailster' );?></a></li>
			</ul>
		</div>

		<div class="designtab" id="tab-buttons">
		<ul>
			<li><label><?php esc_html_e( 'Background Color', 'mailster' ) ?>:</label> <input class="color-field" value="<?php $this->_get_style( $style, '.submit-button', 'background-color' );?>" data-selector=".submit-button" data-property="background-color"><a class="add-custom-style button button-small" href="#custom-style"><?php esc_html_e( 'custom style', 'mailster' );?></a></li>
			<li><label><?php esc_html_e( 'Text Color', 'mailster' ) ?>:</label> <input class="color-field" value="<?php $this->_get_style( $style, '.submit-button', 'color' );?>" data-selector=".submit-button" data-property="color"><a class="add-custom-style button button-small" href="#custom-style"><?php esc_html_e( 'custom style', 'mailster' );?></a></li>
			<li><label><?php esc_html_e( 'Hover Color', 'mailster' ) ?>:</label> <input class="color-field" value="<?php $this->_get_style( $style, '.submit-button:hover', 'background-color' );?>" data-selector=".submit-button:hover" data-property="background-color"><a class="add-custom-style button button-small" href="#custom-style"><?php esc_html_e( 'custom style', 'mailster' );?></a></li>
			<li><label><?php esc_html_e( 'Hover Text Color', 'mailster' ) ?>:</label> <input class="color-field" value="<?php $this->_get_style( $style, '.submit-button:hover', 'color' );?>" data-selector=".submit-button:hover" data-property="color"><a class="add-custom-style button button-small" href="#custom-style"><?php esc_html_e( 'custom style', 'mailster' );?></a></li>
		</ul>
		</div>

		<div class="designtab" id="tab-fields">
		<ul>
			<?php foreach ( $form->fields as $field_id => $field ) {?>
				<li><strong><?php echo $field->name ?></strong><ul>
				<li><label><?php esc_html_e( 'Label', 'mailster' ) ?></label>
					<input class="color-field" value="<?php $this->_get_style( $style, '.mailster-' . $field_id . '-wrapper label', 'color' );?>" data-selector=".mailster-<?php echo $field_id ?>-wrapper label" data-property="color"><a class="add-custom-style button button-small" href="#custom-style"><?php esc_html_e( 'custom style', 'mailster' );?></a>
				</li>
				<li><label><?php esc_html_e( 'Input', 'mailster' ) ?></label>
					<input class="color-field" value="<?php $this->_get_style( $style, '.mailster-' . $field_id . '-wrapper .input', 'color' );?>" data-selector=".mailster-<?php echo $field_id ?>-wrapper .input" data-property="color"><a class="add-custom-style button button-small" href="#custom-style"><?php esc_html_e( 'custom style', 'mailster' );?></a>
				</li>
				<li><label><?php esc_html_e( 'Input Background', 'mailster' ) ?></label>
					<input class="color-field" value="<?php $this->_get_style( $style, '.mailster-' . $field_id . '-wrapper .input', 'background-color' );?>" data-selector=".mailster-<?php echo $field_id ?>-wrapper .input" data-property="background-color"><a class="add-custom-style button button-small" href="#custom-style"><?php esc_html_e( 'custom style', 'mailster' );?></a>
				</li>
				</ul></li>
			<?php }?>

		</ul>
		</div>

		<div class="designtab" id="tab-messages">
		<ul>
			<li><label><?php esc_html_e( 'Success message Color', 'mailster' ) ?>:</label> <input class="color-field" value="<?php $this->_get_style( $style, '.mailster-form-info.success', 'color' );?>" data-selector=".mailster-form-info.success" data-property="color"><a class="add-custom-style button button-small" href="#custom-style"><?php esc_html_e( 'custom style', 'mailster' );?></a></li>
			<li><label><?php esc_html_e( 'Success message Background', 'mailster' ) ?>:</label> <input class="color-field" value="<?php $this->_get_style( $style, '.mailster-form-info.success', 'background-color' );?>" data-selector=".mailster-form-info.success" data-property="background-color"><a class="add-custom-style button button-small" href="#custom-style"><?php esc_html_e( 'custom style', 'mailster' );?></a></li>
			<li><label><?php esc_html_e( 'Error message Color', 'mailster' ) ?>:</label> <input class="color-field" value="<?php $this->_get_style( $style, '.mailster-form-info.error', 'color' );?>" data-selector=".mailster-form-info.error" data-property="color"><a class="add-custom-style button button-small" href="#custom-style"><?php esc_html_e( 'custom style', 'mailster' );?></a></li>
			<li><label><?php esc_html_e( 'Error message Background', 'mailster' ) ?>:</label> <input class="color-field" value="<?php $this->_get_style( $style, '.mailster-form-info.error', 'background-color' );?>" data-selector=".mailster-form-info.error" data-property="background-color"><a class="add-custom-style button button-small" href="#custom-style"><?php esc_html_e( 'custom style', 'mailster' );?></a></li>
		</ul>
		</div>

	</div>
	</div>

	</fieldset>

	<input type="hidden" name="mailster_design[style]" value="<?php echo esc_attr( json_encode( $form->style ) ) ?>" id="style">
	<div class="clear"></div>

	<fieldset>
		<legend><?php esc_html_e( 'Custom Style', 'mailster' ) ?></legend>
		<p class="description"><?php esc_html_e( 'add custom CSS to your form', 'mailster' ) ?></p>
		<div id="custom-style-wrap" class="wrapper">
		<div class="wrapper-left">
		<textarea id="custom-style" class="code" name="mailster_design[custom]"><?php echo esc_textarea( $form->custom_style ) ?></textarea>
		</div>
		<div class="wrapper-right">
		<input type="text" class="widefat" placeholder="<?php esc_html_e( 'Selector Prefix', 'mailster' ) ?>" id="custom-style-prefix">
		<select id="custom-style-samples" multiple>
			<option value=""><?php esc_html_e( 'Form selector', 'mailster' ) ?></option>
			<option value=" .mailster-wrapper"><?php esc_html_e( 'Field wrapper', 'mailster' ) ?></option>
			<optgroup label="<?php esc_html_e( 'Custom Field Wrapper divs', 'mailster' ) ?>">
			<?php foreach ( $defaultfields as $key => $field ) {?>
			<option value=" .mailster-<?php echo $key ?>-wrapper"><?php echo $field ?></option>
			<?php }?>
			</optgroup>
			<optgroup label="<?php esc_html_e( 'Custom Field Inputs', 'mailster' ) ?>">
			<?php foreach ( $defaultfields as $key => $field ) {?>
			<option value=" .mailster-<?php echo $key ?>-wrapper input.input"><?php echo $field ?></option>
			<?php }?>
			</optgroup>
			<optgroup label="<?php esc_html_e( 'Other', 'mailster' ) ?>">
			<option value=" label .mailster-required"><?php esc_html_e( 'Required Asterisk', 'mailster' ) ?></option>
			</optgroup>
		</select>
		</div>
		</div>
	</fieldset>

</div>

<p class="section-nav"><span class="alignleft"><input type="submit" name="structure" value="&laquo; <?php esc_html_e( 'back to Fields', 'mailster' );?>" class="button-primary button-small"></span><span class="alignright"><input type="submit" name="settings" value="<?php esc_html_e( 'define the Options', 'mailster' );?> &raquo;" class="button-primary button-small"></span></p>

<?php elseif ( 'settings' == $currentpage ) : ?>

<?php $is_profile = mailster_option( 'profile_form', 0 ) == $form->ID; ?>

<p class="section-nav"><span class="alignleft"><input type="submit" name="design" value="&laquo; <?php esc_html_e( 'back to Design', 'mailster' );?>" class="button-primary button-small"></span></p>

<div id="form-options">
		<div class="subtab form" id="form-tab-<?php echo $id ?>">

		<fieldset>
			<legend><?php esc_html_e( 'Form Options', 'mailster' ) ?></legend>
				<p><label><input type="hidden" name="mailster_data[asterisk]" value="0"><input type="checkbox" name="mailster_data[asterisk]" value="1" <?php checked( $form->asterisk ) ?>> <?php esc_html_e( 'show asterisk on required fields', 'mailster' );?></label>
				</p>

				<p><label><input type="hidden" name="mailster_data[inline]" value="0"><input type="checkbox" name="mailster_data[inline]" value="1" <?php checked( $form->inline ) ?>> <?php esc_html_e( 'place labels inside input fields', 'mailster' );?></label>
				</p>

				<p><label><input type="hidden" name="mailster_data[prefill]" value="0"><input type="checkbox" name="mailster_data[prefill]" value="1" <?php checked( $form->prefill ) ?>> <?php esc_html_e( 'fill fields with known data if user is logged in', 'mailster' );?></label>
				</p>

				<p><label><input type="hidden" name="mailster_data[redirect]" value=""><input id="redirect-cb" type="checkbox" <?php checked( ! ! $form->redirect ) ?>> <?php esc_html_e( 'redirect after submit', 'mailster' );?></label>
				<input type="text" id="redirect-tf" name="mailster_data[redirect]" class="widefat regular-text" value="<?php echo $form->redirect; ?>" placeholder="https://www.example.com" >
				</p>

				<p><label><input type="hidden" name="mailster_data[overwrite]" value="0"><input type="checkbox" name="mailster_data[overwrite]" value="1" <?php checked( $form->overwrite ) ?>> <?php esc_html_e( 'allow users to update their data with this form', 'mailster' );?></label>
				</p>
		</fieldset>

		<fieldset>
			<legend><?php esc_html_e( 'Profile', 'mailster' ) ?></legend>
				<p><label>
					<input type="hidden" name="profile_form" value="0"><input type="checkbox" name="profile_form" value="1" <?php checked( $is_profile ) ?> <?php if ( $is_profile ) { echo 'disabled'; }?>> <?php esc_html_e( 'use this form as user profile.', 'mailster' );?>
				</label>
				</p>
					<?php if ( ! $is_profile ) :
						if ( $profile_form = mailster( 'forms' )->get( mailster_option( 'profile_form', 0 ), false, false ) ) :?>
							<p class="description"><?php printf( __( 'Currently %s is your profile form', 'mailster' ), '<a href="edit.php?post_type=newsletter&page=mailster_forms&ID=' . $profile_form->ID . '&tab=settings">' . $profile_form->name . '</a>' ) ?></p>
						<?php endif; ?>
					<?php endif; ?>

		</fieldset>

		<fieldset>
			<legend><?php esc_html_e( 'List Options', 'mailster' ) ?></legend>
				<p>
				<label><input type="hidden" name="mailster_data[userschoice]" value="0"><input type="checkbox" name="mailster_data[userschoice]" class="mailster_userschoice" value="1" <?php checked( $form->userschoice );?>> <?php esc_html_e( 'users decide which list they subscribe to', 'mailster' );?></label>
				<br> &nbsp; <label><input type="hidden" name="mailster_data[dropdown]" value="0"><input type="checkbox" name="mailster_data[dropdown]" class="mailster_dropdown" value="1" <?php checked( $form->dropdown ) ?><?php disabled( ! $form->userschoice ) ?>> <?php esc_html_e( 'show drop down instead of check boxes', 'mailster' );?></label>
				</p>
				<fieldset>
				<legend class="mailster_userschoice_td" <?php if ( $form->userschoice ) {echo ' style="display:none"';}?>><?php esc_html_e( 'subscribe new users to', 'mailster' );?></legend>
				<legend class="mailster_userschoice_td" <?php if ( ! $form->userschoice ) {echo ' style="display:none"';}?>><?php esc_html_e( 'users can subscribe to', 'mailster' );?></legend>

				<?php mailster( 'lists' )->print_it( null, null, 'mailster_data[lists]', false, $form->lists ); ?>

				<p><label><input type="hidden" name="mailster_data[precheck]" value="0"><input type="checkbox" name="mailster_data[precheck]" value="1" <?php checked( $form->precheck ) ?>> <?php esc_html_e( 'checked by default', 'mailster' ); ?></label>
				</p>
				</fieldset>
				<p><label><input type="hidden" name="mailster_data[addlists]" value="0"><input type="checkbox" name="mailster_data[addlists]" value="1" <?php checked( $form->addlists ) ?>> <?php esc_html_e( 'assign new lists automatically to this form', 'mailster' ); ?></label>
				</p>

		</fieldset>

		<fieldset>
			<legend><?php esc_html_e( 'Double Opt In', 'mailster' ) ?></legend>

				<p><label><input type="radio" name="mailster_data[doubleoptin]" class="double-opt-in" data-id="<?php echo $id; ?>" value="0" <?php checked( ! $form->doubleoptin ) ?>> [Single-Opt-In] <?php esc_html_e( 'new subscribers are subscribed instantly without confirmation.', 'mailster' ) ?></label>
				</p>
				<p><label><input type="radio" name="mailster_data[doubleoptin]" class="double-opt-in" data-id="<?php echo $id; ?>" value="1" <?php checked( $form->doubleoptin ) ?>> [Double-Opt-In] <?php esc_html_e( 'new subscribers must confirm their subscription.', 'mailster' ) ?></label>
				</p>
				<div id="double-opt-in-field" class="double-opt-in-field" <?php if ( ! $form->doubleoptin ) { echo ' style="display:none"'; } ?>>

					<fieldset>
						<legend><?php esc_html_e( 'Confirmation Settings', 'mailster' );?></legend>
					<table class="nested">

						<tr>
							<td colspan="2">
							<table class="form-table">
								<tr valign="top">
									<td scope="row" width="200"><label for="mailster_text_subject"><?php esc_html_e( 'Subject', 'mailster' ); ?>: <code>{subject}</code></label></td>
									<td><input type="text" id="mailster_text_subject" name="mailster_data[subject]" value="<?php echo esc_attr( $form->subject ); ?>" class="regular-text"></td>
								</tr>
								<tr valign="top">
									<td scope="row"><label for="mailster_text_headline"><?php esc_html_e( 'Headline', 'mailster' );?>: <code>{headline}</code></label></td>
									<td><input type="text" id="mailster_text_headline" name="mailster_data[headline]" value="<?php echo esc_attr( $form->headline ); ?>" class="regular-text"></td>
								</tr>
								<tr valign="top">
									<td scope="row"><label for="mailster_text_link"><?php esc_html_e( 'Linktext', 'mailster' );?>:</label> <code>{link}</code></td>
									<td><input type="text" id="mailster_text_link" name="mailster_data[link]" value="<?php echo esc_attr( $form->link ); ?>" class="regular-text"></td>
								</tr>
								<tr valign="top">
									<td scope="row"><label for="mailster_text_content"><?php esc_html_e( 'Text', 'mailster' );?>: <code>{content}</code></label><p class="description"><?php printf( __( 'The text new subscribers get when Double-Opt-In is selected. Use %s for the link placeholder. Basic HTML is allowed', 'mailster' ), '<code>{link}</code>' ); ?></p></td>
									<td><textarea id="mailster_text_content" name="mailster_data[content]" rows="10" cols="50" class="large-text"><?php echo esc_attr( $form->content ); ?></textarea></td>
								</tr>
								<tr><td><?php esc_html_e( 'used template file', 'mailster' );?></td><td>
									<select name="mailster_data[template]">
									<?php
									$templatefiles = mailster( 'templates' )->get_files( mailster_option( 'default_template' ) );
									foreach ( $templatefiles as $slug => $filedata ) {
										if ( $slug == 'index.html' ) {
											continue;
										} ?>
										<option value="<?php echo $slug ?>"<?php selected( $slug == $form->template ) ?>><?php echo esc_attr( $filedata['label'] ) ?> (<?php echo $slug ?>)</option>
									<?php } ?>
									</select>
									</td>
								</tr>

								<tr>
									<td><?php esc_html_e( 'Resend Confirmation', 'mailster' ) ?></td>
									<td><div><input type="checkbox" name="mailster_data[resend]" value="1" <?php checked( $form->resend ) ?>> <?php printf( __( 'resend confirmation %1$s times with a delay of %2$s hours if user hasn\'t confirmed the subscription', 'mailster' ), '<input type="text" name="mailster_data[resend_count]" value="' . esc_attr( $form->resend_count ) . '" class="small-text">', '<input type="text" name="mailster_data[resend_time]" value="' . esc_attr( $form->resend_time ) . '" class="small-text">' ) ?></div></td>
								</tr>

								<tr><td><?php esc_html_e( 'redirect after confirm', 'mailster' );?></td><td><input type="text" name="mailster_data[confirmredirect]" class="widefat" value="<?php if ( isset( $form->confirmredirect ) ) {	echo $form->confirmredirect; } ?>" placeholder="http://www.example.com" ></td>
								</tr>
								<tr>
									<td>&nbsp;</td>
									<td><label><input type="hidden" name="mailster_data[vcard]" class="vcard" value="0"><input type="checkbox" name="mailster_data[vcard]" class="vcard" value="1" <?php checked( $form->vcard );?> data-id="<?php echo $id; ?>"> <?php esc_html_e( 'attach vCard to all confirmation mails', 'mailster' ) ?></label>
									<div id="vcard-field" <?php if ( ! $form->vcard ) {	echo ' style="display:none"'; } ?> class="vcard-field">
									<p class="description"><?php printf( __( 'paste in your vCard content. You can use %s to generate your personal vcard', 'mailster' ), '<a href="http://vcardmaker.com/" class="external">vcardmaker.com</a>' ); ?></p>
									<?php $vcard = $form->vcard_content ? $form->vcard_content : $this->get_vcard(); ?><textarea name="mailster_data[vcard_content]" rows="10" cols="50" class="large-text code"><?php echo esc_textarea( $vcard ); ?></textarea>
									</div>

									</td>
								</tr>

							</table>
							</td>
						</tr>

					</table>
					</fieldset>
				</div>
		</fieldset>

		</div>

</div>

<p class="section-nav"><span class="alignleft"><input type="submit" name="design" value="&laquo; <?php esc_html_e( 'back to Design', 'mailster' );?>" class="button-primary button-small"></span></p>

<?php endif; ?>
<?php if ( ! $is_new ) :  ?>
<div class="clear" id="useitbox" style="display:none">

	<div class="useit-wrap">
		<div class="useit-nav">
			<div class="mainnav contextual-help-tabs hide-if-no-js">
				<ul>
					<li><a href="#shortcode" class="nav-shortcode"><?php esc_html_e( 'Shortcode', 'mailster' ) ?></a></li>
					<li><a href="#subscriber-button" class="nav-subscriber-button"><?php esc_html_e( 'Subscriber Button', 'mailster' ) ?></a></li>
					<li><a href="#form-html" class="nav-form-html"><?php esc_html_e( 'Form HTML', 'mailster' ) ?></a></li>
				</ul>
			</div>
		</div>

		<div class="useit-tabs">

			<div id="tab-intro" class="useit-tab">
				<h3><?php esc_html_e( 'Use your form as', 'mailster' ) ?>&hellip;</h3>

				<h4>&hellip; <?php esc_html_e( 'Shortcode', 'mailster' ) ?></h4>
				<p class="description"><?php esc_html_e( 'Use a shortcode on a blog post, page or wherever they are excepted.', 'mailster' ) ?> <?php printf( __( 'Read more about shortcodes at %s', 'mailster' ), '<a href="https://codex.wordpress.org/Shortcode">WordPress Codex</a>' ) ?></p>

				<h4>&hellip; <?php esc_html_e( 'Widget', 'mailster' ) ?></h4>
				<p class="description"><?php printf( __( 'Use this form as a %s in one of your sidebars', 'mailster' ), '<a href="widgets.php">' . __( 'widget', 'mailster' ) . '</a>' ) ?>.</p>

				<h4>&hellip; <?php esc_html_e( 'Subscriber Button', 'mailster' ) ?></h4>
				<p class="description"><?php esc_html_e( 'Embed your form on any site, no matter if it is your current or a third party one. It\'s similar to the Twitter button.', 'mailster' ) ?></p>

				<h4>&hellip; HTML</h4>
				<p class="description"><?php esc_html_e( 'Use your form via the HTML markup. This is often required by third party plugins. You can choose between an iframe or the raw HTML.', 'mailster' ) ?></p>
			</div>

			<div id="tab-shortcode" class="useit-tab">
				<h3><?php esc_html_e( 'Shortcode', 'mailster' ) ?></h3>
				<input type="text" class="code widefat" value="[newsletter_signup_form id=<?php echo $id ?>]">
				<p class="description"><?php esc_html_e( 'Use this shortcode wherever they are excepted.', 'mailster' ) ?></p>
			</div>

			<div id="tab-subscriber-button" class="useit-tab">
				<h3><?php esc_html_e( 'Subscriber Button', 'mailster' ) ?></h3>
				<p class="description"><?php esc_html_e( 'Embed a button where users can subscribe on any website', 'mailster' ) ?></p>

				<?php
				$subscribercount = mailster( 'subscribers' )->get_count( 'kilo' );
				$embeddedcode = mailster( 'forms' )->get_subscribe_button();
				?>

				<div class="wrapper">

					<h4><?php esc_html_e( 'Button Style', 'mailster' ) ?></h4>
					<?php $styles = array( 'default', 'wp', 'twitter', 'flat', 'minimal' ) ?>
					<ul class="subscriber-button-style">
					<?php foreach ( $styles as $i => $style ) { ?>
						<li><label>
						<input type="radio" name="subscriber-button-style" value="<?php echo esc_attr( $style ) ?>" <?php checked( ! $i );?>>
						<div class="btn-widget design-<?php echo $style ?> count">
							<div class="btn-count"><i></i><u></u><a><?php echo $subscribercount ?></a></div>
							<a class="btn"><?php echo esc_html( $form->submit ); ?></a>
						</div>
						</label></li>
					<?php } ?>
					</ul>

				<div class="clear"></div>

				<div class="wrapper-left">

					<h4><?php esc_html_e( 'Button Options', 'mailster' ) ?></h4>

					<div class="button-options-wrap">

						<p><?php esc_html_e( 'Popup width', 'mailster' ) ?>:
							<input type="text" id="buttonwidth" placeholder="480" value="480" class="small-text"></p>

						<h4><?php esc_html_e( 'Label', 'mailster' ) ?></h4>
						<p><label><input type="radio" name="buttonlabel" value="default" checked>
						<?php esc_html_e( 'Use Form Default', 'mailster' ) ?></label></p>
						<p><input type="radio" name="buttonlabel" value="custom">
						<input type="text" id="buttonlabel" placeholder="<?php echo esc_attr( $form->submit ); ?>" value="<?php echo esc_attr( $form->submit ); ?>"></p>

						<h4><?php esc_html_e( 'Subscriber Count', 'mailster' ) ?></h4>
						<p><label><input type="checkbox" id="showcount" checked> <?php esc_html_e( 'Display subscriber count', 'mailster' ) ?></label></p>
						<p><label><input type="checkbox" id="ontop"> <?php esc_html_e( 'Count above Button', 'mailster' ) ?></label></p>

						</div>

					</div>

					<div class="wrapper-right">

						<h4><?php esc_html_e( 'Preview and Code', 'mailster' ) ?></h4>

						<p><?php esc_html_e( 'Test your button', 'mailster' ) ?> &hellip;</p>
							<div class="button-preview">
								<?php echo $embeddedcode; ?>
							</div>

						<p>&hellip; <?php esc_html_e( 'embed it somewhere', 'mailster' ) ?> &hellip;</p>
							<div class="code-preview">
								<textarea class="code" readonly></textarea>
							</div>
						<p>&hellip; <?php esc_html_e( 'or use this shortcode on your site', 'mailster' ) ?></p>
							<div class="shortcode-preview">
								<input type="text" class="widefat code" readonly>
							</div>

					</div>
				</div>

			</div>

			<div id="tab-form-html" class="useit-tab">

				<h3><?php esc_html_e( 'Form HTML', 'mailster' ) ?></h3>

				<h4><?php esc_html_e( 'iFrame Version', 'mailster' ) ?></h4>

				<?php $embedcode = '<iframe width="%s" height="%s" allowTransparency="true" frameborder="0" scrolling="no" style="border:none" src="' . $this->url( array( 'id' => $id ) ) . '%s"></iframe>'; ?>

				<div>
					<label><?php esc_html_e( 'width', 'mailster' );?>: <input type="text" class="small-text embed-form-input" value="100%"></label>
					<label><?php esc_html_e( 'height', 'mailster' );?>: <input type="text" class="small-text embed-form-input" value="500"></label>
					<label title="<?php esc_html_e( 'check this option to include the style.css of your theme into the form', 'mailster' );?>"><input type="checkbox" value="1" class="embed-form-input" checked> <?php esc_html_e( 'include themes style.css', 'mailster' );?></label>
					<textarea class="widefat code embed-form-output" data-embedcode="<?php echo esc_attr( $embedcode ) ?>"><?php echo esc_textarea( $embedcode ) ?></textarea>
				</div>

				<h4><?php esc_html_e( 'HTML Version', 'mailster' ) ?></h4>

				<div>
				<?php
					$form = mailster( 'form' )->id( $id );
					$form->add_class( 'extern' );
					$form->prefill( false );
					$form->ajax( false );
					$form->embed_style( false );
					$form->referer( 'extern' );
				?>
					<textarea class="widefat code form-output"><?php echo esc_textarea( $form->render( false ) ) ?></textarea>
				</div>

			</div>

		</div>

	</div>

</div>
<?php endif; ?>

<hr>

<p class="alignright">
	<input type="submit" name="save" class="button button-primary" value="<?php esc_html_e( 'Save', 'mailster' );?>">
</p>

</form>
</div>
