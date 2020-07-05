

Espo.define('crm:views/contact/detail', 'views/detail', function (Dep) {

    return Dep.extend({

        relatedAttributeMap: {
            'opportunities': {
                'accountId': 'accountId',
                'accountName': 'accountName'
            },
            'cases': {
                'accountId': 'accountId',
                'accountName': 'accountName',
                'id': 'contactId',
                'name': 'contactName',
            }
        },

        selectRelatedFilters: {
            'cases': {
                'account': function () {
                    if (this.model.get('accountId')) {
                        return {
                            attribute: 'accountId',
                            type: 'equals',
                            value: this.model.get('accountId'),
                            data: {
                                type: 'is',
                                nameValue: this.model.get('accountName')
                            }
                        };
                    }
                }
            },
            'opportunities': {
                'account': function () {
                    if (this.model.get('accountId')) {
                        return {
                            attribute: 'accountId',
                            type: 'equals',
                            value: this.model.get('accountId'),
                            data: {
                                type: 'is',
                                nameValue: this.model.get('accountName')
                            }
                        };
                    }
                }
            }
        }

    });
});
