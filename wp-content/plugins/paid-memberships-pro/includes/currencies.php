<?php
	//thanks jigoshop
	global $pmpro_currencies, $pmpro_default_currency;
	$pmpro_default_currency = apply_filters("pmpro_default_currency", "USD");
	
	function pmpro_euro_position_from_locale($position = 'right')
	{
		$locale = get_locale();
		if(strpos($locale, 'en_') === 0)
		{
			$position = 'left';
		}
		return $position;
	}
	
	$pmpro_currencies = array( 
			'USD' => __('US Dollars (&#36;)', 'pmpro'),
			'EUR' => array(
				'name' => __('Euros (&euro;)', 'pmpro'),
				'symbol' => '&euro;',
				'position' => apply_filters("pmpro_euro_position", pmpro_euro_position_from_locale())
				),				
			'GBP' => array(
				'name' => __('Pounds Sterling (&pound;)', 'pmpro'),
				'symbol' => '&pound;',
				'position' => 'left'
				),
			'ARS' => __('Argentine Peso (&#36;)', 'pmpro'),
			'AUD' => __('Australian Dollars (&#36;)', 'pmpro'),
			'BRL' => array(
				'name' => __('Brazilian Real (R&#36;)', 'pmpro'),
				'symbol' => 'R&#36;',
				'position' => 'left'
				),
			'CAD' => __('Canadian Dollars (&#36;)', 'pmpro'),
			'CNY' => __('Chinese Yuan', 'pmpro'),
			'CZK' => array(
				'name' => __('Czech Koruna', 'pmpro'),
            			'decimals' => '0',
            			'thousands_separator' => '&nbsp;',
            			'decimal_separator' => ',',
            			'symbol' => '&nbsp;Kč',
            			'position' => 'right',
				),
			'DKK' => __('Danish Krone', 'pmpro'),
			'HKD' => __('Hong Kong Dollar (&#36;)', 'pmpro'),
			'HUF' => __('Hungarian Forint', 'pmpro'),
			'INR' => __('Indian Rupee', 'pmpro'),
			'IDR' => __('Indonesia Rupiah', 'pmpro'),
			'ILS' => __('Israeli Shekel', 'pmpro'),
			'JPY' => array(
				'name' => __('Japanese Yen (&yen;)', 'pmpro'),
				'symbol' => '&yen;',
				'position' => 'right',
				'decimals' => 0,
				),
			'MYR' => __('Malaysian Ringgits', 'pmpro'),
			'MXN' => __('Mexican Peso (&#36;)', 'pmpro'),
			'NGN' => __('Nigerian Naira (&#8358;)', 'pmpro'),
			'NZD' => __('New Zealand Dollar (&#36;)', 'pmpro'),
			'NOK' => __('Norwegian Krone', 'pmpro'),
			'PHP' => __('Philippine Pesos', 'pmpro'),
			'PLN' => __('Polish Zloty', 'pmpro'),
			'SGD' => array(
				'name' => __('Singapore Dollar (&#36;)', 'pmpro'),
				'symbol' => '&#36;',
				'position' => 'right'
				),
			'ZAR' => array(
				'name' => __('South African Rand (R)', 'pmpro'),
				'symbol' => 'R ',
				'position' => 'left'
			),			
			'KRW' => array(
				'name' => __('South Korean Won', 'pmpro'),
				'decimals' => 0,
				),
			'SEK' => __('Swedish Krona', 'pmpro'),
			'CHF' => __('Swiss Franc', 'pmpro'),
			'TWD' => __('Taiwan New Dollars', 'pmpro'),
			'THB' => __('Thai Baht', 'pmpro'),
			'TRY' => __('Turkish Lira', 'pmpro'),
			'VND' => array(
				'name' => __('Vietnamese Dong', 'pmpro'),
				'decimals' => 0,
				),
			);
	
	$pmpro_currencies = apply_filters("pmpro_currencies", $pmpro_currencies);
	
	//stripe only supports a few (not using this anymore since 1.7.4)
	global $pmpro_stripe_currencies;
	$pmpro_stripe_currencies = array(
			'USD' => __('US Dollars (&#36;)', 'pmpro'),			
			'CAD' => __('Canadian Dollars (&#36;)', 'pmpro'),
			'GBP' => __('Pounds Sterling (&pound;)', 'pmpro'),
			'EUR' => __('Euros (&euro;)', 'pmpro')
	);
?>
