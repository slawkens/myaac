function togglePassword(button) {
	const input = button.previousElementSibling;
	const isPassword = input.type === 'password';
	input.type = isPassword ? 'text' : 'password';
	button.setAttribute('aria-pressed', isPassword);
}
