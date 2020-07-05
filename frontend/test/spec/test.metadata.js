

describe('metadata', function () {
	var metadata;

	beforeEach(function (done) {
		require('metadata', function (Metadata) {
			metadata = new Metadata();
			metadata.data = {
				recordDefs: {
					Lead: {
						some: {type: 'varchar'},
					}
				}
			};
			done();
		});
	});

	it('#get should work correctly', function () {
		expect(metadata.get('recordDefs.Lead.some')).toBe(metadata.data.recordDefs.Lead.some);
		expect(metadata.get('recordDefs.Contact')).toBe(null);
	});

});
