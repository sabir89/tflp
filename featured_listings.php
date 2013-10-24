<?php 
	//not using cloud anymore
//require_once(WP_PLUGIN_DIR . "/tflp/photos/GetRackspacePhoto.php");

class FeaturedListings{
	
	const BASE_SOLR_URL = "http://mightytitan.info:8983/solr/db/select?wt=json";
	//private $rackspacePhotos;
	
	public function __construct(){
		//$this->rackspacePhotos = new GetRackspacePhoto();
		add_action( 'wp_enqueue_scripts', array($this, 'enqueue_method') );
	}
	
	public function register_shortcodes(){
			//[titanfeaturedagents type="all" result="default" details="default" rows="5" nrdsid="502003013, 502001624, 502001753"]
			add_shortcode('titanfeaturedagents', array($this, 'titan_featuredagent_listings_handler')); 
			
			//[titanfeaturedoffices type="act" result="default" details="default" officeid = "7165, 5112"]
			add_shortcode('titanfeaturedoffices', array($this, 'titan_featuredoffice_listings_handler')); 
			
			//[titanfeaturedmlnumbers result="default" details="default" mlnumber = "4353357"]
			add_shortcode('titanfeaturedmlnumbers', array($this, 'titan_featuredmlnumbers_handler')); 
			
			//[titanfeaturedcities type="act" result="default" details="default" city="Apple Valley"]
			add_shortcode('titanfeaturedcities', array($this, 'titan_featuredcities_handler')); 
			
			//[titanfeaturedneighborhoods type="act" result="default" details="default" neighborhood="Victory"]		
			add_shortcode('titanfeaturedneighborhoods', array($this, 'titan_featuredneighborhoods_handler')); 
			
			//[titanfeaturedzips type="act" result="default" details="default" zip="55410"]
			add_shortcode('titanfeaturedzips', array($this, 'titan_featuredzips_handler')); 
			
			//[titanfeaturedschooldistricts type="act" result="default" details="default" schooldistrict="1 - Minneapolis"]		
			add_shortcode('titanfeaturedschooldistricts', array($this, 'titan_featuredschooldistricts_handler'));  
			
			//new codes, 25 april 2013
			//[titanfeaturedlakes type="act" result="default" details="default" lake="Lac Lavon"]
			add_shortcode('titanfeaturedlakes', array($this, 'titan_featuredlakes_handler'));
			
			//[titanfeaturedstreets type="act" result="default" details="default" streetnumber="4349" streetname="Washburn"]
			add_shortcode('titanfeaturedstreets', array($this, 'titan_featuredstreets_handler'));
		
	}

	public function enqueue_method() {
		wp_enqueue_script(			   
			'titan-featured-menu-script',		  
			plugins_url( '/templates/js/titan-featured-menu.js' , __FILE__ ), 
			//WP_PLUGIN_DIR . '/tflp/templates/js/titan-featured-menu.js',
			array( 'jquery', 'jquery-ui-dialog')		   
		);
		//wp_enqueue_script(
		//	'jquery-tools',
		//	plugins_url( '/js/jquery.tools.min.js' , __FILE__ ),
		//	//WP_PLUGIN_DIR . '/tflp/templates/js/titan-featured-menu.js',
		//	array( 'jquery')
		//);
		wp_enqueue_style( 'titan-featured-style', plugins_url( '/css/titan-featured.css' , __FILE__ ) );
	}	

	public function titan_featuredstreets_handler($atts) {
		extract(shortcode_atts( array(
			'streetnumber' =>  NULL, //required field
			'streetname' =>  NULL, //required field
			
			'type' => 'act', 
			'result' => 'default', 
			'details' => 'default',
			'price' => NULL,
			'style' => NULL,			
			'sort' => NULL,			
			'rows' => 10
		), $atts ));
		if(isset($_REQUEST['mlnumber']) && $_REQUEST['mlnumber']!=''){
			$solrQuery = $this->get_mlnumbers_solr_query(array($_REQUEST['mlnumber']), 0, $rows);
			//echo "Solr Query : " . $solrQuery;
			$myTemplate = WP_PLUGIN_DIR . "/tflp/templates/details/${details}.php";
			
			return $this->show_listings_page($solrQuery, $myTemplate, $myTemplate,  0, $rows);
		
		}
		
		
		//ensure that mandatory attribute is passed
		if(!isset($streetnumber) || !isset($streetname)){
			return "Attributes 'streetnumber' & 'streetname' both must be passed in the short-code";
		}
		$detailmlnum = addslashes(sanitize_text_field($_GET['titanmlnumber']));
		
		if (!empty($detailmlnum)){
			$solrQuery = $this->get_mlnumbers_solr_query(array($detailmlnum), 0, $rows);
			//echo "Solr Query : " . $solrQuery;
			$myTemplate = WP_PLUGIN_DIR . "/tflp/templates/details/${details}.php";
			return $this->show_listings_page($solrQuery, $myTemplate, $myTemplate,  0, $rows);

		// othwise show the relustls
		} else { 
			$myTemplate = WP_PLUGIN_DIR . "/tflp/templates/result/${result}.php";
			//echo "Results Template = " . $myTemplate . "<br/>";
			$solrQuery = $this->get_streets_solr_query($streetnumber, $streetname, $type, 0, $rows, $price, $sort, $style);
			//echo "Solr Query : " . $solrQuery;
			return $this->show_listings_page($solrQuery, $myTemplate, $details,  0, $rows);
		}					
	}
	
