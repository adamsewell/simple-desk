<?php
/**
 * Returns a list marked-up as drop-down menu options */
/**
 * Generates HTML markup for the options of a drop-down menu
 *
 * Takes a list of options and generates the option elements for an HTML
 * select element.  By default, the option values and labels will be the
 * same.  If the values option is set, the option values will use the
 * key of the associative array, and the option label will be the value in
 * the array.  The extend option can be used to ensure that if the selected
 * value does not exist in the menu, it will be automatically added at the
 * top of the list.
 *
 * @author Jonathan Davis
 * @package Shopp Plugin
 *
 * @param array $list The list of options
 * @param int|string $selected The array index, or key name of the selected value
 * @param boolean $values (optional) Use the array key as the option value attribute (defaults to false)
 * @param boolean $extend (optional) Use to add the selected value if it doesn't exist in the specified list of options
 * @return string The markup of option elements
 **/
function sd_menuoptions ($list,$selected=null,$values=false,$extend=false) {
	if (!is_array($list)) return "";

	$_ = array();
	// Extend the options if the selected value doesn't exist
	if ((!in_array($selected,$list) && !isset($list[$selected])) && $extend)
		$_[] = '<option value="'.esc_attr($selected).'">'.esc_html($selected).'</option>';
	foreach ($list as $value => $text) {

		$valueattr = $selectedattr = '';

		if ($values) $valueattr = ' value="'.esc_attr($value).'"';
		if (($values && (string)$value === (string)$selected)
			|| (!$values && (string)$text === (string)$selected))
				$selectedattr = ' selected="selected"';
		if (is_array($text)) {
			$label = $value;
			$_[] = '<optgroup label="'.esc_attr($label).'">';
			$_[] = sd_menuoptions($text,$selected,$values);
			$_[] = '</optgroup>';
			continue;
		}
		$_[] = "<option$valueattr$selectedattr>$text</option>";

	}
	return join('',$_);
}

