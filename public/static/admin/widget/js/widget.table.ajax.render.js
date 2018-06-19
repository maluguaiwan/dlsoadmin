/**
 * dataTable render
 */
 (function($){
 	Date.prototype.format=function (fmt) {
	    var o = {
	        "M+": this.getMonth() + 1, //月份
	        "D+": this.getDate(), //日
	        "h+": this.getHours(), //小时
	        "m+": this.getMinutes(), //分
	        "s+": this.getSeconds(), //秒
	        "q+": Math.floor((this.getMonth() + 3) / 3), //季度
	        "S": this.getMilliseconds() //毫秒
	    };
	    if (/([Y]+)/.test(fmt))
	        fmt = fmt.replace(RegExp.$1, (this.getFullYear() + "").substr(4 - RegExp.$1.length));
	    for (var k in o)
	        if (new RegExp("(" + k + ")").test(fmt))
	            fmt = fmt.replace(RegExp.$1, (RegExp.$1.length == 1) ? (o[k]) : (("00" + o[k]).substr(("" + o[k]).length)));
	    return fmt;
} ;
 	$.fn.dataTable.actionRender('formatDate',function(data,type,row){
 		var newDate = new Date();
		newDate.setTime(Number(data) * 1000);
		return newDate.format('YYYY-MM-DD');
 	});
 	$.fn.dataTable.actionRender('formatDateTime',function(data,type,row){
 		if(!isNaN(data)){
 			var newDate = new Date();
			newDate.setTime(Number(data) * 1000);
			return newDate.format('YYYY-MM-DD hh:mm:ss');
 		}else{
 			return data;
 		}
 		
 	});
 })(jQuery);