	//[titanfeaturedlakes type="act" result="default" details="default" lake="Cobblestone Lake"]	
	public function titan_featuredlakes_handler($atts) {
		extract(shortcode_atts( array(
			'lake' =>  NULL, //required field
			'type' => 'act', 
			'result' => 'default', 
			'details' => 'default',
			'price' => NULL,
			'style' => NULL,			
			'sort' => NULL,			
			'rows' => 10
		), $atts ));
		if(isset($_REQUEST['mlnumber']) && $_REQUEST['mlnumber']!=''){
			$solrQuery = $this->get_mlnumbers_solr_query(array($_REQUEST['mlnumber']), 0, $rows);
			//echo "Solr Query : " . $solrQuery;
			$myTemplate = WP_PLUGIN_DIR . "/tflp/templates/details/${details}.php";
			
			return $this->show_listings_page($solrQuery, $myTemplate, $myTemplate,  0, $rows);
		
		}
		
		
		//ensure that mandatory attribute is passed
		if(!isset($lake)){
			return "Attribute 'lake' must be passed in the short-code";
		}
		
		$lakes = explode(",", $lake);	
		$detailmlnum = addslashes(sanitize_text_field($_GET['titanmlnumber']));
		
		if (!empty($detailmlnum)){
			$solrQuery = $this->get_mlnumbers_solr_query(array($detailmlnum), 0, $rows);
			//echo "Solr Query : " . $solrQuery;
			$myTemplate = WP_PLUGIN_DIR . "/tflp/templates/details/${details}.php";
			return $this->show_listings_page($solrQuery, $myTemplate, $myTemplate,  0, $rows);

		// othwise show the relustls
		} else { 
			$myTemplate = WP_PLUGIN_DIR . "/tflp/templates/result/${result}.php";
			//echo "Results Template = " . $myTemplate . "<br/>";
			$solrQuery = $this->get_lakes_solr_query($lakes, $type, 0, $rows, $price, $sort, $style);
			//echo "Solr Query : " . $solrQuery;
			return $this->show_listings_page($solrQuery, $myTemplate, $details,  0, $rows);
		}
			
			return $this->show_listings_page($solrQuery, $myTemplate, $details,  0, $rows);
	}				
			
	//[titanfeaturedschooldistricts type="act" result="default" details="default" schooldistrict="Rosemount/Apple Valley"] 	
	public function titan_featuredschooldistricts_handler($atts) {
		extract(shortcode_atts( array(
			'schooldistrict' =>  NULL, //required field
			'type' => 'act', 
			'result' => 'default', 
			'details' => 'default',
			'price' => NULL,
			'style' => NULL,					
			'sort' => NULL,					
			'rows' => 10
		), $atts ));
		if(isset($_REQUEST['mlnumber']) && $_REQUEST['mlnumber']!=''){
			$solrQuery = $this->get_mlnumbers_solr_query(array($_REQUEST['mlnumber']), 0, $rows);
			//echo "Solr Query : " . $solrQuery;
			$myTemplate = WP_PLUGIN_DIR . "/tflp/templates/details/${details}.php";
			
			return $this->show_listings_page($solrQuery, $myTemplate, $myTemplate,  0, $rows);
		
		}
		
		
		//ensure that mandatory attribute is passed
		if(!isset($schooldistrict)){
			return "Attribute 'schooldistrict' must be passed in the short-code";
		}
		
		$schools = explode(",", $schooldistrict);
		$detailmlnum = addslashes(sanitize_text_field($_GET['titanmlnumber']));
		
		if (!empty($detailmlnum)){
			$solrQuery = $this->get_mlnumbers_solr_query(array($detailmlnum), 0, $rows);
			//echo "Solr Query : " . $solrQuery;
			$myTemplate = WP_PLUGIN_DIR . "/tflp/templates/details/${details}.php";
			return $this->show_listings_page($solrQuery, $myTemplate, $myTemplate,  0, $rows);

		// othwise show the relustls
		} else { 
			$myTemplate = WP_PLUGIN_DIR . "/tflp/templates/result/${result}.php";
			//echo "Results Template = " . $myTemplate . "<br/>";
			$solrQuery = $this->get_schools_solr_query($schools, $type, 0, $rows, $price, $sort, $style);
			//echo "Solr Query : " . $solrQuery;
			return $this->show_listings_page($solrQuery, $myTemplate, $details,  0, $rows);
		}
			
			return $this->show_listings_page($solrQuery, $myTemplate, $details,  0, $rows);
	}			
				
			
	
