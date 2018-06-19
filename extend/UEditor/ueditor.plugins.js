UE.registerUI('callphone',function(editor,uiName){
    //创建dialog
    var dialog = new UE.ui.Dialog({
        //指定弹出层中页面的路径，这里只能支持页面,因为跟addCustomizeDialog.js相同目录，所以无需加路径
        iframeUrl:UEDITOR_HOME_URL+'dialogs/callphone/callphone.html',
        //需要指定当前的编辑器实例
        editor:editor,
        //指定dialog的名字
        //dialog的标题
        name:'callphone',
        title:"一键拨号",
        //指定dialog的外围样式
        cssRules:"width: 300px;height: 211px;",
        //如果给出了buttons就代表dialog有确定和取消
        buttons:[
            {
                className:'edui-okbutton',
                label:'确定',
                onclick:function () {
                    dialog.close(true);
                }
            },
            {
                className:'edui-cancelbutton',
                label:'取消',
                onclick:function () {
                    dialog.close(false);
                }
            }
        ]
    });
    editor.ui._dialogs.phoneDialog=dialog;
});

UE.registerUI('mobile-preview',function(editor,uiName){
	 editor.registerCommand('gettitle',{
	        execCommand:function(cmdName){
	        	console.log($('#title').val());
	        	return $('#title').val();
	        }
	 });
   //创建dialog
   var dialog = new UE.ui.Dialog({
       //指定弹出层中页面的路径，这里只能支持页面,因为跟addCustomizeDialog.js相同目录，所以无需加路径
       iframeUrl:UEDITOR_HOME_URL+'dialogs/preview/preview.html',
       //需要指定当前的编辑器实例
       editor:editor,
       //指定dialog的名字
       className:'edui-for-mobile-preview',
       //dialog的标题
       title:"预览",

       //指定dialog的外围样式
       cssRules:"width:600px;height:300px;",
   });
   editor.ui._dialogs.previewDialog=dialog;
});

UE.registerUI('insertform',function(editor,uiName){
	editor.registerCommand('insertform',{
		execCommand:function(cmdName,data){
			var me = this;
			var text=editor.getContent();
			var reg=new RegExp("_FORM_BROKE_TAG_");
			reg.test(text)?'':(editor.execCommand('inserthtml','<input class="_FORM_BROKE_TAG_" type="text" style="width:90%;text-align: center;    display: block; margin: 0 auto;" readonly="readonly" value="表单站位"/>'),
			$('._FORM_BROKE_TAG_').click(function(){
				dialog.render();
	            dialog.open();
			})		
			);
			$('#formdata').val(JSON.stringify(data));
		}
	});
	 editor.registerCommand('getform',{
	        execCommand:function(cmdName){
	        	var data=$('#formdata').val();
	        	return data?JSON.parse(data):undefined;
	        }
	    });
	 
    //创建dialog
    var dialog = new UE.ui.Dialog({
        //指定弹出层中页面的路径，这里只能支持页面,因为跟addCustomizeDialog.js相同目录，所以无需加路径
        iframeUrl:UEDITOR_HOME_URL+'dialogs/form/form.html',
        //需要指定当前的编辑器实例
        editor:editor,
        //指定dialog的名字
        className:'edui-for-insertform',
        //dialog的标题
        title:"编辑表单",

        //指定dialog的外围样式
        cssRules:"width:600px;height:300px;",

        //如果给出了buttons就代表dialog有确定和取消
        buttons:[
            {
                className:'edui-okbutton',
                label:'确定',
                onclick:function () {
                    dialog.close(true);
                }
            },
            {
                className:'edui-cancelbutton',
                label:'取消',
                onclick:function () {
                    dialog.close(false);
                }
            }
        ]});
    editor.ui._dialogs.formDialog=dialog;
});

UE.registerUI('imageradius',function(editor,uiName){
    //注册按钮执行时的command命令，使用命令默认就会带有回退操作
    editor.registerCommand(uiName,{
        execCommand:function(){
            var me = this,
                range = me.selection.getRange();
            if (!range.collapsed) {
                var img = range.getClosedNode();
                if (img && img.tagName == 'IMG') {
                	var width='90%',radius='10px',center=true;
					if(!confirm('是否按默认设置?')){
						width=prompt('宽度:','90%');
                        radius=prompt('圆角度数:','10px');
					}
					img.style.borderRadius=radius;
					img.style.width=width;
                }
            }
        }
    });

    //创建一个button
    var btn = new UE.ui.Button({
        //按钮的名字
        name:uiName,
        //提示
        title:'图片圆角',
        //需要添加的额外样式，指定icon图标，这里默认使用一个重复的icon
        cssRules :'',
        //点击时执行的命令
        onclick:function () {
            //这里可以不用执行命令,做你自己的操作也可
           editor.execCommand(uiName);
        }
    });

    //当点到编辑内容上时，按钮要做的状态反射
    editor.addListener('selectionchange', function () {
        var range = editor.selection.getRange(),
            startNode;
        if (range.collapsed) {
        	btn.setDisabled(true);
        }
        startNode = range.getClosedNode();
        if (startNode && startNode.nodeType == 1 && startNode.tagName == 'IMG') {
        	 btn.setDisabled(false);
        }else{
        	btn.setDisabled(true);
        }
    });

    //因为你是添加button,所以需要返回这个button
    return btn;
});

