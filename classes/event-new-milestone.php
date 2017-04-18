<?php
/**
 * New Milestone Event
 */
namespace CPMS;
class EventNewMilestone extends Event
{
	/**
	 * Конструктор
	 *
	 * @param INTEAM\Plugin $plugin		Ссылка на основной объект плагина
	 */
	public function __construct( $plugin )
	{
		// Родительский конструктор
		parent::__construct( $plugin );
		
		$this->title 		= __( 'New Milestone', CPMS );
		$this->icon 		= $this->plugin->settings->get( $this->iconProperty, 	':golf:' );
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
		
		// Hooks
		add_action('cpm_milestone_new', array( $this, 'cpm_milestone_new' ) );
	}
	
	/* ------------------------------- HOOKS ------------------------------- */
	/**
	 * New project
	 *
	 * @param int 	$milestone_id	
	 * @param int 	$project_id	
	 * @param mixed $data		
	 */
	public function cpm_milestone_new( $milestone_id, $project_id, $data )
	{	
		$this-send( array(
			'%id%'	=> $project_id,
		));
	}
}