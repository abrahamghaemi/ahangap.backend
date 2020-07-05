

Espo.define('crm:views/case/fields/contact', 'views/fields/link', function (Dep) {

    return Dep.extend({

        getSelectFilters: function () {
            if (this.model.get('accountId')) {
                return {
                    'account': {
                        type: 'equals',
                        attribute: 'accountId',
                        value: this.model.get('accountId'),
                        data: {
                            type: 'is',
                            nameValue: this.model.get('accountName')
                        }
                    }
                };
            }
        },

        getCreateAttributes: function () {
            if (this.model.get('accountId')) {
                return {
                    accountId: this.model.get('accountId'),
                    accountName: this.model.get('accountName')
                }
            }
        }

    });

});
