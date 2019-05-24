// File Parser Worker ------------------------------------------
//
// To decode a wave file send command: WorkerName.postMessage({ command: "decode", id: "nameOrNumber", file: File(), bitDepth: 16 });
//
// To encode a wave file send command: WorkerName.postMessage({ command: "encode", id: "whateverYouWant", signal: {} }, bufferList);
//
// -----------------------------------------------------------------
"use strict";

importScripts("signalCom.js");

// helper function to determine endianness on user's system
function getEndianness() {
  var buffer = new ArrayBuffer(2);
  new DataView(buffer).setInt16(0, 256, true);
  return new Int16Array(buffer)[0] === 256;
}

var isLittleEndian = getEndianness();

// takes number of bytes to concatenate into an int, the array from which to extract the bytes, where to start extracting, and whether to concatenate little endian or big
// returns an full Int32 no matter what number of bytes are concatenated
function assembleInt(numBytes, buffer, offset, isLittleEndian) {

	var sample = 0;
	
	if (isLittleEndian) {
		var b = numBytes;
		while (b) {
			b--; // if numBytes is 3, we actually want to count down from 2 to 0.
			sample <<= 8; // move bytes in sample over 1 position
			sample |= buffer[offset+b];	// add next byte to sample	
		} // end byte loop		
	} else {
		var b = 0;
		while (b < numBytes) {													
			sample <<= 8; // move bytes in sample over 1 position
			sample |= buffer[offset+b];	// add next byte to sample	
			b++;
		} // end byte loop
	}
	
	sample <<= (4 - numBytes) * 8; // here we move
	
	return sample;
	
}

function disassembleInt(numBytes, theInt, dataView, offset, isLittleEndian) {
	
	if (!isLittleEndian) {
		var b = numBytes;
		while (b) {
			b--; // if numBytes is 3, we actually want to count down from 2 to 0.				
			dataView.setUint8(offset+b, theInt & 0x000000FF);
			theInt >>= 8;
		} // end byte loop		
	} else {
		var b = 0;
		while (b < numBytes) {													
			dataView.setUint8(offset+b, theInt & 0x000000FF);
			theInt >>= 8;
			b++;
		} // end byte loop
	}
	
}

var worker = this;