	//[titanfeaturedzips type="act" result="default" details="default" zip="55410"]
	public function titan_featuredzips_handler($atts) {
		extract(shortcode_atts( array(
			'zip' =>  NULL, //required field
			'type' => 'act', 
			'result' => 'default', 
			'details' => 'default',
			'price' => NULL,	
			'style' => NULL,			
			'sort' => NULL,						
			'rows' => 10
		), $atts ));
		if(isset($_REQUEST['mlnumber']) && $_REQUEST['mlnumber']!=''){
			$solrQuery = $this->get_mlnumbers_solr_query(array($_REQUEST['mlnumber']), 0, $rows,$sort);
			//echo "Solr Query : " . $solrQuery;
			$myTemplate = WP_PLUGIN_DIR . "/tflp/templates/details/${details}.php";
			
			return $this->show_listings_page($solrQuery, $myTemplate, $myTemplate,  0, $rows);
		
		}
		
		
		//ensure that mandatory attribute is passed
		if(!isset($zip)){
			return "Attribute 'zip' must be passed in the short-code";
		}		
		
		$zips = explode(",", $zip);
		$detailmlnum = addslashes(sanitize_text_field($_GET['titanmlnumber']));
		
		if (!empty($detailmlnum)){
			$solrQuery = $this->get_mlnumbers_solr_query(array($detailmlnum), 0, $rows);
			//echo "Solr Query : " . $solrQuery;
			$myTemplate = WP_PLUGIN_DIR . "/tflp/templates/details/${details}.php";
			return $this->show_listings_page($solrQuery, $myTemplate, $myTemplate,  0, $rows);

		// othwise show the relustls
		} else { 
			$myTemplate = WP_PLUGIN_DIR . "/tflp/templates/result/${result}.php";
			//echo "Results Template = " . $myTemplate . "<br/>";
			$solrQuery = $this->get_zips_solr_query($zips, $type, 0, $rows, $price, $sort, $style);
			//echo "Solr Query : " . $solrQuery;
			return $this->show_listings_page($solrQuery, $myTemplate, $details,  0, $rows);
		}
			
			return $this->show_listings_page($solrQuery, $myTemplate, $details,  0, $rows);
	}			
			
		
	//[titanfeaturedneighborhoods type="act" result="default" details="default" neighborhood="Victory"]
	public function titan_featuredneighborhoods_handler($atts) {
		extract(shortcode_atts( array(
			'neighborhood' =>  NULL, //required field
			'type' => 'act', 
			'result' => 'default', 
			'details' => 'default',
			'price' => NULL,
			'style' => NULL,				
			'sort' => NULL,						
			'rows' => 10
		), $atts ));
		if(isset($_REQUEST['mlnumber']) && $_REQUEST['mlnumber']!=''){
			$solrQuery = $this->get_mlnumbers_solr_query(array($_REQUEST['mlnumber']), 0, $rows);
			//echo "Solr Query : " . $solrQuery;
			$myTemplate = WP_PLUGIN_DIR . "/tflp/templates/details/${details}.php";
			
			return $this->show_listings_page($solrQuery, $myTemplate, $myTemplate,  0, $rows);
		
		}
		
		
		//ensure that mandatory attribute is passed
		if(!isset($neighborhood)){
			return "Attribute 'neighborhood' must be passed in the short-code";
		}			
		
		$neighborhoods = explode(",", $neighborhood);
		$detailmlnum = addslashes(sanitize_text_field($_GET['titanmlnumber']));
		
		if (!empty($detailmlnum)){
			$solrQuery = $this->get_mlnumbers_solr_query(array($detailmlnum), 0, $rows);
			//echo "Solr Query : " . $solrQuery;
			$myTemplate = WP_PLUGIN_DIR . "/tflp/templates/details/${details}.php";
			return $this->show_listings_page($solrQuery, $myTemplate, $myTemplate,  0, $rows);

		// othwise show the relustls
		} else { 
			$myTemplate = WP_PLUGIN_DIR . "/tflp/templates/result/${result}.php";
			//echo "Results Template = " . $myTemplate . "<br/>";
			$solrQuery = $this->get_neighborhoods_solr_query($neighborhoods, $type, 0, $rows, $price, $sort, $style);
			//echo "Solr Query : " . $solrQuery;
			return $this->show_listings_page($solrQuery, $myTemplate, $details,  0, $rows);
		}
			
			return $this->show_listings_page($solrQuery, $myTemplate, $details,  0, $rows);
	}		
	
			
			
	//[titanfeaturedcities type="act" result="default" details="default" city="Apple Valley"]	
	public function titan_featuredcities_handler($atts) {
		extract(shortcode_atts( array(
			'city' =>  NULL, //required field
			'type' => 'act', 
			'result' => 'default', 
			'details' => 'default',
			'price' => NULL,
			'style' => NULL,			
			'sort' => NULL,							
			'rows' => 10
		), $atts ));
		if(isset($_REQUEST['mlnumber']) && $_REQUEST['mlnumber']!=''){
			$solrQuery = $this->get_mlnumbers_solr_query(array($_REQUEST['mlnumber']), 0, $rows);
			//echo "Solr Query : " . $solrQuery;
			$myTemplate = WP_PLUGIN_DIR . "/tflp/templates/details/${details}.php";
			
			return $this->show_listings_page($solrQuery, $myTemplate, $myTemplate,  0, $rows);
		
		}
		
		
		//ensure that mandatory attribute is passed
		if(!isset($city)){
			return "Attribute 'city' must be passed in the short-code";
		}							
		
		$cities = explode(",", $city);
		$detailmlnum = addslashes(sanitize_text_field($_GET['titanmlnumber']));
		
		if (!empty($detailmlnum)){
			$solrQuery = $this->get_mlnumbers_solr_query(array($detailmlnum), 0, $rows);
			//echo "Solr Query : " . $solrQuery;
			$myTemplate = WP_PLUGIN_DIR . "/tflp/templates/details/${details}.php";
			return $this->show_listings_page($solrQuery, $myTemplate, $myTemplate,  0, $rows);

		// othwise show the relustls
		} else { 
			$myTemplate = WP_PLUGIN_DIR . "/tflp/templates/result/${result}.php";
			//echo "Results Template = " . $myTemplate . "<br/>";
			$solrQuery = $this->get_cities_solr_query($cities, $type, 0, $rows, $price, $sort, $style);
			//echo "Solr Query : " . $solrQuery;
			return $this->show_listings_page($solrQuery, $myTemplate, $details,  0, $rows);
		}
			
			return $this->show_listings_page($solrQuery, $myTemplate, $details,  0, $rows);
	}		
				
		
	
