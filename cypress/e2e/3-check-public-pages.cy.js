describe('Check Public Pages', () => {

	/// news
	it('Go to news page', () => {
		cy.visit({
			url: '/news',
			method: 'GET',
		})
	})

	it('Go to news archive page', () => {
		cy.visit({
			url: '/news/archive',
			method: 'GET',
		})
	})

	it('Go to changelog page', () => {
		cy.visit({
			url: '/change-log',
			method: 'GET',
		})
	})

	/// account management
	it('Go to account manage page', () => {
		cy.visit({
			url: '/account/manage',
			method: 'GET',
		})
	})

	it('Go to account create page', () => {
		cy.visit({
			url: '/account/create',
			method: 'GET',
		})
	})

	it('Go to account lost page', () => {
		cy.visit({
			url: '/account/lost',
			method: 'GET',
		})
	})

	it('Go to rules page', () => {
		cy.visit({
			url: '/rules',
			method: 'GET',
		})
	})

	// community
	it('Go to online page', () => {
		cy.visit({
			url: '/online',
			method: 'GET',
		})
	})

	it('Go to characters list page', () => {
		cy.visit({
			url: '/characters',
			method: 'GET',
		})
	})

	it('Go to guilds page', () => {
		cy.visit({
			url: '/guilds',
			method: 'GET',
		})
	})

	it('Go to highscores page', () => {
		cy.visit({
			url: '/highscores',
			method: 'GET',
		})
	})

	it('Go to last kills page', () => {
		cy.visit({
			url: '/last-kills',
			method: 'GET',
		})
	})

	it('Go to houses page', () => {
		cy.visit({
			url: '/houses',
			method: 'GET',
		})
	})

	it('Go to bans page', () => {
		cy.visit({
			url: '/bans',
			method: 'GET',
		})
	})

	it('Go to forum page', () => {
		cy.visit({
			url: '/forum',
			method: 'GET',
		})
	})

	it('Go to team page', () => {
		cy.visit({
			url: '/team',
			method: 'GET',
		})
	})

	// library
	it('Go to monsters page', () => {
		cy.visit({
			url: '/monsters',
			method: 'GET',
		})
	})

	it('Go to spells page', () => {
		cy.visit({
			url: '/spells',
			method: 'GET',
		})
	})

	it('Go to server info page', () => {
		cy.visit({
			url: '/ots-info',
			method: 'GET',
		})
	})

	it('Go to commands page', () => {
		cy.visit({
			url: '/commands',
			method: 'GET',
		})
	})

	it('Go to downloads page', () => {
		cy.visit({
			url: '/downloads',
			method: 'GET',
		})
	})

	it('Go to gallery page', () => {
		cy.visit({
			url: '/gallery',
			method: 'GET',
		})
	})

	it('Go to experience table page', () => {
		cy.visit({
			url: '/exp-table',
			method: 'GET',
		})
	})

	it('Go to faq page', () => {
		cy.visit({
			url: '/faq',
			method: 'GET',
		})
	})
})