function decode(file, id) {

	worker.postMessage({ type: "status", message: "Decoding wav file..." });
	
	var signal = {};
	var reader = new FileReaderSync();	
	
	var buffer = reader.readAsArrayBuffer(file);

	var searchLength = (buffer.byteLength < 10000) ? buffer.byteLength : 10000;

	var fileArray = new Uint8Array(buffer, 0, searchLength);
	var fileStr = "";
	
	for ( var i = 0; i < searchLength; i++ ) {
		fileStr += String.fromCharCode(fileArray[i]);
	}
	
	var riffLoc = fileStr.search("RIFF");
	var fmtLoc = fileStr.search("fmt ");
	var dataLoc = fileStr.search("data");
	//chunkLoc.junk = fileStr.search("JUNK");
	//chunkLoc.fact = fileStr.search("fact");
	
	var riffChunk	= new DataView(buffer, riffLoc, 12);
	var fmtChunk	= new DataView(buffer, fmtLoc, 24);
	var dataChunk	= new DataView(buffer, dataLoc, 8);

	var RIFF_ID				= riffChunk.getUint32(0, false);	// byte 00, 4 bytes, RIFF Header
	var RIFF_size			= riffChunk.getUint32(4, true);		// byte 04, 4 bytes, RIFF Chunk Size
	var RIFF_WAVE			= riffChunk.getUint32(8, false);	// byte 08, 4 bytes, WAVE Header
	var fmt_ID				= fmtChunk.getUint32(0, false);		// byte 12, 4 bytes, FMT header
	var fmt_size			= fmtChunk.getUint32(4, true);		// byte 16, 4 bytes, Size of the fmt chunk
	var fmt_audioFormat		= fmtChunk.getUint16(8, true);		// byte 20, 2 bytes, Audio format 1=PCM,6=mulaw,7=alaw, 257=IBM Mu-Law, 258=IBM A-Law, 259=ADPCM 
	var fmt_channels		= fmtChunk.getUint16(10, true);		// byte 22, 2 bytes, Number of channels 1=Mono 2=Sterio
	var fmt_sampleRate		= fmtChunk.getUint32(12, true);		// byte 24, 4 bytes, Sampling Frequency in Hz
	var fmt_bytesPerSec		= fmtChunk.getUint32(16, true);		// byte 28, 4 bytes, == SampleRate * NumChannels * BitsPerSample/8
	var fmt_blockAlign		= fmtChunk.getUint16(20, true);		// byte 32, 2 bytes, == NumChannels * BitsPerSample/8
	var fmt_bitsPerSample	= fmtChunk.getUint16(22, true);		// byte 34, 2 bytes, Number of bits per sample
	var data_ID				= dataChunk.getUint32(0, false);	// byte 60, 4 bytes, "data"  string   
	var data_size			= dataChunk.getUint32(4, true);
	
	// Is buffer a RIFF file?
	if ( 0x52494646 != RIFF_ID ) { // 0x52494646 = 'R' 'I' 'F' 'F'
		worker.postMessage({ type: "error", message: "Not a RIFF file" });
		return;
	}
	
	// Is buffer a WAVE file?
	if ( 0x57415645 != RIFF_WAVE ) { // 0x57415645 = 'W' 'A' 'V' 'E'
		worker.postMessage({ type: "error", message: "Not a WAVE file"});
		return;
	}	
	
	// is the sample rate normal?
	if ( fmt_sampleRate < 40000 ) {
		worker.postMessage({ type: "error", message: "Sample rate is below 40000"});
		return;
	}
	
	// ensure format is PCM or IEEE float
	if ( (1 != fmt_audioFormat) && (3 != fmt_audioFormat) ) { 
		worker.postMessage({ type: "error", message: "Unrecognized data format"});
		return;
	}
	
	signal.sampleRate = fmt_sampleRate;	
	signal.channels = fmt_channels;	
	signal.X = [];

	var bytesPerSample = Math.round(fmt_bitsPerSample / 8); // number of bytes per sample
	var sampPerChan = data_size / fmt_channels / bytesPerSample; // number of samples per channel
	var toFloat = Math.pow(2, ((4 * 8) - 1));

	if ( fmt_audioFormat == 3 ) {
	
		var	data = new DataView(buffer, dataLoc + 8, data_size);
	
		for (var chan = 0; chan < fmt_channels; chan++) { 
		
			signal.X[chan] = new Float32Array(sampPerChan);
		
			for (var n = 0; n < sampPerChan; n++) { 

				// swap the bytes into a temporary buffer
				signal.X[chan][n] = data.getFloat32(((n*fmt_channels)+chan)*bytesPerSample, isLittleEndian);	

			} // end sample loop

		} // end channel loop
		
	} else {
	
		var	data = new Uint8Array(buffer, dataLoc + 8, data_size);	// array of bytes. must convert into 24-bit ints or 16 bit ints
		
		for (var chan = 0; chan < fmt_channels; chan++) { // array of data contains 1 or 2 channels, interleaved
		
			signal.X[chan] = new Float32Array(sampPerChan);
		
			for (var n = 0; n < sampPerChan; n++) { // for every 16 or 24 bit int per channel...

				var offset = ((n*fmt_channels)+chan)*bytesPerSample;
				signal.X[chan][n] = assembleInt(bytesPerSample, data, offset, isLittleEndian) / toFloat; // convert the int to a floating pint number ranging from +1 to -1	
				
			} // end sample loop

		} // end channel loop
		
	} // end format logic
	
	worker.postMessage({ type: "status", message: "Decoding complete."});

	signal = deconstruct(signal);
	
	worker.postMessage({ type: "signal", id: id, signal: signal }, getBufferList(signal));

}

