<?php
/**
 * Класс реализует основной функционал плагина
 */
namespace CPMS;
class Plugin
{
	/**
	 * Путь к файлам плагина
	 * @var string
	 */
	public $path;

	/**
	 * URL к файлам плагина
	 * @var string
	 */
	public $url;
	
	/**
	 * Параметры плагина
	 * @var CPMS\Settings
	 */
	public $settings;
	
	/**
	 * Объект Slack
	 * @var CPMS\Slack
	 */
	public $slack;	
	
	/**
	 * Модули событий
	 * @var mixed
	 */
	public $events;	

	/**
	 * Конструктор
	 * Инициализация плагина
	 */
	public function __construct( $pluginPath, $pluginURL )
	{
		$this->path = $pluginPath;						// Путь к файлам плагина
		$this->url = $pluginURL;						// URL к файлам плагина
		$this->settings = new Settings( CPMS, $this );	// Инициализируем параметры
		$this->slack 	= new Slack( $this ); 			// Инициализируем Slack
		
		// Инициализируем модули
		$this->events = array(
			new EventNewProject( $this ),				// New project
			new EventNewTask( $this ),					// New task
			new EventNewComment( $this ),				// New comment
			new EventCompleteTask( $this ),				// Complete task
		);
		
	}
	
}