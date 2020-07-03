<?php
include("admin/database.class.php");
$database	= new Database(); // Create a new instance
$showallevent		= $database->show_all_event(0,3);
$numallevent		= count($showallevent);	
/*$dataevent			= $database->show_all_event($eu,$limit);
$numevent			= count($dataevent);*/
$showallpress		= $database->show_all_press(0,3); //limit to two latest press releases
$numallpress		= count($showallpress);	
date_default_timezone_set('Asia/Dubai'); 
$currentdate = date('d/m/Y'); 
//print $currentdate; exit();
//print "SELECT * FROM tbl_online_booking WHERE date = '$currentdate'"; exit();
//$onlinebookQry = $database->mysqlQuery("SELECT * FROM tbl_online_booking WHERE date = '$currentdate'");
//$count = $database->mysqlNumRows($onlinebookQry);
//while($onlineCount = $database->mysqlFetchArray($onlinebookQry)) {
//$onlineCount = $database->mysqlFetchArray($onlinebookQry);
//print 'count '.$onlineCount['count'];
//}
//print $count;


$today = date( 'd-m-Y', strtotime( 'today') );
$todayString = strtotime( 'today');

$tuesday = date( 'd-m-Y', strtotime( 'tuesday this week' ) );
$tuesdayString = strtotime( 'tuesday this week' );
$bookQryTuesday = $database->mysqlQuery("SELECT * FROM tbl_online_booking WHERE date = '$tuesday'");
$resultTuesday = $database->mysqlFetchArray($bookQryTuesday);
$onlineCountTuesday = $resultTuesday['count'];


$thursday = date( 'd-m-Y', strtotime( 'thursday this week' ) );
$thursdayString = strtotime( 'thursday this week' );
$bookQryThursday = $database->mysqlQuery("SELECT * FROM tbl_online_booking WHERE date = '$thursday'");
$resultThursday = $database->mysqlFetchArray($bookQryThursday);
$onlineCountThursday = $resultThursday['count'];

$sunday = date( 'd-m-Y', strtotime( 'sunday this week' ) );
$sundayString = strtotime( 'sunday this week' );
$bookQrySunday = $database->mysqlQuery("SELECT * FROM tbl_online_booking WHERE date = '$sunday'");
$resultSunday = $database->mysqlFetchArray($bookQrySunday);
$onlineCountSunday = $resultSunday['count'];
/*
print "Tues-> ".$onlineCountTuesday;
print "<br>Thus-> ".$onlineCountThursday;
print "<br>Sund-> ".$onlineCountSunday;

exit();
*/

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>

<meta name="keywords" content=""  />

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<link rel="stylesheet" type="text/css" href="style.css"/>
<link rel="stylesheet" href="css/datepicker.css">

<link href="css/joined.css" rel="stylesheet" type="text/css">



<link rel="stylesheet" href="css/flexslider.css" type="text/css" media="screen" />

<!--<script src="js/jquery-main.js"></script>
<script src="js/datepicker.js"></script>-->

<script src="js/joined.js" type="text/javascript"></script>


<script type="text/javascript" src="js/script.js"></script>

<!--<script type='text/javascript' src='js/jQuery.js'></script>-->


<script type='text/javascript' src='js/Slideshow.js' charset='utf-8'></script>

<!--[if lt IE 6]><link rel="stylesheet" href="css/ie6.css" type="text/css" media="screen"><![endif]-->


<link rel="stylesheet" type="text/css" href="highslide/highslide.css" />

<script type="text/javascript" src="highslide/highslide-with-html.js"></script>



<script type="text/javascript">

//<![CDATA[

hs.registerOverlay({

	html: '<div class="closebutton" onclick="return hs.close(this)" title="Close"></div>',

	position: 'top right',

	fade: 2 // fading the semi-transparent overlay looks bad in IE

});





hs.graphicsDir = 'highslide/graphics/';
hs.outlineType = 'rounded-white';
hs.wrapperClassName = 'draggable-header';

//]]>

</script>

<title>Consulate General of the Republic of Uzbekistan in Dubai</title>

