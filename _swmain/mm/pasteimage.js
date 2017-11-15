/* Readme file has additional notes */
/* Credits: 
 Joel Besda http://joelb.me/blog/2011/code-snippet-accessing-clipboard-images-with-javascript/
 Rafael http://stackoverflow.com/questions/11850970/javascript-blob-object-to-base64
 Nick et al http://stackoverflow.com/questions/6333814/how-does-the-paste-image-from-clipboard-functionality-work-in-gmail-and-google-c	
 */

var pasteCatcher;
var allowPaste = true;
var foundImage = false;
var callback;
var alertInSafari = false;
function registpasteimage(callbackf) {
    if(typeof(callbackf) == "function") {
	// Patch jQuery to add clipboardData property support in the event object
	$.event.props.push('clipboardData');
	// Add the paste event listener
	var userAgent = window.navigator.userAgent.toLowerCase();
	if (userAgent.indexOf("firefox") != -1){
	    if (!window.Clipboard) { // Firefox
		pasteCatcher = $(document.createElement("div"));
		pasteCatcher.attr("contenteditable","true").css({"position" : "absolute", "left" : "-999", 	width : "0", height : "0", "overflow" : "hidden", outline : 0});
		$(document.body).prepend(pasteCatcher);
		$(document).bind("paste", doPaste);
		console.log("bind paste");
	    }
	} else {
	    $(document).bind("paste", doPaste);
	}
	
	// If Firefox (doesn't support clipboard object), create DIV to catch pasted image
	callback = callbackf;
    }
}
// Handle paste event
function doPaste(e)  { 
    //miura: userAgentでFirefoxなら，フォーカスをずらす...失敗
    var userAgent = window.navigator.userAgent.toLowerCase();
    if (userAgent.indexOf("firefox") != -1){
	//		 alert("firefox");
	//		 var focused = document.activeElement;
	//		 console.log(focused);
	//		 return true;
	//		 console.log("firefox!");
    }
    
    if(allowPaste == true) {	 // conditionally set allowPaste to false in situations where you want to do regular paste instead
	// Check for event.clipboardData support
	if (e.clipboardData.items) { // Chrome
	    // Get the items from the clipboard
	    var items = e.clipboardData.items;
	    if (items) {
		// Search clipboard items for an image
		for (var i = 0; i < items.length; i++) { // removed: i < items.length, items[i].type.indexOf("image") !== -1
		    if (items[i].type.indexOf("image") !== -1) {
			//foundImage = true; Not sure why this was here								
			// Convert image to blob using File API	               
			var blob = items[i].getAsFile();
			var reader = new FileReader();
			reader.onload = function(event){
			    callback(event.target.result); //event.target.results contains the base64 code to create the image
			};
			/* Convert the blob from clipboard to base64 */		
			reader.readAsDataURL(blob);
			//foundImage = false; Not sure why this was here
		    }
		}
	    } else { 
		alert("Nothing found in the clipboard!"); // possibly e.clipboardData undersupported
	    }
	} else {
	    /* If we can't handle clipboard data directly (Firefox), we need to read what was pasted from the contenteditable element */
	    //Since paste event detected, focus on DIV to receive pasted image
	    if (pasteCatcher != null && pasteCatcher != undefined) {
		pasteCatcher.get(0).focus();
		foundImage = true;
	    // "This is a cheap trick to make sure we read the data AFTER it has been inserted"
	    //		     console.log("firefox2!");
	    
		setTimeout(checkInput, 300); // May need to be longer if large image
	    } else {
		if (!alertInSafari) {
		    fuwariMes("Safari does not support paste image from clipboard",3000);
		    alertInSafari = true;
		}
	    }
	}
    }
}

/* Parse the input in the paste catcher element */
function checkInput() {
//    console.log("checkinput");
    // Store the pasted content in a variable
    if(foundImage == true) {
	var child = pasteCatcher.children().last().get(0);
	if (child) {
	    // If the user pastes an image, the src attribute will represent the image as a base64 encoded string.
	    if (child.tagName === "IMG" && child.src.substr(0, 5) == 'data:') {
		//			 console.log("firefox3!");
		
		callback(child.src);
		foundImage = false;
		console.log("foundImage = false");
	    } else {
//		console.log("has child but no image");
		var src = pasteCatcher.html().replace(/(<br>|<br \/>)/gi,'\n');
		src = src.replace(/\n+$/g,"");
		insertTextAtPosition(src);
		pasteCatcher.html("");
		editor.focus();
		return false;
	    }
	    pasteCatcher.html(""); // erase contents of pasteCatcher DIV
	} else { 
	    //alert("No children found in pastecatcher DIV.");
	    console.log("no child");
	    var src = pasteCatcher.html().replace(/(<br>|<br \/>)/gi,'\n');
	    src = src.replace(/\n+$/g,"");
	    insertTextAtPosition(src);
	    pasteCatcher.html("");
	    editor.focus();
	    return true;
	}
    } else { 
	alert("No image found in the clipboard!");
	return true;
    }
}	


// http://stackoverflow.com/questions/15253468/get-pasted-image-from-clipboard-firefox

function handlepaste (elem, e) {
    var savedcontent = elem.innerHTML;
    if (e && e.clipboardData && e.clipboardData.getData) {// Webkit - get data from clipboard, put into editdiv, cleanup, then cancel event
        if (/text\/html/.test(e.clipboardData.types)) {
            elem.innerHTML = e.clipboardData.getData('text/html');
        }
        else if (/text\/plain/.test(e.clipboardData.types)) {
            elem.innerHTML = e.clipboardData.getData('text/plain');
        }
        else {
            elem.innerHTML = "";
        }
        waitforpastedata(elem, savedcontent);
        if (e.preventDefault) {
                e.stopPropagation();
                e.preventDefault();
        }
        return false;
    }
    else {// Everything else - empty editdiv and allow browser to paste content into it, then cleanup
        elem.innerHTML = "";
        waitforpastedata(elem, savedcontent);
        return true;
    }
}

function waitforpastedata (elem, savedcontent) {
    if (elem.childNodes && elem.childNodes.length > 0) {
        processpaste(elem, savedcontent);
    }
    else {
        that = {
            e: elem,
            s: savedcontent
        }
        that.callself = function () {
            waitforpastedata(that.e, that.s)
        }
        setTimeout(that.callself,20);
    }
}

function processpaste (elem, savedcontent) {
    pasteddata = elem.innerHTML;
    //^^Alternatively loop through dom (elem.childNodes or elem.getElementsByTagName) here

    elem.innerHTML = savedcontent;

    // Do whatever with gathered data;
    alert(pasteddata);
}

