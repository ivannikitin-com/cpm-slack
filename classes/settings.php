<?php
/**
 * Класс реализует загрузку и сохранение любых параметров
 */
namespace CMPS;
class Settings
{
	/**
	 * Основной класс плагина
	 * @var string
	 */
	protected $plugin;	
	
	/**
	 * Название опции в Wordpress
	 * @var string
	 */
	protected $_name;	
	
	
	/**
	 * Массив хранения параметров
	 * @var mixed
	 */
	protected $_params;
	
	/**
	 * Конструктор
	 * инициализирует параметры и загружает данные
	 * @param string 		$optionName		Название опции в Wordpress, по умолчанию используется имя класса
	 * @param INTEAM\Plugin $plugin			Ссылка на основной объект плагина
	 */
	public function __construct( $optionName = '', $plugin )
	{
		if ( empty ( $optionName ) ) $optionName = get_class( $this );
		$this->_name = $optionName;
		
		$this->plugin = $plugin;
		
		// Загружаем параметры
		$this->load();
		
		// Если это работа в админке
		if ( is_admin() )
		{
			// Стили для админки
			 wp_enqueue_style( CMPS, $this->plugin->url . 'assets/css/admin.css' );
			
			// Страница настроек
			add_action( 'admin_menu', array( $this, 'addSettingsPage' ) );
		}
	}
	
	/**
	 * Загрузка параметров в массив из БД Wordpress
	 */
	public function load()
	{
		$this->_params = get_option( $this->_name, array() );
	}
	
	/**
	 * Сохранение параметров в БД Wordpress
	 */
	public function save()
	{
		update_option( $this->_name, $this->_params );
	}

	/**
	 * Чтение параметра
	 * @param string	$param		Название параметра
	 * @param mixed 	$default	Значение параметра по умолчанию, если его нет или он пустой
	 * @return mixed				Возвращает параметр
	 */
	public function get( $param, $default = false )
	{
		if ( ! isset( $this->_params[ $param ] ) )
			return $default;
		
		if ( empty( $this->_params[ $param ] ) )
			return $default;
		
		return $this->_params[ $param ];
	}
	
	/**
	 * Сохранение параметра
	 * @param string	$param		Название параметра
	 * @param mixed 	$value		Значение параметра
	 */
	public function set( $param, $value )
	{
		$this->_params[ $param ] = $value;
	}
	
	/**
	 * Чтение свойства
	 * @param string	$param		Название параметра
	 */
	public function __get( $param )
	{
		return $this->get( $param );
	}
	/**
	 * Запись свойства
	 * @param string	$param		Название параметра
	 */
	public function __set( $param, $value )
	{
		return $this->set( $param, $value );
	}	
	

	/** ==========================================================================================
	 * Добавляет страницу настроект плагина в меню типа данных
	 */
	public function addSettingsPage()
	{
		add_submenu_page(
			'cpm_projects',
			__( 'Slack Integration', CMPS ),
			__( 'Slack Integration', CMPS ),
			'manage_options',
			CMPS,
			array( $this, 'showSettingsPage' )
		);		
	}
	
	/** 
	 * Выводит страницу настроект плагина
	 */
	public function showSettingsPage( )
	{	
		$nonceField = CMPS;
		$nonceAction = 'save-settings';
		$nonceError = false;
		
		// Обработка формы
		if ( $_SERVER['REQUEST_METHOD'] == 'POST' )
		{
			if ( ! isset( $_POST[$nonceField] ) || ! wp_verify_nonce( $_POST[$nonceField], $nonceAction ) ) 
			{
				$nonceError = true;
			} 
			else 
			{
				// process form data
				$this->set( Slack::SEVICE_URL_PARAM, 	sanitize_text_field( $_POST['cmpsServiceURL'] ) );
				$this->set( Slack::CHANNEL_PARAM, 		sanitize_text_field( $_POST['cmpsChannel'] ) );
				$this->set( Slack::USERNAME_PARAM, 		sanitize_text_field( $_POST['cmpsUserName'] ) );
				$this->save();
				
				if ( isset( $_POST['test'] ) )
				{
					$testSlack = new Slack( $this->plugin );
					$testSlack->send( __( 'Test message', CMPS ) );
				}
			}		
		}
		
?>
<h1><?php esc_html_e( 'Slack Integration Settings', CMPS ) ?></h1>
<p><?php esc_html_e( 'Please, specify settings for Slack integration.', CMPS ) ?></p>
<?php if ( $nonceError ) _e( 'Error: The nonce is not valid!', CMPS ) ?>

<form id="cmps-settings" action="<?php echo $_SERVER['REQUEST_URI']?>" method="post">
	<?php wp_nonce_field( $nonceAction, $nonceField ) ?>
	
	<div class="cmps-field">
		<label for="cmpsServiceURL"><?php esc_html_e( 'Service URL', CMPS ) ?></label>
		<div class="cmps-input">
			<input id="cmpsServiceURL" name="cmpsServiceURL" type="text" value="<?php echo esc_attr( $this->get( Slack::SEVICE_URL_PARAM ) ) ?>" />
			<p><?php esc_html_e( 'Specify URL for Slack incoming webhooks.', CMPS ); 
			echo ' ';
			_e( 'Read <a href="https://my.slack.com/services/new/incoming-webhook/" target="_blank">this</a> first.', CMPS );
			echo ' ';
			_e( 'Please note, no data sends to Slack if this field is blank.', CMPS );
			?></p>
		</div>
	</div>
	
	<div class="cmps-field">
		<label for="cmpsChannel"><?php esc_html_e( 'Slack Channel', CMPS ) ?></label>
		<div class="cmps-input">
			<input id="cmpsChannel" name="cmpsChannel" type="text" value="<?php echo esc_attr( $this->get( Slack::CHANNEL_PARAM ) ) ?>" />
			<p><?php esc_html_e( 'Specify Slack Channel.', CMPS ); 
			echo ' ';
			_e( 'Please note, no data sends to Slack if this field is blank.', CMPS );
			?></p>
		</div>
	</div>
	
	<div class="cmps-field">
		<label for="cmpsUserName"><?php esc_html_e( 'UserName (Bot)', CMPS ) ?></label>
		<div class="cmps-input">
			<input id="cmpsUserName" name="cmpsUserName" type="text" value="<?php echo esc_attr( $this->get( Slack::USERNAME_PARAM ) ) ?>" />
			<p><?php esc_html_e( 'Specify the Username (Bot name).', CMPS ); 
			echo ' ';
			_e( 'Please note, no data sends to Slack if this field is blank.', CMPS );
			?></p>
		</div>
	</div>	
	<?php submit_button( __( 'Save and test', CMPS ), 'secondary', 'test' ) ?>
	
	<hr>
	
	<h2><?php esc_html_e( 'New item', CMPS ) ?></h2>
	<p><?php esc_html_e( 'This settings are apply for all new items.', CMPS ) ?></p>
	
	<div class="cmps-field">
		<label for="cmpsNewItem"><?php esc_html_e( 'Message', CMPS ) ?></label>
		<div class="cmps-input">
			<input id="cmpsNewItem" name="cmpsNewItem" type="text" value="<?php echo esc_attr( $this->get( Slack::USERNAME_PARAM ) ) ?>" />
			<p><?php esc_html_e( 'Specify the Username (Bot name).', CMPS ); 
			echo ' ';
			_e( 'Please note, no data sends to Slack if this field is blank.', CMPS );
			?></p>
		</div>
	</div>	
	
	
	<?php submit_button() ?>
</form>
<?php	
	}
}