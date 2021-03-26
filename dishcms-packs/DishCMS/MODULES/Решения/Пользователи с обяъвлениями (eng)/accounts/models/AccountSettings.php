<?php
/**
 * Настройки модуля "Аккаунты"
 * 
 */
namespace accounts\models;

class AccountSettings extends \settings\components\base\SettingsModel
{
    public $id=1;
    
    /**
     * Секретный ключ для хэширования
     * @var string
     */
    public $secret_key='';
    
    /**
     * Подтверждать регистрацию
     * @var string
     */
    public $reg_confirm_mode=1;
    
    /**
     * Текст 
     * @var string
     */
    public $reg_done_text='Регистрация успешно завершена';
    
    /**
     * Дополнительный текст в форме регистрации
     * @var string
     */
    public $reg_form_text='';
    
    public $privacy_link;
    public $terms_link;
    
    public $signin_form_text;
    
    public $phone_country_codes;
    public $phone_country_codes_preferrer;
    
    /**
     * @var boolean для совместимости со старым виджетом
     * редактора admin.widget.EditWidget.TinyMCE
     */
    public $isNewRecord=false;
    
    /**
     * Для совместимости со старым виджетом
     * редактора admin.widget.EditWidget.TinyMCE
     */
    public function tableName()
    {
        return 'account_settings';
    }    
    
    /**
     * {@inheritDoc}
     * @see \settings\components\base\SettingsModel::rules()
     */
    public function rules()
    {
        return [
            ['secret_key', 'required'],
            ['secret_key', 'length', 'min'=>8],
            ['reg_confirm_mode', 'boolean'],
            ['reg_done_text, reg_form_text', 'safe'],
            ['reg_email_before, reg_email_after', 'safe'],
            ['privacy_link, terms_link', 'safe'],
            ['signin_form_text, phone_country_codes, phone_country_codes_preferrer', 'safe'],
        ];
    }
    
    /**
     * {@inheritDoc}
     * @see \settings\components\base\SettingsModel::attributeLabels()
     */
    public function attributeLabels()
    {
       return [
           'secret_key'=>'Секретный ключ',
           'reg_confirm_mode'=>'Пользователю необходимо подтверждать регистрацию',
           'reg_done_text'=>'Текст успешного завершения регистрации',
           'reg_form_text'=>'Дополнительный текст в форме регистрации',
           'signin_form_text'=>'Дополнительный текст в форме авторизации',
           'terms_link'=>'URL страницы Terms of Service',
           'privacy_link'=>'URL страницы Privacy Policy',
           'phone_country_codes'=>'Страны, которые будут доступны при выборе кода страны для номера телефона',
           'phone_country_codes_preferrer'=>'Страны, которые будут доступны при выборе кода страны для номера телефона, как приоритетные'
       ];
    }
    
    /**
     * {@inheritDoc}
     * @see \CModel::beforeValidate()
     */
    public function beforeValidate()
    {
        $this->secret_key=trim($this->secret_key);
        
        return true;
    }
    
    public function getPhoneCountryCodesPreferrer()
    {
        if(!is_array($this->phone_country_codes_preferrer)) {
            return json_decode($this->phone_country_codes_preferrer, true);
        }
        
        return $this->phone_country_codes_preferrer;
    }
   
    public function getPhoneCountryCodes()
    {
        if(!is_array($this->phone_country_codes)) {
            return json_decode($this->phone_country_codes, true);
        }
        
        return $this->phone_country_codes;
    }
    
    public function beforeSave()
    {
        if(is_array($this->phone_country_codes)) {
            $this->phone_country_codes=json_encode($this->phone_country_codes);
        }
        if(is_array($this->phone_country_codes_preferrer)) {
            $this->phone_country_codes_preferrer=json_encode($this->phone_country_codes_preferrer);
        }
    }
    
    /**
     * Подтверждение регистрации активировано
     * @return boolean
     */
    public function isRegConfirmMode()
    {
        return false; // ((int)$this->reg_confirm_mode > 0);
    }
    
