<?php

         //start session in all pages
        if (session_status() == PHP_SESSION_NONE) { session_start(); } //PHP >= 5.4.0
         //if(session_id() == '') { session_start(); } //uncomment this line if PHP < 5.4.0 and comment out line above

	// sandbox or live
	define('PPL_MODE', 'sandbox');

	if(PPL_MODE=='sandbox'){
		
		define('PPL_API_USER', 'dinisnet_api1.hotmail.com');
		define('PPL_API_PASSWORD', 'MS7A35F5UTVZJAAW');
		define('PPL_API_SIGNATURE', 'AexNTB8IxXpiKSx.CEkitV3hSdDaAs6332ZG1hAgiBPFSDqZQc8WuVJU');
	}
	else{
		
		define('PPL_API_USER', 'vilar156_api1.gmail.com');
		define('PPL_API_PASSWORD', 'BBYPS8AC6CHQMJKZ');
		define('PPL_API_SIGNATURE', 'AFcWxV21C7fd0v3bYYYRCpSSRl31Ac2s3EzRidLGedgCBkCgdu31szfl');
	}
	
	define('PPL_LANG', 'EN');
        
        define('PPL_LOGO_IMG', 'http://totalemprego:82/img/thejoblogo5.png');
        
        define('PPL_RETURN_URL', 'http://totalemprego:82/paypalone');
        define('PPL_CANCEL_URL', 'http://totalemprego:82');
//	
//	define('PPL_LOGO_IMG', 'http://www.etiju.com/img/logo.png');
//        
//
//        define('PPL_RETURN_URL', 'http://www.etiju.com/paypalone');
//        define('PPL_CANCEL_URL', 'http://www.etiju.com');

	define('PPL_CURRENCY_CODE', 'EUR');
