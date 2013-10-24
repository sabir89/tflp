function putFeaturedListings(agentIDs, listingType, resultTemplate, detailsTemplate, divID){
	//alert("Inside putFeaturedListings");
	var solrURL = "http://mightytitan.info:8983/solr/db/select"; 
	var solrQuery = "";	
		
	var queryAct = ' AND (status:Active OR status:Pending) AND distributetointernet:1';
	var querySold = ' AND (status:"Comp Sold" OR status:Sold) AND distributetointernet:1';
	var queryAll = ' AND (status:Active OR status:Pending OR status:"Comp Sold" OR status:Sold) AND distributetointernet:1';

	//construct query for featured listings
	var agentNRDS = agentIDs.split(",");
	var queryAgents = "(";
	for(i = 0; i < agentNRDS.length; i++){
		queryAgents += "listagent1nrdsmemberid:" + agentNRDS[i] + " OR listagent2nrdsmemberid:" + agentNRDS[i];
		if((i +1) < agentNRDS.length){
			queryAgents += " OR ";
		}
	}
	queryAgents += ")";
	
	if("all" == listingType.toLowerCase()){
		solrQuery = queryAgents + queryAll;
	}else if("sold" == listingType.toLowerCase()){
		solrQuery = queryAgents + querySold;
	}else{ //default is active
		solrQuery = queryAgents + queryAct;
	}
	//alert("solrQuery=" + solrQuery);
	
	//fire query now
	jQuery.ajax({
	  'url': solrURL,
	  'data': {'wt':'json', 'q':solrQuery},	  
	  'success': function(data) { 
	  		//alert("Agent listings = " + data.response.numFound);
	  		parseJSON(data, divID);	
	  },
	  'dataType': 'jsonp',
	  'jsonp': 'json.wrf',		  	  	  	  
	});		
		
}

//json parsing using jquery, though there is hardly any difference - manishh
function parseJSON(data, divID){
	var jsonObj = jQuery.parseJSON(JSON.stringify(data));
	var str = "<strong>Total Featured Listings = " +  jsonObj.response.numFound + "</strong><br/><br/>";
		//loop through the listings
	for(var doc in jsonObj.response.docs){
		var prop = jsonObj.response.docs[doc];
		var status = prop.status;
		
		str += "MLS#: " + prop.mlnumber + "<br/>";
		str += "Status: " + status + "<br/>";
		str += "Address: " +  prop.streetnumber + " " + prop.streetname + ", " + prop.city + ", " + prop.stateorprovince + " " + prop.postalcode + "<br/>";					
		str += "Style: " + prop.style + "<br/>";
		str += "Beds: " +  prop.bedrooms + " Baths: " + prop.bathstotal + "<br/>";
			
		str += "Agent: " + prop.listagent1name + "<br/>";
		if("Active" == status || "Pending" == status){
			str += "<span style=\"color:#007f00;\">List Price: $" + prop.listprice.formatMoney(0, '.', ',') + "</span><br/>";
		}else{
			str += "<span style=\"color:#F00000;\">Sold Price: $" + prop.salescloseprice.formatMoney(0, '.', ',') + "</span><br/>";	
		}				
		str += "<br/>"
	} 
	
	jQuery("#"+divID).html(str); 
}

Number.prototype.formatMoney = function(c, d, t){
	var n = this, c = isNaN(c = Math.abs(c)) ? 2 : c, d = d == undefined ? "," : d, t = t == undefined ? "." : t, s = n < 0 ? "-" : "", i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", j = (j = i.length) > 3 ? j % 3 : 0;
	return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
}

/*
function parseJSON_OLD(data, divID){
	//alert("Agent listings = " + data.response.numFound);
	var str = "<strong>Total Featured Listings = " +  data.response.numFound + "</strong><br/><br/>";
		//loop through the listings
	for(var doc in data.response.docs){
		var prop = data.response.docs[doc];
		var status = prop.status;
		
		str += "MLS#: " + prop.mlnumber + "<br/>";
		str += "Status: " + status + "<br/>";
		str += "Address: " +  prop.streetnumber + " " + prop.streetname + ", " + prop.city + ", " + prop.stateorprovince + " " + prop.postalcode + "<br/>";					
		str += "Style: " + prop.style + "<br/>";
		str += "Beds: " +  prop.bedrooms + " Baths: " + prop.bathstotal + "<br/>";
			
		str += "Agent: " + prop.listagent1name + "<br/>";
		if("Active" == status || "Pending" == status){
			str += "<span style=\"color:#007f00;\">List Price: $" + prop.listprice.formatMoney(0, '.', ',') + "</span><br/>";
		}else{
			str += "<span style=\"color:#F00000;\">Sold Price: $" + prop.salescloseprice.formatMoney(0, '.', ',') + "</span><br/>";	
		}				
		str += "<br/>"
	} 
	
	document.getElementById(divID).innerHTML = str; 
}
*/
