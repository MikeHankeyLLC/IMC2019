<?

    // SMTP
	define('SMTP_HOST','smtp.gmail.com');
	define('SMTP_USER','webserver@imo.net');
	define('SMTP_PWD','PleaseRelayMyIMOMail!!');
	define('SMTPSecure','tls');
	define('SMTP_TLS_PORT','587');


    // Encryption Variables
    const ENC_ALGO = 'AES-256-CBC';
    const ENC_HASHING = 'sha256';
    const ENC_SEC = 'Met30rsAreB3autiful!';

    // Cookie Variables
	define ('COOKIE_EXPIRES', 3600);

    // Excel Path
    define ('PHPEXCEL_ROOT', ROOT_DIR . '/application/system/libraries/'); 
    
    // reCaptcha v2.0
    define ('reCaptcha_Site_Key', '6LdAGA8TAAAAAIEHtIL1zcJHdkKZxZuL-xnGrxCd');
    define ('reCaptcha_Secret_Key', '6LdAGA8TAAAAANWYcaTkJuzkRsjCuBA8HCbX16To');

    // Encryption Variables
	define ('CRYPT_ALGO', 'tripledes');
	define ('CRYPT_MODE', 'cbc');
	define ('CRYPT_IV',   '37128423');

    // Pathing Variables
	define ('VIEWS', ROOT_DIR . '/application/views');
    
    define ('MAX_RECORD_TO_DISPLAY', 2000);
    define ('PAGINATION_PER_PAGE',100);

    // Sessions
    define ('SESSIONS', serialize(array(
         1=>'Video meteor work',
         2=>'Radio meteor work',
         3=>'Visual meteor work',
         4=>'Meteor physics and dynamics',
         5=>'Meteor stream analyses and modelling',
         6=>'Meteor related software and hardware',
         7=>'Ongoing meteor work',
         8=>'Miscellaneous')));

    // Payment Methods
    define ('PAYMENT_METHODS', serialize(array(
        1=>'Paypal or Credit Card',
        2=>'Bank Transfer',  
        3=>'Other')));
        
    // Food Requiement
    define ('FOOD_REQUIREMENTS', serialize(array(
        'no_req'=>'No requirement',
        'veg'=>'Vegetarian',
        'veg2'=>'Vegan',
        'coel'=>'Coeliac',
        'lact'=>'Lactose-intolerant', 
        'other'=>'Other')));   
    
    // Proceeding
    define ('PROCEEDINGS', serialize(array(
        'pdf'   =>'Only PDF (Free)',
        'print' =>'PDF and Printed copy (20&euro;)',
        ' '     => 'No Proceedings')));        
        
    
   // Paper delivery dates
   define ('DELIVERY_DATES', serialize(array(
        'before' => array(
            'code' => 'Before_the_IMC',
            'text' => 'Before the IMC'
        ),
        'during' => array(
            'code' => 'During_the_IMC',
            'text' => 'During the IMC'
        ),
        'date1' => array(
            'code' => 'No_later_than',
            'text' => 'No later than November 1, 2019'
        ) 
   )));
   
   //  DEADLINE  SUBMIT PAPER
   define('DEADLINE_PAPER','2019-09-23 23:59:59.9');
   


   // CONF YEAR 
   define('CONF_YEAR',2019);
   define('CONF_MONTH','10');
   define('CONF_DATES',' October 3-6');
   define('CONF_DATE_START','October 3<sup>rd</sup>');
   define('CONF_DATE_END','October 6<sup>th</sup>');
   define('CONF_LOCATION','Bollmannsruh, Germany');
   define('CONF_CITY','Bollmannsruh');
   define('CONF_VENUE','TBD');
   define('CONF_EDITION','38<sup>th</sup>');
   
   
   // Cancellation Policy
   define('FULL_REIMBURSEMENT','2019-07-01');
   
   // For Registration form (arrival/departure)
   define('CONF_ARRIVAL_DAY','2019-10-03');
   define('CONF_DEPARTURE_DAY','2019-10-06');
    
   // Thank you in local langugage
   define('THANK_YOU','Danke schön!');
    
    
   // REGISTER DEADLINE  
   define('DEADLINE','2019-10-01 23:59:59.9');
    
    // REGISTER DEADLINE  
   define('PHOTO_DEADLINE','2019-09-20 23:59:59.9');
          
   // EARLY BIRDS
   define('EARLY_BIRD_DATE','2019-06-30 23:59:59.9');

   define('FEE_AFTER_EARLY',20);
   define('PROCEEDINGS_PRICE',20);

   define ('ACCOMODATIONS', serialize(array(
        '2_bed_room' => array(
            'details' => 'Shared double room in the IMC host',
            'abbr'    => 'Double r.',
            'price'   => 160,
            'price_late' => 180,
            'early_bird_date' => EARLY_BIRD_DATE,
            'currency' => '&euro;',
            'covers'   => 'Standard accommodation in a double room for <strong> 3 nights (only)</strong> with full board + participation in the conference, conference materials, coffee breaks and excursion (price per person).'
        ),               
        '1_bed_room' => array(
            'details' => 'Single room in the IMC host',
            'abbr'    => 'Single r.',
            'price'   => 190,
            'price_late' => 210,
            'early_bird_date' => EARLY_BIRD_DATE,
            'currency' => '&euro;',
            'covers'   => 'Accommodation in a single room for <strong> 3 nights (only)</strong> with full board + participation in the conference, conference materials, coffee breaks and excursion. Limited availability.'
        ),               
        'no_acc'   => array(
            'details' => 'No accommodation',
            'abbr'    => 'No acc.',
            'price'   => 90,
            'price_late' => 100,
            'early_bird_date' => EARLY_BIRD_DATE,
            'currency' => '&euro;',
            'covers'   => 'All meals except breakfasts + participation in the conference, conference materials, coffee breaks and excursion.'
        )        
)));              



     // Tshirt PRICE
    define ('TSHIRT_PRICE', 10);
        
    // Currencies
    define ('CURRENCIES', serialize(array('Euro','Dollar')));

    // Facebook page
    //define('FB_EVENT_PAGE','https://www.facebook.com/events/354941928201809/');
         
    
    // IMC 2016 Specific
    define ('MAIN_SITE', 'http://imc2019.imo.net'); 
     
    // ADMINS (receive emails for actions)
    // define ('ADMINS',   serialize(array('imc2017@imo.net')));
    // ==> see dev/conf.php or dev/prod.php
    
    
    