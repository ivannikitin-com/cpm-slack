<?php
/**
 * Abstact Event Module
 */
namespace CPMS;
abstract class Event
{
	/**
	 * The Plugin object
	 * @var string
	 */
	protected $plugin;
	
	/**
	 * The event module id
	 * @var string
	 */
	protected $id;
	
	/**
	 * Event module title
	 * @var string
	 */
	protected $title;	
	
	/**
	 * Message icon
	 * @var string
	 */
	protected $icon;
	
	/**
	 * Message property
	 * @var string
	 */
	protected $message;	
	
	/**
	 * Message template description
	 * @var string
	 */
	protected $description;	
	
	/**
	 * icon property (for settings)
	 * @var string
	 */
	protected $iconProperty;
	
	/**
	 * Message property (for settings)
	 * @var string
	 */
	protected $messageProperty;		
	
	/**
	 * Constructor
	 *
	 * @param INTEAM\Plugin $plugin		The Plugin object
	 */
	public function __construct( $plugin )
	{
		// This is the plugin object
		$this->plugin = $plugin;
		
		// Set properties
		$this->setProperties();
	}
	
	/**
	 * Set properties on init and after save settings
	 */
	protected function setProperties()
	{
		// Event module id
		$this->id = str_replace( '\\', '_', strtolower( get_class( $this ) ) );
		
		// Settings properties
		$this->iconProperty 	= $this->id . '_icon';
		$this->messageProperty 	= $this->id . '_message';
	}
	
	
	/**
	 * Preparing the message
	 *
	 * @param mixed 		$details	Assoc array with event data
	 *
	 */
	protected function prepare( $details )
	{
		// The message template
		$message = $this->message;
		
		// Do all shortcodes
		$message = do_shortcode( $message );

		// Replace all values
		foreach ($details as $code => $content)
		{
			if ( $code == '%content%' )
			{
				// Some formatting
				$content = str_replace( array('</div>', '</p>', '</li>', '<br>', '<br/>'), "\n", $content );
				$content = str_replace( '&nbsp;', ' ', $content );
				// Remove HTML		
				$content = strip_tags( $content );
			}
			
			$message = str_replace( $code, $content, $message );
		}	
		
		// That's all
		return $message;
	}	
	
	/**
	 * Send event to Slack
	 *
	 * @param mixed 	$details	Assoc array with event data
	 * @param string 	$icon		The message icon. If empty we use the default for this module
	 * @param string 	$userName	The message user. If empty we use the default value from settings
	 * @param int 		$projectId	Current project Id
	 */
	public function send( $details, $icon = '', $userName = '', $projectId = 0 )
	{
		// Prepare message
		$message 	= $this->prepare( $details );
		$icon 		= ( empty( $icon ) ) ? $this->icon : $icon;
	
		// Отправляем событие
		if ( ! empty( $message ) ) 
			return $this->plugin->slack->send( $message, $icon, $userName, $projectId );
		else
			return false;
	}

	/**
	 * Show event module settings 
	 */
	public function renderSettings()
	{	?>
		<h2><?php echo $this->title ?></h2>
		
		<div class="cpms-field">
			<label for="<?php echo $this->iconProperty ?>"><?php esc_html_e( 'Icon', CPMS ) ?></label>
			<div class="cpms-input">
				<input id="<?php echo $this->iconProperty ?>" name="<?php echo $this->iconProperty ?>" type="text" 
					value="<?php echo $this->icon ?>" />
				<p><?php 
					esc_html_e( 'Specify the icon of event. Get the icon', CPMS ); 
					echo ' <a href="http://www.emoji-cheat-sheet.com/" target="_blank">'; 
					esc_html_e( 'here', CPMS ); 
					echo '</a>.';
				?></p>
			</div>
		</div>
		<div class="cpms-field">
			<label for="<?php echo $this->messageProperty ?>"><?php esc_html_e( 'Message', CPMS ) ?></label>
			<div class="cpms-input">
				<textarea id="<?php echo $this->messageProperty ?>" name="<?php echo $this->messageProperty ?>"><?php echo $this->message ?></textarea>
				<p><?php echo $this->description ?></p>
			</div>
		</div>
		
		<hr>
	<?php		
	}		
	
	/**
	 * Save event module settings 
	 */
	public function saveSettings()
	{ 
		$icon = sanitize_text_field( $_POST[ $this->iconProperty ] );
		//$message = implode( "\n", array_map( 'sanitize_text_field', explode( "\n", $_POST[ $this->messageProperty ] ) ) );
		$message = implode( "\n", array_map( 'trim', explode( "\n", $_POST[ $this->messageProperty ] ) ) );
		
		// Set settings
		$this->plugin->settings->set( $this->iconProperty, $icon );
		$this->plugin->settings->set( $this->messageProperty, $message );
		
		// Reread properties
		$this->setProperties();
	}
	
	/**
	 * Returns the user name 
	 *
	 * @param int 	$userId		User ID	 
	 */
	public function getUserName( $userId )
	{
		 $userInfo = get_userdata( $userId );
		 return $userInfo->display_name;
	}
	
	/**
	 * Returns the post title 
	 *
	 * @param int 	$userId		User ID	 
	 */
	public function getPostTitle( $postId )
	{
		$post = get_post( $postId ); 
		return $post->post_title;
	}
	
	/**
	 * Returns the paretn post title 
	 *
	 * @param int 	$postId		Post ID	 
	 */
	public function getParentPostTitle( $postId )
	{
		$post = get_post( $postId );
		$parentPost = get_post( $post->post_parent );
		return $parentPost->post_title;
	}
	
	/**
	 * Returns the paretn post ID 
	 *
	 * @param int 	$postId		Post ID	 
	 */	
	public function getParentPostId( $postId )
	{
		$post = get_post( $postId );
		$parentPost = get_post( $post->post_parent );
		return $parentPost->ID;
	}
	
}