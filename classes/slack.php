<?php
/**
 * Класс реализует шорткоды плагина
 */
namespace CPMS;
class Slack
{
	/**
	 * Основной класс плагина
	 * @var string
	 */
	protected $plugin;
	
	/**
	 * Сервисный URL
	 * @var string
	 */
	protected $serviceURL;	
	
	/**
	 * Параметр настроек Сервисный URL
	 * @var string
	 */
	const SEVICE_URL_PARAM = 'sevice_url';	
	
	/**
	 * Канал Slack
	 * @var string
	 */
	protected $channel;	
	
	/**
	 * Параметр настроек Канал Slack
	 * @var string
	 */
	const CHANNEL_PARAM = 'channel';
	
	/**
	 * Канал Slack значение по умолчанию
	 * @var string
	 */
	const CHANNEL_PARAM_DEFAULT = 'CMP';	
	
	/**
	 * Пользователь Slack
	 * @var string
	 */
	protected $userName;	
	
	/**
	 * Параметр настроек Канал Slack
	 * @var string
	 */
	const USERNAME_PARAM = 'username';
	
	/**
	 * Канал Slack значение по умолчанию
	 * @var string
	 */
	const USERNAME_PARAM_DEFAULT = 'CMP Bot';	
	
	/**
	 * Конструктор
	 *
	 * @param INTEAM\Plugin plugin		Ссылка на основной объект плагина 
	 */
	public function __construct( $plugin )
	{
		// Сохраняем ссылку на объект плагина
		$this->plugin = $plugin;
		
		// Читаем настройки
		$this->serviceURL 	= $this->plugin->settings->get( self::SEVICE_URL_PARAM );
		$this->channel 		= $this->plugin->settings->get( self::CHANNEL_PARAM, self::CHANNEL_PARAM_DEFAULT );
		$this->userName		= $this->plugin->settings->get( self::USERNAME_PARAM, self::USERNAME_PARAM_DEFAULT );
	}
	
	/**
	 * Отправка сообщения
	 *
	 * @param string 	$message		Текст сообщения
	 * @param string 	$iconEmoji		Иконка сообщения
	 */	
    function send($message, $iconEmoji = ':rocket:') 
	{

        if ( empty( $this->serviceURL ) || empty( $this->channel ) || empty( $this->userName ) )
			return false;
		
        $data = array(
            'payload' => json_encode( array(
				 "channel"    => $this->channel,
				 "text"       => $message,
				 "username"   => $this->userName,
				 "icon_emoji" => $icon_emoji
			 )
            )
        );

        $posting_to_slack = wp_remote_post($this->serviceURL, array(
				'method'      => 'POST',
				'timeout'     => 30,
				'redirection' => 5,
				'httpversion' => '1.0',
				'blocking'    => true,
				'headers'     => array(),
				'body'        => $data,
				'cookies'     => array()
			)
        );

        if ( is_wp_error( $posting_to_slack ) ) 
		{
            echo sprintf(__('Error Found: %s', CPMS ), $posting_to_slack->get_error_message());
			return false;
        } 
		else 
		{
            $status  = intval( wp_remote_retrieve_response_code( $posting_to_slack ) );
            $message = wp_remote_retrieve_body( $posting_to_slack );
            if ( $status !== 200) 
			{
                return new WP_Error( __('Unexpected_response', CPMS ), $message );
            }
        }
		return true;
    }
}