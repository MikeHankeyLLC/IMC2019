$(document).ready(function(){

    $('input[name=pay_date], input[name=departure_date], input[name=arrival_date]').datepicker({ format: "yyyy-mm-dd"});
    
    // Contributions
    //init_contributions();
    
    // T-shirts
    init_tshirts();
    
    // Roomates
    init_roomates();
    
    // Food
    init_food_requirement();
});
   