<!--Emergency Assistance pop up -->

  <!--[if lt IE 9]>
  <script src="//cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv.min.js"></script>
  <![endif]-->

<script type="text/javascript">

$(document).ready(function(){



                $("#contactLink").click(function(){

                    if ($("#contactForm").is(":hidden")){

                        $("#contactForm").slideDown("slow");

                    }

                    else{

                        $("#contactForm").slideUp("slow");

                    }

                });

                

            });

            
/*$('.bookin_btn_text').css('transform', 'rotate(-90deg)');*/

            function closeForm(){

                $("#messageSent").show("slow");

                setTimeout('$("#messageSent").hide();$("#contactForm").slideUp("slow")',100);

           }

function MM_preloadImages() { //v3.0

  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();

    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)

    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}

}

function MM_swapImgRestore() { //v3.0

  var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;

}

function MM_swapImage() { //v3.0

  var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3)

   if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}

}

</script>

<script type="text/javascript">
			function pop(div) {
				document.getElementById(div).style.display = 'block';
			}
			function hide(div) {
				document.getElementById(div).style.display = 'none';
			}
			//To detect escape button
			//document.onkeydown = function(evt) {
//				evt = evt || window.event;
//				if (evt.keyCode == 27) {
//					hide('popDiv');
//				}
//			};
			
		
		</script>
        
     <script type="text/javascript">
			 $(function() {    // Makes sure the code contained doesn't run until
			//    $( "#datepicker" ).datepicker({ minDate: 1, maxDate: "+1D +3D",dateFormat: 'dd-mm-yy'  }); all the DOM elements have loaded
		
			$('#test').change(function(){
				$('#Others').css('display','none');
				$('#' + $(this).val()).css('display','block');
			});
		
			$(".close_pop").bind("click", function() {
			  $("input[type=text], textarea, select").val("");
			});
			 
			 $('.cancel_button').click(function(event) { 
				$('#Others').css('display','none');
				$(".day_booking").css('display','none');
				$(".booking_button_cc").css('display','block');
			 });
			 $('.bookin_btn_text').click(function(event) { 
				$(".booking_button_cc").css('display','block');
				$(".day_booking").css('display','none');
				$('#Others').css('display','none');
			 });
			 
			 
			 $("#select").change(function(){
            	$( "#select option:selected").each(function(){
					//alert();
				if($(this).attr("value")=="<?=$tuesday?>" || $(this).attr("value")=="<?=$thursday?>" || $(this).attr("value")=="<?=$sunday?>"){
					$(".day_booking").css('display','block');
					$(".booking_button_cc").css('display','none');
					$(".ico_cld").val($(this).attr("value"));
					
				}
			    });
        }).change();
			 
		});
		
	
		
     </script>  
      
      
  
</body>

<style>
.rssincl-entry{padding-bottom:17.5px !important;}
#rssincl-box-455196{
	/*max-height:500px;*/
	overflow:inherit !important;
	min-height:500px
	}
.carouselContainer .carousel{margin-right:12px;}
div#rssincl-box-455196 div.rssincl-content div.rssincl-entry{padding-bottom: 12px !important;
padding-top: 12px !important;}
</style>

</head>

<body>

<?php include( 'includes/social.php') ?>

<!-- ie6 notification starts here -->

