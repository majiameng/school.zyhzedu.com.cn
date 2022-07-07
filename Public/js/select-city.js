
var Region = {
	listCity: function(pid, target) {
		if(!regionJson[pid]) return false;
		var city = regionJson[pid],selectstr='';
		var selectid = $(target).data('select');
		if(!pid) pid = 0;
		var cityStr = '<option value="">请选择</option>';			
		for(var i in city){
			if(city[i]['id']==selectid) {
				selectstr= 'selected';
			}
			else selectstr= '';
			cityStr += '<option value="'+city[i]['id']+'" '+selectstr+'>'+city[i]['name']+'</option>';
		}
		$(target).html(cityStr);
	},
	init: function(obj, childObj, lastObj, inputObj){
		Region.listCity(1, obj);
		Region.citySelect(obj, childObj, lastObj);			
		Region.bind(obj, childObj, lastObj, inputObj);
	}, 
	citySelect: function(obj, childObj, lastObj) {		
		var val = $(obj).data('select');
		if(val == undefined) return false;
		Region.listCity(val, childObj);
		val = $(childObj).data('select');
		if(val == undefined) return false;
		Region.listCity(val, lastObj);
	},
	bind: function(obj, childObj, lastObj, inputObj) {
		$(obj).change(function(){
			var val = $(this).val();
			$(inputObj).val('');
			Region.listCity(val, childObj);				
		});

		$(childObj).change(function(){
			var val = $(this).val();
			$(inputObj).val('');
			Region.listCity(val, lastObj);				
		});
		
		$(lastObj).change(function(){
			$(inputObj).val($(this).val());			
		});
	}
};