

describe('loader', function () {

	it("should convert name to path", function () {
		expect(Espo.loader._nameToPath('views/record/edit')).toBe('../../client/src/views/record/edit.js');
		expect(Espo.loader._nameToPath('views/home/dashlet-header')).toBe('../../client/src/views/home/dashlet-header.js');
	});

});