	// [titanfeaturedagents type="all" result="default" details="default" rows="5" nrdsid="502003013, 502001624, 502001753"]
	public function titan_featuredagent_listings_handler($atts) {
		extract(shortcode_atts( array(
			'nrdsid' =>  NULL, //required field
			'type' => 'act', 
			'result' => 'default', 
			'details' => 'default',
			'price' => NULL,
			'style' => NULL,			
			'sort' => NULL,			
			'rows' => 10
		), $atts ));
		if(isset($_REQUEST['mlnumber']) && $_REQUEST['mlnumber']!=''){
			$solrQuery = $this->get_mlnumbers_solr_query(array($_REQUEST['mlnumber']), 0, $rows,$sort);
			//echo "Solr Query : " . $solrQuery;
			$myTemplate = WP_PLUGIN_DIR . "/tflp/templates/details/${details}.php";
			
			return $this->show_listings_page($solrQuery, $myTemplate, $myTemplate,  0, $rows);
		
		}
		
				
		//ensure that mandatory attribute is passed
		if(!isset($nrdsid)){
			return "Attribute 'nrdsid' must be passed in the short-code";
		}			
		
		//we should get this from MA-roster plugin later
		$agentNRDS = explode(",", $nrdsid);	
		$detailmlnum = addslashes(sanitize_text_field($_GET['titanmlnumber']));
		if (!empty($detailmlnum)){
			$solrQuery = $this->get_mlnumbers_solr_query(array($detailmlnum), 0, $rows);
			//echo "Solr Query : " . $solrQuery;
			$myTemplate = WP_PLUGIN_DIR . "/tflp/templates/details/${details}.php";
			return $this->show_listings_page($solrQuery, $myTemplate, $myTemplate,  0, $rows);

		// othwise show the relustls
		} else { 
			$myTemplate = WP_PLUGIN_DIR . "/tflp/templates/result/${result}.php";
			//echo "Results Template = " . $myTemplate . "<br/>";
			$solrQuery = $this->get_agents_solr_query($agentNRDS, $type, 0, $rows, $price, $sort, $style);
			//echo "Solr Query : " . $solrQuery;
			return $this->show_listings_page($solrQuery, $myTemplate, $details,  0, $rows);
		}
	}
	
	//[titanfeaturedoffices type="act" result="default" details="default" officeid = "7165, 5112"]
	public function titan_featuredoffice_listings_handler($atts) {
		extract(shortcode_atts( array(
			'officeid' =>  NULL, //required field
			'type' => 'act', 
			'result' => 'default', 
			'details' => 'default',
			'price' => NULL,	
			'style' => NULL,			
			'sort' => NULL,						
			'rows' => 10
		), $atts ));
		if(isset($_REQUEST['mlnumber']) && $_REQUEST['mlnumber']!=''){
			$solrQuery = $this->get_mlnumbers_solr_query(array($_REQUEST['mlnumber']), 0, $rows);
			//echo "Solr Query : " . $solrQuery;
			$myTemplate = WP_PLUGIN_DIR . "/tflp/templates/details/${details}.php";
			
			return $this->show_listings_page($solrQuery, $myTemplate, $myTemplate,  0, $rows);
		
		}
		
		
		//ensure that mandatory attribute is passed
		if(!isset($officeid)){
			return "Attribute 'officeid' must be passed in the short-code";
		}		
		
		//passed as attribute
		$officeIDs = explode(",", $officeid);	
		$detailmlnum = addslashes(sanitize_text_field($_GET['titanmlnumber']));
		
		if (!empty($detailmlnum)){
			$solrQuery = $this->get_mlnumbers_solr_query(array($detailmlnum), 0, $rows);
			//echo "Solr Query : " . $solrQuery;
			$myTemplate = WP_PLUGIN_DIR . "/tflp/templates/details/${details}.php";
			return $this->show_listings_page($solrQuery, $myTemplate, $myTemplate,  0, $rows);

		// othwise show the relustls
		} else { 
			$myTemplate = WP_PLUGIN_DIR . "/tflp/templates/result/${result}.php";
			//echo "Results Template = " . $myTemplate . "<br/>";
			$solrQuery = $this->get_office_solr_query($officeIDs, $type, 0, $rows, $price, $sort, $style);
			//echo "Solr Query : " . $solrQuery;
			return $this->show_listings_page($solrQuery, $myTemplate, $details,  0, $rows);
		}
		//echo "Results Template = " . $myTemplate . "<br/>";
		$solrQuery = $this->get_office_solr_query($officeIDs, $type, 0, $rows, $price, $sort, $style);
		//echo "Solr Query : " . $solrQuery;
			
			return $this->show_listings_page($solrQuery, $myTemplate, $details,  0, $rows);
	}
	
