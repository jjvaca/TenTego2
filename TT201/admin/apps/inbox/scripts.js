function wstaw(start_tag,stop_tag){
	var okno = document.getElementById("content_input");
	if(!okno.setSelectionRange)
	{
		var selected = document.selection.createRange().text; 
		if(selected.length <= 0)
		{
			okno.value +=start_tag + stop_tag;
		}
		else{
			document.selection.createRange().text = start_tag + selected + stop_tag;
		}
	}
	else
	{
		var pretext = okno.value.substring(0, okno.selectionStart);
		var codetext = start_tag + okno.value.substring(okno.selectionStart,
		okno.selectionEnd) + stop_tag;
		var posttext = okno.value.substring(okno.selectionEnd, okno.value.length)
		if(codetext == start_tag + stop_tag)
		{
			okno.value +=start_tag + stop_tag;
		} else {
			okno.value = pretext + codetext + posttext;
		}
	}
	okno.focus ();
}