 // Food special requirements
function init_food_requirement() {
    // Food Requirement
   $('select[name=food]').change(function(){
      if($(this).val()=='other') {
        $('#food_other').removeClass("hidden");   
      } else {
        $('#food_other').addClass("hidden");   
      }
   });
   
  // Init Food Requirement 
  if( $('select[name=food]').val()=='other') {
       $('#food_other').removeClass("hidden");  
  }
}   