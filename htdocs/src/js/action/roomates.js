function init_roomates() {
    $('input[name=reg_type]').click(function(){
        if($(this).val()=='4_bed_room' || $(this).val()=='2_bed_room' || $(this).val()=='3_bed_room' || $(this).val()=='5_bed_room' || $(this).val()=='6_bed_room') {
            $('#roommate').removeClass('hidden');     
        } else {
            $('#roommate').addClass('hidden');     
        }
     });
     
     if($('input[name=reg_type][value=4_bed_room]').is(":checked") 
        || $('input[name=reg_type][value=2_bed_room]').is(":checked") 
        || $('input[name=reg_type][value=3_bed_room]').is(":checked")  || $('input[name=reg_type][value=5_bed_room]').is(":checked") || $('input[name=reg_type][value=6_bed_room]').is(":checked")) {
        $('#roommate').removeClass('hidden');     
    }
}