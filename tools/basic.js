function MouseOverBigButton(source) {
	if (source?.firstElementChild?.style) {
		source.firstElementChild.style.visibility = "visible";
	}
}
function MouseOutBigButton(source) {
	if (source?.firstElementChild?.style) {
		source.firstElementChild.style.visibility = "hidden";
	}
}
function BigButtonAction(path) {
  window.location = path;
}