	public function titan_featuredmlnumbers_handler($atts) {
		extract(shortcode_atts( array(
			'mlnumber' =>  NULL, //required field
			'result' => 'default', 
			'details' => 'default',
			'sort' => NULL,				
			'rows' => 10
		), $atts ));
		if(isset($_REQUEST['mlnumber']) && $_REQUEST['mlnumber']!=''){
			$solrQuery = $this->get_mlnumbers_solr_query(array($_REQUEST['mlnumber']), 0, $rows,$sort);
			//echo "Solr Query : " . $solrQuery;
			$myTemplate = WP_PLUGIN_DIR . "/tflp/templates/details/${details}.php";
			
			return $this->show_listings_page($solrQuery, $myTemplate, $myTemplate,  0, $rows);
		
		}
		
		
		//ensure that mandatory attribute is passed
		if(!isset($mlnumber)){
			return "Attribute 'mlnumber' must be passed in the short-code";
		}			
		
		//passed as attribute
		$mlnumbers = explode(",", $mlnumber);	
		//echo "$mlnumbers = " . $mlnumbers;
		
		//$detailmlnum = addslashes(sanitize_text_field($_GET['titanmlnumber']));
		echo $detailmlnum = $_REQUEST['mlnumber'];
		if (!empty($detailmlnum)){
			$solrQuery = $this->get_mlnumbers_solr_query(array($detailmlnum), 0, $rows);
			//echo "Solr Query : " . $solrQuery;
			echo $myTemplate = WP_PLUGIN_DIR . "/tflp/templates/details/${details}.php";
			
			return $this->show_listings_page($solrQuery, $myTemplate, $myTemplate,  0, $rows);

		// othwise show the relustls
		} else { 
			$myTemplate = WP_PLUGIN_DIR . "/tflp/templates/result/${result}.php";
			
			//echo "Results Template = " . $myTemplate . "<br/>";
			$solrQuery = $this->get_mlnumbers_solr_query($mlnumbers, 0, $rows, $sort);
			//echo "Solr Query : " . $solrQuery;
			return $this->show_listings_page($solrQuery, $myTemplate, $details,  0, $rows);
		}
		//echo "Results Template = " . $myTemplate . "<br/>";
		$solrQuery = $this->get_mlnumbers_solr_query($mlnumbers, 0, $rows, $sort);
		//echo "Solr Query : " . $solrQuery;
			
		return $this->show_listings_page($solrQuery, $myTemplate, $details,  0, $rows);
	}



	protected function get_lakes_solr_query($lakes, $listingType, $offset=0, $rows=10, $price, $sort, $style){
		$solrURL = self::BASE_SOLR_URL . "&start=" . $offset . "&rows=" . $rows . "&q="; 
		$solrQuery = "";	
			
		$queryAct = ' AND (status:Active) AND distributetointernet:1';
		$querySold = ' AND (status:"Comp Sold" OR status:Sold) AND distributetointernet:1';
		$queryAll = ' AND (status:Active OR status:"Comp Sold" OR status:Sold) AND distributetointernet:1';
	
		//construct query for featured listings
		$totalLakes = count($lakes);
		$queryLakes = "(";
		for($i = 0; $i < $totalLakes; $i++){
			$queryLakes = $queryLakes . "waterfrontname:\"" . trim($lakes[$i]) . "\"";
			if(($i +1) < $totalLakes){
				$queryLakes = $queryLakes . " OR ";
			}
		}
		$queryLakes = $queryLakes . ")";
		
		if(0 == strcasecmp("all", $listingType)){
			$solrQuery = $solrURL . urlencode($queryLakes . $queryAll);
		}else if(0 == strcasecmp("sold", $listingType)){
			$solrQuery = $solrURL . urlencode($queryLakes . $querySold);
		}else{ //default is active
			$solrQuery = $solrURL . urlencode($queryLakes . $queryAct);
		}
		
		//optional attribute price 
		if(isset($price)){
			$solrQuery = $solrQuery . urlencode($this->getPriceQuery($price));
		}
		
		//optional attribute style
		if(isset($style)){
			$solrQuery = $solrQuery . urlencode($this->getStyleQuery($style));
		}			
					
		//optional attribute sort
		if(isset($sort)){
			$solrQuery = $solrQuery . $this->getSortQuery($sort);
		}	
		
		return $solrQuery;
	}

	protected function get_schools_solr_query($schools, $listingType, $offset=0, $rows=10, $price, $sort, $style){
		if(isset($_REQUEST['mlnumber']) && $_REQUEST['mlnumber']!=''){
			$solrQuery = $this->get_mlnumbers_solr_query(array($_REQUEST['mlnumber']), 0, $rows);
			//echo "Solr Query : " . $solrQuery;
			$myTemplate = WP_PLUGIN_DIR . "/tflp/templates/details/${details}.php";
			
			return $this->show_listings_page($solrQuery, $myTemplate, $myTemplate,  0, $rows);
		
		}
		$solrURL = self::BASE_SOLR_URL . "&start=" . $offset . "&rows=" . $rows . "&q="; 
		$solrQuery = "";	
			
		$queryAct = ' AND (status:Active) AND distributetointernet:1';
		$querySold = ' AND (status:"Comp Sold" OR status:Sold) AND distributetointernet:1';
		$queryAll = ' AND (status:Active OR status:"Comp Sold" OR status:Sold) AND distributetointernet:1';
	
		//construct query for featured listings
		$totalSchools = count($schools);
		$querySchools = "(";
		for($i = 0; $i < $totalSchools; $i++){
			$querySchools = $querySchools . "schooldistrictnumber:\"" . trim($schools[$i]) . "\"";
			if(($i +1) < $totalSchools){
				$querySchools = $querySchools . " OR ";
			}
		}
		$querySchools = $querySchools . ")";
		
		if(0 == strcasecmp("all", $listingType)){
			$solrQuery = $solrURL . urlencode($querySchools . $queryAll);
		}else if(0 == strcasecmp("sold", $listingType)){
			$solrQuery = $solrURL . urlencode($querySchools . $querySold);
		}else{ //default is active
			$solrQuery = $solrURL . urlencode($querySchools . $queryAct);
		}
		
		//optional attribute price 
		if(isset($price)){
			$solrQuery = $solrQuery . urlencode($this->getPriceQuery($price));
		}	

		//optional attribute style
		if(isset($style)){
			$solrQuery = $solrQuery . urlencode($this->getStyleQuery($style));
		}			
		
		//optional attribute sort
		if(isset($sort)){
			$solrQuery = $solrQuery . $this->getSortQuery($sort);
		}					
		
		return $solrQuery;
	}
		