<!--[if lt IE 7]>  <div style='margin:5px 20px;border: 1px solid #F7941D; background: #FEEFDA; text-align: center; clear: both; height: 75px; position: relative;'>    <div style='position: absolute; right: 3px; top: 3px; font-family: courier new; font-weight: bold;'><a href='#' onclick='javascript:this.parentNode.parentNode.style.display="none"; return false;'><img src='images/ie6nomore-cornerx.jpg' style='border: none;' alt='Close this notice'/></a></div>    <div style='width: 640px; margin: 0 auto; text-align: left; padding: 0; overflow: hidden; color: black;'>      <div style='width: 75px; float: left;'><img src='images/ie6nomore-warning.jpg' alt='Warning!'/></div>      <div style='width: 275px; float: left; font-family: Arial, sans-serif;'>        <div style='font-size: 14px; font-weight: bold; margin-top: 12px;'>You are using an outdated browser</div>        <div style='font-size: 12px; margin-top: 6px; line-height: 12px;'>For a better experience using this site, please upgrade to a modern web browser.</div>      </div>      <div style='width: 75px; float: left;'><a href='http://www.firefox.com' target='_blank'><img src='images/ie6nomore-firefox.jpg' style='border: none;' alt='Get Firefox 3.5'/></a></div>      <div style='width: 75px; float: left;'><a href='http://www.browserforthebetter.com/download.html' target='_blank'><img src='images/ie6nomore-ie8.jpg' style='border: none;' alt='Get Internet Explorer 8'/></a></div>      <div style='width: 73px; float: left;'><a href='http://www.apple.com/safari/download/' target='_blank'><img src='images/ie6nomore-safari.jpg' style='border: none;' alt='Get Safari 4'/></a></div>      <div style='float: left;'><a href='http://www.google.com/chrome' target='_blank'><img src='images/ie6nomore-chrome.jpg' style='border: none;' alt='Get Google Chrome'/></a></div>    </div>  </div>  <![endif]-->

<!-- ie6 notification ends here -->

<div id="container" class="clearfix">

  <div id="header">

  <?php include( 'includes/header-demo.php') ?> 

  </div>

  <!-- header ends here -->

  <div id="Navbar"><?php include( 'includes/navbar.php') ?></div>

  <!-- Navbar ends here -->

  <div id="Banner"><?php include( 'includes/menu_banner_home.php') ?></div>

  <!-- Banner ends here -->

  <div id="main">
  
  
    <!-- IdxColumn_01 ends here -->

    <div class="IdxColumn_02">

      <div style="min-height: 500px;border: solid 1px #e6e4e4;" class="Event_box">
		<h1>Special Rubrics</h1>
        
         <div class="flexslider">
          <ul class="slides">
          
          <li>
		<div class="specila_rubics_new_div">
        	 <h4 >"24th Anniversary of Adoption<br /> of the Constitution of <br />the Republic of Uzbekistan"
</h4>
        	<a href="24-anniversary.php" target="_blank"><img src='images/24th-Anniversary.jpg' alt="24th Anniversary of Adoption" /></a> 
        </div>
    </li>
          
          
          <li>
		<div class="specila_rubics_new_div">
        	 <h4 >SAYLOV 2016 <br />
