describe('Check Public Pages', () => {

	/// news
	it('Go to news page', () => {
		cy.visit({
			url: Cypress.env('URL') + '/news',
			method: 'GET',
		})
	})

	it('Go to news archive page', () => {
		cy.visit({
			url: Cypress.env('URL') + '/news/archive',
			method: 'GET',
		})
	})

	it('Go to changelog page', () => {
		cy.visit({
			url: Cypress.env('URL') + '/changelog',
			method: 'GET',
		})
	})

	/// account management
	it('Go to account manage page', () => {
		cy.visit({
			url: Cypress.env('URL') + '/account/manage',
			method: 'GET',
		})
	})

	it('Go to account create page', () => {
		cy.visit({
			url: Cypress.env('URL') + '/account/create',
			method: 'GET',
		})
	})

	it('Go to account lost page', () => {
		cy.visit({
			url: Cypress.env('URL') + '/account/lost',
			method: 'GET',
		})
	})

	it('Go to rules page', () => {
		cy.visit({
			url: Cypress.env('URL') + '/rules',
			method: 'GET',
		})
	})

	// community
	it('Go to online page', () => {
		cy.visit({
			url: Cypress.env('URL') + '/online',
			method: 'GET',
		})
	})

	it('Go to characters list page', () => {
		cy.visit({
			url: Cypress.env('URL') + '/characters',
			method: 'GET',
		})
	})

	it('Go to guilds page', () => {
		cy.visit({
			url: Cypress.env('URL') + '/guilds',
			method: 'GET',
		})
	})

	it('Go to highscores page', () => {
		cy.visit({
			url: Cypress.env('URL') + '/highscores',
			method: 'GET',
		})
	})

	it('Go to last kills page', () => {
		cy.visit({
			url: Cypress.env('URL') + '/lastkills',
			method: 'GET',
		})
	})

	it('Go to houses page', () => {
		cy.visit({
			url: Cypress.env('URL') + '/houses',
			method: 'GET',
		})
	})

	it('Go to bans page', () => {
		cy.visit({
			url: Cypress.env('URL') + '/bans',
			method: 'GET',
		})
	})

	it('Go to forum page', () => {
		cy.visit({
			url: Cypress.env('URL') + '/forum',
			method: 'GET',
		})
	})

	it('Go to team page', () => {
		cy.visit({
			url: Cypress.env('URL') + '/team',
			method: 'GET',
		})
	})

	// library
	it('Go to creatures page', () => {
		cy.visit({
			url: Cypress.env('URL') + '/creatures',
			method: 'GET',
		})
	})

	it('Go to spells page', () => {
		cy.visit({
			url: Cypress.env('URL') + '/spells',
			method: 'GET',
		})
	})

	it('Go to server info page', () => {
		cy.visit({
			url: Cypress.env('URL') + '/serverInfo',
			method: 'GET',
		})
	})

	it('Go to commands page', () => {
		cy.visit({
			url: Cypress.env('URL') + '/commands',
			method: 'GET',
		})
	})

	it('Go to downloads page', () => {
		cy.visit({
			url: Cypress.env('URL') + '/downloads',
			method: 'GET',
		})
	})

	it('Go to gallery page', () => {
		cy.visit({
			url: Cypress.env('URL') + '/gallery',
			method: 'GET',
		})
	})

	it('Go to experience table page', () => {
		cy.visit({
			url: Cypress.env('URL') + '/experienceTable',
			method: 'GET',
		})
	})

	it('Go to faq page', () => {
		cy.visit({
			url: Cypress.env('URL') + '/faq',
			method: 'GET',
		})
	})
})
