


describe("search-manager", function () {
	var searchManager;

	beforeEach(function () {
		require('search-manager', function (SearchManager) {
			var collection = {
				name: 'some',
			}
			searchManager = new SearchManager(collection, 'list', null);
			done();
		}
	});


});
