"use strict";
$(function () {

var inputFile = $("#input_file");
var progressPopUp = $("#progress");
var messageDisplay = $("#messages");
var progressBar = $("#progress_bar");
var contributeForm = $("#frm_contribute");

var SIGNALS = { signal: false, filter: false };

var processor = new Worker("js/Processor.js");
var fileParser = new Worker("js/FileParser.js");

// ---------------------------------------------------
// Step 4: Upload processed file to server
// ---------------------------------------------------

function upload(file) {

	var fd = new FormData();
	fd.append("audio_file", file);
	
	contributeForm.find("input:not([type=file]), textarea").each(function() { // find all input and textarea fields on the contribute form that are not the file selector
		fd.append($(this).attr("name"), $(this).val()); // append each one to our FormData object
	});

	var xhr = new XMLHttpRequest();
	xhr.open('POST', 'recieve.php', true);

	// Listen to the upload progress.
	xhr.upload.onprogress = function(e) {
		messageDisplay.html("Uploading...");
		if (e.lengthComputable) {			
			progressBar.val((e.loaded / e.total) * 100);
		}
	};
	
	xhr.onreadystatechange = function() {

		if (xhr.readyState == 4 && xhr.status == 200) {			
			// this is the only way you've found so far to get a programmatic form POST effect with files
			document.open();
			document.write(xhr.response); // just overwrite the whole current document with "recieve.php"
			document.close();
		}
		
	};

	xhr.send(fd);

}

// ---------------------------------------------------
// Step 3: Process the amp file
// ---------------------------------------------------

function onLoadSignals() {
	
	if ( SIGNALS.signal && SIGNALS.filter ) {
		messageDisplay.html("begin processing..");
		
		var signal = deconstruct(SIGNALS.signal);
		var filter = deconstruct(SIGNALS.filter);	

		var bufferList = getBufferList(signal).concat(getBufferList(filter));		
	
		processor.postMessage({ command: "process", signal: signal, filter: filter }, bufferList);
		
	}
	
}

// ---------------------------------------------------
// Step 2: Get raw amp tone file from user
// ---------------------------------------------------

function getUserFile(e) {
	e.preventDefault();
	progressPopUp.show();
	var file = inputFile[0].files[0];
	fileParser.postMessage({ command: "decode", id: "signal", file: file });
}

// ---------------------------------------------------
// Step 1: Request impulse from server
// ---------------------------------------------------

var xmlGetImpulse = new XMLHttpRequest();
xmlGetImpulse.open("GET", "files/site/cab_impulse.wav", true);
xmlGetImpulse.responseType = "blob";
xmlGetImpulse.send();

xmlGetImpulse.onreadystatechange = function() {

	if (xmlGetImpulse.readyState == 4 && xmlGetImpulse.status == 200) {
		fileParser.postMessage({ command: "decode", id: "filter", file: xmlGetImpulse.response });
	}
	
};

// ---------------------------------------------------
// Listen for messages from the fileParser worker
// ---------------------------------------------------

fileParser.onmessage = function(message) {

	var messageType = message.data.type;
	
	switch (messageType) {
	// File menu commands
	case "error":
		messageDisplay.html(message.data.message);
		break;
	case "status":
		messageDisplay.html(message.data.message);
		break;
	case "progress":
		progressBar.val(message.data.value);
		break;
	case "file":
	
		upload(message.data.file);
	
		break;
	case "signal":
	
		SIGNALS[message.data.id] = reconstruct(message.data.signal);
		
		onLoadSignals();
		
		break;
	default:
		console.log("Message not recognized");
	}

};

// ---------------------------------------------------
// Listen for messages from the processor worker
// ---------------------------------------------------

processor.onmessage = function(message) {

	var messageType = message.data.type;
	
	switch (messageType) {
	// File menu commands
	case "error":
		messageDisplay.html(message.data.message);
		break;
	case "status":
		messageDisplay.html(message.data.message);
		break;
	case "progress":
		progressBar.val(message.data.value);
		break;
	case "signal":
	
		var bufferList = getBufferList(message.data.signal);
		
		fileParser.postMessage({ command: "encode", id: "signal", signal: message.data.signal, bitDepth: 16 }, bufferList);
		
		break;
	default:
		console.log("Message not recognized");
	}
	
};

// ---------------------------------------------------
// Listen for user to submit file
// ---------------------------------------------------

contributeForm.on("submit", getUserFile);

}); // end wrapper function