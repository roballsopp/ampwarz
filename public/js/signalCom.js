"use strict";
function deconstruct(signal) {

	signal.L = signal.X[0].buffer;
		
	if (signal.channels == 2) {
		signal.R = signal.X[1].buffer;
	}
	
	delete signal.X;
	
	return signal;
}

function reconstruct(signal) { // expects an object with unsigned int channels, arraybuffer L, and arraybuffer R

	signal.X = [new Float32Array(signal.L)];	
	delete signal.L;
		
	if (signal.channels == 2) {
		signal.X[1] = new Float32Array(signal.R);
		delete signal.R;
	}
	
	return signal;
		
}

function getBufferList(signal) { // expects deconstructed signal
	var bufferList = [signal.L];
	if (signal.channels == 2) bufferList.push(signal.R);
	return bufferList;
}