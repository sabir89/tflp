<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<head><title>Mortgage Calculator</title>
<script src="../js/common.js" type="text/javascript"></script>

<script language="JavaScript">

function putPrice(){
	var price = Number(getParam("listprice"));
	var tax = Number(getParam("tax"));

	var lp = document.getElementById("listprice");
	var dp = document.getElementById("downpayment");
	var ta = document.getElementById("AT");
	var loan = document.getElementById("LA");

		//see case 40037
	lp.value = price;
	dp.value = price/10;
	ta.value = tax;
	loan.value = (price - (price/10))

	dosum();
	//alert("price = " + price);
}

function floor(number)
{
  return Math.floor(number*Math.pow(10,2) + 0.5)/Math.pow(10,2);
}

function dosum()
{
	// *** Added by manish
	//calculate loan amount based on price and down payment

  var lp = Number(document.getElementById("listprice").value);
  var dp = Number(document.getElementById("downpayment").value);
  var loan = document.getElementById("LA");
  loan.value = lp - dp
	//end manish, I assume that the calculation below are correct

  var mi = document.temps.IR.value / 1200;
  var base = 1;
  var mbase = 1 + mi;
  for (i=0; i<document.temps.YR.value * 12; i++)
  {
    base = base * mbase
  }
  document.temps.PI.value = floor(document.temps.LA.value * mi / ( 1 - (1/base)))
  document.temps.MT.value = floor(document.temps.AT.value / 12)
  document.temps.MI.value = floor(document.temps.AI.value / 12)
  var dasum = document.temps.LA.value * mi / ( 1 - (1/base)) +
	document.temps.AT.value / 12 +
	document.temps.AI.value / 12;
  document.temps.MP.value = floor(dasum);
}
</script><link rel="STYLESHEET" type="text/css" href="../css/default.css">
</head>
<body onload="putPrice();">
 <div id="containercal">
  <form name="temps">
   <fieldset>
    <legend class="borderl">Monthly Payment Calculator</legend>
    <br>
    <div id="frmrow212">


     <div calss="formcol1"><span class="col1cal">List Price:&nbsp;</span><span class="formcol2"><input type="TEXT" name="listprice" id="listprice" onChange="dosum()" size="6" value="<?php echo $_GET["value"] ;?>" class="inptcal"></span></div>

     <div calss="formcol1"><span class="col1cal">Down Payment:&nbsp;</span><span class="formcol2"><input type="TEXT" name="downpayment" id="downpayment" onChange="dosum()" size="6" value="" class="inptcal"></span></div>

     <div calss="formcol1"><span class="col1cal">Loan Amount:&nbsp;</span><span class="formcol2"><input type="TEXT" name="LA" id="LA" onChange="dosum()" size="6" value="100000" class="inptcal"></span></div>

<div calss="formcol1"><span class="col1cal">Interest:&nbsp;</span><span class="formcol2"><input type="TEXT" name="IR" onChange="dosum()" size="6" value="8.0" class="inptcal"></span></div>

     <div calss="formcol1"><span class="col1cal">Years:&nbsp;</span><span class="formcol2"><input type="TEXT" name="YR" onChange="dosum()" size="6" value="30" class="inptcal"></span></div>

     <div calss="formcol1"><span class="col1cal">Annual Tax:&nbsp;</span><span class="formcol2"><input type="TEXT" name="AT" id="AT" onChange="dosum()" size="6" value="1000" class="inptcal"></span></div>
     <div calss="formcol1"><span class="col1cal">Annual Insur:&nbsp;</span><span class="formcol2"><input type="TEXT" name="AI" onChange="dosum()" size="6" value="900" class="inptcal"></span></div>

     <div class="subbut212"><input type="BUTTON" value="Calculate Now!" onClick="dosum()" class="submt"></div>
    </div>
   </fieldset>
   <br><br>
   <fieldset>
    <legend class="borderl">Results</legend>
    <br>
    <div id="frmrow212">
     <div calss="formcol1"><span class="col1cal">Monthly Prin+Int:&nbsp;</span><span class="formcol2"><input type="TEXT" name="PI" size="10" class="inptcal"></span></div>
     <div calss="formcol1"><span class="col1cal">Monthly Tax:&nbsp;</span><span class="formcol2"><input type="TEXT" name="MT" size="10" class="inptcal"></span></div>

     <div calss="formcol1"><span class="col1cal">Monthly Ins:&nbsp;</span><span class="formcol2"><input type="TEXT" name="MI" size="10" class="inptcal"></span></div>
     <div calss="formcol1"><span class="col1cal">Total Payment:&nbsp;</span><span class="formcol2"><input type="TEXT" name="MP" size="10" class="inptcal"></span></div>
    </div>
   </fieldset>
  </form>
 </div>
</body>
