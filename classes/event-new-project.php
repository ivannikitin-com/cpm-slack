<?php
/**
 * New Project Event
 */
namespace CPMS;
class EventNewProject extends Event
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
		
		// Свойства модуля
		$elIconId 		= 'cpms_' . $this->id . '_icon';
		$elMessageId 	= 'cpms_' . $this->id . '_message';		
		$this->title 		= __( 'New Project', CPMS );
		$this->icon 		= $this->plugin->settings->get( $elIconId, 	':new:' );
		$this->message 		= $this->plugin->settings->get( $elMessageId, __( 'The new project', CPMS ) );
		$this->description 	= __( 'You can use this codes:', CPMS ) . '<ul>' . 
			'<li>' . __( '%id% - The project ID (int)', CPMS ) . '</li>' . 
			'</ul> ' . 
			__( 'and ANY shortcodes', CPMS );
		
		// Hooks
		//add_action('cpm_project_new', array( $this, 'cpm_project_new' ) );
	}
	
	/* ------------------------------- HOOKS ------------------------------- */
	/**
	 * New project
	 *
	 * @param int $project_id	
	 * @param int $data	
	 * @param int $posted	
	 */
	public function cpm_project_new( $project_id, $data, $posted )
	{	
		$this-send( array(
			'%id%'	=> $project_id,
		));
	}
}