ELECTION 2016<br />
ВЫБОРЫ 2016
</h4>
        	<a href="election-2016.php" target="_blank"><img src='images/rubrics-17-9-2016.jpg' alt="SAYLOV 2016 ELECTION 2016 ВЫБОРЫ 2016" /></a> 
        </div>
    </li>
    
            <li>
		<div class="specila_rubics_new_div">
        	 <h4 >25 th ANNIVERSARY OF INDEPENDENCE OF UZBEKISTAN</h4>
        	<a href="25anniversary.php" target="_blank"><img src='images/rubrics-30-05-2016.jpg' alt="O'ZBEKISTON MUSTAQILLIGIGA 25 YIL" /></a> 
        </div>
    </li>
     <li>
		<div class="specila_rubics_new_div">
        	 <h4 >2017 - Year of Dialogue with People and Human Interests</h4>
        	<a href="Year_dialogue.php" target="_blank"><img src='images/xalq2017.jpg' alt="2015 YIL-KEKSALARNI E'ZOZLASH YILI" /></a> 
        </div>
    </li>
    
     <li>
		<div class="specila_rubics_new_div">
        	 <h4 >PRESIDENCY OF THE REPUBLIC OF UZBEKISTAN IN SCO 2015-2016</h4>
        	<a href="presidency-news.php" target="_blank"><img src='images/presidency.jpg' alt="2015 YIL-KEKSALARNI E'ZOZLASH YILI" /></a> 
        </div>
    </li>
    
       
    <li>    
         <div class="specila_rubics_new_div">
        	   <h4 >Millennium Development Goals Report Uzbekistan - 2015</h4>
        	<a href="millennium_goals.php" target="_blank"> <img src="images/millennium_goals.jpg" > </a>
        </div>
    </li>
    
        
    <li>   
         <div class="specila_rubics_new_div">
        	  <h4>Information Digest of Press of Uzbekistan</h4>	
        		<a href="information_digest.php"> <img src="images/information_digest.jpg"> </a>
        </div>
    </li>
     <li>   
         <div class="specila_rubics_new_div">
        	  <h4>XII International Uzbek Cotton and Textile Fair</h4>	
        		<a target="_blank" href="http://www.cottonfair.uz"> <img src="images/uzbek-cotton&textile.jpg"> </a>
        </div>
    </li>
    
    <li>   
         <div class="specila_rubics_new_div">
        	  <h4>www.tourfair.uz</h4>	
        		<a target="_blank" href="http://www.tourfair.uz"> <img src="images/tourfair.jpg"> </a>
        </div>
    </li>
    
     <li>
          <div class="specila_rubics_new_div">
        	 <h4 >National Holiday - Navruz</h4>
        	<a href="new_rubric.php" target="_blank"><img src='images/rubric.jpg' alt="2015 YIL-KEKSALARNI E'ZOZLASH YILI" /></a> 
        </div>
    </li>
    
  	    		
          </ul>
       
      
    </div>       

   
      </div>

      

      

      <!--<div class="Abt_Embassy">

        <h1> About the Embassy </h1>

        <ul>

          <li><a href="http://evisa.mfa.uz/evisa_en/" target="_blank">Visa information</a></li>

          <li><a href="embassy_offices.php">Embassy offices, hours etc.</a></li>

          <li><a href="#">Press</a></li>

        </ul>

      </div>-->

      


      

    </div>

    <!-- IdxColumn_02 ends here --><!-- IdxColumn_03 ends here -->

    <div style="width:345px; float:left; padding-bottom:10px;">

    <div class="IdxColumn_01" style="padding-bottom:0px;" >

      <div style="min-height:500px;" class="PressReleases_box">

        <h1>Press Releases  </h1>

        

        <?php

 	   	if($numallpress!=0)

		{ ?>

	  <div class="PressRow">

			<?php

            for($i=0;$i<$numallpress;$i++)

			{

				//$n=$n+1;

				$row_press			= $database->show_press_details($showallpress[$i]['id']); 

			?>

	

	

			

			<div class="press_cnt">

			  

			

				<h3><?=$row_press['title']?></h3>

				<p><?=$database->limit_text($row_press['description'],150)?>

	</p>

				<a href="press_releases.php#press_<?=$row_press['id']?>">Read More</a> 

				</div>

	
</div>
			

			 <?php

			}

			?></div>

             <div style="text-align:right; color:#CC9900; padding:0 10px 10px 0;"><a href="press_releases.php"  style="color:#CC9900;">More Press Release</a> </div>

			<?php

		}

		else

		{

		?>

			<div style="padding: 10px 0 20px 50px;"> Coming Soon...</div>

		<?php

        }

		 ?> 

        

        <!--<div class="PressRow" style="padding-top:0px;">

          <div class="press_cnt">        

            <h3>Additional measures on stimulating attraction of foreign direct investments to Uzbekistan</h3>

            <p>President of Uzbekistan Islam Karimov signed a decree "On additional measures on stimulating attraction of foreign direct investments" on 10 April 2012.<br />

The document is directed at creating maximum favourable investment climate for foreign investors....

</p>

          <a href="press_releases.php#press_02">Read More</a> </div>

        </div>-->

        

      </div>

    </div>

    </div>

<div style="padding-left:10px;" >   

 
</div>



<!---bottom_second_container--->

  

 <div style="width:371px; float:left;  ">

   <?php include( 'includes/side_link.php') ?>

 

</div>

</div>

<!---bottom_second_container--->

  




  