function sd_get_countries() {
	$countries = array(
		'US' => 'United States',
		'CA' => 'Canada',
		'GB' => 'United Kingdom',
		'AF' => 'Afghanistan',
		'AL' => 'Albania',
		'DZ' => 'Algeria',
		'AS' => 'American Samoa',
		'AD' => 'Andorra',
		'AO' => 'Angola',
		'AI' => 'Anguilla',
		'AQ' => 'Antarctica',
		'AG' => 'Antigua and Barbuda',
		'AR' => 'Argentina',
		'AM' => 'Armenia',
		'AW' => 'Aruba',
		'AU' => 'Australia',
		'AT' => 'Austria',
		'AZ' => 'Azerbaijan',
		'BS' => 'Bahamas',
		'BH' => 'Bahrain',
		'BD' => 'Bangladesh',
		'BB' => 'Barbados',
		'BY' => 'Belarus',
		'BE' => 'Belgium',
		'BZ' => 'Belize',
		'BJ' => 'Benin',
		'BM' => 'Bermuda',
		'BT' => 'Bhutan',
		'BO' => 'Bolivia',
		'BA' => 'Bosnia and Herzegovina',
		'BW' => 'Botswana',
		'BV' => 'Bouvet Island',
		'BR' => 'Brazil',
		'IO' => 'British Indian Ocean Territory',
		'BN' => 'Brunei Darrussalam',
		'BG' => 'Bulgaria',
		'BF' => 'Burkina Faso',
		'BI' => 'Burundi',
		'KH' => 'Cambodia',
		'CM' => 'Cameroon',
		'CV' => 'Cape Verde',
		'KY' => 'Cayman Islands',
		'CF' => 'Central African Republic',
		'TD' => 'Chad',
		'CL' => 'Chile',
		'CN' => 'China',
		'CX' => 'Christmas Island',
		'CC' => 'Cocos Islands',
		'CO' => 'Colombia',
		'KM' => 'Comoros',
		'CD' => 'Congo, Democratic People\'s Republic',
		'CG' => 'Congo, Republic of',
		'CK' => 'Cook Islands',
		'CR' => 'Costa Rica',
		'CI' => 'Cote d\'Ivoire',
		'HR' => 'Croatia/Hrvatska',
		'CU' => 'Cuba',
		'CY' => 'Cyprus Island',
		'CZ' => 'Czech Republic',
		'DK' => 'Denmark',
		'DJ' => 'Djibouti',
		'DM' => 'Dominica',
		'DO' => 'Dominican Republic',
		'TP' => 'East Timor',
		'EC' => 'Ecuador',
		'EG' => 'Egypt',
		'GQ' => 'Equatorial Guinea',
		'SV' => 'El Salvador',
		'ER' => 'Eritrea',
		'EE' => 'Estonia',
		'ET' => 'Ethiopia',
		'FK' => 'Falkland Islands',
		'FO' => 'Faroe Islands',
		'FJ' => 'Fiji',
		'FI' => 'Finland',
		'FR' => 'France',
		'GF' => 'French Guiana',
		'PF' => 'French Polynesia',
		'TF' => 'French Southern Territories',
		'GA' => 'Gabon',
		'GM' => 'Gambia',
		'GE' => 'Georgia',
		'DE' => 'Germany',
		'GR' => 'Greece',
		'GH' => 'Ghana',
		'GI' => 'Gibraltar',
		'GL' => 'Greenland',
		'GD' => 'Grenada',
		'GP' => 'Guadeloupe',
		'GU' => 'Guam',
		'GT' => 'Guatemala',
		'GG' => 'Guernsey',
		'GN' => 'Guinea',
		'GW' => 'Guinea-Bissau',
		'GY' => 'Guyana',
		'HT' => 'Haiti',
		'HM' => 'Heard and McDonald Islands',
		'VA' => 'Holy See (City Vatican State)',
		'HN' => 'Honduras',
		'HK' => 'Hong Kong',
		'HU' => 'Hungary',
		'IS' => 'Iceland',
		'IN' => 'India',
		'ID' => 'Indonesia',
		'IR' => 'Iran',
		'IQ' => 'Iraq',
		'IE' => 'Ireland',
		'IM' => 'Isle of Man',
		'IL' => 'Israel',
		'IT' => 'Italy',
		'JM' => 'Jamaica',
		'JP' => 'Japan',
		'JE' => 'Jersey',
		'JO' => 'Jordan',
		'KZ' => 'Kazakhstan',
		'KE' => 'Kenya',
		'KI' => 'Kiribati',
		'KW' => 'Kuwait',
		'KG' => 'Kyrgyzstan',
		'LA' => 'Lao People\'s Democratic Republic',
		'LV' => 'Latvia',
		'LB' => 'Lebanon',
		'LS' => 'Lesotho',
		'LR' => 'Liberia',
		'LY' => 'Libyan Arab Jamahiriya',
		'LI' => 'Liechtenstein',
		'LT' => 'Lithuania',
		'LU' => 'Luxembourgh',
		'MO' => 'Macau',
		'MK' => 'Macedonia',
		'MG' => 'Madagascar',
		'MW' => 'Malawi',
		'MY' => 'Malaysia',
		'Mv' => 'Maldives',
		'ML' => 'Mali',
		'MT' => 'Malta',
		'MH' => 'Marshall Islands',
		'MQ' => 'Martinique',
		'MR' => 'Mauritania',
		'MU' => 'Mauritius',
		'YT' => 'Mayotte',
		'MX' => 'Mexico',
		'FM' => 'Micronesia',
		'MD' => 'Moldova, Republic of',
		'MC' => 'Monaco',
		'MN' => 'Mongolia',
		'ME' => 'Montenegro',
		'MS' => 'Montserrat',
		'MA' => 'Morocco',
		'MZ' => 'Mozambique',
		'MM' => 'Myanmar',
		'NA' => 'Namibia',
		'NR' => 'Nauru',
		'NP' => 'Nepal',
		'NL' => 'Netherlands',
		'AN' => 'Netherlands Antilles',
		'NC' => 'New Caledonia',
		'NZ' => 'New Zealand',
		'NI' => 'Nicaragua',
		'NE' => 'Niger',
		'NG' => 'Nigeria',
		'NU' => 'Niue',
		'NF' => 'Norfolk Island',
		'KR' => 'North Korea',
		'MP' => 'Northern Mariana Islands',
		'NO' => 'Norway',
		'OM' => 'Oman',
		'PK' => 'Pakistan',
		'PW' => 'Palau',
		'PS' => 'Palestinian Territories',
		'PA' => 'Panama',
		'PG' => 'Papua New Guinea',
		'PY' => 'Paraguay',
		'PE' => 'Peru',
		'PH' => 'Phillipines',
		'PN' => 'Pitcairn Island',
		'PL' => 'Poland',
		'PT' => 'Portugal',
		'PR' => 'Puerto Rico',
		'QA' => 'Qatar',
		'RE' => 'Reunion Island',
		'RO' => 'Romania',
		'RU' => 'Russian Federation',
		'RW' => 'Rwanda',
		'SH' => 'Saint Helena',
		'KN' => 'Saint Kitts and Nevis',
		'LC' => 'Saint Lucia',
		'PM' => 'Saint Pierre and Miquelon',
		'VC' => 'Saint Vincent and the Grenadines',
		'SM' => 'San Marino',
		'ST' => 'Sao Tome and Principe',
		'SA' => 'Saudi Arabia',
		'SN' => 'Senegal',
		'RS' => 'Serbia',
		'SC' => 'Seychelles',
		'SL' => 'Sierra Leone',
		'SG' => 'Singapore',
		'SK' => 'Slovak Republic',
		'SI' => 'Slovenia',
		'SB' => 'Solomon Islands',
		'SO' => 'Somalia',
		'ZA' => 'South Africa',
		'GS' => 'South Georgia',
		'KP' => 'South Korea',
		'ES' => 'Spain',
		'LK' => 'Sri Lanka',
		'SD' => 'Sudan',
		'SR' => 'Suriname',
		'SJ' => 'Svalbard and Jan Mayen Islands',
		'SZ' => 'Swaziland',
		'SE' => 'Sweden',
		'CH' => 'Switzerland',
		'SY' => 'Syrian Arab Republic',
		'TW' => 'Taiwan',
		'TJ' => 'Tajikistan',
		'TZ' => 'Tanzania',
		'TG' => 'Togo',
		'TK' => 'Tokelau',
		'TO' => 'Tonga',
		'TH' => 'Thailand',
		'TT' => 'Trinidad and Tobago',
		'TN' => 'Tunisia',
		'TR' => 'Turkey',
		'TM' => 'Turkmenistan',
		'TC' => 'Turks and Caicos Islands',
		'TV' => 'Tuvalu',
		'UG' => 'Uganda',
		'UA' => 'Ukraine',
		'AE' => 'United Arab Emirates',
		'UY' => 'Uruguay',
		'UM' => 'US Minor Outlying Islands',
		'UZ' => 'Uzbekistan',
		'VU' => 'Vanuatu',
		'VE' => 'Venezuela',
		'VN' => 'Vietnam',
		'VG' => 'Virgin Islands (British)',
		'VI' => 'Virgin Islands (USA)',
		'WF' => 'Wallis and Futuna Islands',
		'EH' => 'Western Sahara',
		'WS' => 'Western Samoa',
		'YE' => 'Yemen',
		'YU' => 'Yugoslavia',
		'ZM' => 'Zambia',
		'ZW' => 'Zimbabwe'
	);

	return $countries;
}
function sd_get_states() {
	$states = array(
		'AL' => 'Alabama',
		'AK' => 'Alaska',
		'AZ' => 'Arizona',
		'AR' => 'Arkansas',
		'CA' => 'California',
		'CO' => 'Colorado',
		'CT' => 'Connecticut',
		'DE' => 'Delaware',
		'DC' => 'District of Columbia',
		'FL' => 'Florida',
		'GA' => 'Georgia',
		'HI' => 'Hawaii',
		'ID' => 'Idaho',
		'IL' => 'Illinois',
		'IN' => 'Indiana',
		'IA' => 'Iowa',
		'KS' => 'Kansas',
		'KY' => 'Kentucky',
		'LA' => 'Louisiana',
		'ME' => 'Maine',
		'MD' => 'Maryland',
		'MA' => 'Massachusetts',
		'MI' => 'Michigan',
		'MN' => 'Minnesota',
		'MS' => 'Mississippi',
		'MO' => 'Missouri',
		'MT' => 'Montana',
		'NE' => 'Nebraska',
		'NV' => 'Nevada',
		'NH' => 'New Hampshire',
		'NJ' => 'New Jersey',
		'NM' => 'New Mexico',
		'NY' => 'New York',
		'NC' => 'North Carolina',
		'ND' => 'North Dakota',
		'OH' => 'Ohio',
		'OK' => 'Oklahoma',
		'OR' => 'Oregon',
		'PA' => 'Pennsylvania',
		'RI' => 'Rhode Island',
		'SC' => 'South Carolina',
		'SD' => 'South Dakota',
		'TN' => 'Tennessee',
		'TX' => 'Texas',
		'UT' => 'Utah',
		'VT' => 'Vermont',
		'VA' => 'Virginia',
		'WA' => 'Washington',
		'WV' => 'West Virginia',
		'WI' => 'Wisconsin',
		'WY' => 'Wyoming',
		'AS' => 'American Samoa',
		'CZ' => 'Canal Zone',
		'CM' => 'Commonwealth of the Northern Mariana Islands',
		'FM' => 'Federated States of Micronesia',
		'GU' => 'Guam',
		'MH' => 'Marshall Islands',
		'MP' => 'Northern Mariana Islands',
		'PW' => 'Palau',
		'PI' => 'Philippine Islands',
		'PR' => 'Puerto Rico',
		'TT' => 'Trust Territory of the Pacific Islands',
		'VI' => 'Virgin Islands',
		'AA' => 'Armed Forces - Americas',
		'AE' => 'Armed Forces - Europe, Canada, Middle East, Africa',
		'AP' => 'Armed Forces - Pacific'
	);
	return $states;
}

