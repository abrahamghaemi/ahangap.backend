

describe('layout-manager', function () {
	var layoutManager;

	beforeEach(function (done) {
		require('layout-manager', function (LayoutManager) {
			layoutManager = new LayoutManager();
			spyOn(layoutManager, 'ajax').and.callFake(function (options) {});

			done();
		});
	});

	it("should call ajax to fetch new layout", function () {
		layoutManager.get('some', 'list');
		expect(layoutManager.ajax.calls.mostRecent().args[0].url).toBe('some/layout/list');
	});
});
