<?php

/*
Template Name: Alternitive Results Layout One
*/

// This is a JSON feed template for the retools plugin
?>
<script type="text/javascript">
function open_window(URL,wt,ht) {
        msgwin=window.open(URL,"NewWindow","toolbar=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,width="+ wt +",height="+ ht +",top=200,left=250")
}
function swapimgb(idno,maxno,listno) {
	var imgids =new Array();
	maxno=maxno+1;
	var idname="";
	for(i=1; i<maxno; i++){imgids[i]="a" + listno + i;}
	var maxlen=imgids.length;
	for(i=1; i<maxlen; i++){
		var idname = imgids[i];
		if (idno==idname) document.getElementById(idno).style.display="";
		else document.getElementById(idname).style.display="none";
	}
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

<div id="featured">
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
if($totalFeaturedListings < $endRow){
	$endRow = $totalFeaturedListings;
}

//pager links here 
include(WP_PLUGIN_DIR . "/tflp/templates/fl-pager.php");


//// Start Of Property Loop
foreach($properties as $prop) {
	//echo "here mlnumber = " .  $prop->mlnumber;

	
	if ($count < $totalFeaturedListings ){
		$detailsuri =  add_query_arg( array( 
			'mlnumber' => $prop->mlnumber
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
		// here we will display that as the listing status (MLS Rule)
		if ($prop->sellingagent == "true"){
			$status= "Selling Agent";
		} else {
			 $status=$prop->status;
		}
		// show max 4 photos here
		if ($prop->aupExists == "true"){ // work with agent uploaded photos
			if (is_string($prop->aupPhotoURLArray)) { // this is temporary remove after publish
				eval('$prop->aupPhotoURLArray = array('.$prop->aupPhotoURLArray.');');
			}
			if ($prop->aupPhotoCount > 4){ 
				$photoshow = 4;
			}else{
				 $photoshow = $prop->aupPhotoCount;
			}
		} else { // work with mls photos
			if ($prop->photocount > 4){ 
				$photoshow = 4;
			}else{
				 $photoshow = $prop->photocount;
			}
		}	
?>
		<div class="listingwrapper">
			<div class="listinghead">
				<div class="alright">
				<span class="alprice">$<?php echo $price; ?></span><br />
				<span class="alstatus"><?php echo $status; ?></span>
				</div> <!-- alright -->
				<div class="alleft">
				<span class="aladd1"><?php echo $address; ?></span><br />
				<span class="aladd2"><?php echo $prop->city . ", " . $prop->stateorprovince . " ".$prop->postalcode;?> </span>
				</div> <!-- alleft -->
			</div> <!-- listing head -->
			<div class="listingbody">
				<div class="lbleft">
				<?php
				// mls images are | size 2 640x480 | size 1 96x68 | size 0 256x192
				// agent uploaded photos (aup) com in 4 sizes the 640 size is the default
				//   if you want a different size you have to replace the size '.x640.' in the filename with
				//   example str_replace( '.x640.', '.x256.',  ) 
				//   str_replace('.x640.', '.x256.', $prop->aupPhotoURLArray[0])	
				//   str_replace('.x640.', '.x96.', $prop->aupPhotoURLArray[0])	
	
				//print_r($prop->aupPhotoURLArray);
				//echo "<br />";
				for ($i = 1; $i <= $photoshow; $i++){ //main photo
					/*
					if ($prop->aupExists == "true") {
						//echo "<h1>aup esists ".$prop->aupExists."</h1>";
						$imgurl= $prop->aupPhotoBasePath . str_replace('.x640.', '.x256.',$prop->aupPhotoURLArray[$i-1]); //256
					} else {	
						//echo "<h1>aup esists ".$prop->aupExists."</h1>";
						$imgurl="http://mightyagent2.com/tc/imsv.img?mlsnum=".$prop->mlnumber."&idx=${i}&size=0"; //256
					}
					 */
					$imgurl="http://mightyagent2.com/tc/imsv.img?mlsnum=".$prop->mlnumber."&idx=${i}&size=0"; 
					$istyle = "";
					if ($i > 1) $istyle = 'style="display: none;"';	
					echo "<img id=\"a${count}${i}\" $istyle src=\"$imgurl\" width=\"256\" alt=\"\" />\n";
				}
				for ($i = 1; $i <= $photoshow; $i++){ // thumbnail 
				?>	
					<div class="althumbnail">
					<?php 
					$imgurl="http://mightyagent2.com/tc/imsv.img?mlsnum=".$prop->mlnumber."&idx=${i}&size=0"; 
					
					/*
					if ($prop->aupExists == "true") {
						//echo "<h1>aup esists ".$prop->aupExists."</h1>";
						$imgurl= $prop->aupPhotoBasePath . str_replace('.x640.', '.x256.',$prop->aupPhotoURLArray[$i-1]); //256
					} else {	
						//echo "<h1>aup esists ".$prop->aupExists."</h1>";
						$imgurl="http://mightyagent2.com/tc/imsv.img?mlsnum=".$prop->mlnumber."&idx=${i}&size=0"; //256
					}
					 */
					?>
					<a href="javascript:" onclick="swapimgb('a<?php echo "${count}${i}"?>',<?php echo $photoshow;?>,<?php echo $count;?>); return false;">
					<img src="<?php echo $imgurl; ?>" width="54" alt="" />
					</a>
					</div>
				<?php } ?>
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
				<a class="viewbut" href="<?php echo $detailsuri;?>">View Details</a>
				</div> <!-- lbleft -->
				<div class="lbright">
				<?php if($prop->open == "true"){// Open House ?>
					<div class="alopen">
						Open House: <?php echo $prop->opendate.": ".$prop->openstarttime." to ".$prop->openendtime ?>
					</div>
				<?php }	?>
				<span class="fldesclab">Description:</span><br />
				<span class="fldesc"><?php echo $prop->publicremarks;?></span>
				<table id="featured-listings">
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
			</div> <!-- listingbody -->
			<div class="listingfoot"></div>
		</div>
<?php
		$count++;
	}			
}
//// End Of Property Loop

//pager links here 
include("fl-pager.php");
 
?>
</div><!-- id="featured" -->