UE.registerUI('imagefull',function(editor,uiName){
    //注册按钮执行时的command命令，使用命令默认就会带有回退操作
    editor.registerCommand(uiName,{
        execCommand:function(){
            var me = this,
                range = me.selection.getRange();
            if (!range.collapsed) {
                var img = range.getClosedNode();
                if (img && img.tagName == 'IMG') {
                	img.style.width='100%';
            		img.style.height='auto';
                }
            }
        }
    });

    //创建一个button
    var btn = new UE.ui.Button({
        //按钮的名字
        name:uiName,
        //提示
        title:'图片100%显示',
        //需要添加的额外样式，指定icon图标，这里默认使用一个重复的icon
        cssRules :'',
        //点击时执行的命令
        onclick:function () {
            //这里可以不用执行命令,做你自己的操作也可
           editor.execCommand(uiName);
        }
    });

    //当点到编辑内容上时，按钮要做的状态反射
    editor.addListener('selectionchange', function () {
        var range = editor.selection.getRange(),
            startNode;
        if (range.collapsed) {
        	btn.setDisabled(true);
        }
        startNode = range.getClosedNode();
        if (startNode && startNode.nodeType == 1 && startNode.tagName == 'IMG') {
        	 btn.setDisabled(false);
        }else{
        	 btn.setDisabled(true);
        }
        
        var img = range.getClosedNode();
        if (img && img.tagName == 'IMG') {
        	btn.setChecked(img.style.width=='100%');
        }
    });

    //因为你是添加button,所以需要返回这个button
    return btn;
});




UE.registerUI('link',function(editor,uiName){
	//创建dialog
	var dialog = new UE.ui.Dialog({
		//指定弹出层中页面的路径，这里只能支持页面,因为跟addCustomizeDialog.js相同目录，所以无需加路径
		iframeUrl:UEDITOR_HOME_URL+'dialogs/link/link.html',
		//需要指定当前的编辑器实例
		editor:editor,
		//指定dialog的名字
		className:'edui-for-link',
		//dialog的标题
		title:"超链接",
		
		//指定dialog的外围样式
		cssRules:"width:600px;height:300px;",
		
		//如果给出了buttons就代表dialog有确定和取消
		buttons:[
		         {
		        	 className:'edui-okbutton',
		        	 label:'确定',
		        	 onclick:function () {
		        		 dialog.close(true);
		        	 }
		         },
		         {
		        	 className:'edui-cancelbutton',
		        	 label:'取消',
		        	 onclick:function () {
		        		 dialog.close(false);
		        	 }
		         }
		         ]});
	editor.ui._dialogs.linkDialog=dialog;
	editor.ui._dialogs.insertimageDialog=dialog;
});

UE.registerUI('insertproduct',function(editor,uiName){
    //注册按钮执行时的command命令，使用命令默认就会带有回退操作
    editor.registerCommand(uiName,{
        execCommand:function(cmd, opt){
            opt = UE.utils.isArray(opt) ? opt : [opt];
            if (!opt.length) {
                return;
            }
            var me = this,
                range = me.selection.getRange(),
                img = range.getClosedNode();

            if(me.fireEvent('beforeinsertimage', opt) === true){
                return;
            }
            var html = [], str = '', ci;
            ci = opt[0];

            str = '<a class="product" href="'+ci.product.url+'" data-product-id="'+ci.product.id+'" data-product-title="'+ci.product.title+'"><img class="product" src="' + ci.src + '" ' + (ci._src ? ' _src="' + ci._src + '" ' : '') +
                (ci.width ? 'width="' + ci.width + '" ' : '') +
                (ci.height ? ' height="' + ci.height + '" ' : '') +
                (ci['floatStyle'] == 'left' || ci['floatStyle'] == 'right' ? ' style="float:' + ci['floatStyle'] + ';"' : '') +
                (ci.title && ci.title != "" ? ' title="' + ci.title + '"' : '') +
                (ci.border && ci.border != "0" ? ' border="' + ci.border + '"' : '') +
                (ci.alt && ci.alt != "" ? ' alt="' + ci.alt + '"' : '') +
                (ci.hspace && ci.hspace != "0" ? ' hspace = "' + ci.hspace + '"' : '') +
                (ci.vspace && ci.vspace != "0" ? ' vspace = "' + ci.vspace + '"' : '') + '/></a>';
            if (ci['floatStyle'] == 'center') {
                str = '<p style="text-align: center">' + str + '</p>';
            }
            html.push(str);

            me.execCommand('insertHtml', html.join(''));
        
            me.fireEvent('afterinsertimage', opt)
        
        }
    });

    //创建一个button
    var btn = new UE.ui.Button({
        //按钮的名字
        name:uiName,
        //提示
        title:'图片圆角',
        //需要添加的额外样式，指定icon图标，这里默认使用一个重复的icon
        cssRules :'',
        //点击时执行的命令
        onclick:function () {
            //这里可以不用执行命令,做你自己的操作也可
           editor.execCommand(uiName);
        }
    });

    //当点到编辑内容上时，按钮要做的状态反射
    editor.addListener('selectionchange', function () {
        var range = editor.selection.getRange(),
            startNode;
        if (range.collapsed) {
        	btn.setDisabled(true);
        }
        startNode = range.getClosedNode();
        if (startNode && startNode.nodeType == 1 && startNode.tagName == 'IMG') {
        	 btn.setDisabled(false);
        }else{
        	btn.setDisabled(true);
        }
    });

    //因为你是添加button,所以需要返回这个button
    return btn;
});