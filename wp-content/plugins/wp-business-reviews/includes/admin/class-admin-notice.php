<?php
/**
 * Defines the Admin_Notice class
 *
 * @package WP_Business_Reviews\Includes\Admin
 * @since   0.1.0
 */

namespace WP_Business_Reviews\Includes\Admin;

use WP_Business_Reviews\Includes\View;

/**
 * Creates an admin notice for the plugin.
 *
 * Notices alert the user of important information such as when saving settings
 * either succeeds or fails.
 *
 * Admin notices can be added anywhere throughout the plugin as long as they are
 * registered prior to the `wpbr_admin_notices` action running.
 *
 * Reference the following example when adding new admin notices.
 *
 * ```
 * $notice = new Admin_Notice( 'Text goes here.', 'success', true );
 * $notice->register();
 * ```
 *
 * @since 0.1.0
 */
class Admin_Notice {
	/**
	 * Notice ID.
	 *
	 * @since 1.2.0
	 * @var string $id
	 */
	public $id;

	/**
	 * Text string displayed in the notice.
	 *
	 * @since 0.1.0
	 * @var string $message
	 */
	public $message;

	/**
	 * Type of notice.
	 *
	 * @since 0.1.0
	 * @var string $type
	 */
	public $type;

	/**
	 * Whether the notice can be dismissed.
	 *
	 * @since 0.1.0
	 * @var string $dismissible
	 */
	public $dismissible;

	/**
	 * Call to action.
	 *
	 * @since 1.2.0
	 * @var bool $cta
	 */
	public $cta;

	/**
	 * Instantiates the Admin_Notice object.
	 *
	 * Dismissible notices can be hidden per page, per user, or per site.
	 *
	 * @since 1.2.0 Update dismissible options, add $id and $cta parameters.
	 * @since 0.1.0
	 *
	 * @param string $id          Notice ID.
	 * @param string $message     Text string displayed in the notice.
	 * @param string $type        Optional. Type of notice. Default 'info'.
	 *                            Accepts 'info', 'success', 'error', 'warning'.
	 * @param bool   $dismissible Optional. Whether the notice can be dismissed.
	 *                            Accepts '', 'page', 'site'.
	 * @param array  $cta {
	 *     Optional. Call to action.
	 *
	 *     @type string $text Call to action text.
	 *     @type string $url  Call to action URL.
	 * }
	 */
	public function __construct(
		$id,
		$message,
		$type = 'info',
		$dismissible = 'page',
		$cta = array()
	) {
		$this->id          = $id;
		$this->message     = $message;
		$this->type        = $type;
		$this->dismissible = $dismissible;
		$this->cta         = $cta;
	}

	/**
	 * Gets the CSS class name of the notice.
	 *
	 * @since 1.2.0
	 *
	 * @return string The CSS class name.
	 */
	protected function get_class_name() {
		$class_name = "wpbr-admin-notice notice notice-{$this->type}";

		switch( $this->dismissible ) {
			case 'page':
				$class_name .= ' is-dismissible';
				break;
			case 'site':
				$class_name .= ' is-dismissible js-wpbr-dismissible-site';
				break;
		}

		return $class_name;
	}

	/**
	 * Renders the admin notice using WordPress core styles.
	 *
	 * @since 0.1.0
	 */
	public function render() {
		$view_object = new View( WPBR_PLUGIN_DIR . 'views/admin-notice.php' );
		$view_object->render(
			array(
				'id'         => $this->id,
				'class_name' => $this->get_class_name(),
				'message'    => $this->message,
				'cta'        => $this->cta
			)
		);
	}
}
