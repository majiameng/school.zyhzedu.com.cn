/**
 * 系统公用js
 */

$(function() {
	jQuery.validator
			.addMethod(
					"isMobile",
					function(value, element) {
						var length = value.length;
						var mobile = /^1\d{10}$/;
						/*
						var mobile = /^(((13[0-9]{1})|(15[0-9]{1})|(14[1-9]{1})|(17[0-9]{1})|(18[0-9]{1}))+\d{8})$/;
						*/
						return this.optional(element)
								|| (length == 11 && mobile.test(value));
					}, "请正确填写手机号码");
	jQuery.validator
			.addMethod(
					"TelorMobile",
					function(value, element) {
						var length = value.length;
						var mobile = /^(((13[0-9]{1})|(15[0-9]{1})|(14[1-9]{1})|(17[0-9]{1})|(18[0-9]{1}))+\d{8})$/;
						var tel = /^0+\d{10,11}$/;
						return this.optional(element)
								|| (tel.test(value) || mobile.test(value));
					}, "请正确填写您的固话或手机号码");

	jQuery.validator.addMethod("notEqualTo", function(value, element, param) {
		return value != $(param).val();
	}, $.validator.format("两次输入不能相同!"));
	jQuery.validator.addMethod("onlyLetterAndDigit", function(value, element,
			params) {
		var regex = new RegExp('^[A-Za-z0-9]+$');
		return regex.test(value);
	}, "只能输入字母或数字");

	jQuery.validator.addMethod("isIdCard", function(value, element,
			params) {
		value = $.trim(value);
		var regex = new RegExp('^(\\d{15}|\\d{17}([0-9]|X))$','ig');		
		//var regex = new RegExp('(^[1-9]\d{5}(18|19|([23]\d))\d{2}((0[1-9])|(10|11|12))(([0-2][1-9])|10|20|30|31)\d{3}[0-9Xx]$)|(^[1-9]\d{5}\d{2}((0[1-9])|(10|11|12))(([0-2][1-9])|10|20|30|31)\d{2}$)');
		return regex.test(value);
	}, "身份证错误");
	
	jQuery.validator.addMethod("checkValidCard", function(value, element,
			params) {
		var result = false;
		$.ajax({
			url : "/index.php/home/public/checkValidCard",
			type : "POST",
			data : {
				card : value
			},
			success : function(data) {
				result = data;
			}
		});
		return result;
	});
	jQuery.validator.addMethod("onlyLetterAndChinese", function(value, element,
			params) {
		var regex = new RegExp('^[\\u4E00-\\u9FA5\\uF900-\\uFA2D\\w]');
		return regex.test(value);
	}, "只能输入字母或汉字");

	jQuery.validator.addMethod("noContainsSpace", function(value, element,
			params) {
		if(value.indexOf(" ")!=-1){
			return false;
		}else{
			return true;
		}
	}, "不能包含空格");

	jQuery.validator.addMethod("isContainDig", function(value, element){
		var pattern = new RegExp("[`~!@%#$^&*()=|{}':;',　\\[\\]<>/? \\.；：%……+￥（）【】‘”“'。，、？]"); 
		return this.optional(element)||!pattern.test(value) ;
	}, "不允许包含特殊符号!");

	jQuery.validator.addMethod("mustContainChinese", function(value, element){
		return /[\u4e00-\u9fa5]/g.test(value);
	}, "必须至少有一个汉字!");

	jQuery.validator.addMethod("stringCheck", function(value, element) {
		return this.optional(element) || /^[\u0391-\uFFE5\w]+$/.test(value);
	}, "只能包括中文字、英文字母、数字和下划线");

	// 电话号码验证
	jQuery.validator.addMethod("isTel", function(value, element) {
		var tel = /^\d{3,4}-?\d{7,9}$/; // 电话号码格式010-12345678
		return this.optional(element) || (tel.test(value)); 
	}, "请正确填写您的电话号码");

	jQuery.validator.addMethod("onlyDigit", function(value, element, params) {
		var regex = new RegExp('^[0-9]+$');
		return regex.test(value);
	}, "只能输入数字");

	jQuery.validator
			.addMethod(
					"MobileOrTel",
					function(value, element) {
						var length = value.length;
						var mobile = /^(((13[0-9]{1})|(15[0-9]{1})|(14[1-9]{1})|(17[0-9]{1})|(18[0-9]{1}))+\d{8})$/;
						var tel = /^\d{3,4}-?\d{7,9}$/;
						return this.optional(element)
								|| (tel.test(value) || mobile.test(value));
					}, "请正确填写您的固话或手机号码");

	jQuery.validator.addMethod("byteRangeLength", function(value, element,
			param) {
		var length = value.length;
		for ( var i = 0; i < value.length; i++) {
			if (value.charCodeAt(i) > 127) {
				length++;
			}
		}
		return this.optional(element)
				|| (length >= param[0] && length <= param[1]);
	}, jQuery.validator.format("请确保输入的值在{0}-{1}个汉字之间(一个中文字算2个字节)"));

	// 邮政编码验证
	jQuery.validator.addMethod("isZipCode", function(value, element) {
		var tel = /^[0-9]{6}$/;
		return this.optional(element) || (tel.test(value));
	}, "请正确填写您的邮政编码");

	jQuery.validator.addMethod("isMoney", function(value, element) {
		var money = /^(([1-9]\d{0,9})|0)(\.\d{1,2})?$/;
		return this.optional(element) || money.test(value);
	}, "请输入正确的金额，如8.88");

	//应用名称中敏感词验证
	jQuery.validator.addMethod("isContainBlackWord", function(value, element) {
		var forbiddenArray=["回拨","VOIP","透传","国际","强显","透手机","透固话","透400","群呼","变号"];
        var re = '';
        for(var i=0;i<forbiddenArray.length;i++){
            if(i==forbiddenArray.length-1)
                re+=forbiddenArray[i];
            else
                re+=forbiddenArray[i]+"|";
        }
         //定义正则表示式对象
        //利用RegExp动态生成正则表示式
         var pattern = new RegExp(re,"gi");
         if(pattern.test(strTrim(value,"g"))){
             return false;
         }else{
             return true;
         }
	}, "应用名称请勿包含敏感词，请重新填写");

	// 输入是否含有中文
	jQuery.validator.addMethod("isNotChinese",
			function(value, element, params) {
				var patrn = /[\u4E00-\u9FA5]|[\uFE30-\uFFA0]/gi;
				if (!patrn.exec(value)) {
					return true;
				} else {
					return false;
				}

			}, "输入不能含有汉字");
	
	jQuery.validator.addMethod("validNickName",
			function(value, element, params) {
				// var regex = new RegExp('^[A-Za-z0-9_@]+$');
				var regex = new RegExp(
						'^[\u4e00-\u9fa5]{2,6}$|^[a-zA-Z]+[A-Za-z0-9-_@]+$');
				return regex.test(value);
			}, "只能输入字母、数字、_和@");

// 
	jQuery.validator.addMethod("checkusername",
			function(value, element, params) {
				// var regex = new RegExp('^[A-Za-z0-9_@]+$');
				var regex = new RegExp(
						'^[A-Za-z0-9]+$');
				return regex.test(value);
			}, "只能输入字母、数字");

	jQuery.validator.addMethod("checkpwd",
			function(value, element, params) {
				// var regex = new RegExp('^[A-Za-z0-9_@]+$');
				var regex = new RegExp(
						'^[A-Za-z0-9]{6,16}$');
				return regex.test(value);
			}, "只能输入6-18字母、数字");
	// 手机或邮箱可以登录
	jQuery.validator
			.addMethod(
					"isEmailOrMobel",
					function(value, element, params) {
						var mobile = /^(((13[0-9]{1})|(15[0-9]{1})|(14[1-9]{1})|(17[0-9]{1})|(18[0-9]{1}))+\d{8})$/;
						var email = /^([a-zA-Z0-9_\.\-])+@([a-zA-Z0-9_\.\-])+\.[a-zA-Z]{2,4}$/;
						return (email.test(value) || mobile.test(value));
					}, "输入错误");
	// 后台验证邮箱的有效性
	jQuery.validator.addMethod("checkValidEmail", function(value, element,
			params) {
		var result = false;
		$.ajax({
			url : "../../user/reg/checkValidEmail",
			async : false,
			type : "POST",
			data : {
				email : value
			},
			dataType : "json",
			success : function(data) {
				result = data;
			}
		});
		return result;
	});
	
	$.validator.addMethod("accept", function(value, element, param) {
		// Split mime on commas in case we have multiple types we can accept
		var typeParam = typeof param === "string" ? param.replace(/\s/g, "").replace(/,/g, "|") : "image/*",
		optionalValue = this.optional(element),
		i, file;

		// Element is optional
		if (optionalValue) {
			return optionalValue;
		}

		if ($(element).attr("type") === "file") {
			// If we are using a wildcard, make it regex friendly
			typeParam = typeParam.replace(/\*/g, ".*");

			// Check if the element has a FileList before checking each file
			if (element.files && element.files.length) {
				for (i = 0; i < element.files.length; i++) {
					file = element.files[i];

					// Grab the mimetype from the loaded file, verify it matches
					if (!file.type.match(new RegExp( "\\.?(" + typeParam + ")$", "i"))) {
						return false;
					}
				}
			}
		}

		// Either return true because we've validated each file, or because the
		// browser does not support element.files and the FileList feature
		return true;
	}, $.validator.format("Please enter a value with a valid mimetype."));
});

