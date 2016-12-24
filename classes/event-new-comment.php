<?php
/**
 * New Project Event
 */
namespace CPMS;
class EventNewComment extends Event
{
	/**
	 * Constructor
	 *
	 * @param INTEAM\Plugin $plugin		Main plugin object
	 */
	public function __construct( $plugin )
	{
		// Parent constructor
		parent::__construct( $plugin );
		
		// Hooks
		add_action('cpm_comment_new', array( $this, 'cpm_comment_new' ), 10, 3 );
	}
	
	/**
	 * Set properties on init and after save settings
	 */
	protected function setProperties()
	{
		// Parent method
		parent::setProperties();
		
		$this->title 		= __( 'New Comment', CPMS );
		$this->icon 		= $this->plugin->settings->get( $this->iconProperty, 	':speech_balloon:' );
		$this->message 		= $this->plugin->settings->get( $this->messageProperty, __( "%project%: %task%\n%content%", CPMS ) );
		$this->description 	= __( 'You can use this codes:', CPMS ) . '<ul>' . 
			'<li>' . __( '%user% - User name', CPMS ) . '</li>' . 
			'<li>' . __( '%project% - The project title', CPMS ) . '</li>' .
			'<li>' . __( '%list% - The task list', CPMS ) . '</li>' . 			
			'<li>' . __( '%task% - The task title', CPMS ) . '</li>' . 
			'<li>' . __( '%content% - The comment content', CPMS ) . '</li>' . 
			'<li>' . __( '%comment_author% - The comment author', CPMS ) . '</li>' . 
			'<li>' . __( '%comment_author_email% - The comment author E-mail', CPMS ) . '</li>' . 
			'</ul> ' . 
			__( 'and ANY shortcodes', CPMS );
	}	
	
	
	/* ------------------------------- HOOKS ------------------------------- */
	/**
	 * New comment
	 *
	 * @param int $comment_id	
	 * @param int $post	
	 * @param int $commentdata	
	 */
	public function cpm_comment_new( $comment_id, $post, $commentdata )
	{	
		/*
		file_put_contents( CPMS_PATH . $this->id . '.log', 
			var_export($post, true) . PHP_EOL . PHP_EOL . 
			var_export($commentdata, true) );
		*/
		
		$this->send( 
			array(
				'%user%'					=> $this->getUserName( $commentdata['user_id'] ),
				'%project%'					=> $this->getPostTitle( $post ),
				'%list%'					=> $this->getParentPostTitle( $commentdata['comment_post_ID'] ),
				'%task%'					=> $this->getPostTitle( $commentdata['comment_post_ID'] ),				
				'%content%'					=> $commentdata['comment_content'],
				'%comment_author%'			=> $commentdata['comment_agent'],
				'%comment_author_email%'	=> $commentdata['comment_author'],				
			),
			$this->icon,
			$this->getUserName( $commentdata['user_id'] )
		);
	}
}