<div class="bottom_third_container">
	<div class="bottom_second_container_head">Useful Links</div>
    
    
    <div class="passport_img usefull_link_new" >

	      <div id='tmpSlideshow' style="padding: 3% 0;">
            <div id='tmpSlide-1' class='tmpSlide'>
              <a href="http://consul.mfa.uz/" target="_blank"><img src='images/passport.jpg' alt='passport' />
              <div style="width:185px;" class="name"><h4>Консульские услуги </h4></div>  </a> 
            </div>
            
           <div id='tmpSlide-2' class='tmpSlide'>
             <a href="#"><img src='images/visa.jpg' alt='passport' />
             <div style="width:185px;" class="name"><h4>e-visa </h4> </div> </a>  
           </div>
            
            <div id='tmpSlide-3' class='tmpSlide'>
             <a href="http://consul.mfa.uz/" target="_blank"><img src='images/passport.jpg' alt='passport' />
             <div style="width:185px;" class="name"><h4>Консульские услуги </h4> </div> </a>  
           </div>
           
           <div id='tmpSlide-4' class='tmpSlide'>
          <a href="#"><img src='images/visa.jpg' alt='passport' />
             <div style="width:185px;" class="name"><h4>e-visa </h4> </div> </a>  
           </div>
            
            
        </div>
           <!-- <div style="padding: 4% 0;" >

              <a href="http://consul.mfa.uz/" target="_blank"><img src='images/passport.jpg' alt='passport' />

              <div style="width:196px;" class="name"><h4>Консульские услуги </h4></div>  </a> 

            </div>-->

           
</div><!---5--->
  
        
 <div class="trade_main_cc">  
    
    <div class="carouselContainer">

    <h2>Government Offices</h2>

          <div class="carousel">

            <ul style="position: relative;  z-index: 1;" >

<!--              <li><a href="http://press-service.uz/en/" target="_blank"> <img src="images/gov_body_01.jpg" /> </a></li>

               <li><a href="http://gov.uz/en/" target="_blank"> <img src="images/gov_body_02.jpg" /> </a></li>

                <li><a href="http://mfa.uz/eng/" target="_blank"> <img src="images/gov_body_03.jpg" /> </a></li>

                 <li><a href="http://www.mfer.uz" target="_blank"> <img src="images/gov_body_04.jpg" /> </a></li>-->

                 

                 <?php

				 $catcode				= 1;//for Government Offices

				 $showallbanner_gov		= $database->show_all_banner_by_category($catcode);

				 $numbanner_gov			= count($showallbanner_gov);

				 

				 for($gov=0 ; $gov<$numbanner_gov ; $gov++)

				 {

				 	$row_gov			= $database->show_banner_details($showallbanner_gov[$gov]['id']);

				?>

				 

                 <li><a href="<?=$row_gov['url']?>" target="_blank"> <img src="<?=ADMIN_URL.$row_gov['image']?>" /> </a></li>

                 <?php

				 }	

				 

				 ?>

            </ul>

          </div>

        </div><!---1-->
        
        
        <div style="margin-left:7%;" class="carouselContainer">

    <h2>Information Resources</h2>

          <div class="carousel">

            <ul style="position: relative;  z-index: 1;" >

            

                 <?php

				 $catcode				= 2;//for Government Offices

				 $showallbanner_gov		= $database->show_all_banner_by_category($catcode);

				 $numbanner_gov			= count($showallbanner_gov);

				 

				 for($gov=0 ; $gov<$numbanner_gov ; $gov++)

				 {

				 	$row_gov			= $database->show_banner_details($showallbanner_gov[$gov]['id']);

				?>

				 

                 <li><a href="<?=$row_gov['url']?>" target="_blank"> <img src="admin/<?=$row_gov['image']?>" /> </a></li>

                 <?php

				 }	

				 

				 ?>

<!--              <li ><a href="http://www.jahonnews.uz/eng/" target="_blank"> <img src="images/Information_resources_img_01.jpg" /> </a></li>

              <li ><a href="http://www.uza.uz" target="_blank"> <img src="images/Information_resources_img_02.jpg" /> </a></li>-->

        

            </ul>

          </div>

        </div><!----2--->
        
    
        
        <div style="margin-left:0%" class="carouselContainer">

    <h2>Trade Links</h2>

          <div class="carousel">

            <ul style="position: relative;  z-index: 1;" >

                 <?php

				 $catcode				= 4;//for Government Offices

				 $showallbanner_gov		= $database->show_all_banner_by_category($catcode);

				 $numbanner_gov			= count($showallbanner_gov);

				 

				 for($gov=0 ; $gov<$numbanner_gov ; $gov++)

				 {

				 	$row_gov			= $database->show_banner_details($showallbanner_gov[$gov]['id']);

				?>

				 

                 <li><a href="<?=$row_gov['url']?>" target="_blank"> <img src="admin/<?=$row_gov['image']?>" /> </a></li>

                 <?php

				 }	

				 

				 ?>

