$(function() {
	var audioEls = $("audio");

	$("button").click( function() {

		var button = $(this);
		
		var audioId = button.attr("id");				
		var isPaused = audioEls[audioId].paused;
		
		$("button").html("Play Sound");

		audioEls.each( function(){
			$(this)[0].pause();
			$(this)[0].currentTime = 0;
		});		

		if (isPaused) { 
			audioEls[audioId].play();
			button.html("Pause Sound");
		} else audioEls[audioId].pause();
		
	});
	
});