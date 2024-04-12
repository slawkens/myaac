describe('Install MyAAC', () => {
	beforeEach(() => {
		// Cypress starts out with a blank slate for each test
		// so we must tell it to visit our website with the `cy.visit()` command.
		// Since we want to visit the same URL at the start of all our tests,
		// we include it in our beforeEach function so that it runs before each test
		cy.visit(Cypress.env('URL'))
	})

	it('Go through installer', () => {
		cy.visit(Cypress.env('URL') + '/install/?step=welcome')
		cy.wait(1000)

		cy.screenshot('install-welcome')

		// step 1 - Welcome
		cy.get('select[name="lang"]').select('en')

		//cy.get('input[type=button]').contains('Next »').click()

		cy.get('form').submit()

		// step 2 - License
		// just skip
		cy.contains('GNU/GPL License');
		cy.get('form').submit()

		// step 3 - Requirements
		cy.contains('Requirements check');

		cy.get('#step').then(elem => {
			elem.val('config');
		});

		cy.get('form').submit()

		// step 4 - Configuration
		cy.contains('Basic configuration');

		cy.get('#vars_server_path').click().clear().type(Cypress.env('SERVER_PATH'))

		cy.get('[type="checkbox"]').uncheck() // usage statistics uncheck

		cy.wait(1000)

		cy.get('form').submit()

		// check if there is any error


		// step 5 - Import Schema
		cy.contains('Import MySQL schema');

		// AAC is not installed yet, this message should not come
		cy.contains('Seems AAC is already installed. Skipping importing MySQL schema..').should('not.exist')

		cy.contains('[class="alert alert-success"]', 'Local configuration has been saved into file: config.local.php').should('be.visible')

		cy.get('form').submit()

		// step 6 - Admin Account
		cy.get('#vars_email').click().clear().type('admin@my-aac.org')
		cy.get('#vars_account').click().clear().type('admin')
		cy.get('#vars_password').click().clear().type('test1234')
		cy.get('#vars_password_confirm').click().clear().type('test1234')
		cy.get('#vars_player_name').click().clear().type('Admin')

		cy.get('form').submit()

		cy.contains('[class="alert alert-success"]', 'Congratulations', { timeout: 60000 }).should('be.visible')

		cy.wait(2000);

		cy.screenshot('install-finish')
	})
})
