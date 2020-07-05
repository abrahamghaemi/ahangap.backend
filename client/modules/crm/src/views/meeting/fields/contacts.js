

Espo.define('crm:views/meeting/fields/contacts', 'crm:views/meeting/fields/attendees', function (Dep) {

    return Dep.extend({

        getSelectFilters: function () {
            if (this.model.get('parentType') == 'Account' && this.model.get('parentId')) {
                return {
                    'account': {
                        type: 'equals',
                        attribute: 'accountId',
                        value: this.model.get('parentId'),
                        data: {
                            type: 'is',
                            nameValue: this.model.get('parentName')
                        }
                    }
                };
            }
        },
    });

});
