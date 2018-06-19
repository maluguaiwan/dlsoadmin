/**
 * 公共函数
 */


function layer_alert(msg){
	return layer.alert(msg);
};

function msg(msg,end){
	layer.msg(msg,{time:2000},end?end:function(){});
};

function loading(status,index){
	if(status == 1){
		// 开启loading
		return layer.load(1, {shade: [0.5,'#fff'],shadeClose:false});
	}else{
		// 关闭loading
		if(typeof index !== 'undefined'){
			layer.close(index);
		}else{
			layer.closeAll();
		}
	}
}
