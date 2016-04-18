function initOrderCountByTypeTable(func, page, pageindex, orderType, orderSubType, tableTag){
    var strtable = '<table class="table table-striped table-bordered bootstrap-datatable datatable responsive">';
    strtable += '<thead><tr> <th>公司名</th> <th>注册时间</th> <th>'+ orderType +'</th> <th>'+ orderSubType +'</th> </tr> </thead>';

    var register_time = new Date();
    for (var i = 0; i < page.items.length; i++) {
        register_time.setTime((parseInt(page.items[i].create_time) ) * 1000);

        strtable += "<tr>";
        strtable += "<td>" + page.items[i].company_name + "</td>";
        strtable += "<td>" + register_time.toDateString() + "</td>";
        strtable += "<td>" + page.items[i].order_num + "</td>";
        strtable += "<td>" + page.items[i].sub_order_num + "</td>";
        strtable += "</tr>";
    }

    strtable += '</table>';

    strtable += '<ul class="pagination pagination-centered">';
    strtable += '<li><a href="#" onclick="'+ func +'('+page.before+','+ orderType +','+ orderSubType +','+tableTag+')">Prev</a></li>';

    for (var i = 0; i < page.total_pages; i++) {
        var index  = i + 1;
        if(index == pageindex){
            strtable += '<li class="active"><a href="#" onclick="'+ func +'('+index+','+ orderType +','+ orderSubType +','+tableTag+')">'+index+'</a></li>';
        }else{
            strtable += '<li><a href="#" onclick="'+ func +'('+index+','+ orderType +','+ orderSubType +','+tableTag+')">'+index+'</a></li>';
        }
    }

    strtable += '<li><a href="#" onclick="'+ func +'('+page.before+','+ orderType +','+ orderSubType +','+tableTag+')">Next</a></li>';
    strtable +='</ul>';

    $("#" + tableTag).html(strtable);
}