//@author : tj
function validateSctype() {
	if ($.trim($("#sctname").val()) == "") {
		alert("素材类型名称必须填写");
		$("#sctname").focus();
		return false;
	}
/*	if ($.trim($("#sctdesc").val()) == "") {
		alert("素材类型介绍必须填写");
		$("#sctdesc").focus();
		return false;
	}*/
	return true;
}

$(document).ready(
function() {
	//$(":input[name=upload_img]").click(function() { $("#upload_img_on:checked").val() == 1 ? $("#img_area").show() : $("#img_area").hide() });
	//$(":input[name=upload_flash]").click(function() { $("#upload_flash_on:checked").val() == 1 ? $("#flash_area").show() : $("#flash_area").hide() });
	$("#oForm").submit(validateSctype);
}
);