function sd_get_ip() {
	if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
		//check ip from share internet
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
		//to check ip is pass from proxy
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} else {
		$ip = $_SERVER['REMOTE_ADDR'];
	}
	return apply_filters( 'edd_get_ip', $ip );
}

function sd_get_ticket_statuses() {
	$ticket_statuses = array(
		'new'   => __( 'New', 'sd' ),
		'inprogress'   => __( 'In Progress', 'sd' ),
		'waitingonme'  => __( 'Waiting on Me', 'sd' ),
		'waitingoncustomer'    => __( 'Waiting on Customer', 'sd' ),
		'resolved' => __( 'Resolved', 'sd' )
	);

	return $ticket_statuses;
}

function sd_send_email($ticket_id, $notification){
	$customer_id = sd_get_ticket_customer($ticket_id);
	$customer_email = sd_get_customer_email($customer_id);
	$current_user = wp_get_current_user();

	if(!sd_customer_has_email($customer_id)){
		return false;
	}


	//headers
	$headers = "From: " . stripslashes_deep( html_entity_decode( $current_user->display_name, ENT_COMPAT, 'UTF-8' ) ) . " <$current_user->user_email>\r\n";
	$headers .= "Reply-To: ". $current_user->user_email . "\r\n";
	$headers .= "Content-Type: text/html; charset=utf-8\r\n";

	//message
	$message = sd_get_email_body_header();

	$message .= sd_get_email_body($notification);

	$message .= sd_get_email_body_footer();

	$mail = wp_mail('adam@mygeeknc.com', sd_get_email_subject($ticket_id), $message, $headers);

	return $mail;
}
