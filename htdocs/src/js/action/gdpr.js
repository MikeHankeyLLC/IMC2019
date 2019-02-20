function calcAge(dateString) {
  var birthday = +new Date(dateString);
  return ~~((Date.now() - birthday) / (31557600000));
}

function showUnder16() {
        $('.under16').removeClass('hidden').find('input').attr('required','required');
        $('input[name=under16]').val(1);
        $('.over16').addClass('hidden');
}

function hideUnder16() {
        $('.under16').addClass('hidden').find('input').removeAttr('required');
        $('input[name=under16]').val(0);
        $('.over16').removeClass('hidden');
}

function showHideUnderage() {


  var dob = $('input[name=dob]').val();
  

  if($('#reg_form').length && typeof dob !='undefined' && dob != '') {
        
      // Test if younger than 16
      if(calcAge(dob)<=15) {
        // We need to update the GRDP text 
        showUnder16();
      } else {
        hideUnder16();
      }
  } else {
    hideUnder16();
  }

    
}


$(function() {
  showHideUnderage();
})