	protected function get_zips_solr_query($zips, $listingType, $offset=0, $rows=10, $price, $sort, $style){
		$solrURL = self::BASE_SOLR_URL . "&start=" . $offset . "&rows=" . $rows . "&q="; 
		$solrQuery = "";	
			
		$queryAct = ' AND (status:Active) AND distributetointernet:1';
		$querySold = ' AND (status:"Comp Sold" OR status:Sold) AND distributetointernet:1';
		$queryAll = ' AND (status:Active OR status:"Comp Sold" OR status:Sold) AND distributetointernet:1';
	
		//construct query for featured listings
		$totalZips = count($zips);
		$queryZips = "(";
		for($i = 0; $i < $totalZips; $i++){
			$queryZips = $queryZips . "postalcode:" . $zips[$i];
			if(($i +1) < $totalZips){
				$queryZips = $queryZips . " OR ";
			}
		}
		$queryZips = $queryZips . ")";
		
		if(0 == strcasecmp("all", $listingType)){
			$solrQuery = $solrURL . urlencode($queryZips . $queryAll);
		}else if(0 == strcasecmp("sold", $listingType)){
			$solrQuery = $solrURL . urlencode($queryZips . $querySold);
		}else{ //default is active
			$solrQuery = $solrURL . urlencode($queryZips . $queryAct);
		}
		
		//optional attribute price 
		if(isset($price)){
			$solrQuery = $solrQuery . urlencode($this->getPriceQuery($price));
		}	
		
		//optional attribute style
		if(isset($style)){
			$solrQuery = $solrQuery . urlencode($this->getStyleQuery($style));
		}			
		
		//optional attribute sort
		if(isset($sort)){
			$solrQuery = $solrQuery . $this->getSortQuery($sort);
		}					
		
		return $solrQuery;
	}
	
	
	protected function get_neighborhoods_solr_query($neighborhoods, $listingType, $offset=0, $rows=10, $price, $sort, $style){
		$solrURL = self::BASE_SOLR_URL . "&start=" . $offset . "&rows=" . $rows . "&q="; 
		$solrQuery = "";	
			
		$queryAct = ' AND (status:Active) AND distributetointernet:1';
		$querySold = ' AND (status:"Comp Sold" OR status:Sold) AND distributetointernet:1';
		$queryAll = ' AND (status:Active OR status:"Comp Sold" OR status:Sold) AND distributetointernet:1';
	
		//construct query for featured listings
		$totalNeighborhoods = count($neighborhoods);
		$queryNeighborhoods = "(";
		for($i = 0; $i < $totalNeighborhoods; $i++){
			$queryNeighborhoods = $queryNeighborhoods . "neighborhood:\"" . trim($neighborhoods[$i]) . "\"";
			if(($i +1) < $totalNeighborhoods){
				$queryNeighborhoods = $queryNeighborhoods . " OR ";
			}
		}
		$queryNeighborhoods = $queryNeighborhoods . ")";
		
		if(0 == strcasecmp("all", $listingType)){
			$solrQuery = $solrURL . urlencode($queryNeighborhoods . $queryAll);
		}else if(0 == strcasecmp("sold", $listingType)){
			$solrQuery = $solrURL . urlencode($queryNeighborhoods . $querySold);
		}else{ //default is active
			$solrQuery = $solrURL . urlencode($queryNeighborhoods . $queryAct);
		}
		
		//optional attribute price 
		if(isset($price)){
			$solrQuery = $solrQuery . urlencode($this->getPriceQuery($price));
		}	
				
		//optional attribute style
		if(isset($style)){
			$solrQuery = $solrQuery . urlencode($this->getStyleQuery($style));
		}					
				
		//optional attribute sort
		if(isset($sort)){
			$solrQuery = $solrQuery . $this->getSortQuery($sort);
		}					
		
		return $solrQuery;
	}
	
	
	protected function get_cities_solr_query($cities, $listingType, $offset=0, $rows=10, $price, $sort, $style){
		
		$solrURL = self::BASE_SOLR_URL . "&start=" . $offset . "&rows=" . $rows . "&q="; 
		$solrQuery = "";	
			
		$queryAct = ' AND (status:Active) AND distributetointernet:1';
		$querySold = ' AND (status:"Comp Sold" OR status:Sold) AND distributetointernet:1';
		$queryAll = ' AND (status:Active OR status:"Comp Sold" OR status:Sold) AND distributetointernet:1';
	
		//construct query for featured listings
		$totalCities = count($cities);
		$queryCities = "(";
		for($i = 0; $i < $totalCities; $i++){
			$queryCities = $queryCities . "city:\"" . trim($cities[$i]) . "\"";
			if(($i +1) < $totalCities){
				$queryCities = $queryCities . " OR ";
			}
		}
		$queryCities = $queryCities . ")";
		
		if(0 == strcasecmp("all", $listingType)){
			$solrQuery = $solrURL . urlencode($queryCities . $queryAll);
		}else if(0 == strcasecmp("sold", $listingType)){
			$solrQuery = $solrURL . urlencode($queryCities . $querySold);
		}else{ //default is active
			$solrQuery = $solrURL . urlencode($queryCities . $queryAct);
		}
		
		//optional attribute price 
		if(isset($price)){
			$solrQuery = $solrQuery . urlencode($this->getPriceQuery($price));
		}	
		
		//optional attribute style
		if(isset($style)){
			$solrQuery = $solrQuery . urlencode($this->getStyleQuery($style));
		}			
				
		//optional attribute sort
		if(isset($sort)){
			$solrQuery = $solrQuery . $this->getSortQuery($sort);
		}
			
		return $solrQuery;
	}
	
	
	protected function get_agents_solr_query($agentNRDS, $listingType, $offset=0, $rows=10, $price, $sort, $style){
		
		$solrURL = self::BASE_SOLR_URL . "&start=" . $offset . "&rows=" . $rows . "&q="; 
		$solrQuery = "";	
			
		$queryActOnly = ' AND (status:Active) AND distributetointernet:1';	
		$queryAct = ' AND (status:Active OR status:Pending) AND distributetointernet:1';
		$querySold = ' AND (status:"Comp Sold" OR status:Sold) AND distributetointernet:1';
		$queryAll = ' AND (status:Active OR status:Pending OR status:"Comp Sold" OR status:Sold) AND distributetointernet:1';
	
		//construct query for featured listings
		$totalAgents = count($agentNRDS);
		$queryAgents = "(";
		for($i = 0; $i < $totalAgents; $i++){
			$queryAgents = $queryAgents . "listagent1nrdsmemberid:" . $agentNRDS[$i] . " OR listagent2nrdsmemberid:" . $agentNRDS[$i];
			if(($i +1) < $totalAgents){
				$queryAgents = $queryAgents . " OR ";
			}
		}
		$queryAgents = $queryAgents . ")";
		
		if(0 == strcasecmp("all", $listingType)){
			$solrQuery = $solrURL . urlencode($queryAgents . $queryAll);
		}else if(0 == strcasecmp("sold", $listingType)){
			$solrQuery = $solrURL . urlencode($queryAgents . $querySold);
		}else if(0 == strcasecmp("actonly", $listingType)){
			$solrQuery = $solrURL . urlencode($queryAgents . $queryActOnly);
		}else{ //default is active
			$solrQuery = $solrURL . urlencode($queryAgents . $queryAct);
		}
		
		//optional attribute price 
		if(isset($price)){
			$solrQuery = $solrQuery . urlencode($this->getPriceQuery($price));
		}	

		//optional attribute style
		if(isset($style)){
			$solrQuery = $solrQuery . urlencode($this->getStyleQuery($style));
		}		

		//optional attribute sort, must be at the end
		if(isset($sort)){
			$solrQuery = $solrQuery . $this->getSortQuery($sort);
		}	
				
		return $solrQuery;
	}
	
