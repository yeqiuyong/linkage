function initDatePlugin(){
    $('.form_date').datetimepicker({
        language:  'fr',
        weekStart: 1,
        todayBtn:  1,
        autoclose: 1,
        todayHighlight: 1,
        startView: 2,
        minView: 2,
        forceParse: 0
    });
}

function initChartXaxis4Mon(offset){
    var xaxis = [];
    var selectMon = getSelectedMon(offset);

    xaxis.push([12, selectMon]);
    for(var i =11 ; i>0; i--){
        selectMon = getDateStr4Mon(selectMon);
        var myDate = [i, selectMon];

        xaxis.push(myDate);
    }

    return xaxis;
}

function getSelectedMon(offset){
    var today = new Date(offset * 1000);

    var strYear = today.getFullYear();
    var strMonth = today.getMonth() + 1;

    if(parseInt(strMonth,10) < 10){
        strMonth="0"+strMonth;
    }

    return strYear+"-"+strMonth;
}

function getDateStr4Week(offset, dateCnt) {
    var cnt = 7 - dateCnt;
    offset = (offset - cnt * 86400) * 1000;

    var newDate = new Date();
    newDate.setTime(offset);

    return newDate.toLocaleDateString();
}

function getDateStr4Mon(thisMon){
    var strYear = parseInt(thisMon.substr(0,4),10);
    var strMonth = parseInt(thisMon.substr(5,7),10);

    if(strMonth - 1 == 0){
        strYear -= 1;
        strMonth = 12;
    } else {
        strMonth -= 1;
    }
    if(strMonth<10){
        strMonth="0"+strMonth;
    }

    var monthstr = strYear+"-"+strMonth;

    return monthstr;

}