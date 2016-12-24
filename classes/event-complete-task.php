<?php
/**
 * Task Complete Event
 */
namespace CPMS;
class EventCompleteTask extends Event
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
		add_action('cpm_task_complete', array( $this, 'cpm_task_complete' ), 10, 1 );
	}
	
	/**
	 * Set properties on init and after save settings
	 */
	protected function setProperties()
	{
		// Parent method
		parent::setProperties();
		
		$this->title 		= __( 'Task Complete', CPMS );
		$this->icon 		= $this->plugin->settings->get( $this->iconProperty, 	':white_check_mark:' );
		$this->message 		= $this->plugin->settings->get( $this->messageProperty, __( "Complete %project%: %list%: %task%", CPMS ) );
		$this->description 	= __( 'You can use this codes:', CPMS ) . '<ul>' . 
			'<li>' . __( '%user% - User name', CPMS ) . '</li>' . 
			'<li>' . __( '%project% - The project title', CPMS ) . '</li>' . 
			'<li>' . __( '%list% - The task list', CPMS ) . '</li>' . 			
			'<li>' . __( '%task% - The task title', CPMS ) . '</li>' . 
			'</ul> ' . 
			__( 'and ANY shortcodes', CPMS );
	}	
	
	
	/* ------------------------------- HOOKS ------------------------------- */
	/**
	 * Task complete
	 *
	 * @param int $task_id	
	 */
	public function cpm_task_complete( $task_id )
	{	
		$task 		= get_post( $task_id );
		$list 		= get_post( $task->post_parent );
		$project 	= get_post( $list->post_parent );
			
		$this->send( 
			array(
				'%user%'			=> $this->getUserName( get_current_user_id() ),
				'%project%'			=> $project->post_title,				
				'%list%'			=> $list->post_title,				
				'%task%'			=> $task->post_title,
			),
			$this->icon,
			$this->getUserName( $commentdata['user_id'] )
		);
	}	
}