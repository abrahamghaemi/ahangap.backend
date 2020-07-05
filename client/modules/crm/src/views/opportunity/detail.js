

Espo.define('crm:views/opportunity/detail', 'views/detail', function (Dep) {

    return Dep.extend({

        relatedAttributeMap: {
            'contacts': {
                'accountId': 'accountId',
                'accountName': 'accountName'
            },
        },

        relatedAttributeFunctions: {
            'documents': function () {
                var data = {};
                if (this.model.get('accountId')) {
                    data['accountsIds'] = [this.model.get('accountId')]
                }
                return data;
            }
        },

        selectRelatedFilters: {
            'contacts': {
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
                },
            },
            'documents': {
                'accounts': function () {
                    var accountId = this.model.get('accountId');
                    if (accountId) {
                        var nameHash = {};
                        nameHash[accountId] = this.model.get('accountName');
                        return {
                            field: 'accounts',
                            type: 'linkedWith',
                            value: [accountId],
                            data: {
                                nameHash: nameHash
                            }
                        };
                    }
                },
            },
        },

    });
});
