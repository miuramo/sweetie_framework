function endsWith(str, suffix) {
    return str.indexOf(suffix, str.length - suffix.length) !== -1;
}
function paste(dataURL){
    console.log("paste");
    var data = {request: dataURL, type: "png" };
    var success = function(data,dataType){
        if (endsWith(openingFileName,".md")){
            insertTextAtPosition("![](img/"+data+" \""+data+"\")");
        } else if (endsWith(openingFileName,".html")){
            insertTextAtPosition("<img src=\"img/"+data+"\" alt=\""+data+"\">");
        } else {
            insertTextAtPosition("["+data+"]");
        }
	editor.focus();
    };
    var error = function(XMLHttpRequest, testStatus, errorThrowm){
        alert("Error :"+errorThrown);
    };
    var send = {
        type: "POST",
        url: "__imgsend.php",
        data: data,
        success: success,
        error: error
    };

    $.ajax(send);
//    $.pasteimage(paste);
}

$(function(){
    var userAgent = window.navigator.userAgent.toLowerCase();
    if (userAgent.indexOf("firefox") != -1){
	$(document).keydown(function(e){
	    if (e.keyCode == 86 && (e.ctrlKey || e.metaKey)){
		pasteCatcher.get(0).focus();
//		console.log("press CTRL+v");
	    }
	    return true;
	});
    }
    registpasteimage(paste);
});

function insertTextAtPosition(txt){
    if (window['editor'] != undefined){
        var cursor = editor.getCursor();
        editor.replaceRange(txt+"\r\n", cursor, cursor);
    }
}

function imgdrop(dataURL, filename){
    var data = {request: dataURL, type: "png", fname: filename };
    var success = function(data,dataType){
        if (endsWith(openingFileName,".md")){
            insertTextAtPosition("![](img/"+data+" \""+data+"\")");
        } else if (endsWith(openingFileName,".html")){
            insertTextAtPosition("<img src=\"img/"+data+"\" alt=\""+data+"\">");
        } else {
            insertTextAtPosition("["+data+"]");
        }
    };
    var error = function(XMLHttpRequest, testStatus, errorThrowm){
        alert("Error :"+errorThrown);
    };
    var send = {
        type: "POST",
        url: "__imgupload.php",
        data: data,
        success: success,
        error: error
    };

    $.ajax(send);
}

function binfiledrop(dataURL, filename){
    var data = {request: dataURL, fname: filename };
    var success = function(rettxt,dataType){
        var ary = rettxt.split("\t",-1);
        var mime = ary[0];
        var fn = ary[1];
        console.log(ary);
        if (mime.match('image.*')){
            if (endsWith(openingFileName,".md")){
                insertTextAtPosition("![](img/"+fn+" \""+fn+"\")");
            } else if (endsWith(openingFileName,".html")){
                insertTextAtPosition("<img src=\"img/"+fn+"\" alt=\""+fn+"\">");
            } else {
                insertTextAtPosition("["+fn+"]");
            }
        } else {  // binary
            if (endsWith(openingFileName,".md")){
                insertTextAtPosition("["+fn+"](img/"+fn+")");
            } else if (endsWith(openingFileName,".html")){
                insertTextAtPosition("<a href=\"img/"+fn+"\">"+fn+"</a>");
            } else {
                insertTextAtPosition("["+fn+"]");
            }
        };
    }
    var error = function(XMLHttpRequest, testStatus, errorThrowm){
        alert("Error :"+errorThrown);
    };
    var send = {
        type: "POST",
        url: "__imgupload.php",
        data: data,
        success: success,
        error: error
    };

    $.ajax(send);
}