<!--              <li ><a href="www.exporter.uz" target="_blank"> <img src="images/Trade_links_01.jpg" /> </a></li>

              <li ><a href="http://www.dgmarket.com/tenders/CountryDetail.do?locationISO=uz" target="_blank"> <img src="images/Trade_links_02.jpg" /> </a></li>

              <li ><a href="http://www.exim.uz/" target="_blank"> <img src="images/Trade_links_03.jpg" /> </a></li>-->

        

            </ul>

          </div>

        </div><!----4---->
        
        
   


        <div style="margin-left:7%;"  class="carouselContainer">

    <h2>Economic Links</h2>

          <div class="carousel">

            <ul style="position: relative;  z-index: 1;" >

<!--              <li ><a href="http://www.uzinfoinvest.uz" target="_blank"> <img src="images/Economic_links_01.jpg" /> </a></li>

              <li ><a href="http://chamber.uz" target="_blank"> <img src="images/Economic_links_02.jpg" /> </a></li>

              <li ><a href="http://www.uzbektourism.uz/en" target="_blank"> <img src="images/Economic_links_03.jpg" /> </a></li>-->

                <?php

				 $catcode				= 3;//for Government Offices

				 $showallbanner_gov		= $database->show_all_banner_by_category($catcode);

				 $numbanner_gov			= count($showallbanner_gov);

				 

				 for($gov=0 ; $gov<$numbanner_gov ; $gov++)

				 {

				 	$row_gov			= $database->show_banner_details($showallbanner_gov[$gov]['id']);

				?>

				 

                 <li><a href="<?=$row_gov['url']?>" target="_blank"> <img src="admin/<?=$row_gov['image']?>" /> </a></li>

                 <?php

				 }	

				 

				 ?>

        

            </ul>

          </div>

        </div><!---3--->
        
    </div>  <!---trade_main_cc--->
        
            
             
      



<!--<div class="passport_img usefull_link_new" >

 <div id='tmpSlide-2' class='tmpSlide'>

              <a href="http://evisa.mfa.uz/evisa_en/" target="_blank"><img src='images/visa.jpg' alt='visa' />

              <div class="name"><h4>E-Visa</h4></div>  </a>

            </div>
              
</div>--><!---6--->         
            
            
            
        
        
        
        
        
    
    
    </div><!--bottom_third_container-->


    </div><!----main--->
</div>


 
  

  

  <!-- main ends here -->



<!-- container ends here -->



  <div id="footer" ><?php include( 'includes/footer.php') ?></div>

  <!-- footer ends here -->
  
  
 
   



