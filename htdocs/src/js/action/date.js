function get_the_Day(v) {
    var weekday = new Array(7);
    weekday[0]=  "Sunday";
    weekday[1] = "Monday";
    weekday[2] = "Tuesday";
    weekday[3] = "Wednesday";
    weekday[4] = "Thursday";
    weekday[5] = "Friday";
    weekday[6] = "Saturday";
    return weekday[v];
} 
 
$('select[name=arrival_day]').change(function() {
    var year = $('input[name=arrival_year]').val(),
        month = $('select[name=arrival_month]').val()-1,
        day = $(this).val(),
        d = new Date(year, month, day);
        console.log(d);    
        console.log(get_the_Day(d.getDay()));
        console.log(d.getMonth());
        console.log(d.getYear());
});