//@author : tj
var edit = false;
var frontName = "bm_";
function editBumen() {
	$("#bmname").val($(this).parents("tr").children("td:eq(0)").text());
	$("#bmfile").val($(this).parents("tr").children("td:eq(1)").text()).attr({"readonly":"readonly"});
	$("#iduzg").val($(this).parents("tr").children("td:eq(3)").text());
	$("#bmhost").val($(this).parents("tr").children("td:eq(4)").text());
	$("#idbm").val($(this).attr("name").replace(frontName, ""));

	$("#bmtitle").html("编辑部门");
	$("#oForm").attr("action", "/manage/bumen/edit/");
	$("#ok").val("编辑");
	$("#cancel").show();
	location.href = "#fm";
	edit = true;
}

function cancelEdit() {
	if(edit) {
		$("#bmtitle").html("添加部门");
		$("#oForm").attr("action", "/manage/bumen/add/");
		$("#ok").val("添加");
		$("#cancel").hide();

		$("#idbm").val(0);
		$("#bmname").val("");
		$("#bmfile").val("").attr({"readonly":""});
		$("#iduzg")[0].selectedIndex = 0;
		$("#bmhost").val("");
		edit = false;
	}
}

function validateBumen() {
	if($.trim($("#bmname").val()) == "") {
		alert("部门名称必须填写");
		$("#bmname").focus();
		return false;
	}
	bmf = $.trim($("#bmfile").val());
	if(bmf == "") {
		alert("部门简称必须填写");
		$("#bmfile").focus();
		return false;
	}
	var reg = new RegExp("[^_\\w\\d]");
	if (reg.test(bmf)) {
		alert("部门简称只能使用字母数字下划线");
		return false;
	}
	return true;
}

$(document).ready(
function() {
	$("td :input[name^=" + frontName + "]").click(editBumen);
	$("#cancel").hide().click(cancelEdit);
	$("#oForm").submit(validateBumen);
}
);