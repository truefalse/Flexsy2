<?php	

	/**
	*	@engine		Flexsy
	*	@author		Ivan Gontarenko
	*	@email		vania.gontarenko@gmail.com
	*	@skype		ivan.gontarenko
	*	
	*	@update		21-10-2012 19:48:23
	*	@host		http://flexsy2
	*	
	*	@file_handler		X:\home\flexsy2\www\_engine\application\confighandler.php	
	*/

	class Config{

		public $site_name	= 'Rent-24';

		public $site_admin_name	= 'Stewie Griffin';

		public $site_admin_email	= 'vania.gontarenko@gmail.com';

		public $site_offline	= '0';

		public $site_offline_message	= 'Site is offline';

		public $db_host	= 'localhost';

		public $db_username	= 'root';

		public $db_passwd	= '';

		public $db_name	= 'rent-24';

		public $db_prefix	= 'fle';

		public $debug	= '1';

		public $err_reporting	= '0';

		public $useSmarty	= '1';

		public $var_separator	= ',';

		public $salt	= 'secret_word';

		public $log_enable	= '0';

		public $defaultLang	= 'en';

		public $addLangCodeToURL	= '0';

		public $template	= 'default';

		public $home_url	= 'index';

		public $handle_route	= '1';

		public $session_name	= 'FLESESS';

		public $session_namespace	= 'FleSessNameSpace';

		public $session_lifetime	= '3600';

		public $cookie_lifetime	= '604800';

		public $cookie_domain	= '';

		public $cookie_path	= '/';

		public $remember_user	= '1';

		public $cache_enable	= '0';

		public $cache_livetime	= '600800';

		public $fcrypt_mask_get	= 'ZX23456A1.';

		public $fcrypt_mask_post	= 'QWERTYZXIJ';

	}