<div id="popDiv" class="ontop">

			<div id="popup">
            	<div class="bokin_pop_head">
                	<span>Online Booking</span>
                	<a style="cursor:pointer;" class="close_btn_cc close_pop" onClick="hide('popDiv')"><img src="images/btn-close.png" /></a>
                </div><!---bokin_pop_head--->
                
                <div style="display:block;" class="booking_popup_content booking_button_cc">
                
                	<div class="booking_avail_cc "> <!--- booking_today_not_avail booking_not_avail ---->
                    
                        <div class="booking_inner_avail_cc">
                            <div class="booking_ico_cc"><img src="images/booking_avail_ico.png" /></div>
                            <div class="booking_text_cc">Book Now!</div> 
                            <div class="main_book_btn_cc">
                           	<select id="select" class="select_booking_date">
                            <option value="" selected="selected">--- Please Select ---</option>
                                <option <?php if($todayString > $tuesdayString || $onlineCountTuesday >= '25') { ?> disabled <?php } ?> value="<?=$tuesday?>">Tuesday <?=$tuesday?> <?php if( $onlineCountTuesday >= '25') { echo "(Closed)";  } ?></option>
								<option <?php if($todayString > $thursdayString || $onlineCountThursday >= '25') { ?> disabled <?php } ?> value="<?=$thursday?>" >Thursday <?=$thursday?>  <?php if( $onlineCountThursday >= '25') { echo "(Closed)";  } ?></option>
								<option <?php if($todayString > $sundayString || $onlineCountSunday >= '25') { ?> disabled <?php } ?> value="<?=$sunday?>">Sunday <?=$sunday?>  <?php if( $onlineCountSunday >= '25') { echo "(Closed)";  } ?></option>
                                </select>
                            </div>
                    	</div> 
                        
                         
                    </div><!--booking_avail_cc-->
                    
                </div><!--booking_button-->
               
               
                <div style="display:none;" class="booking_popup_content day_booking">
                 <form name="frm_onlinebooking"  action="bookingmail.php" method="post">
                 <div class="text_box_cc">
                		<div class="booking_text">Appointment Date </div>
                   		<div class="booking_text_box_cc "><input class="booking_text_box ico_cld textbox_disble" placeholder=""  name="date" type="text"  /></div>
                    </div>
                 
                	
                    
                    <div class="text_box_cc">
                		<div class="booking_text">Given name </div>
                   		<div class="booking_text_box_cc"><input class="booking_text_box" placeholder="Enter Given name" name="txtGivenName" type="text"  onkeypress="if(event.keyCode=='13')return validate_online(document.frm_onlinebooking)" /></div>
                    </div>
                    
                    <div class="text_box_cc">
                		<div class="booking_text">Surname </div>
                   		<div class="booking_text_box_cc"><input class="booking_text_box" placeholder="Enter Surname" name="txtSurname" type="text"  onkeypress="if(event.keyCode=='13')return validate_online(document.frm_onlinebooking)" /></div>
                    </div>
                    
                     <div class="text_box_cc">
                    	<div class="booking_text">Passport No </div>
                   		<div class="booking_text_box_cc"><input class="booking_text_box" name="txtPassportnumber"  placeholder="Enter Passport no" type="text" onkeypress="if(event.keyCode=='13')return validate_online(document.frm_onlinebooking)"/></div>
                    </div>
                    
                    
                     <div class="text_box_cc">
                    	<div class="booking_text">Nationality </div>
                   		<div class="booking_text_box_cc"><input class="booking_text_box" name="txtNationality"  placeholder="Enter Nationality" type="text" onkeypress="if(event.keyCode=='13')return validate_online(document.frm_onlinebooking)"/></div>
                    </div>
                    
                    
                    <div class="text_box_cc" style="height:70px !important;">
                    	<div class="booking_text">Residence Address </div>
                   		<div class="booking_text_box_cc">
                        <textarea class="booking_text_box_area" name="txtAddress" placeholder="Residence Address" style="height:60px;"></textarea>
                        </div>
                    </div>
                    
                    
                     <div class="text_box_cc">
                    	<div class="booking_text">Mobile </div>
                   		<div class="booking_text_box_cc"><input class="booking_text_box" name="txtMobile"  placeholder="Enter Mobile no" type="text" onkeypress="if(event.keyCode=='13')return validate_online(document.frm_onlinebooking)"/></div>
                    </div>
                    
                    <div class="text_box_cc">
                    	<div class="booking_text">Email </div>
                    	<div class="booking_text_box_cc"><input class="booking_text_box" placeholder="Enter Email address" name="txtEmail" type="text" onkeypress="if(event.keyCode=='13')return validate_online(document.frm_onlinebooking)"/></div>
                    </div>
                   
                    
                     <div class="text_box_cc">
                    	<div class="booking_text">Bookings Required </div>
                   		<div class="booking_text_box_cc">
                        <select id="test" style="width: 97%;"  class="booking_text_box" name="txtBooking">
                        
                        
                        
                        	<option value="Visa">Visa</option>
                            <option value="Notaries">Notaries</option>
                            <option value="Legalization & Attestation">Legalization & Attestation</option>
                            <option value="Civil Registration">Civil Registration</option>
                            <option value="Passport">Passport</option>
                            <option value="Travel document for return to Uzbekistan">Travel document for return to Uzbekistan</option>
                            <option value="UAE E-visa Attestation">UAE E-visa Attestation</option>
                            <option value="Others">Others (specify your issue or request) </option>
                            
                        </select>
                        
                        </div>
                    </div><!---tect_box_cc--->
                    
                     <div id="Others" class="text_box_cc">
                    	<div class="booking_text">Other</div>
                   		<div class="booking_text_box_cc">
                       		 <textarea class="booking_text_box_area" placeholder="Other" name="txtOthers" style="height:60px;" ></textarea>
                        </div>
                    </div><!---tect_box_cc--->
                    
                     <div class="text_box_cc">
                     	<a class="bookin_button" style="cursor:pointer;" onclick="return validate_online();">Book Now</a>
                        <a class="cancel_button close_pop" style="cursor:pointer;">Cancel</a>
                      </div><!---tect_box_cc--->
                      
                      
                     </form>  
                    
                </div><!---booking_popup_content next_day_booking--->
               
            
			</div><!---popup--->
             	
		</div><!----popDiv--->



