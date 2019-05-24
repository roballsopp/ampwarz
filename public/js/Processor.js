"use strict";
importScripts("signalCom.js");

var worker = this;

Math.logB = function(base, x) { // base, followed by number to find log of
	return Math.log(x) / Math.log(base);
};

function dB2Mag(dB) {
	return Math.pow(10, dB/20);
}

function mag2dB(mag) {
	return 20 * Math.logB(10, mag);
}

function zeroFill(arr) {
	var n = arr.length;
	while (n) {
		n--;
		arr[n] = 0;
	}
}

function toMono(signal) { 

	if ( signal.X[0].length == 0 ) { 
		worker.postMessage({ type: "error", message: "Cannot convert to mono. No data."});
		return false;
	}

	if ( signal.channels != 1 ) {
		for ( var n = 0; n < signal.X[0].length; n++ ) {
			signal.X[0][n] += signal.X[1][n];
		}
		signal.X[1] = null;
		signal.channels = 1;
	}

	return true;

}

function toStereo(signal) { 

	if ( signal.X[0].length == 0 ) { 
		worker.postMessage({ type: "error", message: "Cannot convert to stereo. No data."});
		return false;
	}

	if ( signal.channels != 2 ) {
		signal.X[1] = new Float32Array(signal.X[0].buffer.slice(0));
		signal.channels = 2;
	}

	return true;

}

function volumedB(signal, dB) {

	var length = signal.X[0].length;

	if ( length == 0 ) { 
		worker.postMessage({ type: "error", message: "Cannot change volume. No data."});
		return false;
	}
	
	var gain = dB2Mag(dB);
	
	for (var chan = 0; chan < signal.channels; chan++) {
		
		for ( var n = 0; n < length; n++) signal.X[chan][n] *= gain;

	}

	worker.postMessage({ type: "status", message: "Volume adjustment success."});

	return true;
}

function getRMS(signal) {

	if ( signal.X[0].length == 0 ) { 
		worker.postMessage({ type: "error", message: "Cannot get RMS. No data."});
		return false;
	}
	
	var length = signal.X[0].length;
	var channels = signal.channels;

	var rms = 0;
	var rmsdB = 0;
	
	for ( var chan = 0; chan < channels; chan++ ) {
	
		for ( var n = 0; n < length; n++) rms += Math.pow(signal.X[chan][n], 2);
	
	}
	
	rms /= channels;	
	rms = Math.pow(rms / length, 0.5);	
	rmsdB = mag2dB(rms);

	worker.postMessage({ type: "status", message: "RMS: " + rms + " (" + rmsdB + " dB)."});

	return rmsdB;
}

function FFTbase( REX, IMX, N ) { // DO NOT TRY TO FIND LENGTH OF REX IN HERE, Process.FFT sends N/2!

	// set constants
	var nd2 = N / 2;
	var NM1 = N - 1;
	var m = Math.floor( Math.log(N) / Math.log(2) );
	var j = nd2;
	var k = nd2;

	var tr, ti, ur, ui, sr, si;

	// bit reversal sorting
	for ( var i = 1; i < NM1; i++ ) {
		if ( i < j ) {
			tr = REX[j];
			ti = IMX[j];
			REX[j] = REX[i];
			IMX[j] = IMX[i];
			REX[i] = tr;
			IMX[i] = ti;
		}
		k = nd2;
		while ( k <= j ) {
			j -= k;
			k /= 2;
		}
		j += k;
	}

	for ( var l = 1; l <= m; l++ ) {

		var le = Math.floor(Math.pow(2, l));
		var le2 = le / 2;
		ur = 1;
		ui = 0;
		sr = Math.cos(Math.PI / le2);
		si = -Math.sin(Math.PI / le2);

		for ( var j = 1; j <= le2; j++ ) {

			var jm1 = j - 1;

			for ( var i = jm1; i < N; i+=le ) {
				var ip = i + le2;
				tr = REX[ip]*ur - IMX[ip]*ui;
				ti = REX[ip]*ui + IMX[ip]*ur;
				REX[ip] = REX[i] - tr;
				IMX[ip] = IMX[i] - ti;
				REX[i] += tr;
				IMX[i] += ti;
			}
	
			tr = ur;
			ur = tr*sr - ui*si;
			ui = tr*si + ui*sr;

		}
	}

	return;
}

