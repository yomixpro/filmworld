<?php

/**
 * BuddyPress - Members Settings Delete Account
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 * @version 3.0.0
 */

do_action('bp_before_member_settings_template'); ?>
<div class="card-inner">
	<div id="message" class="info">

		<?php if (bp_is_my_profile()) : ?>

			<p><?php esc_html_e('Deleting your account will delete all of the content you have created. It will be completely irrecoverable.', 'socialv'); ?></p>

		<?php else : ?>

			<p><?php esc_html_e('Deleting this account will delete all of the content it has created. It will be completely irrecoverable.', 'socialv'); ?></p>

		<?php endif; ?>

	</div>

	<form action="<?php echo bp_displayed_user_domain() . bp_get_settings_slug() . '/delete-account'; ?>" name="account-delete-form" id="account-delete-form" class="standard-form" method="post">

		<?php

		/**
		 * Fires before the display of the submit button for user delete account submitting.
		 *
		 * @since 1.5.0
		 */
		do_action('bp_members_delete_account_before_submit'); ?>

		<label for="delete-account-understand">
			<input type="checkbox" name="delete-account-understand" id="delete-account-understand" value="1" onclick="if(this.checked) { document.getElementById('delete-account-button').disabled = ''; } else { document.getElementById('delete-account-button').disabled = 'disabled'; }" />
			<?php esc_html_e('I understand the consequences.', 'socialv'); ?>
		</label>

		<div class="form-edit-btn">
			<div class="submit">
				<input type="submit" disabled="disabled" value="<?php esc_attr_e('Delete Account', 'socialv'); ?>" id="delete-account-button" class="btn socialv-btn-danger" name="delete-account-button" />
			</div>
		</div>

		<?php

		/**
		 * Fires after the display of the submit button for user delete account submitting.
		 *
		 * @since 1.5.0
		 */
		do_action('bp_members_delete_account_after_submit'); ?>

		<?php wp_nonce_field('delete-account'); ?>

	</form>
</div>
<?php

do_action('bp_after_member_settings_template');