</body>

</html>
<script type="text/javascript">

function validate_online()
{
	var thisform = document.frm_onlinebooking;
	if(thisform.txtGivenName.value=='')
	{
       alert("Please enter given name");
       thisform.txtGivenName.focus();
       return false;                 
	}
		
	if(thisform.txtSurname.value=='')
	{
       alert("Please enter surname");
       thisform.txtSurname.focus();
       return false;                 
	}
	
	if(thisform.txtPassportnumber.value=='')
	{
		alert("Please enter passport number");
	   	thisform.txtPassportnumber.focus();
	    return false;
	}
	
	if(thisform.txtNationality.value=='')
	{
		alert("Please enter nationality");
	   	thisform.txtNationality.focus();
	    return false;
	}
	
	if(thisform.txtAddress.value=='')
	{
		alert("Please enter residence address");
	   	thisform.txtAddress.focus();
	    return false;
	}
	
	
	
	if(thisform.txtMobile.value=='')
	{
		alert("Please enter mobile number");
	   	thisform.txtMobile.focus();
	    return false;
	}
				
    if(IsNumeric(thisform.txtMobile.value)==false)
    {
	   alert("Invalid mobile number! Please re-enter");
	   thisform.txtMobile.select();
	   thisform.txtMobile.focus();
	   return false;
	}
	
	if(thisform.txtEmail.value=='')
	{
	   alert("Please enter email");
	   thisform.txtEmail.focus();
	   return false;
	}
	       
	if(validate_email(thisform.txtEmail,"Invalid email address! Please re-enter!")==false)
    {
       thisform.txtEmail.select();
       thisform.txtEmail.focus();
       return false;
    }	
	
	

	
	if(thisform.txtBooking.value=='Others')
	{
		if(thisform.txtOthers.value=='')
		{
		alert("Please specify your issue or request");
	   	thisform.txtOthers.focus();
	    return false;
		}

	}

thisform.submit();
}


function validate_email(entered,alertbox) {

   var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
   var address = entered.value;
   if(reg.test(address) == false) {
     if (alertbox!="") alert(alertbox,entered);
      return false;
   }
}
function IsNumeric(strString)
{
   var strValidChars = "0123456789-+(). ";
   var strChar;
   var blnResult = true;
   if (strString.length == 0) return false;

   for (i = 0; i < strString.length && blnResult == true; i++)
      {
      strChar = strString.charAt(i);
      if (strValidChars.indexOf(strChar) == -1)
         	{
        	 blnResult = false;
         	}
      }
   return blnResult;
}

</script>


     <script defer src="js/jquery.flexslider.js"></script>

  <script type="text/javascript">
    $(function(){
      SyntaxHighlighter.all();
    });
    $(window).load(function(){
      $('.flexslider').flexslider({
        animation: "slide",
        start: function(slider){
          $('body').removeClass('loading');
        }
      });
    });
  </script>