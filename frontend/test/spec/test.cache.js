

describe('cache', function () {
	var cache;

	beforeEach(function (done) {
        require('cache', function (Cache) {
		  cache = new Cache();
          done();
        });
	});

	it('should have \'cache\' prefix', function () {
		expect(cache.prefix).toBe('cache');
	});
});
