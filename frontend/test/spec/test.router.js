

describe('router', function () {
	var router;

	beforeEach(function (done) {
		require('router', function (Router) {
			router = new Router();
			done();
		});
	});

	it('should parse slashed options', function () {
		var options = router._parseOptionsParams("p=1&t=2");
		expect(options.p).toBe('1');
		expect(options.t).toBe('2');

		var options = router._parseOptionsParams("p=1&");
		expect(options.p).toBe('1');

		var options = router._parseOptionsParams("p");
		expect(options).toBe("p");

		var options = router._parseOptionsParams("p=1&t");
		expect(options.p).toBe('1');
		expect(options.t).toBe(true);

		var options = router._parseOptionsParams("p=1&t=");
		expect(options.t).toBe('');
	});

});
