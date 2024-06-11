<?php
/**
 * Provide a admin area view for Import X-Profile fields data.
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       www.wbcomdesigns.com
 * @since      1.0.0
 *
 * @package    Bp_Xprofile_Export_Import
 * @subpackage Bp_Xprofile_Export_Import/admin/partials
 */

	$bpxp_import_spinner = includes_url() . '/images/spinner.gif';
?>
<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="wbcom-tab-content">
	<div class="wbcom-welcome-main-wrapper">
		<div class="wbcom-admin-title-section">
			<h3><?php esc_html_e( 'Import CSV File Data', 'bp-xprofile-export-import' ); ?></h3>
		</div>
	<div class="wbcom-admin-option-wrap wbcom-admin-option-wrap-view">
	<div class="bpxp-admin-container">
		<div class="" id="csv_header_error">
				<p class="bpxp-error-message bpxp-message">
				<?php esc_html_e( 'Sorry CVS file did not imported. Please remove all extra rows from csv file. CSV file must have column name in first row eg. user_login , user_pass, user_email, user_role', 'bp-xprofile-export-import' ); ?>
				<a href="javascript:void(0)" id="bpxp_header_close">x</a>
				</p>
		</div>

		<div class="bpxp-admin-row bpxp-limit wbcom-settings-section-wrap">
				<?php do_action( 'bpxp_before_import_limit' ); ?>
				<div class="wbcom-settings-section-options-heading">
					<label for="bpxp_xprofile_fields"><?php echo esc_html__( 'CSV Chunk Limit', 'bp-xprofile-export-import' ); ?></label>
					<p class="description"><?php esc_html_e( 'This is the number of rows in the CSV file that get grouped by the value that is saved above, eg. 10. This means that the complete number of rows will be chunked and processed.', 'bp-xprofile-export-import' ); ?></p>
				</div>
				<div class="wbcom-settings-section-options">
						<input type="number" name="bpxp_set_member_limit" id="bpxp_set_member_limit" value="<?php echo esc_attr( 10 ); ?>" />						
				</div>
				<?php do_action( 'bpxp_after_import_limit' ); ?>
		</div>

		<div class="bpxp-admin-row wbcom-settings-section-wrap">
				<div class="wbcom-settings-section-options-heading">
						<label for="bpxp_xprofile_fields"><?php echo esc_html__( ' Password ', 'bp-xprofile-export-import' ); ?></label>
						<p class="description"><?php esc_html_e( 'Please confirm your password is already ecrypted or you need encrypted your password?', 'bp-xprofile-export-import' ); ?></p>
				</div>
				<div class="wbcom-settings-section-options">
						<input type="checkbox" name="bpxp_set_password_encrypted" id="bpxp_set_password_encrypted" value="<?php echo esc_html__( 'bpxp-set-encrypt', 'bp-xprofile-export-import' ); ?>" checked />
						<span><?php esc_html_e( 'Enable checkbox to encrypt password', 'bp-xprofile-export-import' ); ?></span>	
				</div>
		</div>

		<?php do_action( 'bpxp_before_import_file' ); ?>
		<div class="bpxp-admin-row wbcom-settings-section-wrap" id="upload_csv">
				<div class="wbcom-settings-section-options-heading">
						<label for="bpxp_xprofile_fields"><?php esc_html_e( 'Uploade CSV File', 'bp-xprofile-export-import' ); ?></label>
				</div>
				<div class="wbcom-settings-section-options">
						<input type="file" name="bpxp_import_file" id="bpxp_import_file" value="" />
				</div>
				<div class="wbcom-settings-section-options">
						<img src="<?php echo esc_html( $bpxp_import_spinner ); ?>" class="bpxp-admin-settings-spinner" />
				</div>
		</div>

		<?php do_action( 'bpxp_before_import_update_user' ); ?>
		<div class="bpxp-admin-row wbcom-settings-section-wrap">
				<div class="wbcom-settings-section-options-heading">
						<label for="bpxp_update_user"><?php esc_html_e( 'Update Users Data', 'bp-xprofile-export-import' ); ?></label>
				</div>
				<div class="wbcom-settings-section-options">
						<input type="checkbox" name="bpxp_update_user" id="bpxp_update_user" value="bpxp-create-user" />
						<span><?php esc_html_e( 'Enable checkbox to update existing users data', 'bp-xprofile-export-import' ); ?></span>
				</div>
		</div>

		<div class="bpxp-admin-row">
				<div class="wbcom-settings-section-options">
						<input type="submit" name="bpxp_import_xprofile_data" id="bpxp_import_xprofile_data" class="bpxp-admin-control button button-primary"  value="<?php esc_html_e( 'Import', 'bp-xprofile-export-import' ); ?>" />
				</div>
				<div class="wbcom-settings-section-options">
				<img src="<?php echo esc_html( $bpxp_import_spinner ); ?>" class="bpxp-admin-button-spinner" />
				</div>
		</div>
		<?php do_action( 'bpxp_after_import_button' ); ?>

		<div class="bpxp-admin-row">
			<p class="notice-wbcom"><?php esc_html_e( 'Note: Please remove all extra rows from csv file. CSV file must have column name in first row.', 'bp-xprofile-export-import' ); ?></p>
		</div>
	</div>
</div>
</div>
</div>