function FFT( REX, IMX ) {

	var N = REX.length;
	
	var NH = N/2;
	var NM1 = N-1;
	var N4 = N/4;
	var l = Math.floor( Math.log(N) / Math.log(2) );
	var le = Math.floor(Math.pow(2, l));
	var le2 = le / 2;
	var jm1, im, ip2, ipm, ip;

	var tr, ti, ur = 1, ui = 0, sr = Math.cos(Math.PI / le2), si = -Math.sin(Math.PI / le2);

	for( var i = 0; i < NH; i++ ) {
		REX[i] = REX[2*i];
		IMX[i] = REX[2*i+1];
	}

	FFTbase(REX, IMX, NH);

	for ( var i = 1; i < N4; i++ ) {
		im = NH-i;
		ip2 = i+NH;
		ipm = im+NH;
		REX[ip2] = (IMX[i]+IMX[im])*0.5;
		REX[ipm] = REX[ip2];
		IMX[ip2] = -(REX[i]-REX[im])*0.5;
		IMX[ipm] = -IMX[ip2];
		REX[i] = (REX[i]+REX[im])*0.5;
		REX[im] = REX[i];
		IMX[i] = (IMX[i]-IMX[im])*0.5;
		IMX[im] = -IMX[i];
	}

	REX[N*3/4] = IMX[N4];
	REX[NH] = IMX[0];
	IMX[N*3/4] = 0;
	IMX[NH] = 0;
	IMX[N4] = 0;
	IMX[0] = 0;

	for ( var j = 1; j <= le2; j++ ) {

		jm1 = j - 1;

		for ( var i = jm1; i < NM1; i+=le ) {
			ip = i + le2;
			tr = REX[ip]*ur - IMX[ip]*ui;
			ti = REX[ip]*ui + IMX[ip]*ur;
			REX[ip] = REX[i] - tr;
			IMX[ip] = IMX[i] - ti;
			REX[i] += tr;
			IMX[i] += ti;
		}
	
		tr = ur;
		ur = tr*sr - ui*si;
		ui = tr*si + ui*sr;

	}

	return true;
}

function inverseFFT( REX,  IMX ) {

	var N = REX.length;

	for ( var n = N/2+1; n < N; n++ ) {
		REX[n] = REX[N-n];
		IMX[n] = -IMX[N-n];
	}

	for ( var n = 0; n < N; n++ ) REX[n] += IMX[n];

	FFT(REX, IMX);

	for ( var n = 0; n < N; n++ ) REX[n] = (REX[n]+IMX[n])/N;	

	return true;

}

function convolve( signal, filter, resolution ) {
	
	worker.postMessage({ type: "status", message: "Convolving..."});
	
	var sigLength = signal.X[0].length;
	var filterLength = filter.X[0].length;

	var N = Math.pow(2, resolution);

	if ( N < filterLength + 3 ) { 

		worker.postMessage({ type: "error", message: "Failed to convolve wave files. Resolution must be at least " + Math.logB(2, filterLength + 3)});
		return false;

	}
	
	if ( signal.channels == 2 ) {
		toStereo(filter);
	} else if ( filter.channels == 2 ) {
		toStereo(signal);
	}
	
	for ( var chan = 0; chan < signal.channels; chan++ ) {
	
		worker.postMessage({ type: "status", message: "Convolving channel " + (chan+1) + "..."});

		var filterREX = new Float32Array(N);
		var filterIMX = new Float32Array(N);
		
		zeroFill(filterREX, N);
		zeroFill(filterIMX, N);

		for ( var n = 0; n < filterLength; n++ ) {
			filterREX[n] = filter.X[chan][n];
		}

		FFT(filterREX, filterIMX);

		var segmentSize = N - filterLength + 1;
		var numSegments = Math.floor(sigLength / segmentSize);

		var result = new Float32Array(sigLength);
		zeroFill(result, sigLength);

		//temporary buffers
		var REX = new Float32Array(N);
		var IMX = new Float32Array(N);
		var TEMP = new Float32Array(N);

		var segStart;

		for ( var seg = 0; seg < numSegments + 1; seg++ ) {

			segStart = seg * segmentSize;

			for ( var n = 0; n < N; n++ ) {
				REX[n] = 0;
				IMX[n] = 0;
				TEMP[n] = 0;
			}

			if ( seg == numSegments ) segmentSize = sigLength % segmentSize;

			for ( var n = 0; n < segmentSize; n++ ) {
				REX[n] = signal.X[chan][n+segStart];
			}

			FFT(REX, IMX);

			for ( var n = 0; n < N/2+1; n++ ) {
				TEMP[n] = (REX[n] * filterREX[n]) - (IMX[n] * filterIMX[n]);
				IMX[n] = (IMX[n] * filterREX[n]) + (REX[n] * filterIMX[n]);
				REX[n] = TEMP[n];
			}

			inverseFFT(REX, IMX);

			if ( seg == numSegments ) { 
				for(var n = 0; n < segmentSize; n++) {
					result[segStart + n] += REX[n];
				}
			} else {
				for(var n = 0; n < N; n++) {
					result[segStart + n] += REX[n];
				}
			}
			
			worker.postMessage({ type: "progress", value: (seg / numSegments) * 100});

		}

		for ( var n = 0; n < sigLength; n++ ) signal.X[chan][n] = result[n];
	
	}

	worker.postMessage({ type: "status", message: "Convolution complete. Convolved " + (numSegments+1) + " segments."});
	
	return true;

}

this.onmessage = function(message) {

	var command = message.data.command;
	
	switch (command) {
	case "process":
	
		var signal = reconstruct(message.data.signal);		
		var filter = reconstruct(message.data.filter);
		
		convolve(signal, filter, 18);
		var rms = getRMS(signal);
		volumedB(signal, -18 - rms);
		
		signal = deconstruct(signal);
		
		var response = { type: "signal", signal: signal };

		worker.postMessage(response, getBufferList(signal));	
		
		break;
	default:
		worker.postMessage({ type: "error", message: "Command not recognized" });
		break;
	}	
	
};