function strTrim(str,is_global)
{
    var result;
    result = str.replace(/(^\s+)|(\s+$)/g,"");
    if(is_global.toLowerCase()=="g")
    {
        result = result.replace(/\s/g,"");
     }
    return result;
}

String.prototype.trim = function() {
	return this.replace(/(^\s*)|(\s*$)/g, "");
};
String.prototype.ltrim = function() {
	return this.replace(/(^\s*)/g, "");
};
String.prototype.rtrim = function() {
	return this.replace(/(\s*$)/g, "");
};


function IsURL(str_url) {
	var strRegex = "^((https|http|ftp|rtsp|mms)://)"
	+ "(([0-9]{1,3}\.){3}[0-9]{1,3}" // IP形式的URL- 199.194.52.184
			+ "|" // 允许IP和DOMAIN（域名）
			+ "([0-9a-zA-Z_!~*'()-]+\.)*" // 域名- www.
			+ "([0-9a-zA-Z][0-9a-zA-Z-]{0,61})?[0-9a-zA-Z]\." // 二级域名
			+ "[a-zA-Z]{2,6})" // first level domain- .com or .museum
			+ "(:[0-9]{1,4})?" // 端口- :80
			+ "((/?))";
	var re = new RegExp(strRegex);
	if (re.test(str_url)) {
		return (true);
	} else {
		return (false);
	}
}
function isTelOrMobel(number) {
	var mobile = /^(((13[0-9]{1})|(15[0-9]{1})|(14[1-9]{1})|(17[0-9]{1})|(18[0-9]{1}))+\d{8})$/;
	var tel = /^0+\d{10,11}$/;
	return (tel.test(number) || mobile.test(number));
}

/**
 * 判断是否包含全角字符
 * 
 * @param str
 * @returns {Boolean}
 */
function chkHalf(str) {
	for ( var i = 0; i < str.length; i++) {
		strCode = str.charCodeAt(i);
		if ((strCode > 65248) || (strCode == 12288)) {
			return false;
		}
	}
	return true;
}

// 全角转换为半角函数
function toCDB(str) {
	var tmp = "";
	for ( var i = 0; i < str.length; i++) {
		if ("，。！".indexOf(str.charCodeAt(i))<0&&str.charCodeAt(i) > 65248 && str.charCodeAt(i) < 65375) {
			tmp += String.fromCharCode(str.charCodeAt(i) - 65248);
		} else {
			tmp += String.fromCharCode(str.charCodeAt(i));
		}
	}
	return tmp;
}
