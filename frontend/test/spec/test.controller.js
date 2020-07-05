

describe('controller', function () {
	var controller;
	var viewFactory;
	var view;

	var ControllerClass;

	beforeEach(function (done) {
		require('controller', function (Controller) {
			ControllerClass = Controller;
			viewFactory = {
				create: {}
			};
			view = {
				render: {},
				setView: {}
			};

			controller = new Controller({}, {viewFactory: viewFactory});
			spyOn(viewFactory, 'create').and.returnValue(view);
			spyOn(view, 'render');
			spyOn(view, 'setView');
			done();
		});
	});

	it ('#set should set param', function () {
		controller.set('some', 'test');
		expect(controller.params['some']).toBe('test');
	});

	it ('#get should get param', function () {
		controller.set('some', 'test');
		expect(controller.get('some')).toBe('test');
	});

	it ("different controllers should use same param set", function () {
		var someController = new ControllerClass(controller.params, {viewFactory: viewFactory});
		someController.set('some', 'test');
		expect(controller.get('some')).toBe(someController.get('some'));
	});

});
