<?php
/*
	Template Name: Default Property Details
*/

// This is a JSON feed template for the retools plugin
?>

<script>
     $(document).ready(function(){
  	$('#myList li:nth-child(odd)').addClass('alternate');
	$('#myList2 li:nth-child(odd)').addClass('alternate');
	$('#myList3 li:nth-child(odd)').addClass('alternate');
	$('#myList4 li:nth-child(odd)').addClass('alternate');
	});
	
	
</script>
 
<script type="text/javascript">
function open_window(URL,wt,ht) {
        msgwin=window.open(URL,"NewWindow","toolbar=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,width="+ wt +",height="+ ht +",top=200,left=250")
}


function showPage(offset){	
	//alert("offset = " + offset);
	document.flPageForm.offset.value = offset;
	//http://stackoverflow.com/questions/7171099/how-to-replace-url-parameter-with-javascript-jquery
	var originalQuery = document.flPageForm.qry.value;
	var revisedQuery = originalQuery.replace(/(start=).*?(&)/,'$1' + offset + '$2');
	document.flPageForm.qry.value = revisedQuery;	
	//alert("Solr query =" + revisedQuery);
	document.flPageForm.submit();
}




</script>

<?php
	$pagerURL = WP_PLUGIN_DIR . '/tflp/fl-pager.php';
?>

<div id="featured">

<h2>Featured Listings Template 2</h2>

<form method="POST" name="flPageForm">
	<input type="hidden" name="qry" value="<?php echo $solrQuery; ?>" />
	<input type="hidden" name="resultTemplate" value="<?php echo $resultTemplate; ?>" />
	<input type="hidden" name="detailsTemplate" value="<?php echo $detailsTemplate; ?>" />
	<input type="hidden" name="offset" value="0"/>
	<input type="hidden" name="rows" value="<?php echo $rows; ?>" />
</form>
	
	
<?php
// This is a JSON feed template for the retools plugin
$count=0;

//print some info here
$endRow = $startRow + $rows;
if($totalFeaturedlistings < $endRow){
	$endRow = $totalFeaturedt-listings;
}

//pager links here 
include("fl-pager.php");

// echo '<pre>';
// print_r($properties);
// echo '</pre>';

