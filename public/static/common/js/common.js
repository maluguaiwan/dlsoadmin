/**
 * 公共函数库
 */

function msg(msg){
	layer.msg(msg);
}

function loading(show,index){
	if(show){
		return layer.load(1, {shade: [0.1,'#fff']});
	}else if(typeof index != 'undefined'){
		layer.close(index);
	}
}

// 字符串去空格（全部）
function strTrim(str){
   	return str.replace(/(^\s*)|(\s*$)/g,"");
}