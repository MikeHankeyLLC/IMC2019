<?php
ini_set('memory_limit', '-1');
class xls_controller extends Template_Controller {
 
    public $default_admin_page = '/admin/participants/';
    public $cur_Col = "A";
    public $cur_Row = "1";
 
    function __construct($cont, $func) {
        parent::__construct($cont, $func);
    }
 
    public function logged_in_required() {
        if(!empty($_SESSION[Cookie::get('sc_381')])) {
            return true;
        }
        redirect('/admin/login');
    }
    
    public function get_next_col() {
           return ++$this->cur_Col;
    }
    
    public function get_next_row() {
           return $this->cur_Row++;
    }
    
    public function reset_col() {
           $this->cur_Col = 'A';
    }
    
     public function reset_row() {
           $this->cur_Row = '1';
    }
    
 
     /*
	 * XLS File (reserved to admin)
	 */
     public function index() {
         
         // Excel
        require_once(ROOT_DIR . '/application/system/libraries/PHPExcel.php');  
        require_once(ROOT_DIR . '/application/system/libraries/PHPExcel/Autoloader.php');  
         
        $this::logged_in_required(); 
        
        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();// Set document properties
        $objPHPExcel->getProperties()->setCreator("Vincent Perlerin")
							 ->setLastModifiedBy("IMC Admin")
							 ->setTitle("IMC " . CONF_YEAR )
							 ->setSubject("IMC Participants List")
							 ->setDescription("IMC Participants List") 
							 ->setKeywords("IMC")
							 ->setCategory("IMC Admin");

                                     
        
        // Get payment methods
        $payment_methods = 	 unserialize(PAYMENT_METHODS); // see config.php
     
        // Get food requirements
        $food_req_ar  = unserialize(FOOD_REQUIREMENTS); // see config.php
        
        // Get proceedings
        $proceedings 	 = 	 unserialize(PROCEEDINGS); // see config.php
        
        // Get Accomodations 
        $accomodations  = unserialize(ACCOMODATIONS); // see config.php
		
		// Get Sessions
		$sessions = unserialize(SESSIONS); // see config.php

        // First row (titles)        
        $objPHPExcel->setActiveSheetIndex(0)
                     
                     ->setCellValue($this->cur_Col.$this->cur_Row, 'Id')
                     ->setCellValue($this->get_next_col().$this->cur_Row, 'CONFIRMED')
                     ->setCellValue($this->get_next_col().$this->cur_Row, 'Date of Registration')
                     ->setCellValue($this->get_next_col().$this->cur_Row, 'Title')
                     ->setCellValue($this->get_next_col().$this->cur_Row, 'Last Name')
                     ->setCellValue($this->get_next_col().$this->cur_Row, 'First Name')
                     ->setCellValue($this->get_next_col().$this->cur_Row, 'Address')
                     ->setCellValue($this->get_next_col().$this->cur_Row, 'Postal Code')
                     ->setCellValue($this->get_next_col().$this->cur_Row, 'City')
                     ->setCellValue($this->get_next_col().$this->cur_Row, 'Country')
                     ->setCellValue($this->get_next_col().$this->cur_Row, 'Email')
                     ->setCellValue($this->get_next_col().$this->cur_Row, 'Phone')
                     ->setCellValue($this->get_next_col().$this->cur_Row, 'Date of Birth')
                     ->setCellValue($this->get_next_col().$this->cur_Row, 'Organization') 
                     ->setCellValue($this->get_next_col().$this->cur_Row, 'Amount')
                     ->setCellValue($this->get_next_col().$this->cur_Row, 'Payment Date')
                     ->setCellValue($this->get_next_col().$this->cur_Row, 'Payment Method')
                     ->setCellValue($this->get_next_col().$this->cur_Row, 'Admin Notes')
                     ->setCellValue($this->get_next_col().$this->cur_Row, 'Registration Type')
                     ->setCellValue($this->get_next_col().$this->cur_Row, 'Room#')
                     ->setCellValue($this->get_next_col().$this->cur_Row, 'Roomate')
                     ->setCellValue($this->get_next_col().$this->cur_Row, 'Remarks')
                     ->setCellValue($this->get_next_col().$this->cur_Row, 'Tshirt')
                     ->setCellValue($this->get_next_col().$this->cur_Row, 'Meteor. Shuttle')
                     ->setCellValue($this->get_next_col().$this->cur_Row, 'Food Requirement')
                     ->setCellValue($this->get_next_col().$this->cur_Row, 'Food Comment')
                     ->setCellValue($this->get_next_col().$this->cur_Row, 'Proceedings')
                     ;
        
        
        // First Row Styling 
        $objPHPExcel->getActiveSheet()->getStyle('A1:'.$this->cur_Col.$this->cur_Row)->applyFromArray(
            array(
                  'font' => array( 'bold' => true, ),  
                  'fill' 	=> array(
                                        'type'		=> PHPExcel_Style_Fill::FILL_SOLID,
                                        'color'		=> array('argb' => 'FFF2F2F2')
                                    ),
                  'borders' => array(   'right'	=> array('style' => PHPExcel_Style_Border::BORDER_THIN),
                                        'bottom'	=> array('style' => PHPExcel_Style_Border::BORDER_THIN) 
                                    )
                 )
        ); 
        
        
        // Get list of users
        $this->input['participants'] = Participants_Model::get_participants($this->input);
        
         
        // Loop over participant
        foreach($this->input['participants'] as $part) :
        
                // Reset Column
                $this->reset_col();
                
                // Go to next row
                $this->get_next_row();
              
                // From imc_participants
                $country  = Countries_model::get_country_name(array('country_id'=>$part['country']));
                //$has_paid = $part['paid']==1?'YES':'NO';
                $pay_date = (empty($part['pay_date']) || $part['pay_date']=='0000-00-00')?'n/a':$part['pay_date'];
                $payment_method  = !empty($part['payment_meth'])?$payment_methods[$part['payment_meth']]:'';
                
                // From imc_details
                $details = Details_model::get_details(array('user_id'=> $part['id']));
                $details = $details[0];
                
                 
                $food_req       = !empty($details['food'])?$food_req_ar[$details['food']]:'';
                $proceed        = empty($details['proceedings'])?'no proceedings':$proceedings[$details['proceedings']];
                
                $accomodation   = !empty($details['reg_type'])?$accomodations[$details['reg_type']]['abbr']:'unknown';
                 
                 
                if(($part['confirmed']==-1 || empty($part['confirmed']))) : 
                    $conf = 'NO';
                elseif(($part['confirmed']==2 || empty($part['confirmed']))):
                    $conf = 'Yes but...';
                elseif(($part['confirmed']==-2 || empty($part['confirmed']))):
                    $conf = 'CANCELLED';
                else:
                    $conf = 'YES';        
                endif;
                 
                
                $shuttle = $details['meteoroid_shuttle']==-1?'NO':'YES';
                
                // Add Row
                $objPHPExcel->setActiveSheetIndex(0)
                     ->setCellValue($this->cur_Col.$this->cur_Row, $part['id'])
                     ->setCellValue($this->get_next_col().$this->cur_Row, $conf)
                     ->setCellValue($this->get_next_col().$this->cur_Row, $part['reg_date'])
                     ->setCellValue($this->get_next_col().$this->cur_Row, $part['title'])
                     ->setCellValue($this->get_next_col().$this->cur_Row, $part['lastname'])
                     ->setCellValue($this->get_next_col().$this->cur_Row, $part['firstname'])
                     ->setCellValue($this->get_next_col().$this->cur_Row, $part['address'])
                     ->setCellValue($this->get_next_col().$this->cur_Row, $part['post_code'])
                     ->setCellValue($this->get_next_col().$this->cur_Row, $part['city'])
                     ->setCellValue($this->get_next_col().$this->cur_Row, $country)
                     ->setCellValue($this->get_next_col().$this->cur_Row, $part['email'])
                     ->setCellValue($this->get_next_col().$this->cur_Row, $part['phone'])
                     ->setCellValue($this->get_next_col().$this->cur_Row, $part['dob'])
                     ->setCellValue($this->get_next_col().$this->cur_Row, $part['org'])
                     ->setCellValue($this->get_next_col().$this->cur_Row, number_format($part['amount'], 2, '.', ''))
                     ->setCellValue($this->get_next_col().$this->cur_Row, $pay_date)
                     ->setCellValue($this->get_next_col().$this->cur_Row, $payment_method)
                     ->setCellValue($this->get_next_col().$this->cur_Row, $part['admin_comment'])
                     ->setCellValue($this->get_next_col().$this->cur_Row, $accomodation)
                     ->setCellValue($this->get_next_col().$this->cur_Row, $part['roomnumber'])
                     ->setCellValue($this->get_next_col().$this->cur_Row, $details['roomate'])
                     ->setCellValue($this->get_next_col().$this->cur_Row, $details['comments'])
                     ->setCellValue($this->get_next_col().$this->cur_Row, $details['tshirt'])
                     ->setCellValue($this->get_next_col().$this->cur_Row, $shuttle)
                     ->setCellValue($this->get_next_col().$this->cur_Row, $food_req)
                     ->setCellValue($this->get_next_col().$this->cur_Row, $details['food_other'])
                     ->setCellValue($this->get_next_col().$this->cur_Row, $proceed);
                     
                     
                
                unset($details, $food_req, $proceed, $conf);
            endforeach;

        
        // Name worksheet
        $objPHPExcel->getActiveSheet()->setTitle('IMC Participants List');
        
        // Auto filters
        //$objPHPExcel->getActiveSheet()->setAutoFilter($objPHPExcel->getActiveSheet()->calculateWorksheetDimension());
        
        // Set column auto width
        $lastColumn = $objPHPExcel->getActiveSheet()->getHighestColumn();
        $lastColumn++;
        for ($column = 'A'; $column != $lastColumn; $column++) {
          if($column!='Y' && $column!='S'):    
            $objPHPExcel->getActiveSheet()->getColumnDimension($column)->setAutoSize(true);  
          endif;
        }
        
        // Format amount column
        $objPHPExcel->getActiveSheet()->getStyle('O1:O200')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
             
             
         // Max Width for details
        $objPHPExcel->getActiveSheet()->getColumnDimension('Y')->setWidth(100);
        $objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(100);
        // Wrap Text
        $objPHPExcel->getActiveSheet()->getStyle('Y1:Y'.$objPHPExcel->getActiveSheet()->getHighestRow())->getAlignment()->setWrapText(true); 
        $objPHPExcel->getActiveSheet()->getStyle('S1:S'.$objPHPExcel->getActiveSheet()->getHighestRow())->getAlignment()->setWrapText(true); 
        // Set Vertical Alignement
        $objPHPExcel->getActiveSheet()->getStyle('A1:'.$this->cur_Col.$this->cur_Row)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
   
        
        /***********************************************************************************************/
         
        // Create new sheet for Traveling Details
        // First row (titles)     
        $objPHPExcel->createSheet();   
        $this->reset_col();
        $this->reset_row();
        $objPHPExcel->setActiveSheetIndex(1)
                     ->setCellValue($this->cur_Col.$this->cur_Row, 'Id')
                     ->setCellValue($this->get_next_col().$this->cur_Row, 'Last Name')
                     ->setCellValue($this->get_next_col().$this->cur_Row, 'First Name')
                     ->setCellValue($this->get_next_col().$this->cur_Row, 'Travel by')
                     ->setCellValue($this->get_next_col().$this->cur_Row, 'Arrival Date')
                     ->setCellValue($this->get_next_col().$this->cur_Row, 'Arrival Time')
                     ->setCellValue($this->get_next_col().$this->cur_Row, 'Departure Date')
                     ->setCellValue($this->get_next_col().$this->cur_Row, 'Departure Time')
                     ->setCellValue($this->get_next_col().$this->cur_Row, 'Details');
        // Name worksheet
        $objPHPExcel->getActiveSheet()->setTitle('IMC Participants Travel Details');
        
        // First Row Styling 
        $objPHPExcel->getActiveSheet()->getStyle('A1:'.$this->cur_Col.$this->cur_Row)->applyFromArray(
            array(
                  'font' => array( 'bold' => true, ),  
                  'fill' 	=> array(
                                        'type'		=> PHPExcel_Style_Fill::FILL_SOLID,
                                        'color'		=> array('argb' => 'FFF2F2F2')
                                    ),
                  'borders' => array(   'right'	=> array('style' => PHPExcel_Style_Border::BORDER_THIN),
                                        'bottom'	=> array('style' => PHPExcel_Style_Border::BORDER_THIN) 
                                    )
                 )
        ); 
        
        
        // Loop over participant
        foreach($this->input['participants'] as $part) :
        
                 // Reset Column
                $this->reset_col();
                
                // Go to next row
                $this->get_next_row();
        
                // Get Travel details
                $details = Travels_model::get_travels(array('user_id'=>$part['id']));
                $details = $details[0]; 
                 
                // Add Row
                $objPHPExcel->setActiveSheetIndex(1)
                     ->setCellValue($this->cur_Col.$this->cur_Row, $part['id'])
                     ->setCellValue($this->get_next_col().$this->cur_Row, $part['lastname'])
                     ->setCellValue($this->get_next_col().$this->cur_Row, $part['firstname'])
                     ->setCellValue($this->get_next_col().$this->cur_Row, $details['travel_type'])
                     ->setCellValue($this->get_next_col().$this->cur_Row, $details['arrival_date'])
                     ->setCellValue($this->get_next_col().$this->cur_Row, $details['arrival_time'])
                     ->setCellValue($this->get_next_col().$this->cur_Row, $details['departure_date'])
                     ->setCellValue($this->get_next_col().$this->cur_Row, $details['departure_time'])
                     ->setCellValue($this->get_next_col().$this->cur_Row, $details['details']);
                
                unset($details);
        endforeach;
        
        // Set column auto width
        for($i='A';$i<=$this->cur_Col; ++$i):
            if($i!='I'): 
                $objPHPExcel->getActiveSheet()->getColumnDimension($i)->setAutoSize(true);  
            endif;
        endfor;
        
         // Max Width for details
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(90);
        // Wrap Text
        $objPHPExcel->getActiveSheet()->getStyle('I1:I'.$objPHPExcel->getActiveSheet()->getHighestRow())->getAlignment()->setWrapText(true); 
        // Set Vertical Alignement
        $objPHPExcel->getActiveSheet()->getStyle('A1:'.$this->cur_Col.$this->cur_Row)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
        unset($posters);
        
       
        
        /***********************************************************************************************/
         
        // Create new sheet for Talks
        // First row (titles)     
        $objPHPExcel->createSheet();   
        $this->reset_col();
        $this->reset_row();
        $objPHPExcel->setActiveSheetIndex(2)
                     ->setCellValue($this->cur_Col.$this->cur_Row, 'User Id')
					 ->setCellValue($this->get_next_col().$this->cur_Row, 'Session')
                     ->setCellValue($this->get_next_col().$this->cur_Row, 'Talk Title')
                     ->setCellValue($this->get_next_col().$this->cur_Row, 'Author Name')
                     ->setCellValue($this->get_next_col().$this->cur_Row, 'Author Email')
                     ->setCellValue($this->get_next_col().$this->cur_Row, 'All authors')
                     ->setCellValue($this->get_next_col().$this->cur_Row, 'Abstract')
                     ->setCellValue($this->get_next_col().$this->cur_Row, 'Duration')
                     ->setCellValue($this->get_next_col().$this->cur_Row, 'Delivery Date');
        // Name worksheet
        $objPHPExcel->getActiveSheet()->setTitle('IMC Talks');
        
        // First Row Styling 
        $objPHPExcel->getActiveSheet()->getStyle('A1:'.$this->cur_Col.$this->cur_Row)->applyFromArray(
            array(
                  'font' => array( 'bold' => true, ),  
                  'fill' 	=> array(
                                        'type'		=> PHPExcel_Style_Fill::FILL_SOLID,
                                        'color'		=> array('argb' => 'FFF2F2F2')
                                    ),
                  'borders' => array(   'right'	=> array('style' => PHPExcel_Style_Border::BORDER_THIN),
                                        'bottom'	=> array('style' => PHPExcel_Style_Border::BORDER_THIN) 
                                    )
                 )
        ); 
        
        // Get Talks
        $talks = Talks_model::get_all_talks_with_authors();
        
        
        $del_dates = unserialize(DELIVERY_DATES); // see config.php;
        
        foreach($talks as $talk):
        
                // Reset Column
                $this->reset_col();
                
                // Go to next row
                $this->get_next_row();
                
                $del_date = $del_dates[$talk['delivery_date']]['text'];
                
                // Add Row
                $objPHPExcel->setActiveSheetIndex(2)
                     ->setCellValue($this->cur_Col.$this->cur_Row, $talk['user_id'])
                     ->setCellValue($this->get_next_col().$this->cur_Row, $sessions[$talk['session']])
                     ->setCellValue($this->get_next_col().$this->cur_Row, $talk['title'])
                     ->setCellValue($this->get_next_col().$this->cur_Row, $talk['lastname'].' ' .$talk['firstname'])
                     ->setCellValue($this->get_next_col().$this->cur_Row, $talk['email'])
                     ->setCellValue($this->get_next_col().$this->cur_Row, $talk['authors'])
                     ->setCellValue($this->get_next_col().$this->cur_Row, $talk['abstract'])
                     ->setCellValue($this->get_next_col().$this->cur_Row, $talk['duration'].'min')
                     ->setCellValue($this->get_next_col().$this->cur_Row, $del_date);
        
        endforeach; 
        
        // Set column auto width
         for($i='A';$i<='I'; ++$i):
            //if($i!='C' && $i!='E'): 
                $objPHPExcel->getActiveSheet()->getColumnDimension($i)->setAutoSize(true);  
            //endif;
        endfor;
        
        // Max Width for abstract
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(110);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(110);
        // Wrap Text
        //$objPHPExcel->getActiveSheet()->getStyle('E1:E'.$objPHPExcel->getActiveSheet()->getHighestRow())->getAlignment()->setWrapText(true); 
		$objPHPExcel->getActiveSheet()->getStyle('D1:D'.$objPHPExcel->getActiveSheet()->getHighestRow())->getAlignment()->setWrapText(true); 
        // Set Vertical Alignement
        $objPHPExcel->getActiveSheet()->getStyle('A1:'.$this->cur_Col.$this->cur_Row)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
        
        unset($posters);
        unset($talks);
        
        
        /***********************************************************************************************/
         
        // Create new sheet for Posters
        // First row (titles)     
        $objPHPExcel->createSheet();   
        $this->reset_col();
        $this->reset_row();
        $objPHPExcel->setActiveSheetIndex(3)
                     ->setCellValue($this->cur_Col.$this->cur_Row, 'User Id')
					 ->setCellValue($this->get_next_col().$this->cur_Row, 'Session')
                     ->setCellValue($this->get_next_col().$this->cur_Row, 'Poster Title')
                     ->setCellValue($this->get_next_col().$this->cur_Row, 'Author Name') 
                     ->setCellValue($this->get_next_col().$this->cur_Row, 'Author Email') 
                     ->setCellValue($this->get_next_col().$this->cur_Row, 'All authors')
                     ->setCellValue($this->get_next_col().$this->cur_Row, 'Abstract')
                     ->setCellValue($this->get_next_col().$this->cur_Row, 'Delivery Date');

       
        // Name worksheet
        $objPHPExcel->getActiveSheet()->setTitle('IMC Posters');
        
        
        // First Row Styling 
        $objPHPExcel->getActiveSheet()->getStyle('A1:'.$this->cur_Col.$this->cur_Row)->applyFromArray(
            array(
                  'font' => array( 'bold' => true, ),  
                  'fill' 	=> array(
                                        'type'		=> PHPExcel_Style_Fill::FILL_SOLID,
                                        'color'		=> array('argb' => 'FFF2F2F2')
                                    ),
                  'borders' => array(   'right'	=> array('style' => PHPExcel_Style_Border::BORDER_THIN),
                                        'bottom'	=> array('style' => PHPExcel_Style_Border::BORDER_THIN) 
                                    )
                 )
        ); 
        
        // Get Posters
        $posters = Posters_model::get_all_posters_with_authors();
        
        foreach($posters as $poster):
        
                // Reset Column
                $this->reset_col();
                
                // Go to next row
                $this->get_next_row();
                
                 $del_date = $del_dates[$poster['delivery_date']]['text'];
                
                // Add Row
                $objPHPExcel->setActiveSheetIndex(3)
                     ->setCellValue($this->cur_Col.$this->cur_Row, $poster['user_id'])
					 ->setCellValue($this->get_next_col().$this->cur_Row, $sessions[$poster['session']])
                     ->setCellValue($this->get_next_col().$this->cur_Row, $poster['title'])
                     ->setCellValue($this->get_next_col().$this->cur_Row, $poster['lastname'] . ' ' . $poster['firstname']) 
                     ->setCellValue($this->get_next_col().$this->cur_Row, $poster['email']) 
                     ->setCellValue($this->get_next_col().$this->cur_Row, $poster['authors'])
                     ->setCellValue($this->get_next_col().$this->cur_Row, $poster['abstract'])
                     ->setCellValue($this->get_next_col().$this->cur_Row,  $del_date);
        
        endforeach; 
        
        // Set column auto width
        for($i='A';$i<='H'; ++$i):
			$objPHPExcel->getActiveSheet()->getColumnDimension($i)->setAutoSize(true);  
        endfor;
        
        // Max Width for abstract
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(110);
        // Wrap Text
        $objPHPExcel->getActiveSheet()->getStyle('C1:C'.$objPHPExcel->getActiveSheet()->getHighestRow())->getAlignment()->setWrapText(true); 
        // Set Vertical Alignement
        $objPHPExcel->getActiveSheet()->getStyle('A1:'.$this->cur_Col.$this->cur_Row)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
        unset($posters);
        
        
        
        /***********************************************************************************************/
         
        // Create new sheet for Workshop1
        // First row (titles)     
        $objPHPExcel->createSheet();   
        $this->reset_col();
        $this->reset_row();
        $objPHPExcel->setActiveSheetIndex(4)
                     ->setCellValue($this->cur_Col.$this->cur_Row, 'User Id')
                     ->setCellValue($this->get_next_col().$this->cur_Row, 'Title')
					 ->setCellValue($this->get_next_col().$this->cur_Row, 'First Name')
                     ->setCellValue($this->get_next_col().$this->cur_Row, 'Last Name')
                     ->setCellValue($this->get_next_col().$this->cur_Row, 'Email');

       
        // Name worksheet
        $objPHPExcel->getActiveSheet()->setTitle('Visual Workshop');
        
        
        // First Row Styling 
        $objPHPExcel->getActiveSheet()->getStyle('A1:'.$this->cur_Col.$this->cur_Row)->applyFromArray(
            array(
                  'font' => array( 'bold' => true, ),  
                  'fill' 	=> array(
                                        'type'		=> PHPExcel_Style_Fill::FILL_SOLID,
                                        'color'		=> array('argb' => 'FFF2F2F2')
                                    ),
                  'borders' => array(   'right'	=> array('style' => PHPExcel_Style_Border::BORDER_THIN),
                                        'bottom'	=> array('style' => PHPExcel_Style_Border::BORDER_THIN) 
                                    )
                 )
        ); 
        
     
        
        foreach($this->input['participants'] as $part):
                 
                if($part['workshop1']==1):  
                
                    // Reset Column
                     $this->reset_col();
                
                    // Go to next row
                    $this->get_next_row();       
                
                    // Add Row
                    $objPHPExcel->setActiveSheetIndex(4)
                        ->setCellValue($this->cur_Col.$this->cur_Row, $part['user_id'])
                        ->setCellValue($this->get_next_col().$this->cur_Row, $part['title'])
                        ->setCellValue($this->get_next_col().$this->cur_Row, $part['lastname'])
                        ->setCellValue($this->get_next_col().$this->cur_Row, $part['firstname'])
                        ->setCellValue($this->get_next_col().$this->cur_Row, $part['email']);
                     
                endif;     
        
        endforeach; 
        
        // Set column auto width
        for($i='A';$i<='H'; ++$i):
			$objPHPExcel->getActiveSheet()->getColumnDimension($i)->setAutoSize(true);  
        endfor;
        
        // Max Width for abstract
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(110);
        // Wrap Text
        $objPHPExcel->getActiveSheet()->getStyle('C1:C'.$objPHPExcel->getActiveSheet()->getHighestRow())->getAlignment()->setWrapText(true); 
        // Set Vertical Alignement
        $objPHPExcel->getActiveSheet()->getStyle('A1:'.$this->cur_Col.$this->cur_Row)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
        
        
         /***********************************************************************************************/
         
        // Create new sheet for Workshop1
        // First row (titles)     
        $objPHPExcel->createSheet();   
        $this->reset_col();
        $this->reset_row();
        $objPHPExcel->setActiveSheetIndex(5)
                     ->setCellValue($this->cur_Col.$this->cur_Row, 'User Id')
                     ->setCellValue($this->get_next_col().$this->cur_Row, 'Title')
					 ->setCellValue($this->get_next_col().$this->cur_Row, 'First Name')
                     ->setCellValue($this->get_next_col().$this->cur_Row, 'Last Name')
                     ->setCellValue($this->get_next_col().$this->cur_Row, 'Email');

       
        // Name worksheet
        $objPHPExcel->getActiveSheet()->setTitle('Spectroscopic Workshop');
        
        
        // First Row Styling 
        $objPHPExcel->getActiveSheet()->getStyle('A1:'.$this->cur_Col.$this->cur_Row)->applyFromArray(
            array(
                  'font' => array( 'bold' => true, ),  
                  'fill' 	=> array(
                                        'type'		=> PHPExcel_Style_Fill::FILL_SOLID,
                                        'color'		=> array('argb' => 'FFF2F2F2')
                                    ),
                  'borders' => array(   'right'	=> array('style' => PHPExcel_Style_Border::BORDER_THIN),
                                        'bottom'	=> array('style' => PHPExcel_Style_Border::BORDER_THIN) 
                                    )
                 )
        ); 
        
     
        
        foreach($this->input['participants'] as $part):
                 
                if($part['workshop2']==1):  
                
                    // Reset Column
                     $this->reset_col();
                
                    // Go to next row
                    $this->get_next_row();       
                
                    // Add Row
                    $objPHPExcel->setActiveSheetIndex(5)
                        ->setCellValue($this->cur_Col.$this->cur_Row, $part['user_id'])
                        ->setCellValue($this->get_next_col().$this->cur_Row, $part['title'])
                        ->setCellValue($this->get_next_col().$this->cur_Row, $part['lastname'])
                        ->setCellValue($this->get_next_col().$this->cur_Row, $part['firstname'])
                        ->setCellValue($this->get_next_col().$this->cur_Row, $part['email']);
                     
                endif;     
        
        endforeach; 
        
        // Set column auto width
        for($i='A';$i<='H'; ++$i):
			$objPHPExcel->getActiveSheet()->getColumnDimension($i)->setAutoSize(true);  
        endfor;
        
        // Max Width for abstract
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(110);
        // Wrap Text
        $objPHPExcel->getActiveSheet()->getStyle('C1:C'.$objPHPExcel->getActiveSheet()->getHighestRow())->getAlignment()->setWrapText(true); 
        // Set Vertical Alignement
        $objPHPExcel->getActiveSheet()->getStyle('A1:'.$this->cur_Col.$this->cur_Row)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
        
        
        /***********************************************************************************************/
        
        unset($this->input['participants']);
        
            
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);
        
         // Set column auto width 
        for($i='A';$i<=$this->cur_Col; ++$i):
            $objPHPExcel->getActiveSheet()->getColumnDimension($i)->setAutoSize(true);  
        endfor;
            
         
        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="IMC-'.CONF_YEAR.' Participants-'.gmdate('d-m-Y').'.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');
        
        // If you're serving to IE over SSL, then the following may be needed
        header ('Expires: Mon, 26 Jul 1969 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0
        
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    
     }
    
}
 