function encode(signal, bitDepth_out, id) {

	worker.postMessage({ type: "status", message: "Encoding wav file..." });

	var length = signal.X[0].length;
	var bytesPerSample = bitDepth_out / 8;
	var data_size_out = length * signal.channels * bytesPerSample;
	
	var dataBufferOut = new ArrayBuffer(44 + data_size_out);
	var dataViewOut = new DataView(dataBufferOut);
	
	dataViewOut.setUint32(0, 1179011410, true);													// byte 00, 4 bytes, RIFF Header
	dataViewOut.setUint32(4, 36 + data_size_out, true);											// byte 04, 4 bytes, RIFF Chunk Size
	dataViewOut.setUint32(8, 1163280727, true);													// byte 08, 4 bytes, WAVE Header
	dataViewOut.setUint32(12, 544501094, true);													// byte 12, 4 bytes, FMT header
	dataViewOut.setUint32(16, 16, true);														// byte 16, 4 bytes, Size of the fmt chunk
	dataViewOut.setUint16(20, 1, true);															// byte 20, 2 bytes, Audio format 1=PCM,6=mulaw,7=alaw, 257=IBM Mu-Law, 258=IBM A-Law, 259=ADPCM 
	dataViewOut.setUint16(22, signal.channels, true);												// byte 22, 2 bytes, Number of channels 1=Mono 2=Sterio
	dataViewOut.setUint32(24, signal.sampleRate, true);											// byte 24, 4 bytes, Sampling Frequency in Hz
	dataViewOut.setUint32(28, signal.sampleRate * signal.channels * ( bitDepth_out / 8 ), true);	// byte 28, 4 bytes, == SampleRate * NumChannels * BitsPerSample/8
	dataViewOut.setUint16(32, signal.channels * ( bitDepth_out / 8 ), true);						// byte 32, 2 bytes, == NumChannels * BitsPerSample/8
	dataViewOut.setUint16(34, bitDepth_out, true);												// byte 34, 2 bytes, Number of bits per sample
	dataViewOut.setUint32(36, 1635017060, true);												// byte 36, 4 bytes, "data"  string   
	dataViewOut.setUint32(40, data_size_out, true);												// byte 40, 4 bytes, data chunk size 
	
	var sample, clipped, offset;
	var clipHi = Math.pow( 2, ( bitDepth_out - 1 ) ) - 1;
	var clipLo = -Math.pow( 2, ( bitDepth_out - 1 ) );
	var range = Math.pow( 2, bitDepth_out - 1 );	

	for ( var n = 0; n < length; n++ ) { // for each sample 

		for ( var chan = 0; chan < signal.channels; chan++ ) {

			sample = Math.round(signal.X[chan][n] * range); // increase range to target format

			if ( sample > clipHi ) {
				clipped = clipHi;
			} else if ( sample < clipLo ) {
				clipped = clipLo;
			} else {
				clipped = sample;
			}
			
			offset = 44 + ((n*signal.channels) + chan)*bytesPerSample;

			disassembleInt(bytesPerSample, clipped, dataViewOut, offset, isLittleEndian);

		}

	}
	
	var fileOut = new Blob([dataViewOut], { type: "audio/wav" });
	
	worker.postMessage({ type: "status", message: "Encoding complete." });
	
	worker.postMessage({ type: "file", id: id, file: fileOut });

}

this.onmessage = function(message) {

	var command = message.data.command;
	
	switch (command) {
	case "decode":
		decode(message.data.file, message.data.id);		
		break;
	case "encode":
	
		var signal = reconstruct(message.data.signal);
		encode(signal, message.data.bitDepth, message.data.id);		
		break;	
	default:
		worker.postMessage({ type: "error", message: "Command not recognized" });
		break;
	}	
	
};