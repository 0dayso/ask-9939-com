//@author : tj
var edit = false;
var frontName = "hy_";
function editHangye() {
	var id = $(this).attr("name").replace(frontName, "");
	$.getJSON("/manage/hangye/getjson?id=" + id, function(data) {
		$.each(data, function(i,item) { if (i != "par_idhy") $("#" + i).val(item == null ? "" : item); });
		setParentHangye();
		}
	);

	$("#hytitle").html("编辑行业");
	$("#oForm").attr("action", "/manage/hangye/edit/");
	$("#ok").val("编辑");
	$("#cancel").show();
	location.href = "#fm";
	edit = true;
}

function cancelEdit() {
	if(edit) {
		$("#hytitle").html("添加行业");
		$("#oForm").attr("action", "/manage/hangye/add/");
		$("#ok").val("添加");
		$("#cancel").hide();

		$("#idhy").val(0);
		$("#hyname").val("");
		$("#hyjiancheng").val("");
		$("#hykey").val("");
		$("#hydesc").val("");
		//$("#idbm").val(0);
		//setParentHangye();
		edit = false;
	}
}

function validateHangye() {
	if($.trim($("#hyname").val()) == "") {
		alert("行业名称必须填写");
		$("#hyname").focus();
		return false;
	}
	if($.trim($("#hyjiancheng").val()) == "") {
		alert("行业简称必须填写");
		$("#hyjiancheng").focus();
		return false;
	}
	return true;
}

function setParentHangye() {
	bmid = $("#idbm").val();
	$("#par_idhy")[0].selectedIndex = 0;
	$("#par_idhy")[0].options.length = 1;
	$.getJSON("/manage/hangye/getjson?bmid=" + bmid, function(data) {
		if (data) $.each(data, function(i,item) { if (item != null) $("#par_idhy")[0].options.add(new Option(item, i)); })
		}
	);
}

$(document).ready(
function() {
	setParentHangye();
	$("#idbm").change(setParentHangye);
	$("td :input[name^=" + frontName + "]").click(editHangye);
	$("#cancel").hide().click(cancelEdit);
	$("#oForm").submit(validateHangye);
}
);