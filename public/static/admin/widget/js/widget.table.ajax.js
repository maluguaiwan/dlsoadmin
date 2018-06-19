(function($){
    var renders={},buttons=[],init=false;;
    $.fn.dataTable.actionRender = function (render,fn) {
     if(render && fn){
        renders[render]=fn;
     }else{
        return function(data,type,row){
            if(renders[render]) return renders[render](data,type,row);
            return data;
        }
     }   
    };
    $.fn.dataTable.execute=function(id,index,data,row){
        if(buttons[Number(id)]){
            var callback=buttons[Number(id)];
            var object=callback.obj?callback.obj:window;
            if(typeof callback.fn=='function'){
                callback.fn.call(object,data,index,function(data){
                    row.data(data);
                    row.invalidate();
                });
            }else if(typeof callback.fn=='string'){
                object[callback.fn]&&object[callback.fn].call(object,data,index,function(data){
                    row.data(data);
                    row.invalidate();
                });
            }
        }
    };
    $.fn.dataTable.button=function(title,icon,fn,object,buttonClass){
        if(typeof buttonClass =='undefined'){
            buttonClass='';
        }
        var index=buttons.length;
        buttons.push({fn:fn,obj:object});
        return '&nbsp;<button data-button="'+index+'" class="btn btn-default btn-table-click '+buttonClass+'" ><i class="icon-'+icon+'"></i>'+title+'</button>';
    };

    $.fn.dataTable.buttonGroup=function(title,icon,buttonClass,buttonMenus){
        if(typeof buttonClass =='undefined'){
            buttonClass='';
        }

        var html='&nbsp;<div class="btn-group"><button  type="button" class="btn btn-default dropdown-toggle '+buttonClass+'" data-toggle="dropdown"><i class="icon-'+icon+'"></i>'+title+'<span class="caret"></span></button>';
        html+='<ul class="dropdown-menu" role="menu">';
        buttonMenus.forEach(function(button){
            if(button.type && button.type=='divider'){
                html+='<li class="divider"></li>';
            }else{
                var index=buttons.length;
                buttons.push({fn:button.fn,obj:button.object});
                html+=' <li><a class="btn-table-click" href="#" data-button="'+index+'">'+button.title+'</a></li>';
            }
        });
        html+=' </ul></div>';
       return html;
    };

})(jQuery);
(function($){
    
})(jQuery);
var AjaxTable = function(options){
	this.options=options;
	this.id='#'+options.id;
	var _self = this;
	this.totalDataCount=-1;
    this.operate = function(options){
    	if(!options.unchecker){
    		$(_self.id).find('input:checkbox').uniform();
    	}
    };
    this.createOptions = function(options){
        var defaultOptions = {
            columnDefs:[],
            "bPaginate": true, // 翻页功能
            "processing": false,
            "bProcessing":false,
            "bServerSide": true,
            'sAjaxSource': '',
            'bAutoWidth':false,
            initComplete:function(){
                if(options.advSearchInTable){
                    $('#adv_search_div').appendTo('.table-search-row .col-sm-12');
                }
            },
            'fnServerData': function(sSource, aoData, fnCallback){
                _self._table=this;
                _self.retrieveData(this,sSource, aoData, fnCallback, result.dataCallback);
            },
            drawCallback:function(){
                //alert(1);
                return false;
            },
            preDrawCallback:function(){
                //return false;
            },
            'dataCallback': function(){
            },
            "aLengthMenu": [[10, 50, 100, 200, 2000], [10, 50, 100, 200,2000]],
            "iDisplayLength": 10,
            "bFilter": true,
            "bStateSave": true,//翻页记录cookie
            "aoPreDrawCallback":function(){
                return false;
            },
            "fnHeaderCallback":function(nHead, aData, iStart, iEnd, aiDisplay){
			},
			"fnStateLoaded":function(oSettings, oData,oInit){
				if(oData==null){
					oInit.saved_aoColumns = [];
					var checks=$(_self.id+'_column_toggler input:checkbox');
					for ( var i=0 ; i<checks.length ; i++ )
					{
						oInit.saved_aoColumns[i] = {};
						var check=checks.eq(i);
						var visble=check.prop('checked');
						oInit.saved_aoColumns[i].bVisible = visble;
					}
				}
			},
			"fnFooterCallback":function(nFoot, aData, iStart, iEnd, aiDisplay){
                if($('#table_footer').length==0)
                    return;
				var columnGroup=$(_self.id).data('column-group');
				if(!columnGroup){
					$('#table_footer').hide();
					return false;
				}
				$('tfoot td').each(function(index,td){
					$(td).html('--');
				});
				var ths=$('thead th.column-group');
				ths.each(function(index,th){
					var classList=$(th).get(0).classList;
					var columnIndex=$(th).index();
					for(var i =0;i<classList.length;i++){
						var clazz=classList[i];
						if(clazz&&clazz.indexOf('column-group-')==0){
							var fun=clazz.replace('column-group-','');
							if(window.columnGroupFunctions[fun]){
								window.columnGroupFunctions[fun](columnIndex,nFoot, aData, iStart, iEnd, aiDisplay);
							}
						}
					}
				});
			}
        };
        var result = $.extend({'search_label_name':'检索'}, defaultOptions, options);
        function map(def){
            if(def.render && typeof def.render =='string'){
                def.render=eval(def.render);
            }
             return def;
        }

        result.columnDefs=result.columnDefs.map(map);
        result.aoColumnDefs&&(result.aoColumnDefs=result.aoColumnDefs.map(map));
        return result;
    };
    this.retrieveData = function(oTable,sSource, aoData, fnCallback, dataCallback){
        var obj = {};
        for (var i in aoData) {
            obj[aoData[i].name] = aoData[i].value;
        }
        console.log(obj);

        var firstRow = obj.iDisplayStart;
        var listRows = obj.iDisplayLength;
        var order_field = obj["mDataProp_" + obj.iSortCol_0];
        var order_type = obj.sSortDir_0;
        if(!obj['bSortable_'+obj.iSortCol_0]){
            order_field='';
        }
        var up_data = {};
        up_data.sSearch = obj.sSearch;
        up_data.advSearch = $('#adv_search_input').val();
        up_data.firstRow = firstRow;
        up_data.listRows = listRows;
        up_data.order_field = order_field;
        up_data.order_type = order_type;
        up_data.ajax = 1;
        $.ajax({
            type: "POST",
            url: sSource,
            beforeSend: function(){
                //$("#loading_ajax").show();
            },
            complete: function(){
                //$("#loading_ajax").hide();
            },
            dataType: "json",
            data: up_data,
            success: function(data){
                if (data.aaData == null) {
                    data.aaData = new Array();
                }
                if(_self.totalDataCount==-1){
                	_self.totalDataCount=Number(data.iTotalDisplayRecords);
                }
                fnCallback(data); // 服务器端返回的对象的returnObject部分是要求的格式
                if(typeof dataCallback == 'string'){
					window[dataCallback]&&window[dataCallback]();
				}else if(typeof dataCallback =='function'){
					dataCallback&&dataCallback();
				}
                _self.operate(options);
                _self._table.fnAdjustColumnSizing(false);

                
            }
        });
    };
    this.createFilter = function(options,oTable){
        var sSearchDefault = $('#sSearch');
    	$('#'+oTable.fnSettings().sTableId+"_wrapper .table-filter").after('<input id="adv_search_input" type="hidden">')
    	if(options.is_default_search=='show'||options.is_default_search=='1'){
    	}else{
    		$(_self.id+'_filter').hide();
    	}
    	
    	if(options.report){
    		$('#dt_wrapper').children().first().children().last().prepend('<div class="report_table" id="report_table" style="float: right;margin-right: 1px;margin-left: 20px;;"><a class="btn blue" href="'+options.report+'">导出</a></div>');
    	}
    	if($('.tools').length>0){
             //高级查询
            jQuery(".tools a").click(function(){
                var obj = $("#adv_search_div");
                if ($.trim(obj.html())) {
                    obj.toggle()
                }
            });

        }
       
    };
    this.createColumnVisibleProcessor = function(options, oTable){
        var columnController=$(_self.id+'_column_toggler input:checkbox');
        if(columnController.length==0){
            return;
        }
        var temp = oTable.fnSettings();
        console.log(temp);
        var aoColumns = oTable.fnSettings().aoColumns;
        for (var i in aoColumns) {
            var column = aoColumns[i];
            columnController.eq(parseInt(i)).attr('checked', column.bVisible);
        }
        
        $(_self.id+'_column_toggler input:checkbox').change(function(){
            var iCol = parseInt($(this).attr("data-column"));
            var bVis = oTable.fnSettings().aoColumns[iCol].bVisible;
            oTable.fnSetColumnVis(iCol, $(this).prop('checked'));
        });
        
    };
    this.refreshData=function(){
    	_self._table.fnPageChange(0,false);
		_self._table.fnDraw(false);
    }
    this.getTable=function(){
    	return this._table;
    };
    this.remove=function(index){

    };
    this.column=function(index){
    	return this._table.api().column(index);
    };
    this.data=function(index,data,update){
        if(typeof index=='undefined'&& typeof data=='undefined'){
            return this._table.api().rows().data();
        }else if(typeof index !='undefined' && typeof data=='undefined'){
            return this._table.api().row(index).data();
        }else if(typeof index!='undefined' && typeof data !='undefined'){
            this._table.api().row(index).data(data);
            if(update!=false){
                this._table.api().row(index).invalidate();
            }
        }
    };
    this.draw=function(){
        this._table.api().rows().draw(false);
    };
    this.createButtonEvent=function(options){
        if(options.useButtons){
            $('#'+options.id+' tbody').on('click','.btn-table-click',function(){
                var tr=$(this).closest('tr');
                var data=_self.data(tr);
                var index=tr.index();
                var id=$(this).data('button'); 
                var row=_self._table.api().row(tr); 
                $.fn.dataTable.execute(id,index,data,row);
            });
        }
    };
    this.init = function(){
        var options= this.createOptions(this.options);
        var oTable = $(_self.id).dataTable(options);
        this.createButtonEvent(options);
		this._table=oTable;
        jQuery(_self.id+' .group-checkable').change(function(){
            var set = jQuery(this).attr("data-set");
            var checked = jQuery(this).is(":checked");
            jQuery(set).each(function(){
                if (checked) {
                    $(this).attr("checked", true);
                }
                else {
                    $(this).attr("checked", false);
                }
            });
            jQuery.uniform.update(set);
        });
        this.createColumnVisibleProcessor(options,oTable);
        this.createFilter(options,oTable);
    };
    window.columnGroupFunctions={
        	sum:function(columnIndex,nFoot, aData, iStart, iEnd, aiDisplay){
        		var totalData=0;
        		for(var i =0;i<aData.length;i++){
        			var columnData=	_self._table.fnGetData(i,columnIndex);
        			var number=parseInt(columnData);
        			if(typeof number =='number'){
        				totalData+=number;
        			}
        		}
        		nFoot.getElementsByTagName('td')[columnIndex].innerHTML = "sum:"+totalData;
        	},
        	sumFloat:function(columnIndex,nFoot, aData, iStart, iEnd, aiDisplay){
        		var totalData=0;
        		for(var i =0;i<aData.length;i++){
        			var columnData=	_self._table.fnGetData(i,columnIndex);
        			var number=parseFloat(columnData);
        			if(typeof number =='number'){
        				totalData+=number;
        			}
        		}
        		nFoot.getElementsByTagName('td')[columnIndex].innerHTML = "sum:"+totalData;
        	},
        	avg:function(columnIndex,nFoot, aData, iStart, iEnd, aiDisplay){
        		var totalData=0;
        		for(var i =0;i<aData.length;i++){
        			var columnData=	_self._table.fnGetData(i,columnIndex);
        			var number=parseInt(columnData);
        			if(typeof number =='number'){
        				totalData+=number;
        			}
        		}
        		nFoot.getElementsByTagName('td')[columnIndex].innerHTML = "avg:"+(totalData/(iEnd-iStart));
        	}
        };
};