	protected function get_office_solr_query($officeIDs, $listingType, $offset=0, $rows=10, $price, $sort, $style){
		
		$solrURL = self::BASE_SOLR_URL . "&start=" . $offset . "&rows=" . $rows . "&q=";  
		$solrQuery = "";	
		
		$queryActOnly = ' AND (status:Active) AND distributetointernet:1';		
		$queryAct = ' AND (status:Active OR status:Pending) AND distributetointernet:1';
		$querySold = ' AND (status:"Comp Sold" OR status:Sold) AND distributetointernet:1';
		$queryAll = ' AND (status:Active OR status:Pending OR status:"Comp Sold" OR status:Sold) AND distributetointernet:1';
	
		//construct query for featured listings
		$totalOffices = count($officeIDs);
		$queryOffices = "(";
		for($i = 0; $i < $totalOffices; $i++){
			$queryOffices = $queryOffices . "listofficeid:" . $officeIDs[$i];
			if(($i +1) < $totalOffices){
				$queryOffices = $queryOffices . " OR ";
			}
		}
		$queryOffices = $queryOffices . ")";
		
		if(0 == strcasecmp("all", $listingType)){
			$solrQuery = $solrURL . urlencode($queryOffices . $queryAll);
		}else if(0 == strcasecmp("sold", $listingType)){
			$solrQuery = $solrURL . urlencode($queryOffices . $querySold);
		}else if(0 == strcasecmp("actonly", $listingType)){
			$solrQuery = $solrURL . urlencode($queryOffices . $queryActOnly);
		}else{ //default is active
			$solrQuery = $solrURL . urlencode($queryOffices . $queryAct);
		}
		
		//optional attribute price 
		if(isset($price)){
			$solrQuery = $solrQuery . urlencode($this->getPriceQuery($price));
		}	
		
		//optional attribute style
		if(isset($style)){
			$solrQuery = $solrQuery . urlencode($this->getStyleQuery($style));
		}					

		//optional attribute sort
		if(isset($sort)){
			$solrQuery = $solrQuery . $this->getSortQuery($sort);
		}		
				
		return $solrQuery;
	}
	
