//@author : tj
var edit = false;
var arr_mb_path = [];
var arr_xm_name = [];
var arr_hy_name = [];

function validateMoban() {
	if ($.trim($("#mbname").val()) == "") {
		alert("模板名称必须填写");
		$("#mbname").focus();
		return false;
	}
	if ($("#idhy").val() == 0) {
		alert("请选择行业");
		$("#idhy").focus();
		return false;
	}
	if ($("#idxm").val() == 0) {
		alert("请选择项目");
		$("#idxm").focus();
		return false;
	}
	if ($("#idmbpath").val() == 0) {
		alert("请选择模板目录");
		$("#idmbpath").focus();
		return false;
	}
	$("#sbt").attr({"disabled":"disabled"});
	return true;
}

function set_opt_HY() {
	var bmid = $("#idbm").val();
	setOption("idhy", bmid);
	clearOption($("#idxm")[0]);
}

function set_opt_XM() {
	var hyid = $("#idhy").val();
	setOption("idxm", hyid);
	clearOption($("#idmbpath")[0]);
}

function set_opt_MBPath() {
	var xmid = $("#idxm").val();
	setOption("idmbpath", xmid);
}

function setOption(sltname, id) {
	var slt = $("#" + sltname)[0];
	clearOption(slt);
	switch (sltname) {
		case "idhy":
		if (arr_hy_name[id]) for (i in arr_hy_name[id]) { slt.options.add(new Option(arr_hy_name[id][i], i)); }
		else {
			arr_hy_name[id] = [];
			$.getJSON("/manage/moban/getjson?bmid=" + id, function(data) {
				if (data) {	$.each(data, function(i,item) { slt.options.add(new Option(item, i)); arr_hy_name[id][i] = item }); }
			});
		}
		case "idxm":
		if (arr_xm_name[id]) for (i in arr_xm_name[id]) { slt.options.add(new Option(arr_xm_name[id][i], i)); }
		else {
			arr_xm_name[id] = [];
			$.getJSON("/manage/moban/getjson?hyid=" + id, function(data) {
				if (data) {	$.each(data, function(i,item) { slt.options.add(new Option(item, i)); arr_xm_name[id][i] = item }); }
			});
		}
		break;
		case "idmbpath":
		if (arr_mb_path[id]) for (i in arr_mb_path[id]) { slt.options.add(new Option(arr_mb_path[id][i], i)); }
		else {
			arr_mb_path[id] = [];
			var bmid = $("#idbm").val();
			$.getJSON("/manage/moban/getjson?xmid=" + id + "&bumenid=" + bmid, function(data) {
				if (data) {	$.each(data, function(i,item) { slt.options.add(new Option(item, i)); arr_mb_path[id][i] = item }); }
			});
		}
		break;
		default:
		return null;
	}
}

function clearOption(slt) {
	slt.selectedIndex = 0;
	slt.options.length = 1;
}

function set_MBPath() {
	var mbpath = $("#idmbpath option:selected").text();
	var bmid = $("#idbm").val();
	var xmid = $("#idxm").val();
	$("#mbpath").val(mbpath);
	$("#mbfile").val("");
	$.getJSON("/manage/Moban/getjson?mbpath=" + mbpath + "&bumenid=" + bmid + "&xiangmuid=" + xmid, function(data) { if (data) $("#mbfile").val(data.join()) } );
}

$(document).ready(
function() {
	$("#idbm").change(set_opt_HY);
	$("#idhy").change(set_opt_XM);
	$("#idxm").change(set_opt_MBPath);
	$("#idmbpath").change(set_MBPath);
	$("#oForm").submit(validateMoban);
}
);