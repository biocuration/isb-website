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
			'USD' => __('US Dollars (&#36;)', 'paid-memberships-pro' ),
			'EUR' => array(
				'name' => __('Euros (&euro;)', 'paid-memberships-pro' ),
				'symbol' => '&euro;',
				'position' => apply_filters("pmpro_euro_position", pmpro_euro_position_from_locale())
				),				
			'GBP' => array(
				'name' => __('Pounds Sterling (&pound;)', 'paid-memberships-pro' ),
				'symbol' => '&pound;',
				'position' => 'left'
				),
			'ARS' => __('Argentine Peso (&#36;)', 'paid-memberships-pro' ),
			'AUD' => __('Australian Dollars (&#36;)', 'paid-memberships-pro' ),
			'BRL' => array(
				'name' => __('Brazilian Real (R&#36;)', 'paid-memberships-pro' ),
				'symbol' => 'R&#36;',
				'position' => 'left'
				),
			'CAD' => __('Canadian Dollars (&#36;)', 'paid-memberships-pro' ),
			'CNY' => __('Chinese Yuan', 'paid-memberships-pro' ),
			'CZK' => array(
				'name' => __('Czech Koruna', 'paid-memberships-pro' ),
            			'decimals' => '0',
            			'thousands_separator' => '&nbsp;',
            			'decimal_separator' => ',',
            			'symbol' => '&nbsp;Kč',
            			'position' => 'right',
				),
			'DKK' => __('Danish Krone', 'paid-memberships-pro' ),
			'HKD' => __('Hong Kong Dollar (&#36;)', 'paid-memberships-pro' ),
			'HUF' => __('Hungarian Forint', 'paid-memberships-pro' ),
			'INR' => __('Indian Rupee', 'paid-memberships-pro' ),
			'IDR' => __('Indonesia Rupiah', 'paid-memberships-pro' ),
			'ILS' => __('Israeli Shekel', 'paid-memberships-pro' ),
			'JPY' => array(
				'name' => __('Japanese Yen (&yen;)', 'paid-memberships-pro' ),
				'symbol' => '&yen;',
				'position' => 'right',
				'decimals' => 0,
				),
			'MYR' => __('Malaysian Ringgits', 'paid-memberships-pro' ),
			'MXN' => __('Mexican Peso (&#36;)', 'paid-memberships-pro' ),
			'NGN' => __('Nigerian Naira (&#8358;)', 'paid-memberships-pro' ),
			'NZD' => __('New Zealand Dollar (&#36;)', 'paid-memberships-pro' ),
			'NOK' => __('Norwegian Krone', 'paid-memberships-pro' ),
			'PHP' => __('Philippine Pesos', 'paid-memberships-pro' ),
			'PLN' => __('Polish Zloty', 'paid-memberships-pro' ),
			'SGD' => array(
				'name' => __('Singapore Dollar (&#36;)', 'paid-memberships-pro' ),
				'symbol' => '&#36;',
				'position' => 'right'
				),
			'ZAR' => array(
				'name' => __('South African Rand (R)', 'paid-memberships-pro' ),
				'symbol' => 'R ',
				'position' => 'left'
			),			
			'KRW' => array(
				'name' => __('South Korean Won', 'paid-memberships-pro' ),
				'decimals' => 0,
				),
			'SEK' => __('Swedish Krona', 'paid-memberships-pro' ),
			'CHF' => __('Swiss Franc', 'paid-memberships-pro' ),
			'TWD' => __('Taiwan New Dollars', 'paid-memberships-pro' ),
			'THB' => __('Thai Baht', 'paid-memberships-pro' ),
			'TRY' => __('Turkish Lira', 'paid-memberships-pro' ),
			'VND' => array(
				'name' => __('Vietnamese Dong', 'paid-memberships-pro' ),
				'decimals' => 0,
				),
			);
	
	$pmpro_currencies = apply_filters("pmpro_currencies", $pmpro_currencies);
	
	//stripe only supports a few (not using this anymore since 1.7.4)
	global $pmpro_stripe_currencies;
	$pmpro_stripe_currencies = array(
			'USD' => __('US Dollars (&#36;)', 'paid-memberships-pro' ),			
			'CAD' => __('Canadian Dollars (&#36;)', 'paid-memberships-pro' ),
			'GBP' => __('Pounds Sterling (&pound;)', 'paid-memberships-pro' ),
			'EUR' => __('Euros (&euro;)', 'paid-memberships-pro' )
	);
?>
