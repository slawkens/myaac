const REQUIRED_LOGIN_MESSAGE = 'Please enter your account name and your password.';
const YOU_ARE_NOT_LOGGEDIN = 'You are not logged in.';

describe('Check Protected Pages', () => {

	// character actions
	it('Go to accouht character creation page', () => {
		cy.visit({
			url: Cypress.env('URL') + '/account/character/create',
			method: 'GET',
		})
		cy.contains(REQUIRED_LOGIN_MESSAGE)
	})

	it('Go to accouht character deletion page', () => {
		cy.visit({
			url: Cypress.env('URL') + '/account/character/delete',
			method: 'GET',
		})
		cy.contains(REQUIRED_LOGIN_MESSAGE)
	})

	// account actions
	it('Go to accouht email change page', () => {
		cy.visit({
			url: Cypress.env('URL') + '/account/email',
			method: 'GET',
		})
		cy.contains(REQUIRED_LOGIN_MESSAGE)
	})

	it('Go to accouht password change page', () => {
		cy.visit({
			url: Cypress.env('URL') + '/account/password',
			method: 'GET',
		})
		cy.contains(REQUIRED_LOGIN_MESSAGE)
	})

	it('Go to accouht info change page', () => {
		cy.visit({
			url: Cypress.env('URL') + '/account/info',
			method: 'GET',
		})
		cy.contains(REQUIRED_LOGIN_MESSAGE)
	})

	it('Go to accouht logout change page', () => {
		cy.visit({
			url: Cypress.env('URL') + '/account/logout',
			method: 'GET',
		})
		cy.contains(REQUIRED_LOGIN_MESSAGE)
	})

	// guild actions
	it('Go to guild creation page', () => {
		cy.visit({
			url: Cypress.env('URL') + '/?subtopic=guilds&action=create',
			method: 'GET',
		})
		cy.contains(YOU_ARE_NOT_LOGGEDIN)
	})

	it('Go to guilds cleanup players action page', () => {
		cy.visit({
			url: Cypress.env('URL') + '/?subtopic=guilds&action=cleanup_players',
			method: 'GET',
		})
		cy.contains(YOU_ARE_NOT_LOGGEDIN)
	})

	it('Go to guilds cleanup guilds action page', () => {
		cy.visit({
			url: Cypress.env('URL') + '/?subtopic=guilds&action=cleanup_guilds',
			method: 'GET',
		})
		cy.contains(YOU_ARE_NOT_LOGGEDIN)
	})

})
