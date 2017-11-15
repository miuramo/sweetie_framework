//This is Lib2
function commentCaretOrSel(){
    if (editor == null) return;
    console.log("commentCaretOrSel!!");
    var selections = editor.listSelections();
    // 複数行選択？
    if (selections[0].anchor.line != selections[0].head.line){
	var anc = selections[0].anchor;
	var head = selections[0].head;
	if (anc.line > head.line){
            anc = selections[0].head;
            head = selections[0].anchor;
	}
	var min = anc.line;
	var max = head.line;
	if (head.ch==0) max--;
	for(var i=min;i<=max;i++){
            toggleComment(i);
	}
    } else {
	var linenum = editor.getCursor().line;
	var ch = editor.getCursor().ch;
	toggleComment(linenum);
	editor.setCursor({line: linenum+1, ch: ch});
    }
    return false;    
}
function toggleComment(linenum){
    var line = editor.getLine(linenum);
    if (cm_mode == "php" || cm_mode == "clike" || cm_mode == "javascript"){
//	console.log("clike");
	if (line.match(/^[ \t]*\/\//)){
	    editor.replaceRange("",{line: linenum, ch: 0},{line: linenum, ch: 2});
	} else {
	    editor.replaceRange("//",{line: linenum, ch: 0},{line: linenum, ch: 0});
	}
    } else if (cm_mode == "css"){
	if (line.match(/^[ \t]*\/\*(.+)\*\/$/)){
	    editor.replaceRange("",{line: linenum, ch: line.length-3},{line: linenum, ch: line.length});
	    editor.replaceRange("",{line: linenum, ch: 0},{line: linenum, ch: 3});
	} else {
	    editor.replaceRange(" */",{line: linenum, ch: line.length},{line: linenum, ch: line.length});
	    editor.replaceRange("/* ",{line: linenum, ch: 0},{line: linenum, ch: 0});
	}
    } else if (cm_mode == "htmlmixed" || cm_mode == "markdown"){ // <!--  -->
	if (line.match(/^[ \t]*<!\-\-(.+)\-\->$/)){
	    editor.replaceRange("",{line: linenum, ch: line.length-4},{line: linenum, ch: line.length});
	    editor.replaceRange("",{line: linenum, ch: 0},{line: linenum, ch: 5});
	} else {
	    editor.replaceRange(" -->",{line: linenum, ch: line.length},{line: linenum, ch: line.length});
	    editor.replaceRange("<!-- ",{line: linenum, ch: 0},{line: linenum, ch: 0});
	}
    }
}

function indentAll(){
    var last = editor.lineCount(); 
    editor.operation(function() { 
        for (var i = 0; i < last; ++i) editor.indentLine(i); 
    }); 
}

function showAll(){
    var status = document.getElementById("viewmodechk").checked;
    //   console.log(status);
    if (status){
	$(".CodeMirror").css("height","auto");
	editor.refresh();
    } else {
	editor.setSize(null, 500);
    }
}
function openfile(fileid){
    fileid = fileid.replace("___","/");
    openingFileName = fileid;
    $("title").text(openingFileName+" : Sweetie");
    $("#ed").load("__edit.php article", {"file": fileid}, function(){

	//	$( ".button" ).button();
	$( ".tooltip" ).tooltip();
	$( "#saveButton" ).button().click( function(e){
	    e.preventDefault();
            // 二重ファイルコピー防止
            if (openingFileName == document.getElementById("file").value){
		save();
		confirm_on_save();
            } else {
                
            }
	    return false;
	});
	$( "#indentButton" ).button().click( function(e){
	    e.preventDefault();
            indentAll();
	    return false;
	});
	$( "#exportButton" ).button().click( function(e){
            e.preventDefault();
            var link = $("#run");
            if (link[0] == undefined) {
		alert("Not Supported MIME Type");
	    } else {
		updateConfirmTab(link[0].href);
	    }
            return false;
	});
	$( "#pasteButton" ).button().click( function(e){
            e.preventDefault();
	    console.log("doPaste");
	    $(document).trigger("paste",e);
            return false;
	});


	editor = CodeMirror.fromTextArea(document.getElementById("code"), {
	    mode: "text/x-csrc",
	    styleActiveLine: true,
	    lineNumbers: true,
	    lineWrapping: true,
	});
	changeMode(openingFileName);
	$("#file").keydown(function(e){ if (e.which=='13'){
            console.log(e);
	    e.preventDefault();
	    save();
            return false;
	}});

	editor.on("drop", function(editor,e){
	    e.preventDefault();
	    var files = e.dataTransfer.files;
	    for(var i=0;i<files.length;i++){
		var f = files[i];
		var reader = new FileReader();
		//           console.log(f.name+" "+f.type+" "+f.size);
		/*           if (f.type.match('image.*')){
		 reader.onload = function(evt){
		 imgdrop(evt.target.result, f.name);
		 }
		 reader.readAsDataURL(f);
		 } else {*/
		reader.onload = function(evt){
		    binfiledrop(evt.target.result, f.name);
		}
		reader.readAsDataURL(f);
		//           }
	    }
	    //         console.log(e);
	});

	/*	          editor.on("cursorActivity",function(editor){
	 //          console.log(editor.getCursor());
	 //          console.log(sessionID);
	 });
	 editor.on("beforeChange",function(editor, chobj){
	 //            console.log(chobj);
	 delete chobj["cancel"];
	 //          chobj["canceled"] = true;
	 //          chobj["line"] = editor.lastLine();
	 cmOps[+new Date()] = chobj;
	 //          console.log(sessionID);
	 });*/
	//	          setTimeout("simul()",3000);


        $("#fontsizeslider").slider({ value: fontSize, min: 2, max: 50, step: 0.25 });
        $(".CodeMirror").css("font-size", fontSize);
        $("#fontsizeslider").on("slide",function(evt, ui){
	    fontSize = ui.value;
	    $(".CodeMirror").css("font-size", fontSize);
        });
        $("#fontsizeslider").on("dblclick",function(evt, ui){
	    fontSize = 12;
	    $(".CodeMirror").css("font-size", fontSize);
	    $("#fontsizeslider").slider("value",fontSize);
        });
	/*	$(function(){
	 var userAgent = window.navigator.userAgent.toLowerCase();
	 if (userAgent.indexOf("firefox") != -1){
	 registpasteimage(paste);
	 } else {
	 }
	 });*/


    });
}
function buttonclickevent_assign(dir){
    if (dir==undefined) dir="./";
    //    $( ".button" ).button();
    $( ".button" ).click( function(){
	//	console.log($(this).data("isdir"));
	if ($(this).data("isdir") == "dir") {
	    $("#"+this.id+"_span").load("__list.php?dir="+this.id,null,function(){
		buttonclickevent_assign(this.id);
	    });
	    return;
	}
	console.log("clicked "+this.id);
	this.id = this.id.replace("___","/");
	openingFileName = this.id;
	$("title").text(openingFileName+" : Sweetie");
	$("#ed").load("__edit.php article", {"file": this.id}, function(){

	    //	    $( ".button" ).button();
	    $( ".tooltip" ).tooltip();
	    $( "#saveButton" ).button().click( function(e){
		e.preventDefault();
                // 二重ファイルコピー防止
                if (openingFileName == document.getElementById("file").value){
		    save();
		    confirm_on_save();
                } else {
                    
                }
		return false;
	    });
	    $( "#indentButton" ).button().click( function(e){
		e.preventDefault();
                indentAll();
		return false;
	    });
	    $( "#exportButton" ).button().click( function(e){
                e.preventDefault();
                $link = $("#run");
                if ($link[0] == undefined) {
		    alert("Not Supported MIME Type");
		} else $link[0].click();
                return false;
	    });


	    editor = CodeMirror.fromTextArea(document.getElementById("code"), {
		mode: "text/x-csrc",
		styleActiveLine: true,
		lineNumbers: true,
		lineWrapping: true,
	    });
	    changeMode(openingFileName);
	    $("#file").keydown(function(e){ if (e.which=='13'){
                console.log(e);
		e.preventDefault();
		save();
                return false;
	    }});

	    editor.on("drop", function(editor,e){
		e.preventDefault();
		var files = e.dataTransfer.files;
		for(var i=0;i<files.length;i++){
		    var f = files[i];
		    var reader = new FileReader();
		    //           console.log(f.name+" "+f.type+" "+f.size);
		    /*           if (f.type.match('image.*')){
		     reader.onload = function(evt){
		     imgdrop(evt.target.result, f.name);
		     }
		     reader.readAsDataURL(f);
		     } else {*/
		    reader.onload = function(evt){
			binfiledrop(evt.target.result, f.name);
		    }
		    reader.readAsDataURL(f);
		    //           }
		}
		//         console.log(e);
	    });

	    /*	          editor.on("cursorActivity",function(editor){
	     //          console.log(editor.getCursor());
	     //          console.log(sessionID);
	     });
	     editor.on("beforeChange",function(editor, chobj){
	     //            console.log(chobj);
	     delete chobj["cancel"];
	     //          chobj["canceled"] = true;
	     //          chobj["line"] = editor.lastLine();
	     cmOps[+new Date()] = chobj;
	     //          console.log(sessionID);
	     });*/
	    //	          setTimeout("simul()",3000);


            $("#fontsizeslider").slider({ value: fontSize, min: 2, max: 50, step: 0.25 });
            $(".CodeMirror").css("font-size", fontSize);
            $("#fontsizeslider").on("slide",function(evt, ui){
		fontSize = ui.value;
		$(".CodeMirror").css("font-size", fontSize);
            });
            $("#fontsizeslider").on("dblclick",function(evt, ui){
		fontSize = 12;
		$(".CodeMirror").css("font-size", fontSize);
		$("#fontsizeslider").slider("value",fontSize);
            });

	});
    });
    $( ".tooltip" ).tooltip();

    /*    $(".button").each(function(idx, obj){
     console.log(idx+" "+obj.id);
     });*/
}

function confirm_on_save(){
    var chk = $("#updateconfirmonsave");
//    console.log(chk);
    if (chk[0].checked) confirm_always();
    return false;
}

function confirm_always(){
    var link = $("#run");
    if (link[0] != undefined && link[0].href != undefined){
	updateConfirmTab(link[0].href);
    } else {
	console.log(cm_mode + " ("+cm_spec+") not supported for confirm");
    }
    return false;
}

function updateConfirmTab(url){
    if (url == undefined || url == null) return;
    if (confirmTabs[url]!=null){
	confirmTab = confirmTabs[url];
	reloadtimeout = setTimeout(confirmreload, 800);
    } else {
	confirmTabs[url] = window.open(url, url, 'width=800,height=600,top=10,left=800');
	confirmTabs[url].onbeforeunload = function(){
	    confirmTabs[this.location.href] = null;
	};
    }
}
var reloadtimeout = null;
var confirmTab = null; // 600ミリ秒後に，再読み込みするタブ
function confirmreload(){
    var u = confirmTab.location.href;
    confirmTab.onbeforeunload = null;
    confirmTab.location.reload();
    //    clearTimeout(reloadtimeout);
    confirmTabs[u] = confirmTab;
    setTimeout(addUnloadEvent, 500);
}
function addUnloadEvent(){
    confirmTab.onbeforeunload = function(){
	confirmTabs[this.location.href] = null;
    };
}


function save(){
    var filev = document.getElementById("file").value;
    if (filev.indexOf("../")>-1) {
	alert("You can NOT specify parent (..) folder. Eliminate them.");
	return;
    }
    if (openingFileName != document.getElementById("file").value){
	var yesorno = confirm("Create a new file?");
	if (!yesorno) {
            document.getElementById("file").value = openingFileName;
            return false;
        }

	//Save!
	var txt = editor.getValue();
	txt = txt.replace(/\\/g, '\\\\');
	document.getElementById("code").value = txt;
	submitFormAjax($("#EditForm"),"Create New File"); 

	openingFileName = document.getElementById("file").value;
	//	$("#fs").append('<button id="'+openingFileName+'" class="button">'+openingFileName+'</button>');
	updatetree();
	buttonclickevent_assign();
	$("#currentfile").html(openingFileName);
	$("title").text(openingFileName+" : Sweetie");
	changeMode(openingFileName);
	return false;       
    }
    if (window['editor'] != undefined){
	//Save!
	var txt = editor.getValue();
	// add backslash
	txt = txt.replace(/\\/g, '\\\\');
	document.getElementById("code").value = txt;

	// check delete ?
	if (editor.getValue().length < 1){
            if (confirm("The content is empty. Delete this file?")){
		submitFormAjax($("#EditForm"),"Delete");
		alert("The file "+openingFileName+" has deleted.");
		$("#ed").html("");
		//            $("#"+openingFileName).remove();
		updatetree();
            } else {
		alert("Press CTRL+Z to revert contents.");
		return false;
            }
            return false;
	}
	submitFormAjax($("#EditForm"),"Save"); 
    } else {
	fuwariMes("No Editor",1000);
    }
}

function fuwariMes(mes,msec){
    $('.alert').text(mes);
    $('.alert').css({left:"200px",zIndex:300});
    $('.alert').animate({top:"10px"},300).delay(msec).animate({top:"-42px"},300).fadeIn(10);
}

function submitFormAjax(form, saveordel){
    saveOrDelete = saveordel;
    $.ajax({
	url: form.attr("action"),
	type: form.attr("method"),
	data: form.serialize(),
	timeout: 10000,
	beforeSend: function(xhr, settings){
	    //		alert("beforeSend");
	},
	complete: function(xhr, textStatus){
	    //		    alert("complete");
	},
	success: function(result, textStatus, xhr){
	    fuwariMes(saveOrDelete+" Success! (by CTRL+S) "+(new Date()).toLocaleTimeString(),3000);
	    //		window.parent.opener.location.reload();
	},
	error: function(xhr, textStatus,error){
	    alert("error");
	}
    });
}


function changeMode(val) {
    //      console.log("changeMode: "+val);
    var m, mode, spec;
    console.log("VAL"+val);
    if (endsWith(val,".pjs")){
	//spec = "text/x-csrc"; mode = "clike";
	spec = "text/x-java"; mode = "clike";
    } else if (m = /.+\.([^.]+)$/.exec(val)) {
	var info = CodeMirror.findModeByExtension(m[1]);
	if (info) {
	    mode = info.mode;
	    spec = info.mime;
	}
    } else if (/\//.test(val)) {
	var info = CodeMirror.findModeByMIME(val);
	if (info) {
	    mode = info.mode;
	    spec = val;
	}
    } else {
	mode = spec = val;
    }
    if (mode) {
	console.log(spec+" "+mode);
	cm_mode = mode;
	cm_spec = spec;
	editor.setOption("mode", spec);
	CodeMirror.autoLoadMode(editor, mode);
	document.getElementById("modeinfo").textContent = spec;

	if (spec=="text/html" || spec=="application/x-httpd-php"){
	    document.getElementById("openlink").innerHTML = "<a id=\"run\" target=\""+openingFileName+"\" href=\"../"+openingFileName+"\">[open "+openingFileName+"]</a>";
	} else if (spec=="text/x-markdown"){
	    document.getElementById("openlink").innerHTML = "<a id=\"run\" target=\"../"+openingFileName+"\" href=\"../index.html#"+openingFileName+"\">[open "+openingFileName+"]</a>";
	} else if (endsWith(openingFileName,".pjs")){
	    document.getElementById("openlink").innerHTML = "<a id=\"run\" href=\"_runpjs.php?fn="+openingFileName+"\" target=\""+openingFileName+"\">[run "+openingFileName+"]</a>";
	}
    } else {
	//    alert("Could not find a mode corresponding to " + val);
    }
}

function cmcheck(){
    editor.getValue();
}

function simul(){
    //    console.log("simul "+sessionID);
    $.ajax({ url: "__simul.php?sid="+sessionID+"&fn="+openingFileName,
             type: "POST",
             dataType: 'json',
             data: { "op": cmOps, "cursor": editor.cursorCoords(true, "local") },
             success: 
	     function(data){
		 var ary = data;
		 for(var t in ary){
		     console.log(t+" "+lastSend);
		     if ( t < lastSend){
			 for(var sid in ary[t]){
			     if (sid != sessionID){
			         console.log(sid+" "+sessionID);
				 console.log(ary[t][sid]);
				 var aop = ary[t][sid]['op'];
				 if (aop != null){
				     for(var h in aop){

                                         //					 editor.replaceRange(aop[h]['text'],aop[h]['from'],aop[h]['to'],aop[h]['origin']);
					 editor.replaceRange(aop[h]['text'],aop[h]['from'],aop[h]['to']);
				     }
				 }
			     }
			 }
		     }
		 }
	     }
	   }
	  );
    lastSend = +new Date();
    cmOps = {};
    setTimeout("simul()",3000);
}
