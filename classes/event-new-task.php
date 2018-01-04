<?php
/**
 * New Project Event
 */
namespace CPMS;
class EventNewTask extends Event
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
		add_action('cpm_task_new', array( $this, 'cpm_task_new' ), 10, 3 );
	}
	
	/**
	 * Set properties on init and after save settings
	 */
	protected function setProperties()
	{
		// Parent method
		parent::setProperties();
		
		$this->title 		= __( 'New Task', CPMS );
		$this->icon 		= $this->plugin->settings->get( $this->iconProperty, 	':triangular_flag_on_post:' );
		$this->message 		= $this->plugin->settings->get( $this->messageProperty, __( "%project%: %list%: %task%\n%content%", CPMS ) );
		$this->description 	= __( 'You can use this codes:', CPMS ) . '<ul>' . 
			'<li>' . __( '%user% - User name', CPMS ) . '</li>' . 
			'<li>' . __( '%project% - The project title', CPMS ) . '</li>' . 
			'<li>' . __( '%project_id% - The project ID', CPMS ) . '</li>' . 
			'<li>' . __( '%list% - The task list', CPMS ) . '</li>' . 			
			'<li>' . __( '%list_id% - The task list ID', CPMS ) . '</li>' . 			
			'<li>' . __( '%task% - The task title', CPMS ) . '</li>' . 
			'<li>' . __( '%task_id% - The task ID', CPMS ) . '</li>' . 
			'<li>' . __( '%content% - The comment content', CPMS ) . '</li>' . 
			'</ul> ' . 
			__( 'and ANY shortcodes', CPMS );
	}	
	
	
	/* ------------------------------- HOOKS ------------------------------- */
	/**
	 * New task
	 *
	 * @param int $list_id	
	 * @param int $task_id	
	 * @param int $data	
	 */
	public function cpm_task_new( $list_id, $task_id, $data )
	{	
		/*
		file_put_contents( CPMS_PATH . $this->id . '.log', 
			'$list_id: ' . var_export( $list_id, true) . PHP_EOL . PHP_EOL . 
			'$task_id: ' . var_export( $task_id, true ) . PHP_EOL . PHP_EOL . 
			'$data: ' . var_export($data, true) );
		*/
		
		$projectId = wp_get_post_parent_id( $list_id );
		
		$this->send( 
			
			array(
				'%user%'			=> $this->getUserName( get_current_user_id() ),
				'%project%'			=> $this->getParentPostTitle( $list_id ),			
				'%project_id%'		=> $this->getParentPostId( $list_id ),			
				'%list%'			=> $this->getPostTitle( $list_id ),				
				'%list_id%'			=> $list_id,				
				'%task%'			=> $data['post_title'],
				'%task_id%'			=> $task_id,
				'%content%'			=> $data['post_content'],
			),
			$this->icon,
			$this->getUserName( $commentdata['user_id'] ),
			$projectId
		);
	}
	
}