describe('Create Account Page', () => {
	beforeEach(() => {
		// Cypress starts out with a blank slate for each test
		// so we must tell it to visit our website with the `cy.visit()` command.
		// Since we want to visit the same URL at the start of all our tests,
		// we include it in our beforeEach function so that it runs before each test
		cy.visit(Cypress.env('URL') + '/index.php/account/create')
	})

	it('Create Test Account', () => {
		cy.screenshot('create-account-page')

		cy.get('#account_input').type('tester')
		cy.get('#email').type('tester@example.com')

		cy.get('#password').type('test1234')
		cy.get('#password_confirm').type('test1234')

		cy.get('#character_name').type('Slaw')

		cy.get('#sex1').check()
		cy.get('#vocation1').check()
		cy.get('#accept_rules').check()

		cy.get('#createaccount').submit()

		// no errors please
		cy.contains('The Following Errors Have Occurred:').should('not.exist')

		// ss of post page
		cy.screenshot('create-account-page-post')
	})
})
