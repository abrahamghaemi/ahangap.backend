

describe('acl-manager', function () {
	var acl;

	beforeEach(function (done) {
		require('acl-manager', function (Acl) {
			acl = new Acl();
			acl.user = {
				isAdmin: function () {
					return false;
				}
			};
			done();
		});
	});

	it("should check an access properly", function () {
		acl.set({
			table: {
				Lead: {
					read: 'team',
					edit: 'own',
					delete: 'no',
				},
				Opportunity: false,
				Meeting: true
			}
		});

		expect(acl.check('Lead', 'read')).toBe(true);

		expect(acl.check('Lead', 'read', false, false)).toBe(true);
		expect(acl.check('Lead', 'read', false, true)).toBe(true);
		expect(acl.check('Lead', 'read', true)).toBe(true);

		expect(acl.check('Lead', 'edit')).toBe(true);
		expect(acl.check('Lead', 'edit', false, true)).toBe(true);
		expect(acl.check('Lead', 'edit', true, false)).toBe(true);

		expect(acl.check('Lead', 'delete')).toBe(false);

		expect(acl.check('Account', 'edit')).toBe(true);

		expect(acl.check('Opportunity', 'edit')).toBe(false);
		expect(acl.check('Meeting', 'edit')).toBe(true);
	});


});
