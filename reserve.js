<!--
// $Id: reserve.js,v 1.3 2003/12/04 18:25:14 sven Exp $

/* Creates Child Window and gives it the Focus */
function openChild(URL){
    return window.open(URL,"LilReserve","width=600,height=600,top=25,left=25,scrollbars=yes");
}

function showChild(URL){
    openChild(URL).focus();
}


// functions for Unix TimeStamp calculation

// create new date object
var reserve_time = new Date();

// current_date is set in the page (print_header())
reserve_time.setTime(current_date);

// these functions access the object
function setReserveDay() {
    var i = document.phonyForm.day.selectedIndex;
    reserve_time.setDate(document.phonyForm.day.options[i].value);
    return true;
}

function setReserveMonth() {
    var i = document.phonyForm.month.selectedIndex;
    reserve_time.setMonth(document.phonyForm.month.options[i].value);
    return true;
}

function setReserveYear() {
    var i = document.phonyForm.year.selectedIndex;
    reserve_time.setFullYear(document.phonyForm.year.options[i].value);
    return true;
}

// convert back to Unix timestamp and submit the form
function setReserveDate() {
    // JavaScript timestamp is in milliseconds
    var temptime = Math.floor(reserve_time.getTime() / 1000);
    document.dateForm.d.value = temptime;
    document.dateForm.submit();
    return true;
}
-->