	protected function get_streets_solr_query($streetnumber, $streetname, $type, $offset=0, $rows=10, $price, $sort, $style){
		
		$solrURL = self::BASE_SOLR_URL . "&start=" . $offset . "&rows=" . $rows . "&q=";  
		$solrQuery = "";	
			
		$queryAct = ' AND (status:Active) AND distributetointernet:1';
		$querySold = ' AND (status:"Comp Sold" OR status:Sold) AND distributetointernet:1';
		$queryAll = ' AND (status:Active OR status:"Comp Sold" OR status:Sold) AND distributetointernet:1';
	
		$queryStreets = "(streetnumber:" . $streetnumber . " AND streetname:\"" . $streetname . "\")";   
		
		if(0 == strcasecmp("all", $listingType)){
			$solrQuery = $solrURL . urlencode($queryStreets . $queryAll);
		}else if(0 == strcasecmp("sold", $listingType)){
			$solrQuery = $solrURL . urlencode($queryStreets . $querySold);
		}else{ //default is active
			$solrQuery = $solrURL . urlencode($queryStreets . $queryAct);
		}
		
		//optional attribute price 
		if(isset($price)){
			$solrQuery = $solrQuery . urlencode($this->getPriceQuery($price));
		}	
		
		//optional attribute style
		if(isset($style)){
			$solrQuery = $solrQuery . urlencode($this->getStyleQuery($style));
		}					

		//optional attribute sort
		if(isset($sort)){
			$solrQuery = $solrQuery . $this->getSortQuery($sort);
		}		
				
		return $solrQuery;
	}
	
	
	protected function get_mlnumbers_solr_query($mlnumbers, $offset=0, $rows=10, $sort){
		$solrURL = self::BASE_SOLR_URL . "&start=" . $offset . "&rows=" . $rows . "&q=";  
		$solrQuery = "";	
	
		//construct query for featured listings
		$totalMlnumbers = count($mlnumbers);
		$queryMlnumbers = "(";
		for($i = 0; $i < $totalMlnumbers; $i++){
			$queryMlnumbers = $queryMlnumbers . "mlnumber:" . $mlnumbers[$i];
			if(($i +1) < $totalMlnumbers){
				$queryMlnumbers = $queryMlnumbers . " OR ";
			}
		}
		$queryMlnumbers = $queryMlnumbers . ")";
		
		$solrQuery = $solrURL . urlencode($queryMlnumbers);
		
		//optional attribute sort
		if(isset($sort)){
			$solrQuery = $solrQuery . $this->getSortQuery($sort);
		}			
		
		return $solrQuery;
	}
	
	
	private function titan_get_json($url) {
		if(!function_exists('json_encode')){
			include_once('JSON.php');
			$GLOBALS['JSON_OBJECT'] = new Services_JSON();
			function json_encode($value) { return $GLOBALS['JSON_OBJECT']->encode($value); }
			function json_decode($value) { return $GLOBALS['JSON_OBJECT']->decode($value); }
		}
		$json = file_get_contents($url); 
		if ($json !== false) {
			$featuredListingsJson = json_decode($json);
		}
		return $featuredListingsJson;
	}

	
	/** this will do pagination **/
	
	public function show_listings_page($solrQuery, $resultTemplate, $detailsTemplate, $offset=0, $rows=10){
		
		//this is for pagination when paramters are posted to this PHP code via HTML form in the template
		if(isset($_REQUEST["qry"]) && isset($_REQUEST["resultTemplate"]) && isset($_REQUEST["detailsTemplate"]) && isset($_REQUEST["offset"]) && isset($_REQUEST["rows"])){
			//echo "Called show_listings_page() : " . $_REQUEST["qry"];
			//$flp->show_listings_page($_REQUEST["qry"], $_REQUEST["resultTemplate"], $_REQUEST["detailsTemplate"], $_REQUEST["offset"], $_REQUEST["rows"]);
			$listingsJson = $this->titan_get_json($_REQUEST["qry"]);
		}else{
			$listingsJson = $this->titan_get_json($solrQuery);
		}
			
		//these values will be used for differnt pages in the template(s)
		$totalFeaturedListings = $listingsJson->response->numFound;
		$startRow =  $listingsJson->response->start;
		
		//echo "totalFeaturedListings = " . $totalFeaturedListings;
		//echo "Start row = " . $startRow;
		
		unset($properties);
		$properties = $listingsJson->response->docs;
		
		// check if template is there
		if (!file_exists($resultTemplate)){
			return "Error: could not find default template: $resultTemplate";
			// WP_Error object not working, Probably expecting string
			//return  new WP_Error('broke', __("Could not read results template file: $resultTemplate"));
		}
		
		// include the template captureing its output into an output buffer,then return the buffer as a string	
		if (isset($properties)) {
			//echo $max .  " JSON results parsing with template " . $resultTemplate . "<br/>";
			ob_start();
			include_once($resultTemplate);
			return ob_get_clean();
		} else {	
			return "Error: Could not read the property feed";
		}
	}

	
	protected function getPriceQuery($price){
		 $priceRange = explode("-", $price);	
		 if(2 != count($priceRange)){
		 	die("Correct price range must be passed - " . $price); 
		 } 	
		 return " AND listprice:[" . $priceRange[0] . " TO " . $priceRange[1] . "]" ;	
	}
	
	protected function getStyleQuery($style){
		$styles = explode(",", $style);
		$totalStyles = count($styles);
		
		$styleQuery = " AND (";
		for($i = 0; $i < $totalStyles; $i++){
			$styleQuery = $styleQuery . "style:\"" . trim($styles[$i]) . "\"";
			if(($i +1) < $totalStyles){
				$styleQuery = $styleQuery . " OR ";
			}
		}
		$styleQuery = $styleQuery . ")";
		return $styleQuery;	
	}
	
	protected function getSortQuery($sort){
		 if(!isset($sort)){
		 	die("Correct sort parameter with asc/desc must be passed - " . $sort); 
		 } 	
		 return "&sort=" . urlencode($sort);	
	}
	
}
// comment
?>
