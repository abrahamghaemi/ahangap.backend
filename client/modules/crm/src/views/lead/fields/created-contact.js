

Espo.define('crm:views/lead/fields/created-contact', 'views/fields/link', function (Dep) {

    return Dep.extend({

        getSelectFilters: function () {
            if (this.model.get('createdAccountId')) {
                return {
                    'account': {
                        type: 'equals',
                        attribute: 'accountId',
                        value: this.model.get('createdAccountId'),
                        data: {
                            type: 'is',
                            nameValue: this.model.get('createdAccountName')
                        }
                    }
                };
            }
        },
    });

});
