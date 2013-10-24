<?php
echo "Pages ";
for($i=0; ($i * $rows) < $totalFeaturedListings; $i++){
	if($startRow == $i * $rows){
		echo " <strong>" . ($i +1) . "</strong>  ";
	}else{
		echo " <a href=\"#\" onClick=\"showPage(" . ($i * $rows) . "); return false;\">" . ($i +1) . "</a>  ";
	}
}
echo "<br/><strong>Showing " . ($startRow + 1) ." to " . $endRow . " of " . $totalFeaturedListings . " listings</strong><br/><br/>";
?>