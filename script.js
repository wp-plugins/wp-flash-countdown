jQuery(document).ready(function() {
	jQuery("#help").hide();	
	jQuery("#helpB").removeClass("helpB").addClass("helpBH"); 
	



//settings tab
jQuery("#settingsB").click( function() {
	jQuery("#settings").show();	
	jQuery("#help").hide();	
	jQuery("#helpB").removeClass("helpBH").addClass("helpB"); 
	jQuery("#settingsB").removeClass("settingsB").addClass("settingsBH"); 
});  

//help tab
jQuery("#helpB").click( function() {
	jQuery("#settings").hide();	
	jQuery("#help").show();	
	jQuery("#helpB").removeClass("helpB").addClass("helpBH"); 
	jQuery("#settingsB").removeClass("settingsBH").addClass("settingsB");
});


			
});

