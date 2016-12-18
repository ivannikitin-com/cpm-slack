<?php
/**
 * Класс реализует основной функционал плагина
 */
namespace CMPS;
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
	 * @var CMPS\Settings
	 */
	public $settings;
	
	/**
	 * Объект Slack
	 * @var CMPS\Slack
	 */
	public $slack;	

	/**
	 * Конструктор
	 * Инициализация плагина
	 */
	public function __construct( $pluginPath, $pluginURL )
	{
		$this->path = $pluginPath;						// Путь к файлам плагина
		$this->url = $pluginURL;						// URL к файлам плагина
		$this->settings = new Settings( CMPS, $this );	// Инициализируем параметры
		$this->slack 	= new Slack( $this ); 			// Инициализируем Slack
		
	}
	
}