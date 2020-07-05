

describe('field-manager', function () {
	var fieldManager;

	var defs = {
				'varchar': {
					'params': [
						{
							'name': 'required',
							'type': 'bool',
							'default': false
						},
						{
							'name': 'maxLength',
							'type': 'int'
						}
					]
				},
				'int': {
					'params': [
						{
							'name': 'required',
							'type': 'bool',
							'default': false
						},
						{
							'name': 'min',
							'type': 'int'
						},
						{
							'name': 'max',
							'type': 'int'
						}
					]
				},
				'float': {
					'params': [
						{
							'name': 'required',
							'type': 'bool',
							'default': false
						},
						{
							'name': 'min',
							'type': 'float'
						},
						{
							'name': 'max',
							'type': 'float'
						}
					]
				},
				'enum': {
					'params': [
						{
							'name': 'required',
							'type': 'bool',
							'default': false
						},
						{
							'name': 'options',
							'type': 'array'
						}
					]
				},
				'bool': {
					'params': []
				},
				'date': {
					'params': [
						{
							'name': 'required',
							'type': 'bool',
							'default': false
						}
					]
				},
				'datetime': {
					'params': [
						{
							'name': 'required',
							'type': 'bool',
							'default': false
						}
					]
				},
				'link': {
					'params': [
						{
							'name': 'required',
							'type': 'bool',
							'default': false
						}
					],
					'actualFields': ['id']
				},
				'linkParent': {
					'params': [
						{
							'name': 'required',
							'type': 'bool',
							'default': false
						}
					],
					'actualFields': ['id', 'type']
				},
				'linkMultiple': {
					'params': [
						{
							'name': 'required',
							'type': 'bool',
							'default': false
						}
					],
					'actualFields': ['ids']
				},
				'personName': {
					'actualFields': ['salutation', 'first', 'last'],
					'fields': {
						'salutation': {
							'type': 'varchar'
						},
						'first': {
							'type': 'enum'
						},
						'last': {
							'type': 'varchar'
						}
					},
					'naming': 'prefix',
					'notMergeable': true,
				},
				'address': {
					'actualFields': ['street', 'city', 'state', 'country', 'postalCode'],
					'fields': {
						'street': {
							'type': 'varchar'
						},
						'city': {
							'type': 'varchar'
						},
						'state': {
							'type': 'varchar'
						},
						'country': {
							'type': 'varchar'
						},
						'postalCode': {
							'type': 'varchar'
						},
					},
					'notMergeable': true
				}
	};

	beforeEach(function (done) {
		require(['field-manager', 'utils'], function (FieldManager) {
			fieldManager = new FieldManager(defs);
			done();
		});
	});

	it ('#isMergable should work correctly', function () {
		expect(fieldManager.isMergeable('address')).toBe(false);
		expect(fieldManager.isMergeable('link')).toBe(true);
	});

	it ('#getActualAttributeList should work correctly', function () {
		var fields = fieldManager.getActualAttributeList('address', 'billingAddress');
		expect(fields[0]).toBe('billingAddressStreet');

		var fields = fieldManager.getActualAttributeList('personName', 'name');
		expect(fields[2]).toBe('lastName');

		var fields = fieldManager.getActualAttributeList('link', 'account');
		expect(fields[0]).toBe('accountId');

		var fields = fieldManager.getActualAttributeList('varchar', 'name');
		expect(fields[0]).toBe('name');
	});


});
