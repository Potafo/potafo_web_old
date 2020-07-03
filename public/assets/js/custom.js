/*
 *
 *		CUSTOM.JS
 *
 */

(function($){
	
	"use strict";
	
	// DETECT TOUCH DEVICE //
	function is_touch_device() {
		return !!('ontouchstart' in window) || ( !! ('onmsgesturechange' in window) && !! window.navigator.maxTouchPoints);
	}
	
	
	// SHOW/HIDE MOBILE MENU //
	function show_hide_mobile_menu() {
		
		$("#mobile-menu-button").on("click", function(e) {
            
			e.preventDefault();
			
			$("#mobile-menu").slideToggle(300);
			
        });	
		
	}
	
	
	// MOBILE MENU //
	function mobile_menu() {
		
		if ($(window).width() < 992) {
			
			if ($("#menu").length > 0) {
			
				if ($("#mobile-menu").length < 1) {
					
					$("#menu").clone().attr({
						id: "mobile-menu",
						class: ""
					}).insertAfter("#header");
					
					$("#mobile-menu .megamenu > a").on("click", function(e) {
                        
						e.preventDefault();
						
						$(this).toggleClass("open").next("div").slideToggle(300);
						
                    });
					
					$("#mobile-menu .dropdown > a").on("click", function(e) {
                        
						e.preventDefault();
						
						$(this).toggleClass("open").next("ul").slideToggle(300);
						
                    });
				
				}
				
			}
				
		} else {
			
			$("#mobile-menu").hide();
			
		}
		
	}
	
	
	// STICKY //
	function sticky() {
		
		var sticky_point = 100;
		
		if ($(window).scrollTop() > sticky_point) {  
			$("#header").addClass("header-sticky");
		} else {
			$("#header").removeClass("header-sticky");
		}
		
		if ($(window).scrollTop() > 200){  
			$(".elements-menu").addClass("elements-menu-sticky");
		} else {
			$(".elements-menu").removeClass("elements-menu-sticky");
		}
		
	}
	
	
	// SEARCH //
	function header_search() {	
		
		$(".search-button").on("click", function(e) { 
	
			e.preventDefault();
			
			if(!$(".search-button").hasClass("open")) {
			
				$("#search-container").fadeIn(300);
				$("#logo, .menu, .search-button, #mobile-menu-button").css("visibility", "hidden");
				
			} else {
				
				$("#search-container").fadeOut(300);	
				$("#logo, .menu, .search-button, #mobile-menu-button").css("visibility", "visible");
			
			}
			
		});
		
		$("#search-form").append('<a class="search-form-close" href="#">x</a>');
		
		$("#search-form a.search-form-close").on("click", function(event){
			
			event.preventDefault();
			$("#search-container").fadeOut(300);
			$("#logo, .menu, .search-button, #mobile-menu-button").css("visibility", "visible");
			
		});
		
	 }
	
 
	// PROGRESS BARS // 
	function progress_bars() {
		
		$(".progress .progress-bar:in-viewport").each(function() {
			
			if (!$(this).hasClass("animated")) {
				$(this).addClass("animated");
				$(this).css("background-color", $(this).attr("data-bar-color"))
				$(this).animate({
					width: $(this).attr("data-width") + "%"
				}, 2000);
			}
			
		});
		
	}


	// CHARTS //
	function pie_chart() {
		
		$(".pie-chart:in-viewport").each(function() {
			
			$(this).easyPieChart({
				animate: 1500,
				lineCap: "round",
				lineWidth: $(this).attr("data-line-width"),
				size: $(this).attr("data-size"),
				barColor: $(this).attr("data-bar-color"),
				trackColor: $(this).attr("data-track-color"),
				scaleColor: "transparent",
				onStep: function(from, to, percent) {
					$(this.el).find(".pie-chart-percent .value").text(Math.round(percent));
				}
			});
			
		});
		
	}
	
	
	// ANIMATE CHARTS //
	function animate_charts() {
		
		$(".statistics-container .animate-chart:in-viewport").each(function() {
			
			if(!$(this).hasClass("animated")) {
				
				$(this).addClass("animated");
				
				// LINE CHART //
				var data = {
					labels : ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"],
					datasets : [
						{
							fillColor: "transparent",
							strokeColor: "transparent",
							pointColor: "transparent",
							pointStrokeColor: "transparent",
							data : [0,0,0,0,0,0,0,0,0,0,0,0]
						},
						{
							fillColor: "transparent",
							strokeColor: "transparent",
							pointColor: "transparent",
							pointStrokeColor: "transparent",
							data : [60,60,60,60,60,60,60,60,60,60,60,60]
						},
						{
							fillColor: "transparent",
							strokeColor: "#a8e12a",
							pointColor: "#fff",
							pointStrokeColor: "a8e12a",
							data : [20,40,45,42,50,25,35,50,32,28,10,20]
						},
						{
							fillColor: "transparent",
							strokeColor: "#0bbff2",
							pointColor: "#fff",
							pointStrokeColor: "#0bbff2",
							data : [10,20,22,40,45,40,28,32,51,45,55,50]
						},							
					]
				}
			
				var line_chart = new Chart(document.getElementById("line-chart").getContext("2d")).Line(data, { 
					responsive: true,
					showTooltips: false,
					bezierCurve: false,
					pointDotStrokeWidth: 2
				});
				
				// FILL LINE CHART //
				var data = {
					labels : ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"],
					datasets : [
						{
							fillColor: "transparent",
							strokeColor: "transparent",
							pointColor: "transparent",
							pointStrokeColor: "transparent",
							data : [0,0,0,0,0,0,0,0,0,0,0,0]
						},
						{
							fillColor: "transparent",
							strokeColor: "transparent",
							pointColor: "transparent",
							pointStrokeColor: "transparent",
							data : [60,60,60,60,60,60,60,60,60,60,60,60]
						},
						{
							fillColor: "rgba(168, 225, 42, 0.5)",
							strokeColor: "rgba(168, 225, 42, 0.5)",
							pointColor: "transparent",
							pointStrokeColor: "transparent",
							data : [20,40,45,42,50,25,35,50,32,28,10,20]
						},
						{
							fillColor: "rgba(11, 191, 242, 0.5)",
							strokeColor: "rgba(11, 191, 242, 0.5)",
							pointColor: "transparent",
							pointStrokeColor: "transparent",
							data : [10,20,22,40,45,40,28,32,51,45,55,50]
						},							
					]
				}
			
				var fill_line_chart = new Chart(document.getElementById("fill-line-chart").getContext("2d")).Line(data, { 
					responsive: true,
					showTooltips: false,
					bezierCurve: false,
					pointDotStrokeWidth: 2
				});
				
				// BAR CHARTS //
				var data = {
					labels: ["First sem", "Second sem", "Third sem"],
					datasets: [
						{
							label: "",
							fillColor: "transparent",
							strokeColor: "transparent",
							highlightFill: "transparent",
							highlightStroke: "transparent",
							data: [100, 100, 100]
						},
						{
							label: "",
							fillColor: "#a8e12a",
							strokeColor: "#a8e12a",
							highlightFill: "#a8e12a",
							highlightStroke: "#a8e12a",
							data: [50, 55, 85]
						},
						{
							label: "",
							fillColor: "#0bbff2",
							strokeColor: "#0bbff2",
							highlightFill: "#0bbff2",
							highlightStroke: "#0bbff2",
							data: [55, 75, 70]
						}
					]
				};
				
				var bar_chart  = new Chart(document.getElementById("bar-chart").getContext("2d")).Bar(data, { 
					responsive: true,
					showTooltips: false,
					barShowStroke: true,
					barStrokeWidth: 10,
					barValueSpacing: 10,
					barDatasetSpacing: 30
				});
				
				// PIE CHART
				var data = [					
					{
						value: 15,
						color: "#a8e12a",
						highlight: "#a8e12a",
						label: "#a8e12a"
					},			
					{
						value: 25,
						color: "#0bbff2",
						highlight: "#0bbff2",
						label: "#0bbff2"
					},
					{
						value: 30,
						color: "#ec284a",
						highlight: "#ec284a",
						label: "#ec284a"
					},										
					{
						value: 30,
						color:"#d7d7d7",
						highlight: "#d7d7d7",
						label: "#d7d7d7"
					}
				]
				
				var circle_chart = new Chart(document.getElementById("circle-chart").getContext("2d")).Pie(data, { 
					responsive: true,
					showTooltips: false,
					animationSteps: 30
				});
					
			}
			
		});
		
	}
	
	
	// COUNTER //
	function counter() {
		
		$(".counter .counter-value:in-viewport").each(function() {
			
			if (!$(this).hasClass("animated")) {
				$(this).addClass("animated");
				$(this).jQuerySimpleCounter({
					start: 0,
					end: $(this).attr("data-value"),
					duration: 2000
				});	
			}
		
		});
		
	}
	
	
	// LOAD MORE PROJECTS //
	function load_more_projects() {
	
		var number_clicks = 0;
		
		$(".load-more").on("click", function(e) {
			
			e.preventDefault();
			
			if (number_clicks == 0) {
				$.ajax({
					type: "POST",
					url: $(".load-more").attr("href"),
					dataType: "html",
					cache: false,
					msg : '',
					success: function(msg) {
						$(".isotope").append(msg);	
						$(".isotope").imagesLoaded(function() {
							$(".isotope").isotope("reloadItems").isotope();
							$(".portfolio-item").hoverdir();
							$(".fancybox-portfolio-gallery").attr("rel","group").fancybox({
								prevEffect: 'none',
								nextEffect: 'none'
							});
							$(".fancybox-blog-gallery").attr("rel","group").fancybox({
								prevEffect: 'none',
								nextEffect: 'none'
							});
						});
						number_clicks++;
						$(".load-more").html("Nothing to load");
					}
				});
			}
			
		});
		
	}
	
	
	// ABOUT ME //
	function about_me() {
		
		$(".about-me").each(function(){
		
			$(this).on({
				mouseenter: function () {
					$(this).find(".social-media").stop().slideDown(300);
				},
				mouseleave: function () {
					$(this).find(".social-media").stop().slideUp(300);
				}
			});
			
		});
		
	}
	
	
	// SERVICE BOX //
	function service_box() {
		
		$(".service-box.style-5").each(function(){
			
			$(this).on("click", function(e){
				
				e.preventDefault();
				$(this).find(".service-box-content").slideToggle(300);
				$(this).toggleClass("open");
				
			});
			
		});
		
	}
	
	
	// CATEGORIES //
	function categories() {
		
		$(".widget-categories li.dropdown").each(function(){
		
			$(this).on({
				mouseenter: function () {
					$(this).addClass("open").find("ul").stop().slideDown(300);
				},
				mouseleave: function () {
					$(this).removeClass("open").find("ul").stop().slideUp(300);
				}
			});
			
		});
		
	}
	
	
	// INSTAGRAM FEED //
	function instagram_feed() {

		if ($('#instafeed').length > 0 ) {
		
			var nr = $('#instafeed').data('number');
			var userid = $('#instafeed').data('user');
			var accesstoken = $('#instafeed').data('accesstoken');
			
			var feed = new Instafeed({
				target: 'instafeed',
				get: 'user',
				userId: userid,
				accessToken: accesstoken,
				limit: nr,
				resolution: 'thumbnail'			
			});
			
			feed.run();		
		
		}
			
	}
	
	
	// MULTILAYER PARALLAX //
	function multilayer_parallax() {
		
		$(".parallax-multilayer .parallax-layer").each(function(){
			
			var x = parseInt($(this).attr("data-x"), 10),
				y = parseInt($(this).attr("data-y"), 10);
			
			$(this).css({
				"margin-left": x + "%", 
				"margin-top": y + "%"
			});
			
		});

		
	}
	
	
	// WIDGET LANGUAGE //
	function widget_language() {
		
		$(".widget-language > a").on("click", function(event){
			
			event.preventDefault();
			$(".widget-language ul").slideToggle(300);
			
		})
		
	}
	
	
	// SHOW/HIDE GO TOP //
	function show_hide_go_top() {
		
		if ($(window).scrollTop() > $(window).height()/2) {
			$("#go-top").fadeIn(300);
		} else {
			$("#go-top").fadeOut(300);
		}
		
	}
	
	
	// GO TOP //
	function go_top() {				
		
		$("#go-top").on("click", function() {
			$("html, body").animate({
				scrollTop: 0
			}, 800);
			return false;
		});
		
	}
	
	
	// FULL SCREEN //
	function full_screen() {
		
		$(".full-screen").css("height", $(window).height() + "px");
		
	}	
	
	
	// ANIMATIONS //
	function animations() {
		
		animations = new WOW({
			boxClass: 'wow',
			animateClass: 'animated',
			offset: 100,
			mobile: false,
			live: true
		})
		
		animations.init();
		
	}
	
	
	// SMOOTH SCROLLING //
	function smooth_scrolling() {
		
		$('.elements-menu a[href*=#]:not([href=#])').on("click", function() {
			
			if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname) {
			
				var target = $(this.hash);
				target = target.length ? target : $('[name=' + this.hash.slice(1) +']');
			
				if (target.length) {
					$('html,body').animate({
						scrollTop: target.offset().top
					}, 1000);
				
					return false;
				
				}
			
			}
			
		});
		
	}
	
	// DOCUMENT READY //
	$(document).ready(function(){
		
		// MENU //
		$(".menu").superfish({
			delay: 500,
			animation: {
				opacity: 'show',
				height: 'show'
			},
			speed: 'fast',
			autoArrows: true
		});
		
		
		// SHOW/HIDE MOBILE MENU //
		show_hide_mobile_menu();
		
		
		// MOBILE MENU //
		mobile_menu();
		
		
		// SEARCH //
		header_search();
		
		
		// FANCYBOX //
		$(".fancybox").fancybox({});
		
		$(".fancybox-portfolio-gallery").attr("rel","group").fancybox({
			prevEffect: 'none',
			nextEffect: 'none'
		});
		
		$(".fancybox-products-gallery").attr("rel","group").fancybox({
			prevEffect: 'none',
			nextEffect: 'none'
		});
		
		$(".fancybox-blog-gallery").attr("rel","group").fancybox({
			prevEffect: 'none',
			nextEffect: 'none'
		});
		
		
		// FULL SCREEN //
		full_screen();
		
		// REVOLUTION SLIDER //
		$(".banner1").revolution({
			delay: 8000,
			startwidth: 1170,
			startheight: 295,
			startWithSlide: 0,
			
			fullScreenAlignForce: "off",
			autoHeight: "off",
			minHeight: "off",
			
			shuffle: "off",
			
			onHoverStop: "on",
			
			thumbWidth: 100,
			thumbHeight: 50,
			thumbAmount: 3,
			
			hideThumbsOnMobile: "off",
			hideNavDelayOnMobile: 1500,
			hideBulletsOnMobile: "off",
			hideArrowsOnMobile: "off",
			hideThumbsUnderResoluition: 0,
			
			hideThumbs: 0,
			hideTimerBar: "off",
			
			keyboardNavigation: "on",
			
			navigationType: "bullet",
			navigationArrows: "solo",
			navigationStyle: "preview3",
			
			navigationHAlign: "center",
			navigationVAlign: "bottom",
			navigationHOffset: 0,
			navigationVOffset: 30,
			
			soloArrowLeftHalign: "left",
			soloArrowLeftValign: "center",
			soloArrowLeftHOffset: 20,
			soloArrowLeftVOffset: 0,
			
			soloArrowRightHalign: "right",
			soloArrowRightValign: "center",
			soloArrowRightHOffset: 20,
			soloArrowRightVOffset: 0,
			
			parallax: "mouse",
			parallaxBgFreeze: "on",
			parallaxLevels: [10,7,4,3,2,5,4,3,2,1],
			parallaxDisableOnMobile: "on",
			
			touchenabled: "off",
			swipe_velocity: "0.7",
			swipe_max_touches: "1",
			swipe_min_touches: "1",
			drag_block_vertical: "false",
			
			stopAtSlide: -1,
			stopAfterLoops: -1,
			hideCaptionAtLimit: 0,
			hideAllCaptionAtLilmit: 0,
			hideSliderAtLimit: 0,
			
			dottedOverlay: "none",
			
			spinned: "spinner4",
			
			fullWidth: "off",
			forceFullWidth: "off",
			fullScreen: "off",
			fullScreenOffsetContainer: "#topheader-to-offset",
			fullScreenOffset: "0px",
			
			panZoomDisableOnMobile: "off",
			
			simplifyAll: "off",
			
			shadow: 0
		});
		
		$(".banner2").revolution({
			delay: 8000,
			startwidth: 1170,
			startheight: 650,
			startWithSlide: 0,
			
			fullScreenAlignForce: "off",
			autoHeight: "off",
			minHeight: "off",
			
			shuffle: "off",
			
			onHoverStop: "on",
			
			thumbWidth: 100,
			thumbHeight: 50,
			thumbAmount: 3,
			
			hideThumbsOnMobile: "off",
			hideNavDelayOnMobile: 1500,
			hideBulletsOnMobile: "off",
			hideArrowsOnMobile: "off",
			hideThumbsUnderResoluition: 0,
			
			hideThumbs: 0,
			hideTimerBar: "on",
			
			keyboardNavigation: "on",
			
			navigationType: "bullet",
			navigationArrows: "solo",
			navigationStyle: "round",
			
			navigationHAlign: "center",
			navigationVAlign: "bottom",
			navigationHOffset: 0,
			navigationVOffset: 60,
			
			soloArrowLeftHalign: "left",
			soloArrowLeftValign: "center",
			soloArrowLeftHOffset: 20,
			soloArrowLeftVOffset: 0,
			
			soloArrowRightHalign: "right",
			soloArrowRightValign: "center",
			soloArrowRightHOffset: 20,
			soloArrowRightVOffset: 0,
			
			touchenabled: "off",
			swipe_velocity: "0.7",
			swipe_max_touches: "1",
			swipe_min_touches: "1",
			drag_block_vertical: "false",
			
			stopAtSlide: -1,
			stopAfterLoops: -1,
			hideCaptionAtLimit: 0,
			hideAllCaptionAtLilmit: 0,
			hideSliderAtLimit: 0,
			
			dottedOverlay: "none",
			
			spinned: "spinner4",
			
			fullWidth: "off",
			forceFullWidth: "off",
			fullScreen: "off",
			fullScreenOffsetContainer: "#topheader-to-offset",
			fullScreenOffset: "0px",
			
			panZoomDisableOnMobile: "off",
			
			simplifyAll: "off",
			
			shadow: 0
		});
		
		$(".banner-fullscreen").revolution({
			delay: 8000,
			startwidth: 1170,
			startWithSlide: 0,
			
			fullScreenAlignForce: "off",
			autoHeight: "off",
			minHeight: "off",
			
			shuffle: "off",
			
			onHoverStop: "on",
			
			thumbWidth: 100,
			thumbHeight: 50,
			thumbAmount: 3,
			
			hideThumbsOnMobile: "off",
			hideNavDelayOnMobile: 1500,
			hideBulletsOnMobile: "off",
			hideArrowsOnMobile: "off",
			hideThumbsUnderResoluition: 0,
			
			hideThumbs: 0,
			hideTimerBar: "off",
			
			keyboardNavigation: "on",
			
			navigationType: "bullet",
			navigationArrows: "solo",
			navigationStyle: "preview3",
			
			navigationHAlign: "center",
			navigationVAlign: "bottom",
			navigationHOffset: 0,
			navigationVOffset: 30,
			
			soloArrowLeftHalign: "left",
			soloArrowLeftValign: "center",
			soloArrowLeftHOffset: 20,
			soloArrowLeftVOffset: 0,
			
			soloArrowRightHalign: "right",
			soloArrowRightValign: "center",
			soloArrowRightHOffset: 20,
			soloArrowRightVOffset: 0,
			
			parallax: "mouse",
			parallaxBgFreeze: "on",
			parallaxLevels: [10,7,4,3,2,5,4,3,2,1],
			parallaxDisableOnMobile: "on",
			
			touchenabled: "off",
			swipe_velocity: "0.7",
			swipe_max_touches: "1",
			swipe_min_touches: "1",
			drag_block_vertical: "false",
			
			stopAtSlide: -1,
			stopAfterLoops: -1,
			hideCaptionAtLimit: 0,
			hideAllCaptionAtLilmit: 0,
			hideSliderAtLimit: 0,
			
			dottedOverlay: "none",
			
			spinned: "spinner4",
			
			fullWidth: "off",
			forceFullWidth: "off",
			fullScreen: "on",
			fullScreenOffsetContainer: "#topheader-to-offset",
			fullScreenOffset: "0px",
			
			panZoomDisableOnMobile: "off",
			
			simplifyAll: "off",
			
			shadow: 0
		});
	
	
		// OWL Carousel //
		$(".owl-carousel.logos").owlCarousel({
			items: 5,
			itemsDesktop: [1199,4],
			itemsDesktopSmall: [991,3],
			itemsTablet: [767,2],
			itemsMobile: [479,2],
			slideSpeed: 500,
			autoPlay: true,
			stopOnHover: true,
			navigation: true,
			navigationText: false,
			pagination: true,
			paginationNumbers: false,
			mouseDrag: true,
			touchDrag: true,
			transitionStyle: false
		});
		
		$(".owl-carousel.main-slider").owlCarousel({
			singleItem: true,
			slideSpeed: 200,
			autoPlay: true,
			stopOnHover: true,
			navigation: true,
			navigationText: false,
			pagination: true,
			paginationNumbers: false,
			mouseDrag: false,
			touchDrag: true,
			transitionStyle: "fade"
		});
		
		$(".owl-carousel.images-slider").owlCarousel({
			singleItem: true,
			slideSpeed: 200,
			autoPlay: true,
			stopOnHover: true,
			navigation: false,
			navigationText: false,
			pagination: true,
			paginationNumbers: false,
			mouseDrag: true,
			touchDrag: true,
			transitionStyle: "fade"
		});
		
		$(".owl-carousel.testimonial-slider").owlCarousel({
			singleItem: true,
			slideSpeed: 200,
			autoPlay: true,
			stopOnHover: true,
			navigation: false,
			navigationText: false,
			pagination: true,
			paginationNumbers: false,
			mouseDrag: true,
			touchDrag: true,
			transitionStyle: "fade"
		});
		
		
		var testimonial_slider_2 = $(".owl-carousel.testimonial-slider-2").owlCarousel({
			singleItem: true,
			slideSpeed: 200,
			autoPlay: false,
			stopOnHover: true,
			navigation: false,
			navigationText: false,
			pagination: false,
			paginationNumbers: false,
			mouseDrag: false,
			touchDrag: false,
			transitionStyle: "fade"
		}).data('owlCarousel');
		
		$(".slider-thumbs a").each(function() {
				
			$(this).click(function() {
				
				var x = $(this).attr("data-slide");
				
				$(".slider-thumbs a").removeClass("active");
				$(this).addClass("active");
				
				testimonial_slider_2.goTo(x-1);
				return false;

			})
			
		});
		
		
		// GOOGLE MAPS //
		$(".map").each(function(){
			
			var data_zoom = 15;
			
			if ($(this).attr("data-zoom") !== undefined) {
				data_zoom = parseInt($(this).attr("data-zoom"),10);
			}	
			
			$(this).gmap3({
				marker: {
					values: [{
						address: $(this).attr("data-address"),
						data: $(this).attr("data-address-details")
					}],
					options:{
						draggable: false
					},
					events:{
						mouseover: function(marker, event, context){
							var map = $(this).gmap3("get"),
							infowindow = $(this).gmap3({get:{name:"infowindow"}});
							if (infowindow){
								infowindow.open(map, marker);
								infowindow.setContent(context.data);
							} else {
								$(this).gmap3({
									infowindow:{
										anchor:marker, 
										options:{content: context.data}
									}
								});
							}
						},
						mouseout: function(){
							var infowindow = $(this).gmap3({get:{name:"infowindow"}});
							if (infowindow){
								infowindow.close();
							}
						}
					}
				},
				map: {
					options: {
						mapTypeId: google.maps.MapTypeId.ROADMAP,
						zoom: data_zoom,
						scrollwheel: false,
						styles: [
							{"featureType":"water","elementType":"geometry.fill","stylers":[{"color":"#d3d3d3"}]},
							{"featureType":"transit","stylers":[{"color":"#808080"},{"visibility":"off"}]},
							{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"visibility":"on"},{"color":"#b3b3b3"}]},
							{"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"color":"#ffffff"}]},
							{"featureType":"road.local","elementType":"geometry.fill","stylers":[{"visibility":"on"},{"color":"#ffffff"},{"weight":1.8}]},
							{"featureType":"road.local","elementType":"geometry.stroke","stylers":[{"color":"#d7d7d7"}]},
							{"featureType":"poi","elementType":"geometry.fill","stylers":[{"visibility":"on"},{"color":"#ebebeb"}]},
							{"featureType":"administrative","elementType":"geometry","stylers":[{"color":"#a7a7a7"}]},
							{"featureType":"road.arterial","elementType":"geometry.fill","stylers":[{"color":"#ffffff"}]},
							{"featureType":"road.arterial","elementType":"geometry.fill","stylers":[{"color":"#ffffff"}]},
							{"featureType":"landscape","elementType":"geometry.fill","stylers":[{"visibility":"on"},{"color":"#efefef"}]},
							{"featureType":"road","elementType":"labels.text.fill","stylers":[{"color":"#696969"}]},
							{"featureType":"administrative","elementType":"labels.text.fill","stylers":[{"visibility":"on"},{"color":"#737373"}]},
							{"featureType":"poi","elementType":"labels.icon","stylers":[{"visibility":"off"}]},
							{"featureType":"poi","elementType":"labels","stylers":[{"visibility":"off"}]},
							{"featureType":"road.arterial","elementType":"geometry.stroke","stylers":[{"color":"#d6d6d6"}]},
							{"featureType":"road","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{},
							{"featureType":"poi","elementType":"geometry.fill","stylers":[{"color":"#dadada"}]}
						]
                    }
				}
			});
			
		});			
		
		
		// ISOTOPE //
		$(".isotope").imagesLoaded( function() {
			
			var container = $(".isotope");
			
			container.isotope({
				itemSelector: '.isotope-item',
				layoutMode: 'masonry',
				transitionDuration: '0.8s'
			});
			
			$(".filter li a").on("click", function() {
				$(".filter li a").removeClass("active");
				$(this).addClass("active");
	
				var selector = $(this).attr("data-filter");
				container.isotope({
					filter: selector
				});
	
				return false;
			});
	
			$(window).resize(function() {
				container.isotope();
			});
			
		});
		
		
		// LOAD MORE PORTFOLIO ITEMS //
		load_more_projects();
		
		
		// PLACEHOLDER //
		$("input, textarea").placeholder();
		
		
		// CONTACT FORM VALIDATE & SUBMIT //
		// VALIDATE //
		$("#contact-form").validate({
			rules: {
				name: {
					required: true
				},
				email: {
					required: true,
					email: true
				},
				subject: {
					required: true
				},
				message: {
					required: true,
					minlength: 10
				}
			},
			messages: {
				name: {
					required: "Please enter your name!"
				},
				email: {
					required: "Please enter your email!",
					email: "Please enter a valid email address"
				},
				subject: {
					required: "Please enter the subject!"
				},
				message: {
					required: "Please enter your message!"
				}
			},
				
			// SUBMIT //
			submitHandler: function(form) {
				var result;
				$(form).ajaxSubmit({
					type: "POST",
					data: $(form).serialize(),
					url: "assets/php/send.php",
					success: function(msg) {
						
						if (msg == 'OK') {
							result = '<div class="alert alert-success">Your message was successfully sent!</div>';
							$("#contact-form").clearForm();
						} else {
							result = msg;
						}
						
						$("#alert-area").html(result);
	
					},
					error: function() {
	
						result = '<div class="alert alert-danger">There was an error sending the message!</div>';
						$("#alert-area").html(result);
	
					}
				});
			}
		});
		
		
		// PARALLX //
		if (!is_touch_device()) {
			
			$(window).stellar({
				horizontalScrolling: false,
				verticalScrolling: true,
				responsive: true
			});
			
		}
		
		multilayer_parallax();
		
		
		// SHOW/HIDE GO TOP
		show_hide_go_top();
		
		
		// GO TOP //
		go_top();
		
		
		// YOUTUBE PLAYER //
		$(".youtube-player").mb_YTPlayer();
		
		
		// ABOUT ME //
		about_me();
		
		
		// SERVICE BOX //
		service_box();
		
		
		// CATEGORIES //
		categories();
		
		
		// WIDGET LANGUAGE //
		widget_language();
		
		
		// HOVERDIR //
		$(".portfolio-item").hoverdir();
		
		
		// MAGNIFY //
		$(".magnify-img").each(function(){
			$(this).magnify();
		});
		
		
		// INSTAGRAM FEED //
		instagram_feed();
		
		
		// TEXT ROTATOR //
		$(".rotate").each(function() {
			
			var rotate_speed = 2000;
			
			if ($(this).attr("data-rotate-speed") !== undefined) {
				rotate_speed = parseInt($(this).attr("data-rotate-speed"),10);
			}
			
			$(this).Morphext({
				animation: $(this).attr("data-rotate-type"),
				separator: ",",
				speed: rotate_speed
			});
			
		});
		
		
		// ANIMATIONS //
		animations();
		
		
		// SMOOTH SCROLLING //
		smooth_scrolling();
		
		
		// SCROLLSPY //
		$("body").scrollspy({
			target: '.elements-menu'
		});
		
	});

	
	// WINDOW SCROLL //
	$(window).scroll(function() {
		
		progress_bars();
		pie_chart();
		animate_charts();
		counter();
		show_hide_go_top();
		sticky();
		
	});
	
	
	// WINDOW RESIZE //
	$(window).resize(function() {

		mobile_menu();
		full_screen();
		
	});
	
})(window.jQuery);