    public function getAllPhoneCountryCodes()
    {
        return [
            'af'=>'Afghanistan (‫افغانستان‬‎)',
            'al'=>'Albania (Shqipëri)',
            'dz'=>'Algeria (‫الجزائر‬‎)',
            'as'=>'American Samoa',
            'ad'=>'Andorra',
            'ao'=>'Angola',
            'ai'=>'Anguilla',
            'ag'=>'Antigua and Barbuda',
            'ar'=>'Argentina',
            'am'=>'Armenia (Հայաստան)',
            'aw'=>'Aruba',
            'au'=>'Australia',
            'at'=>'Austria (Österreich)',
            'az'=>'Azerbaijan (Azərbaycan)',
            'bs'=>'Bahamas',
            'bh'=>'Bahrain (‫البحرين‬‎)',
            'bd'=>'Bangladesh (বাংলাদেশ)',
            'bb'=>'Barbados',
            'by'=>'Belarus (Беларусь)',
            'be'=>'Belgium (België)',
            'bz'=>'Belize',
            'bj'=>'Benin (Bénin)',
            'bm'=>'Bermuda',
            'bt'=>'Bhutan (འབྲུག)',
            'bo'=>'Bolivia',
            'ba'=>'Bosnia and Herzegovina (Босна и Херцеговина)',
            'bw'=>'Botswana',
            'br'=>'Brazil (Brasil)',
            'io'=>'British Indian Ocean Territory',
            'vg'=>'British Virgin Islands',
            'bn'=>'Brunei',
            'bg'=>'Bulgaria (България)',
            'bf'=>'Burkina Faso',
            'bi'=>'Burundi (Uburundi)',
            'kh'=>'Cambodia (កម្ពុជា)',
            'cm'=>'Cameroon (Cameroun)',
            'ca'=>'Canada',
            'cv'=>'Cape Verde (Kabu Verdi)',
            'bq'=>'Caribbean Netherlands',
            'ky'=>'Cayman Islands',
            'cf'=>'Central African Republic (République centrafricaine)',
            'td'=>'Chad (Tchad)',
            'cl'=>'Chile',
            'cn'=>'China (中国)',
            'cx'=>'Christmas Island',
            'cc'=>'Cocos (Keeling) Islands',
            'co'=>'Colombia',
            'km'=>'Comoros (‫جزر القمر‬‎)',
            'cd'=>'Congo (DRC) (Jamhuri ya Kidemokrasia ya Kongo)',
            'cg'=>'Congo (Republic) (Congo-Brazzaville)',
            'ck'=>'Cook Islands',
            'cr'=>'Costa Rica',
            'ci'=>'Côte d’Ivoire',
            'hr'=>'Croatia (Hrvatska)',
            'cu'=>'Cuba',
            'cw'=>'Curaçao',
            'cy'=>'Cyprus (Κύπρος)',
            'cz'=>'Czech Republic (Česká republika)',
            'dk'=>'Denmark (Danmark)',
            'dj'=>'Djibouti',
            'dm'=>'Dominica',
            'do'=>'Dominican Republic (República Dominicana)',
            'ec'=>'Ecuador',
            'eg'=>'Egypt (‫مصر‬‎)',
            'sv'=>'El Salvador',
            'gq'=>'Equatorial Guinea (Guinea Ecuatorial)',
            'er'=>'Eritrea',
            'ee'=>'Estonia (Eesti)',
            'et'=>'Ethiopia',
            'fk'=>'Falkland Islands (Islas Malvinas)',
            'fo'=>'Faroe Islands (Føroyar)',
            'fj'=>'Fiji',
            'fi'=>'Finland (Suomi)',
            'fr'=>'France',
            'gf'=>'French Guiana (Guyane française)',
            'pf'=>'French Polynesia (Polynésie française)',
            'ga'=>'Gabon',
            'gm'=>'Gambia',
            'ge'=>'Georgia (საქართველო)',
            'de'=>'Germany (Deutschland)',
            'gh'=>'Ghana (Gaana)',
            'gi'=>'Gibraltar',
            'gr'=>'Greece (Ελλάδα)',
            'gl'=>'Greenland (Kalaallit Nunaat)',
            'gd'=>'Grenada',
            'gp'=>'Guadeloupe',
            'gu'=>'Guam',
            'gt'=>'Guatemala',
            'gg'=>'Guernsey',
            'gn'=>'Guinea (Guinée)',
            'gw'=>'Guinea-Bissau (Guiné Bissau)',
            'gy'=>'Guyana',
            'ht'=>'Haiti',
            'hn'=>'Honduras',
            'hk'=>'Hong Kong (香港)',
            'hu'=>'Hungary (Magyarország)',
            'is'=>'Iceland (Ísland)',
            'in'=>'India (भारत)',
            'id'=>'Indonesia',
            'ir'=>'Iran (‫ایران‬‎)',
            'iq'=>'Iraq (‫العراق‬‎)',
            'ie'=>'Ireland',
            'im'=>'Isle of Man',
            'il'=>'Israel (‫ישראל‬‎)',
            'it'=>'Italy (Italia)',
            'jm'=>'Jamaica',
            'jp'=>'Japan (日本)',
            'je'=>'Jersey',
            'jo'=>'Jordan (‫الأردن‬‎)',
            'kz'=>'Kazakhstan (Казахстан)',
            'ke'=>'Kenya',
            'ki'=>'Kiribati',
            'xk'=>'Kosovo',
            'kw'=>'Kuwait (‫الكويت‬‎)',
            'kg'=>'Kyrgyzstan (Кыргызстан)',
            'la'=>'Laos (ລາວ)',
            'lv'=>'Latvia (Latvija)',
            'lb'=>'Lebanon (‫لبنان‬‎)',
            'ls'=>'Lesotho',
            'lr'=>'Liberia',
            'ly'=>'Libya (‫ليبيا‬‎)',
            'li'=>'Liechtenstein',
            'lt'=>'Lithuania (Lietuva)',
            'lu'=>'Luxembourg',
            'mo'=>'Macau (澳門)',
            'mk'=>'Macedonia (FYROM) (Македонија)',
            'mg'=>'Madagascar (Madagasikara)',
            'mw'=>'Malawi',
            'my'=>'Malaysia',
            'mv'=>'Maldives',
            'ml'=>'Mali',
            'mt'=>'Malta',
            'mh'=>'Marshall Islands',
            'mq'=>'Martinique',
            'mr'=>'Mauritania (‫موريتانيا‬‎)',
            'mu'=>'Mauritius (Moris)',
            'yt'=>'Mayotte',
            'mx'=>'Mexico (México)',
            'fm'=>'Micronesia',
            'md'=>'Moldova (Republica Moldova)',
            'mc'=>'Monaco',
            'mn'=>'Mongolia (Монгол)',
            'me'=>'Montenegro (Crna Gora)',
            'ms'=>'Montserrat',
            'ma'=>'Morocco (‫المغرب‬‎)',
            'mz'=>'Mozambique (Moçambique)',
            'mm'=>'Myanmar (Burma) (မြန်မာ)',
            'na'=>'Namibia (Namibië)',
            'nr'=>'Nauru',
            'np'=>'Nepal (नेपाल)',
            'nl'=>'Netherlands (Nederland)',
            'nc'=>'New Caledonia (Nouvelle-Calédonie)',
            'nz'=>'New Zealand',
            'ni'=>'Nicaragua',
            'ne'=>'Niger (Nijar)',
            'ng'=>'Nigeria',
            'nu'=>'Niue',
            'nf'=>'Norfolk Island',
            'kp'=>'North Korea (조선 민주주의 인민 공화국)',
            'mp'=>'Northern Mariana Islands',
            'no'=>'Norway (Norge)',
            'om'=>'Oman (‫عُمان‬‎)',
            'pk'=>'Pakistan (‫پاکستان‬‎)',
            'pw'=>'Palau',
            'ps'=>'Palestine (‫فلسطين‬‎)',
            'pa'=>'Panama (Panamá)',
            'pg'=>'Papua New Guinea',
            'py'=>'Paraguay',
            'pe'=>'Peru (Perú)',
            'ph'=>'Philippines',
            'pl'=>'Poland (Polska)',
            'pt'=>'Portugal',
            'pr'=>'Puerto Rico',
            'qa'=>'Qatar (‫قطر‬‎)',
            're'=>'Réunion (La Réunion)',
            'ro'=>'Romania (România)',
            'ru'=>'Russia (Россия)',
            'rw'=>'Rwanda',
            'bl'=>'Saint Barthélemy',
            'sh'=>'Saint Helena',
            'kn'=>'Saint Kitts and Nevis',
            'lc'=>'Saint Lucia',
            'mf'=>'Saint Martin (Saint-Martin (partie française))',
            'pm'=>'Saint Pierre and Miquelon (Saint-Pierre-et-Miquelon)',
            'vc'=>'Saint Vincent and the Grenadines',
            'ws'=>'Samoa',
            'sm'=>'San Marino',
            'st'=>'São Tomé and Príncipe (São Tomé e Príncipe)',
            'sa'=>'Saudi Arabia (‫المملكة العربية السعودية‬‎)',
            'sn'=>'Senegal (Sénégal)',
            'rs'=>'Serbia (Србија)',
            'sc'=>'Seychelles',
            'sl'=>'Sierra Leone',
            'sg'=>'Singapore',
            'sx'=>'Sint Maarten',
            'sk'=>'Slovakia (Slovensko)',
            'si'=>'Slovenia (Slovenija)',
            'sb'=>'Solomon Islands',
            'so'=>'Somalia (Soomaaliya)',
            'za'=>'South Africa',
            'kr'=>'South Korea (대한민국)',
            'ss'=>'South Sudan (‫جنوب السودان‬‎)',
            'es'=>'Spain (España)',
            'lk'=>'Sri Lanka (ශ්‍රී ලංකාව)',
            'sd'=>'Sudan (‫السودان‬‎)',
            'sr'=>'Suriname',
            'sj'=>'Svalbard and Jan Mayen',
            'sz'=>'Swaziland',
            'se'=>'Sweden (Sverige)',
            'ch'=>'Switzerland (Schweiz)',
            'sy'=>'Syria (‫سوريا‬‎)',
            'tw'=>'Taiwan (台灣)',
            'tj'=>'Tajikistan',
            'tz'=>'Tanzania',
            'th'=>'Thailand (ไทย)',
            'tl'=>'Timor-Leste',
            'tg'=>'Togo',
            'tk'=>'Tokelau',
            'to'=>'Tonga',
            'tt'=>'Trinidad and Tobago',
            'tn'=>'Tunisia (‫تونس‬‎)',
            'tr'=>'Turkey (Türkiye)',
            'tm'=>'Turkmenistan',
            'tc'=>'Turks and Caicos Islands',
            'tv'=>'Tuvalu',
            'vi'=>'U.S. Virgin Islands',
            'ug'=>'Uganda',
            'ua'=>'Ukraine (Україна)',
            'ae'=>'United Arab Emirates (‫الإمارات العربية المتحدة‬‎)',
            'gb'=>'United Kingdom',
            'us'=>'United States',
            'uy'=>'Uruguay',
            'uz'=>'Uzbekistan (Oʻzbekiston)',
            'vu'=>'Vanuatu',
            'va'=>'Vatican City (Città del Vaticano)',
            've'=>'Venezuela',
            'vn'=>'Vietnam (Việt Nam)',
            'wf'=>'Wallis and Futuna (Wallis-et-Futuna)',
            'eh'=>'Western Sahara (‫الصحراء الغربية‬‎)',
            'ye'=>'Yemen (‫اليمن‬‎)',
            'zm'=>'Zambia',
            'zw'=>'Zimbabwe',
            'ax'=>'Åland Islands'
        ];
    }
}