//// Start Of Property Loop
foreach($properties as $prop) {
	//echo "here mlnumber = " .  $prop->mlnumber;
	
	if ($count < $totalFeaturedListings ){
	
		$detailsurl =  add_query_arg( array( 
			'detail' => $details
				
		));
			
		// the address is a bit of a pain. we have to build it:
		$address = $prop->streetnumber . " " . $prop->streetname ;
		// if sold show sold price
		if ($prop->status == "Sold" || $prop->status == "Comp Sold"){
			$price = number_format($prop->salescloseprice);
		} else {
			$price = number_format($prop->listprice);
		}
		// If property was sold by but not listed by this agent we must state "Selling Agent" somwhere 
		// here we will display that as the t-listing status (MLS Rule)
		if ($prop->sellingagent == "true"){
			$status= "Selling Agent";
		} else {
			 $status=$prop->status;
		}
		
		//**********************************************************************************************************************************
		// changes to show all property photos
		//**********************************************************************************************************************************
		$totalPhotos = $prop->photocount;
?>
		<div class="t-listingwrapper">
			<div class="row t-listinghead">
			<div class="six columns  left alleft">
				<span class="aladd1"><?php echo $address; ?></span><br />
				<span class="aladd2"><?php echo $prop->city . ", " . $prop->stateorprovince . " ".$prop->postalcode;?> </span>
			</div> <!-- alleft -->
		<div class="six columns right alright">
				<span class="alprice">$<?php echo $price; ?></span><br />
				<span class="alstatus"><?php echo $status; ?></span>
		</div> <!-- alright -->				
		</div> <!-- row-t-listing head -->
		<div class="row t-listingimages">
		<?php
				// mls images are | size 2 640x480 | size 1 96x68 | size 0 256x192
				// agent uploaded photos (aup) com in 4 sizes the 640 size is the default
				//   if you want a different size you have to replace the size '.x640.' in the filename with
				//   example str_replace( '.x640.', '.x256.',  ) 
				//   str_replace('.x640.', '.x256.', $prop->aupPhotoURLArray[0])	
				//   str_replace('.x640.', '.x96.', $prop->aupPhotoURLArray[0])	
	
				//print_r($prop->aupPhotoURLArray);
				//echo "<br />";
				?>
				
				<div class="flexslider">
				<ul class="slides">
				<?php
				for ($i = 1; $i <= $totalPhotos; $i++){ //main photo

					$imgurl="http://mightyagent2.com/tc/imsv.img?mlsnum=".$prop->mlnumber."&idx=${i}&size=0"; 
					$istyle = "";
					//if ($i > 1) $istyle = 'style="display: none;"';
					
					echo "<li><img src=\"$imgurl\" /></li>\n";
					
				}
				?>
				</ul>
				</div>				
			
		</div><!-- row-t-listingimages-->
			<div class="t-listingbody">
				<div class="lbleft">
				
				<ul>
					<li><a href="javascript:open_window('<?php echo $prop->inquiryurl; ?>',520,480)">Inquiry</a></li>	
					<li><a href="javascript:open_window('<?php echo $prop->showingrequesturl; ?>',675,445)">Request a Showing</a></li>
					<li><a href="javascript:open_window('<?php echo $prop->calcurl ;?>',330,490)">Mortgage Calculator</a></li>	
				<?php
					if($prop->vt == "true"){
						echo '<li><a href="'.$prop->vturl .'" target="_blank">Virtual Tour</a></li>'."\n";	
					}
					if($prop->spw == "true"){
						echo '<li><a href="'.$prop->spwurl .'" target="_blank">Property Website</a></li>'."\n";	
					}
					if($prop->spwdoc == "true"){
						echo '<li><a href="'.$prop->spwdocurl .'" target="_blank">Document</a></li>'."\n";	
					}
				?>			
				</ul>	
				</div>			
				</div> <!-- lbleft -->
				<div class="lbright">
				<?php if($prop->open == "true"){// Open House ?>
					<div class="alopen">
						Open House: <?php echo $prop->opendate.": ".$prop->openstarttime." to ".$prop->openendtime ?>
					</div>
				<?php }	?>
				<span class="fldesclab">Description:</span><br />
				<span class="fldesc"><?php echo $prop->publicremarks;?></span>
				<table id="featured-t-listings">
				<tr>
				<td class="fllab">Price:</td>
				<td class="flval">$<?php echo $price;?></td>
				</tr>
				<tr>
				<td class="fllab">Status:</td>
				<td class="flval"><?php echo $status;?></td>
				</tr>
				<tr>
				<td class="fllab">Property Type:</td>
				<td class="flval"><?php echo $prop->style;?></td>
				</tr>
				<tr>
				<td class="fllab">Bedrooms:</td>
				<td class="flval"><?php echo $prop->bedrooms;?></td>
				</tr>
				<tr>
				<td class="fllab">Bathrooms:</td>
				<td class="flval"><?php echo $prop->bathstotal;?></td>
				</tr>
				<tr>
				<td class="fllab">Year Built:</td>
				<td class="flval"><?php echo $prop->yearbuilt;?></td>
				</tr>
				<tr>
				<td class="fllab">Approx. Sq. Ft:</td>
				<td class="flval"><?php echo $prop->livingarea;?></td>
				</tr>
				<tr>
				<td class="fllab">School District:</td>
				<td class="flval"><?php echo $prop->schooldistrictnumber;?></td>
				</tr>
				<tr>
				<td class="fllab">Taxes:</td>
				<td class="flval">$<?php echo number_format($prop->taxes);?></td>
				</tr>
				<tr>
				<td class="fllab">MLS#:</td>
				<td class="flval"><?php echo $prop->mlnumber;?> </td>
				</tr>
				</table>
				</div> <!-- lbright -->
			
			
<div id="roomgrid" class="row">	
	
	
 	
 		<div class="twelve columns retspublicrooms">
		<h2>Rooms</h2>	
        <ul id="myList">					
		<?php if(!empty($prop->roomarea1)): ?>    
        <li><div class="roomrows">    	
			<div class="four columns retsrooms">Living Room:</div>
			<div class="two columns retsroomarea"><?php echo $prop->roomarea1;?></div>	
			<div class="four columns retsroomfloor"><?php if(!empty($prop->roomfloor1)){ ?>
				<?php echo $prop->roomfloor1;?>&nbsp;Level</div>
                <?php } else { ?> </div>
			<?php }?>
			</div></li>
		<?php endif;?>  
        
		
		<?php if(!empty($prop->roomarea2)): ?>
         <li><div class="roomrows">           		
			<div class="four columns retsrooms">Dining Room:</div>
			<div class="two columns retsroomarea"><?php echo $prop->roomarea2;?></div>	
			<div class="four columns retsroomfloor"><?php if(!empty($prop->roomfloor2)){ ?>
				<?php echo $prop->roomfloor2;?>&nbsp;Level</div>
			<?php } else { ?> </div>
			<?php }?>
            </div></li>
		<?php endif;?>   
		
        
		<?php if(!empty($prop->roomarea3)): ?>
        <li><div class="roomrows">                		
			<div class="four columns retsrooms">Kitchen:</div>
			<div class="two columns retsroomarea"><?php echo $prop->roomarea3;?></div>	
			<div class="four columns retsroomfloor"><?php if(!empty($prop->roomfloor3)){ ?>
				<?php echo $prop->roomfloor3;?>&nbsp;Level</div>
			<?php } else { ?> </div>
			<?php }?>
            </div></li>
		<?php endif;?>   
		
        
		<?php if(!empty($prop->roomarea4)): ?>
        <li><div class="roomrows">           	
			<div class="four columns retsrooms">Family Room:</div>
			<div class="two columns retsroomarea"><?php echo $prop->roomarea4;?></div>	
			<div class="four columns retsroomfloor"><?php if(!empty($prop->roomfloor4)){ ?>
				<?php echo $prop->roomfloor4;?>&nbsp;Level</div>
			<?php } else { ?> </div>
			<?php }?>
            </div></li>
		<?php endif;?>   
		
        
		<?php if(!empty($prop->roomfamilychar)): ?>
        <li><div class="roomrows">     	
			<div class="four columns retsrooms">Family Room</div>
			<div class="eight columns retsfloorandarea"><?php echo $prop->roomfamilychar;?>	
			</div></div></li>
		<?php endif;?>        
	
	
	<!--<div class="twelve columns retsbedrooms">-->
	<?php if(!empty($prop->roomarea5)): ?>    	
    	<li><div class="roomrows">   
			<div class="four columns retsrooms">Bedroom 1:</div>
			<div class="two columns retsroomarea"><?php echo $prop->roomarea5;?></div>	
			<div class="four columns retsroomfloor"><?php if(!empty($prop->roomfloor5)){ ?>
				<?php echo $prop->roomfloor5;?>&nbsp;Level</div>
			<?php } else { ?> </div>
			<?php }?>
            </div></li>
		<?php endif;?>   
     
		
		<?php if(!empty($prop->roomarea6)): ?>   
        <li><div class="roomrows">     
			<div class="four columns retsrooms">Bedroom 2:</div>
			<div class="two columns retsroomarea"><?php echo $prop->roomarea6;?></div>	
			<div class="four columns retsroomfloor"><?php if(!empty($prop->roomfloor6)){ ?>
				<?php echo $prop->roomfloor6;?>&nbsp;Level</div>
			<?php } else { ?> </div>
			<?php }?>
            </div></li>
		<?php endif;?>   
	
		<?php if(!empty($prop->roomarea7)): ?>
        <li><div class="roomrows">  
			<div class="four columns retsrooms">Bedroom 3:</div>
			<div class="two columns retsroomarea"><?php echo $prop->roomarea7;?></div>	
			<div class="four columns retsroomfloor"><?php if(!empty($prop->roomfloor7)){ ?>
				<?php echo $prop->roomfloor7;?>&nbsp;Level</div>
			<?php } else { ?> </div>
			<?php }?>
            </div></li>
		<?php endif;?>   
		
		<?php if(!empty($prop->roomarea8)): ?>
        <li><div class="roomrows"> 
			<div class="four columns retsrooms">Bedroom 4:</div>
			<div class="two columns retsroomarea"><?php echo $prop->roomarea8;?></div>	
			<div class="four columns retsroomfloor"><?php if(!empty($prop->roomfloor8)){ ?>
				<?php echo $prop->roomfloor8;?>&nbsp;Level</div>
			<?php } else { ?> </div>
			<?php }?>
            </div></li>
		<?php endif;?>   
        
	
	<!--<div class="twelve columns retsextrarooms">-->
		
		<?php if(!empty($prop->roomtype9)): ?>
       <li> <div class="roomrows"> 
			<div class="four columns retsrooms"><?php echo $prop->roomtype9;?></div>
			<div class="two columns retsfloorandarea"><?php echo $prop->roomarea9;?></div>
			<div class="four columns retsroomfloor"><?php if(!empty($prop->roomfloor9)){ ?>
			<?php echo $prop->roomfloor9;?>&nbsp;Level</div>
			<?php } else { ?> </div>
			<?php }?>
            </div></li>
		<?php endif;?>   
		
		<?php if(!empty($prop->roomtype10)): ?>
        <li><div class="roomrows"> 
			<div class="four columns retsrooms"><?php echo $prop->roomtype10;?></div>
			<div class="two columns retsfloorandarea"><?php echo $prop->roomarea10;?></div>
			<div class="four columns retsroomfloor"><?php if(!empty($prop->roomfloor10)){ ?>
		<?php echo $prop->roomfloor10;?>&nbsp;Level</div>
			<?php } else { ?> </div>
			<?php }?>
            </div></li>
		<?php endif;?>   
		
		<?php if(!empty($prop->roomtype11)): ?>
        <li><div class="roomrows"> 
			<div class="four columns retsrooms"><?php echo $prop->roomtype11;?></div>
			<div class="two columns retsfloorandarea"><?php echo $prop->roomarea11;?></div>
			<div class="four columns retsroomfloor"><?php if(!empty($prop->roomfloor11)){ ?>
		<?php echo $prop->roomfloor11;?>&nbsp;Level</div>
			<?php } else { ?> </div>
			<?php }?>
            </div></li>
		<?php endif;?>   
		
		<?php if(!empty($prop->roomtype12)): ?>
		<li><div class="roomrows"> 	
            <div class="four columns retsrooms"><?php echo $prop->roomtype12;?></div>
			<div class="two columns retsfloorandarea"><?php echo $prop->roomarea12;?></div>
			<div class="four columns retsroomfloor"><?php if(!empty($prop->roomfloor12)){ ?>
		<?php echo $prop->roomfloor12;?>&nbsp;Level</div>
			<?php } else { ?> </div>
			<?php }?>
            </div></li>
		<?php endif;?>   
		
		<?php if(!empty($prop->roomtype13)): ?>
		<li><div class="roomrows"> 		
            <div class="four columns retsrooms"><?php echo $prop->roomtype13;?></div>
			<div class="two columns retsfloorandarea"><?php echo $prop->roomarea13;?></div>
			<div class="four columns retsroomfloor"><?php if(!empty($prop->roomfloor13)){ ?>
		<?php echo $prop->roomfloor13;?>&nbsp;Level</div>
			<?php } else { ?> </div>
			<?php }?>
            </div></li>
		<?php endif;?>   
		
		<?php if(!empty($prop->roomtype14)): ?>
        <li><div class="roomrows"> 	
			<div class="four columns retsrooms"><?php echo $prop->roomtype14;?></div>
			<div class="two columns retsfloorandarea"><?php echo $prop->roomarea14;?></div>
			<div class="four columns retsroomfloor"><?php if(!empty($prop->roomfloor14)){ ?>
		<?php echo $prop->roomfloor14;?>&nbsp;Level</div>
			<?php } else { ?> </div>
			<?php }?>
            </div></li>
		<?php endif;?>   
					
		
		<?php if(!empty($prop->bathstotal)): ?>
        <li><div class="roomrows"> 	
			<div class="four columns retsattributes"><b>Bath - Total</b></div>
			<div class="eight columns retsvalues"><?php echo $prop->bathstotal;?></div>
            </div></li>
		<?php endif;?>
		
		<?php if(!empty($prop->bathsfull)): ?>
        <li><div class="roomrows"> 	
			<div class="four columns retsattributes"><b>Bath - Full</b></div>
			<div class="eight columns retsvalues"><?php echo $prop->bathsfull;?></div>
            </div></li>
		<?php endif;?>
		
		<?php if(!empty($prop->bathsthreequarter)): ?>
        <li><div class="roomrows"> 	
			<div class="four columns retsattributes"><b>Bath - Three Quarters</b></div>
			<div class="eight columns retsvalues"><?php echo $prop->bathsthreequarter;?></div>
            </div></li>
		<?php endif;?>
		
		<?php if(!empty($prop->bathshalf)): ?>
        <li><div class="roomrows"> 
			<div class="four columns retsattributes"><b>Bath - Half</b></div>
			<div class="eight columns retsvalues"><?php echo $prop->bathshalf;?></div>
            </div></li>
		<?php endif;?>
		
		<?php if(!empty($prop->bathdesc)): ?>
        <li><div class="roomrows"> 
			<div class="four columns retsattributes"><b>Bath Description</b></div>
			<div class="eight columns retsvalues"><?php echo $prop->bathdesc;?></div>
            </div></li></ul>
		<?php endif;?>
		

			
	
		<h2>Interior Features</h2>
        <ul id="myList2">	
		<?php if(!empty($prop->livingarea)): ?>
        <li><div class="roomrows">
			<div class="four columns retsattributes"><b>Total Finished Sq Ft</b></div>
			 <div class="eight columns retsvalues"><?php echo $prop->livingarea;?></div>
             </div></li>
		<?php endif;?>
		
		<?php if(!empty($prop->sqftaboveground)): ?>
        <li><div class="roomrows">
			<div class="four columns retsattributes"><b>Above Ground Finished</b></div>
			<div class="eight columns retsvalues"><?php echo $prop->sqftaboveground;?></div>
            </div></li>
		<?php endif;?>
		
		<?php if(!empty($prop->sqftbelowground)): ?>
        <li><div class="roomrows">
			<div class="four columns retsattributes"><b>Below Ground Finished</b></div>
			<div class="eight columns retsvalues"><?php echo $prop->sqftbelowground;?></div>
            </div></li>
		<?php endif;?>
		
		<?php if(!empty($prop->foundationsize)): ?>
        <li><div class="roomrows">
			<div class="four columns retsattributes"><b>Foundation Size</b></div>
			<div class="eight columns retsvalues"><?php echo $prop->foundationsize;?></div>
            </div></li>
		<?php endif;?>
		
		<?php if(!empty($prop->fireplaceyn)): ?>
        <li><div class="roomrows">
			<div class="four columns retsattributes"><b>Fireplace Y:N</b></div>
			<div class="eight columns retsvalues"><?php echo $prop->fireplaceyn;?></div>
			</div></li>
			<?php endif;?>
		
		<?php if(!empty($prop->fireplaces)): ?>
        <li><div class="roomrows">
			<div class="four columns retsattributes"><b>Number Of Fireplaces</b></div>
			<div class="eight columns retsvalues"><?php echo $prop->fireplaces;?></div>
            </div></li>
		<?php endif;?>		
		
		<?php if(!empty($prop->fireplaceloc)): ?>	
        <li><div class="roomrows">
			<div class="four columns retsattributes"><b>Fireplace Characteristic</b></div>
			<div class="eight columns retsvalues"><?php echo $prop->fireplaceloc;?></div>
            </div></li>
		<?php endif;?>
		
		<?php if(!empty($prop->appliances)): ?>
        <li><div class="roomrows">
			<div class="four columns retsattributes"><b>Appliances</b></div>
			<div class="eight columns retsvalues"><?php echo $prop->appliances;?></div>
            </div></li>
		<?php endif;?>
		
		<?php if(!empty($prop->amenitiesshared)): ?>
        <li><div class="roomrows">
			<div class="four columns retsattributes"><b>Amenities - Shared</b></div>
			<div class="eight columns retsvalues"><?php echo $prop->amenitiesshared;?></div>
            </div></li>
		<?php endif;?>
		
		<?php if(!empty($prop->amenitiesunit)): ?>
        <li><div class="roomrows">
			<div class="four columns retsattributes"><b>Amenities - Unit</b></div>
			<div class="eight columns retsvalues"><?php echo $prop->amenitiesunit;?></div>
			</div></li></ul>
			<?php endif;?>
		
	
	
	<!--<div class="twelve columns retsattributes">-->
		<h2>Structural Features</h2>
		<ul id="myList3">
		<?php if(!empty($prop->parkinggarage)): ?>
        <li><div class="roomrows">
			<div class="four columns retsattributes"><b>Garage</b></div>
			<div class="eight columns retsvalues"><?php echo $prop->parkinggarage;?></div>
            </div></li>			
		<?php endif;?>
		
		<?php if(!empty($prop->parkingopen)): ?>
        <li><div class="roomrows">
			<div class="four columns retsattributes"><b>Other Parking Spaces</b></div>
			<div class="eight columns retsvalues"><?php echo $prop->parkingopen;?></div>
            </div></li>
		<?php endif;?>
		
		<?php if(!empty($prop->garagedescription)): ?>
        <li><div class="roomrows">
			<div class="four columns retsattributes"><b>Parking Characteristics</b></div>
			<div class="eight columns retsvalues"><?php echo $prop->garagedescription;?></div>
            </div></li>
		<?php endif;?>
		
		<?php if(!empty($prop->garagestallnum)): ?>
        <li><div class="roomrows">
			<div class="four columns retsattributes"><b>GarageStallNum</b></div>
			<div class="eight columns retsvalues"><?php echo $prop->garagestallnum;?></div>
            </div></li>			
		<?php endif;?>
		
		<?php if(!empty($prop->pooldescription)): ?>
        <li><div class="roomrows">
			<div class="four columns retsattributes"><b>Pool</b></div>
			<div class="eight columns retsvalues"><?php echo $prop->pooldescription;?></div>
            </div></li>
		<?php endif;?>
		
		<?php if(!empty($prop->coolingdescription)): ?>
        <li><div class="roomrows">
			<div class="four columns retsattributes"><b>Air Conditioning</b></div>
			<div class="eight columns retsvalues"><?php echo $prop->coolingdescription;?></div>
            </div></li>
		<?php endif;?>
		
		<?php if(!empty($prop->handicapaccess)): ?>
        <li><div class="roomrows">
			<div class="four columns retsattributes"><b>Handicap Accessible</b></div>
			<div class="eight columns retsvalues"><?php echo $prop->handicapaccess;?></div>
            </div></li>
		<?php endif;?>
		
		<?php if(!empty($prop->heatingdescription)): ?>
        <li><div class="roomrows">
			<div class="four columns retsattributes"><b>Heating</b></div>
			<div class="eight columns retsvalues"><?php echo $prop->heatingdescription;?></div>
            </div></li>
		<?php endif;?>
		
		<?php if(!empty($prop->roof)): ?>
        <li><div class="roomrows">
			<div class="four columns retsattributes"><b>Roof</b></div>
			<div class="eight columns retsvalues"><?php echo $prop->roof;?></div>
            </div></li>
		<?php endif;?>
		
		<?php if(!empty($prop->basement)): ?>
        <li><div class="roomrows">
			<div class="four columns retsattributes"><b>Basement</b></div>
			<div class="eight columns retsvalues"><?php echo $prop->basement;?></div>
            </div></li>
		<?php endif;?>
		
		<?php if(!empty($prop->fuel)): ?>
        <li><div class="roomrows">
			<div class="four columns retsattributes"><b>Fuel</b></div>
			<div class="eight columns retsvalues"><?php echo $prop->fuel;?></div>
            </div></li>
		<?php endif;?>
		
		<?php if(!empty($prop->water)): ?>
        <li><div class="roomrows">
			<div class="four columns retsattributes"><b>Water</b></div>
			<div class="eight columns retsvalues"><?php echo $prop->water;?></div>
            </div></li>
		<?php endif;?>
		
		<?php if(!empty($prop->sewer)): ?>
        <li><div class="roomrows">
			<div class="four columns retsattributes"><b>Sewer</b></div>
			<div class="eight columns retsvalues"><?php echo $prop->sewer;?></div>
            </div></li></ul>
		<?php endif;?>
		
		
	
		<!--<div class="twelve columns retsattributes">-->
		<h2>Lot and Location</h2>
		<ul id="myList4">
		<?php if(!empty($prop->acres)): ?>
        <li><div class="roomrows">
			<div class="four columns retsattributes"><b>Acres</b></div>
			<div class="eight columns retvalues"><?php echo $prop->acres;?></div>
            </div></li>
		<?php endif;?>
		
		<?php if(!empty($prop->lotdescription)): ?>
        <li><div class="roomrows">
			<div class="four columns retsattributes"><b>Lot Description</b></div>
			<div class="eight columns retsvalues"><?php echo $prop->lotdescription;?></div>
            </div></li>
		<?php endif;?>
		
		<?php if(!empty($prop->lotprice)): ?>
        <li><div class="roomrows">
			<div class="four columns retsattributes"><b>Lot Price</b></div>
			<div class="eight columns retsvalues"><?php echo $prop->lotprice;?></div>
            </div></li>
		<?php endif;?>
		
		<?php if(!empty($prop->lotsizedimensions)): ?>
        <li><div class="roomrows">
			<div class="four columns retsattributes"><b>Lot Dimensions</b></div>
			<div class="eight columns retsvalues"><?php echo $prop->lotsizedimensions;?></div>
            </div></li>
		<?php endif;?>
		
		<?php if(!empty($prop->fence)): ?>
        <li><div class="roomrows">
			<div class="four columns retsattributes"><b>Fencing</b></div>
			<div class="eight columns retsvalues"><?php echo $prop->fence;?></div>
            </div></li>
		<?php endif;?>
		
		<?php if(!empty($prop->postalcity)): ?>
        <li><div class="roomrows">
			<div class="four columns retsattributes"><b>PostalCity</b></div>
			<div class="eight columns retsvalues"><?php echo $prop->postalcity;?></div>
            </div></li>
		<?php endif;?>
		
		<?php if(!empty($prop->postalcode)): ?>
        <li><div class="roomrows">
			<div class="four columns retsattributes"><b>Zip Code</b></div>
			<div class="eight columns retsvalues"><?php echo $prop->postalcode;?></div>
            </div></li>
		<?php endif;?>
		
		<?php if(!empty($prop->neighborhood)): ?>
        <li><div class="roomrows">
			<div class="four columns retsattributes"><b>Neighborhood</b></div>
			<div class="eight columns retsvalues"><?php echo $prop->neighborhood;?></div>
            </div></li>
		<?php endif;?>
        
		<?php if(!empty($prop->agentowner)): ?>
        <li><div class="roomrows">
			<div class="four columns retsattributes"><b>Agent/Owner</b></div>
            <div class="eight columns retsvalues"><?php echo $prop->agentowner;?></div>
            </div></li>
		<?php endif;?>
		
		<?php if(!empty($prop->agriculturalwater)): ?>
        <li><div class="roomrows">
        	<div class="four columns retsattributes"><b>Agricultural Water</b></div>
        	<div class="eight columns retsvalues"><?php echo $prop->agriculturalwater;?></div>
            </div></li>
		<?php endif;?>

		<?php if(!empty($prop->applicationfee)): ?>
        <li><div class="roomrows">
        <div class="four columns retsattributes"><b>Application Fee</b></div>
        <div class="eight columns retsvalues"><?php echo $prop->applicationfee;?></div>
            </div></li>
		<?php endif;?>
        
		<?php if(!empty($prop->assocfeeincludes)): ?>
        <li><div class="roomrows">
        <div class="four columns retsattributes"><b>Association Fee Includes</b></div>
        <div class="eight columns retsvalues"><?php echo $prop->assocfeeincludes;?></div>
            </div></li>
		<?php endif;?>
        
		<?php if(!empty($prop->assocfeepaid)): ?>
        <li><div class="roomrows">
        <div class="four columns retsattributes"><b>Fee Frequency</b></div>
        <div class="eight columns retsvalues"><?php echo $prop->assocfeepaid;?></div>
            </div></li>
		<?php endif;?>
        
		<?php if(!empty($prop->assocfeeyn)): ?>
        <li><div class="roomrows">
        <div class="four columns retsattributes"><b>AssocFeeYN</b></div>
        <div class="eight columns retsvalues"><?php echo $prop->assocfeeyn;?></div>
            </div></li>
		<?php endif;?>
        
		<?php if(!empty($prop->associationfee)): ?>
        <li><div class="roomrows">
        <div class="four columns retsattributes"><b>Association Fee</b></div>
        <div class="eight columns retsvalues"><?php echo $prop->associationfee;?></div>
            </div></li>
		<?php endif;?>
    
		<?php if(!empty($prop->attached)): ?>
        <li><div class="roomrows">
        <div class="four columns retsattributes"><b>Common Wall</b></div>
        <div class="eight columns retsvalues"><?php echo $prop->attached;?></div>
            </div></li>
		<?php endif;?>
        
		<?php if(!empty($prop->auctioneerlicense)): ?>
        <li><div class="roomrows">
        <div class="four columns retsattributes"><b>Auctioneer License #</b></div>
        <div class="eight columns retsvalues"><?php echo $prop->auctioneerlicense;?></div>
            </div></li>
		<?php endif;?>
        
		<?php if(!empty($prop->auctionyn)): ?>
        <li><div class="roomrows">
        <div class="four columns retsattributes"><b>Auction Y/N</b></div>
        <div class="eight columns retsvalues"><?php echo $prop->auctionyn;?></div>
            </div></li>
		<?php endif;?>
        
		<?php if(!empty($prop->autciontype)): ?>
        <li><div class="roomrows">
        <div class="four columns retsattributes"><b>Auction Type</b></div>
        <div class="eight columns retsvalues"><?php echo $prop->autciontype;?></div>
            </div></li>
		<?php endif;?>

		<?php if(!empty($prop->class)): ?>
        <li><div class="roomrows">
        <div class="four columns retsattributes"><b>PropType</b></div>
        <div class="eight columns retsvalues"><?php echo $prop->class;?></div>
            </div></li>
		<?php endif;?>
        
		<?php if(!empty($prop->completiondate)): ?>
        <li><div class="roomrows">
        <div class="four columns retsattributes"><b>Projected Completion Dat</b></div>
        <div class="eight columns retsvalues"><?php echo $prop->completiondate;?></div>
            </div></li>
		<?php endif;?>
        
		<?php if(!empty($prop->constructionstatus)): ?>
        <li><div class="roomrows">
        <div class="four columns retsattributes"><b>Construction Status</b></div>
        <div class="eight columns retsvalues"><?php echo $prop->constructionstatus;?></div>
            </div></li>
		<?php endif;?>
        
		<?php if(!empty($prop->contingency)): ?>
        <li><div class="roomrows">
        <div class="four columns retsattributes"><b>Contingency</b></div>
        <div class="eight columns retsvalues"><?php echo $prop->contingency;?></div>
            </div></li>
		<?php endif;?>

		<?php if(!empty($prop->county)): ?>
        <li><div class="roomrows">
        <div class="four columns retsattributes"><b>County</b></div>
        <div class="eight columns retsvalues"><?php echo $prop->county;?></div>
            </div></li>
		<?php endif;?>
        
		<?php if(!empty($prop->croptype)): ?>
        <li><div class="roomrows">
        <div class="four columns retsattributes"><b>Crop Type</b></div>
        <div class="eight columns retsvalues"><?php echo $prop->croptype;?></div>
            </div></li>
		<?php endif;?>
        
		<?php if(!empty($prop->directions)): ?>
        <li><div class="roomrows">
        <div class="four columns retsattributes"><b>Directions</b></div>
        <div class="eight columns retsvalues"><?php echo $prop->directions;?></div>
            </div></li>
		<?php endif;?>
        
		<?php if(!empty($prop->dpresource)): ?>
        <li><div class="roomrows">
        <div class="four columns retsattributes"><b>DPResource</b></div>
        <div class="eight columns retsvalues"><?php echo $prop->dpresource;?></div>
            </div></li>
		<?php endif;?>
        
		<?php if(!empty($prop->exterior)): ?>
        <li><div class="roomrows">
        <div class="four columns retsattributes"><b>Exterior</b></div>
        <div class="eight columns retsvalues"><?php echo $prop->exterior;?></div>
            </div></li>
		<?php endif;?>
        
		<?php if(!empty($prop->farmtype)): ?>
        <li><div class="roomrows">
        <div class="four columns retsattributes"><b>Farm Type</b></div>
        <div class="eight columns retsvalues"><?php echo $prop->farmtype;?></div>
            </div></li>
		<?php endif;?>

		<?php if(!empty($prop->financingterms)): ?>
        <li><div class="roomrows">
        <div class="four columns retsattributes"><b>Financing Terms</b></div>
        <div class="eight columns retsvalues"><?php echo $prop->financingterms;?></div>
            </div></li>
		<?php endif;?>

		<?php if(!empty($prop->foreclosure)): ?>
        <li><div class="roomrows">
        <div class="four columns retsattributes"><b>ForeclosureStatus</b></div>
        <div class="eight columns retsvalues"><?php echo $prop->foreclosure;?></div>
            </div></li>
		<?php endif;?>
        
		<?php if(!empty($prop->forrentmlnumber)): ?>
        <li><div class="roomrows">
        <div class="four columns retsattributes"><b>forRentMLNumber</b></div>
        <div class="eight columns retsvalues"><?php echo $prop->forrentmlnumber;?></div>
            </div></li>
		<?php endif;?>
        
		<?php if(!empty($prop->forsalemlnumber)): ?>
        <li><div class="roomrows">
        <div class="four columns retsattributes"><b>forSaleMLNumber</b></div>
        <div class="eight columns retsvalues"><?php echo $prop->forsalemlnumber;?></div>
            </div></li>
		<?php endif;?>

		<?php if(!empty($prop->frontagefeet)): ?>
        <li><div class="roomrows">
        <div class="four columns retsattributes"><b>Road Frontage</b></div>
        <div class="eight columns retsvalues"><?php echo $prop->frontagefeet;?></div>
            </div></li>
		<?php endif;?>

		<?php if(!empty($prop->furnished)): ?>
        <li><div class="roomrows">
        <div class="four columns retsattributes"><b>furnished</b></div>
        <div class="eight columns retsvalues"><?php echo $prop->furnished;?></div>
            </div></li>
		<?php endif;?>

		<?php if(!empty($prop->homesteaddesc)): ?>
        <li><div class="roomrows">
        <div class="four columns retsattributes"><b>Homestead</b></div>
        <div class="eight columns retsvalues"><?php echo $prop->homesteaddesc;?></div>
            </div></li>
		<?php endif;?>

		<?php if(!empty($prop->insurancefee)): ?>
        <li><div class="roomrows">
        <div class="four columns retsattributes"><b>InsuranceFee</b></div>
        <div class="eight columns retsvalues"><?php echo $prop->insurancefee;?></div>
            </div></li>
		<?php endif;?>
        
		<?php if(!empty($prop->insurancefeefrequency)): ?>
        <li><div class="roomrows">
        <div class="four columns retsattributes"><b>InsuranceFeeFreq</b></div>
        <div class="eight columns retsvalues"><?php echo $prop->insurancefeefrequency;?></div>
            </div></li>
		<?php endif;?>
        
		<?php if(!empty($prop->isnewdevelopment)): ?>
        <li><div class="roomrows">
        <div class="four columns retsattributes"><b>IsNewDevelopment</b></div>
        <div class="eight columns retsvalues"><?php echo $prop->isnewdevelopment;?></div>
            </div></li>
		<?php endif;?>
        
		<?php if(!empty($prop->lakeacres)): ?>
        <li><div class="roomrows">
        <div class="four columns retsattributes"><b>LakeAcres</b></div>
        <div class="eight columns retsvalues"><?php echo $prop->lakeacres;?></div>
            </div></li>
		<?php endif;?>
        
		<?php if(!empty($prop->lakebottom)): ?>
        <li><div class="roomrows">
        <div class="four columns retsattributes"><b>LakeBottom</b></div>
        <div class="eight columns retsvalues"><?php echo $prop->lakebottom;?></div>
            </div></li>
		<?php endif;?>
        
		<?php if(!empty($prop->lakechain)): ?>
        <li><div class="roomrows">
        <div class="four columns retsattributes"><b>LakeChain</b></div>
        <div class="eight columns retsvalues"><?php echo $prop->lakechain;?></div>
            </div></li>
		<?php endif;?>
        
		<?php if(!empty($prop->lakechainacreage)): ?>
        <li><div class="roomrows">
        <div class="four columns retsattributes"><b>LakeChainAcreage</b></div>
        <div class="eight columns retsvalues"><?php echo $prop->lakechainacreage;?></div>
            </div></li>
		<?php endif;?>
        
		<?php if(!empty($prop->lakechainname)): ?>
        <li><div class="roomrows">
        <div class="four columns retsattributes"><b>LakeChainName</b></div>
        <div class="eight columns retsvalues"><?php echo $prop->lakechainname;?></div>
            </div></li>
		<?php endif;?>
        
		<?php if(!empty($prop->lakedepth)): ?>
        <li><div class="roomrows">
        <div class="four columns retsattributes"><b>LakeDepth</b></div>
        <div class="eight columns retsvalues"><?php echo $prop->lakedepth;?></div>
		</div></li>
		<?php endif;?>
        
		<?php if(!empty($prop->lakewaterfront)): ?>
        <li><div class="roomrows">
        <div class="four columns retsattributes"><b>Lake/Waterfront</b></div>
        <div class="eight columns retsvalues"><?php echo $prop->lakewaterfront;?></div>
            </div></li>
		<?php endif;?>
        
		<?php if(!empty($prop->latitude)): ?>
        <li><div class="roomrows">
        <div class="four columns retsattributes"><b>Latitude</b></div>
        <div class="eight columns retsvalues"><?php echo $prop->latitude;?></div>
            </div></li>
		<?php endif;?>
        
		<?php if(!empty($prop->laundry)): ?>
        <li><div class="roomrows">
        <div class="four columns retsattributes"><b>laundry</b></div>
        <div class="eight columns retsvalues"><?php echo $prop->laundry;?></div>
            </div></li>
		<?php endif;?>
        
		<?php if(!empty($prop->legaldesc)): ?>
        <li><div class="roomrows">
        <div class="four columns retsattributes"><b>Legal Description</b></div>
        <div class="eight columns retsvalues"><?php echo $prop->legaldesc;?></div>
            </div></li>
		<?php endif;?>
        
		<?php if(!empty($prop->lenderowned)): ?>
        <li><div class="roomrows">
        <div class="four columns retsattributes"><b>Lender Owned</b></div>
        <div class="eight columns retsvalues"><?php echo $prop->lenderowned;?></div>
            </div></li>
		<?php endif;?>
        
		<?php if(!empty($prop->listprice)): ?>
        <li><div class="roomrows">
        <div class="four columns retsattributes"><b>List Price</b></div>
        <div class="eight columns retsvalues"><?php echo $prop->listprice;?></div>
            </div></li>
		<?php endif;?>
        
		<?php if(!empty($prop->listpricehigh)): ?>
        <li><div class="roomrows">
        <div class="four columns retsattributes"><b>High Range Price</b></div>
        <div class="eight columns retsvalues"><?php echo $prop->listpricehigh;?></div>
            </div></li>
		<?php endif;?>
        
		<?php if(!empty($prop->listpricelow)): ?>
        <li><div class="roomrows">
        <div class="four columns retsattributes"><b>Low Range Price</b></div>
        <div class="eight columns retsvalues"><?php echo $prop->listpricelow;?></div>
            </div></li>
		<?php endif;?>

		<?php if(!empty($prop->longitude)): ?>
        <li><div class="roomrows">
        <div class="four columns retsattributes"><b>Longitude</b></div>
        <div class="eight columns retsvalues"><?php echo $prop->longitude;?></div>
            </div></li>
		<?php endif;?>

		<?php if(!empty($prop->mapletter)): ?>
        <li><div class="roomrows">
        <div class="four columns retsattributes"><b>Map Coordinate</b></div>
        <div class="eight columns retsvalues"><?php echo $prop->mapletter;?></div>
            </div></li>
		<?php endif;?>
        
		<?php if(!empty($prop->mappage)): ?>
        <li><div class="roomrows">
        <div class="four columns retsattributes"><b>Map Page</b></div>
        <div class="eight columns retsvalues"><?php echo $prop->mappage;?></div>
            </div></li>
		<?php endif;?>
        
		<?php if(!empty($prop->matrix_mapcoord)): ?>
        <li><div class="roomrows">
        <div class="four columns retsattributes"><b>Enter Map Page and Coordinates</b></div>
        <div class="eight columns retsvalues"><?php echo $prop->matrix_mapcoord;?></div>
            </div></li>
		<?php endif;?>
        
		<?php if(!empty($prop->minleasemonths)): ?>
        <li><div class="roomrows">
        <div class="four columns retsattributes"><b>minLeaseMonths</b></div>
        <div class="eight columns retsvalues"><?php echo $prop->minleasemonths;?></div>
            </div></li>
		<?php endif;?>
        
		<?php if(!empty($prop->mlnumber)): ?>
        <li><div class="roomrows">
        <div class="four columns retsattributes"><b>List Number</b></div>
        <div class="eight columns retsvalues"><?php echo $prop->mlnumber;?></div>
            </div></li>
		<?php endif;?>
        
		<?php if(!empty($prop->mlsid)): ?>
        <li><div class="roomrows">
        <div class="four columns retsattributes"><b>MLS ID</b></div>
        <div class="eight columns retsvalues"><?php echo $prop->mlsid;?></div>
            </div></li>
		<?php endif;?>
        
		<?php if(!empty($prop->modelhours)): ?>
        <li><div class="roomrows">
        <div class="four columns retsattributes"><b>Hours Model Open</b></div>
        <div class="eight columns retsvalues"><?php echo $prop->modelhours;?></div>
            </div></li>
		<?php endif;?>
        
		<?php if(!empty($prop->modellocation)): ?>
        <li><div class="roomrows">
        <div class="four columns retsattributes"><b>Model Location</b></div>
        <div class="eight columns retsvalues"><?php echo $prop->modellocation;?></div>
            </div></li>
		<?php endif;?>
        
		<?php if(!empty($prop->modelphone)): ?>
        <li><div class="roomrows">
        <div class="four columns retsattributes"><b>Model Phone</b></div>
        <div class="eight columns retsvalues"><?php echo $prop->modelphone;?></div>
            </div></li>
		<?php endif;?>

		<?php if(!empty($prop->otherdepositsfees)): ?>
        <li><div class="roomrows">
        <div class="four columns retsattributes"><b>otherDepositsFees</b></div>
        <div class="eight columns retsvalues"><?php echo $prop->otherdepositsfees;?></div>
            </div></li>
		<?php endif;?>
        
		<?php if(!empty($prop->outbuildings)): ?>
        <li><div class="roomrows">
        <div class="four columns retsattributes"><b>Out Buildings</b></div>
        <div class="eight columns retsvalues"><?php echo $prop->outbuildings;?></div>
            </div></li>
		<?php endif;?>

		<?php if(!empty($prop->pastureacres)): ?>
        <li><div class="roomrows">
        <div class="four columns retsattributes"><b>Pasture Acres</b></div>
        <div class="eight columns retsvalues"><?php echo $prop->pastureacres;?></div>
            </div></li>
		<?php endif;?>
        
		<?php if(!empty($prop->payassociationfee)): ?>
        <li><div class="roomrows">
        <div class="four columns retsattributes"><b>payAssociationFee</b></div>
        <div class="eight columns retsvalues"><?php echo $prop->payassociationfee;?></div>
            </div></li>
		<?php endif;?>
        
		<?php if(!empty($prop->paycable)): ?>
        <li><div class="roomrows">
        <div class="four columns retsattributes"><b>payCable</b></div>
        <div class="eight columns retsvalues"><?php echo $prop->paycable;?></div>
            </div></li>
		<?php endif;?>
        
		<?php if(!empty($prop->payelectric)): ?>
        <li><div class="roomrows">
        <div class="four columns retsattributes"><b>payElectric</b></div>
        <div class="eight columns retsvalues"><?php echo $prop->payelectric;?></div>
            </div></li>
		<?php endif;?>
        
		<?php if(!empty($prop->paygas)): ?>
        <li><div class="roomrows">
        <div class="four columns retsattributes"><b>payGas</b></div>
        <div class="eight columns retsvalues"><?php echo $prop->paygas;?></div>
            </div></li>
		<?php endif;?>
        
		<?php if(!empty($prop->payheat)): ?>
        <li><div class="roomrows">
        <div class="four columns retsattributes"><b>payHeat</b></div>
        <div class="eight columns retsvalues"><?php echo $prop->payheat;?></div>
            </div></li>
		<?php endif;?>
        
		<?php if(!empty($prop->paytrash)): ?>
        <li><div class="roomrows">
        <div class="four columns retsattributes"><b>payTrash</b></div>
        <div class="eight columns retsvalues"><?php echo $prop->paytrash;?></div>
            </div></li>
		<?php endif;?>
        
		<?php if(!empty($prop->paywater)): ?>
        <li><div class="roomrows">
        <div class="four columns retsattributes"><b>payWater</b></div>
        <div class="eight columns retsvalues"><?php echo $prop->paywater;?></div>
            </div></li>
		<?php endif;?>
        
		<?php if(!empty($prop->photoremarks)): ?>
        <li><div class="roomrows">
        <div class="four columns retsattributes"><b>Photo Remarks</b></div>
        <div class="eight columns retsvalues"><?php echo $prop->photoremarks;?></div>
            </div></li>
		<?php endif;?>
        
		<?php if(!empty($prop->pid)): ?>
        <li><div class="roomrows">
        <div class="four columns retsattributes"><b>Property Identification Number</b></div>
        <div class="eight columns retsvalues"><?php echo $prop->pid;?></div>
            </div></li>
		<?php endif;?>


		<?php if(!empty($prop->potentialshortsale)): ?>
        <li><div class="roomrows">
        <div class="four columns retsattributes"><b>Potential Short Sale</b></div>
        <div class="eight columns retsvalues"><?php echo $prop->potentialshortsale;?></div>
            </div></li>
		<?php endif;?>
        
		<?php if(!empty($prop->prepaidrent)): ?>
        <li><div class="roomrows">
        <div class="four columns retsattributes"><b>prepaidRent</b></div>
        <div class="eight columns retsvalues"><?php echo $prop->prepaidrent;?></div>
            </div></li>
		<?php endif;?>
        
		<?php if(!empty($prop->publicremarks)): ?>
        <li><div class="roomrows">
        <div class="four columns"><b>Public Remarks</b></div>
        <div class="eight columns"><?php echo $prop->publicremarks;?></div>
            </div></li>
		<?php endif;?>
        
		<?php if(!empty($prop->restrictions)): ?>
        <li><div class="roomrows">
        <div class="four columns retsattributes"><b>Restrictions/Covenants</b></div>
        <div class="eight columns retsvalues"><?php echo $prop->restrictions;?></div>
            </div></li>
		<?php endif;?>
        
		<?php if(!empty($prop->roadbetweenwaterfrontandhomeyn)): ?>
        <li><div class="roomrows">
        <div class="four columns retsattributes"><b>RoadBetweenWaterfrontAnd</b></div>
        <div class="eight columns retsvalues"><?php echo $prop->roadbetweenwaterfrontandhomeyn;?></div>
            </div></li>
		<?php endif;?>

		<?php if(!empty($prop->schooldistrictnumber)): ?>
        <li><div class="roomrows">
        <div class="four columns retsattributes"><b>School District</b></div>
        <div class="eight columns retsvalues"><?php echo $prop->schooldistrictnumber;?></div>
            </div></li>
		<?php endif;?>
        
		<?php if(!empty($prop->schooldistrictphone)): ?>
        <li><div class="roomrows">
        <div class="four columns retsattributes"><b>School District Phone</b></div>
        <div class="eight columns retsvalues"><?php echo $prop->schooldistrictphone;?></div>
            </div></li>
		<?php endif;?>
        
		<?php if(!empty($prop->secondaryfinance)): ?>
        <li><div class="roomrows">
        <div class="four columns retsattributes"><b>Secondary Financing</b></div>
        <div class="eight columns retsvalues"><?php echo $prop->secondaryfinance;?></div>
            </div></li>
		<?php endif;?>
        
		<?php if(!empty($prop->secondunit)): ?>
        <li><div class="roomrows">
        <div class="four columns retsattributes"><b>Second Unit</b></div>
        <div class="eight columns retsvalues"><?php echo $prop->secondunit;?></div>
            </div></li>
		<?php endif;?>
        
		<?php if(!empty($prop->securitydeposit)): ?>
        <li><div class="roomrows">
        <div class="four columns retsattributes"><b>securityDeposit</b></div>
        <div class="eight columns retsvalues"><?php echo $prop->securitydeposit;?></div>
            </div></li>
		<?php endif;?>

		<?php if(!empty($prop->sharedrooms)): ?>
        <li><div class="roomrows">
        <div class="four columns retsattributes"><b>Shared Rooms</b></div>
        <div class="eight columns retsvalues"><?php echo $prop->sharedrooms;?></div>
            </div></li>
		<?php endif;?>
        
		<?php if(!empty($prop->smokingpermitted)): ?>
        <li><div class="roomrows">
        <div class="four columns retsattributes"><b>smokingPermitted</b></div>
        <div class="eight columns retsvalues"><?php echo $prop->smokingpermitted;?></div>
            </div></li>
		<?php endif;?>
        
		<?php if(!empty($prop->soiltype)): ?>
        <li><div class="roomrows">
        <div class="four columns retsattributes"><b>Soil Type</b></div>
        <div class="eight columns retsvalues"><?php echo $prop->soiltype;?></div>
            </div></li>
		<?php endif;?>
        
		<?php if(!empty($prop->specialsearch)): ?>
        <li><div class="roomrows">
        <div class="four columns retsattributes"><b>Special Search</b></div>
        <div class="eight columns retsvalues"><?php echo $prop->specialsearch;?></div>
            </div></li>
		<?php endif;?>

		<?php if(!empty($prop->style)): ?>
        <li><div class="roomrows">
        <div class="four columns retsattributes"><b>Style</b></div>
        <div class="eight columns retsvalues"><?php echo $prop->style;?></div>
            </div></li>
		<?php endif;?>
        
		<?php if(!empty($prop->subdivision)): ?>
        <li><div class="roomrows">
        <div class="four columns retsattributes"><b>Complex/Development/Subd</b></div>
        <div class="eight columns retsvalues"><?php echo $prop->subdivision;?></div>
            </div></li>
		<?php endif;?>
        
		<?php if(!empty($prop->supplement_cnt)): ?>
        <li><div class="roomrows">
        <div class="four columns retsattributes"><b>Supplement_CNT</b></div>
        <div class="eight columns retsvalues"><?php echo $prop->supplement_cnt;?></div>
		</div></li>
		<?php endif;?>
        
		<?php if(!empty($prop->supplement_mod_dt)): ?>
        <li><div class="roomrows">
        <div class="four columns retsattributes"><b>Supplement_Mod_DT</b></div>
        <div class="eight columns retsvalues"><?php echo $prop->supplement_mod_dt;?></div>
            </div></li>
		<?php endif;?>
        
		<?php if(!empty($prop->table)): ?>
        <li><div class="roomrows">
        <div class="four columns retsattributes"><b>table</b></div>
        <div class="eight columns retsvalues"><?php echo $prop->table;?></div>
            </div></li>
		<?php endif;?>
        
		<?php if(!empty($prop->taxes)): ?>
        <li><div class="roomrows">
        <div class="four columns retsattributes"><b>Tax Amount</b></div>
        <div class="eight columns retsvalues"><?php echo $prop->taxes;?></div>
            </div></li>
		<?php endif;?>
        
		<?php if(!empty($prop->taxwithassessments)): ?>
        <li><div class="roomrows">
        <div class="four columns retsattributes"><b>Tax With Assessments</b></div>
        <div class="eight columns retsvalues"><?php echo $prop->taxwithassessments;?></div>
            </div></li>
		<?php endif;?>
        
		<?php if(!empty($prop->taxyear)): ?>
        <li><div class="roomrows">
        <div class="four columns retsattributes"><b>Tax Year</b></div>
        <div class="eight columns retsvalues"><?php echo $prop->taxyear;?></div>
            </div></li>
		<?php endif;?>
        
		<?php if(!empty($prop->thcharacteristics)): ?>
        <li><div class="roomrows">
        <div class="four columns retsattributes"><b>TH Characteristics</b></div>
        <div class="eight columns retsvalues"><?php echo $prop->thcharacteristics;?></div>
            </div></li>
		<?php endif;?>
        
		<?php if(!empty($prop->tillableacres)): ?>
        <li><div class="roomrows">
        <div class="four columns retsattributes"><b>Tillable Acres</b></div>
        <div class="eight columns retsvalues"><?php echo $prop->tillableacres;?></div>
            </div></li>
		<?php endif;?>
        
		<?php if(!empty($prop->topography)): ?>
        <li><div class="roomrows">
        <div class="four columns retsattributes"><b>Topography</b></div>
        <div class="eight columns retsvalues"><?php echo $prop->topography;?></div>
            </div></li>
		<?php endif;?>
        
		<?php if(!empty($prop->totalunitsavailable)): ?>
        <li><div class="roomrows">
        <div class="four columns retsattributes"><b>TotalUnitsAvail</b></div>
        <div class="eight columns retsvalues"><?php echo $prop->totalunitsavailable;?></div>
            </div></li>
		<?php endif;?>
        
		<?php if(!empty($prop->transactiontype)): ?>
        <li><div class="roomrows">
        	<div class="four columns retsattributes"><b>transactionType</b></div>
        	<div class="eight columns retsvalues"><?php echo $prop->transactiontype;?></div>
            </div></li>
		<?php endif;?>
        
		<?php if(!empty($prop->unitnumber)): ?>
        <li><div class="roomrows">
        <div class="four columns retsattributes"><b>Unit Number</b></div>
        <div class="eight columns retsvalues"><?php echo $prop->unitnumber;?></div>
            </div></li>
		<?php endif;?>
        
		<?php if(!empty($prop->updatedate)): ?>
        <li><div class="roomrows">
        <div class="four columns retsattributes"><b>UpdateDate</b></div>
        <div class="eight columns retsvalues"><?php echo $prop->updatedate;?></div>
            </div></li>
		<?php endif;?>
        
		<?php if(!empty($prop->variableratecomp)): ?>
        <li><div class="roomrows">
        <div class="four columns retsattributes"><b>Variable Rate Compensati</b></div>
        <div class="eight columns retsvalues"><?php echo $prop->variableratecomp;?></div>
            </div></li>
		<?php endif;?>
        
		<?php if(!empty($prop->virtualtour_cnt)): ?>
        <li><div class="roomrows">
        <div class="four columns retsattributes"><b>VirtualTour_CNT</b></div>
        <div class="eight columns retsvalues"><?php echo $prop->virtualtour_cnt;?></div>
            </div></li>
		<?php endif;?>
        
		<?php if(!empty($prop->virtualtour_url1)): ?>
        <li><div class="roomrows">
        <div class="four columns retsattributes"><b>VirtualTour_URL1</b></div>
        <div class="eight columns retsvalues"><?php echo $prop->virtualtour_url1;?></div>
            </div></li>
		<?php endif;?>
        
		<?php if(!empty($prop->virtualtour_url2)): ?>
        <li><div class="roomrows">
        <div class="four columns retsattributes"><b>VirtualTour_URL2</b></div>
        <div class="eight columns retsvalues"><?php echo $prop->virtualtour_url2;?></div>
            </div></li>
		<?php endif;?>

		<?php if(!empty($prop->waterfrontpresent)): ?>
        <li><div class="roomrows">
        <div class="four columns retsattributes"><b>Lake/Waterfront - Y:N</b></div>
        <div class="eight columns retsvalues"><?php echo $prop->waterfrontpresent;?></div>
            </div></li>
		<?php endif;?>
        
		<?php if(!empty($prop->waterfrontslope)): ?>
        <li><div class="roomrows">
        <div class="four columns retsattributes"><b>WaterfrontSlope</b></div>
        <div class="eight columns retsvalues"><?php echo $prop->waterfrontslope;?></div>
            </div></li>
		<?php endif;?>
        
		<?php if(!empty($prop->waterfrontview)): ?>
        <li><div class="roomrows">
        <div class="four columns retsattributes"><b>WaterFrontView</b></div>
        <div class="eight columns retsvalues"><?php echo $prop->waterfrontview;?></div>
            </div></li>
		<?php endif;?>
        
		<?php if(!empty($prop->woodedacres)): ?>
        <li><div class="roomrows">
        <div class="four columns retsattributes"><b>Wooded Acres</b></div>
        <div class="eight columns retsvalues"><?php echo $prop->woodedacres;?></div>
            </div></li>
		<?php endif;?>

		<?php if(!empty($prop->zoning)): ?>
        <li><div class="roomrows">
        <div class="four columns retsattributes"><b>Zoning</b></div>
        <div class="eight columns retsvalues"><?php echo $prop->zoning;?></div>
            </div></li>
		<?php endif;?>	
        
		<?php if(!empty($prop->waterfrontelevation)): ?>
        <li><div class="roomrows">
        <div class="four columns retsattributes"><b>WaterfrontElevation</b></div>
        <div class="eight columns retsvalues"><?php echo $prop->waterfrontelevation;?></div>
            </div></li>
		<?php endif;?>
        
		<?php if(!empty($prop->waterfrontfeet)): ?>
        <li><div class="roomrows">
        <div class="four columns retsattributes"><b>WaterfrontFeet</b></div>
        <div class="eight columns retsvalues"><?php echo $prop->waterfrontfeet;?></div>
            </div></li>
		<?php endif;?>
        
		<?php if(!empty($prop->waterfrontname)): ?>
        <li><div class="roomrows">
        <div class="four columns retsattributes"><b>WaterFrontName</b></div>
        <div class="eight columns retsvalues"><?php echo $prop->waterfrontname;?></div>
            </div></li>
		<?php endif;?>
        
		<?php if(!empty($prop->waterfrontnum)): ?>
        <li><div class="roomrows">
        <div class="four columns retsattributes"><b>WaterfrontNum</b></div>
        <div class="eight columns retsvalues"><?php echo $prop->waterfrontnum;?></div>
            </div></li></ul>
		
		<?php endif;?>					
			
			</div> <!-- t-listingbody -->
</div> <!-- t-t-listingwrapper -->

			<div class="t-listingfoot"></div>
		
<?php
		$count++;
	}			
}
//// End Of Property Loop

//pager links here 
include("fl-pager.php");
 
?>
</div><!-- id="featured" -->

