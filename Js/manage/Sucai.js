//@author : tj
function validateSucai() {
	if ($("#idxm").val() == 0) {
		alert("请选择项目");
		$("#idxm").focus();
		return false;
	}
	if ($("#idsctype").val() == 0) {
		alert("请选择素材类型");
		$("#idsctype").focus();
		return false;
	}
	var title = $.trim($("#sctitle").val());
	if (title == "") {
		alert("素材标题必须填写");
		$("#sctitle").focus();
		return false;
	}
	var reg = new RegExp("[" + getDenyChar() + "]");
	if (reg.test(title)) {
		alert("素材标题不合法，含有禁用字符");
		return false;
	}
	return true;
}

function set_opt_XM() {
	slt = $("#idxm")[0];
	clearOption(slt);
	hyid = $("#idhy").val();
	$.getJSON("/manage/sucai/getjson?hyid=" + hyid, function(data) {
		if (data) {	$.each(data, function(i,item) { slt.options.add(new Option(item, i)); }); }
	});
}

function set_area() {
	id = $("#idsctype").val();
	$.getJSON("/manage/sucai/getjson?sctid=" + id, function(data) {
		if (data) {
			$("#title_length").val(data.title_length);
			$("#desc_length").val(data.desc_length);
			$("#desc2_length").val(data.desc2_length);
			$("#desc3_length").val(data.desc3_length);
			$("#allow_char").val(data.allow_char);
			data.allow_char & 1 ? $("#sign_bj_area").show() : $("#sign_bj_area").hide();
			data.allow_char & 2 ? $("#sign_qj_area").show() : $("#sign_qj_area").hide();
			data.allow_char & 4 ? $("#sign_sp_area").show() : $("#sign_sp_area").hide();
			$("#img_width").val(data.img_width);
			$("#img_height").val(data.img_height);
			data.has_img == 1 ? $("#scpicfile_area").show() : $("#scpicfile_area").hide();
			$("#flash_width").val(data.flash_width);
			$("#flash_height").val(data.flash_height);
			data.has_flash == 1 ? $("#scflashfile_area").show() : $("#scflashfile_area").hide();
		}
	});
}

function getDenyChar() {
	var ac = $("#allow_char").val();
	var str = "";
	if (!(ac & 1)) str += $("#sign_bj").val();
	if (!(ac & 2)) str += $("#sign_qj").val();
	if (!(ac & 4)) str += $("#sign_sp").val();
	return str;
}

function clearOption(slt) {
	slt.selectedIndex = 0;
	slt.options.length = 1;
}

$(document).ready(
function() {
	$("#idhy").change(set_opt_XM);
	set_area();
	$("#idsctype").change(set_area);
	$("#oForm").submit(validateSucai);
}
);