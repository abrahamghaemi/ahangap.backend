


Espo.define('crm:views/campaign/detail', 'views/detail', function (Dep) {

    return Dep.extend({

        relatedAttributeMap: {
            'massEmails': {
                'targetListsIds': 'targetListsIds',
                'targetListsNames': 'targetListsNames',
                'excludingTargetListsIds': 'excludingTargetListsIds',
                'excludingTargetListsNames': 'excludingTargetListsNames',
            },
        },

        relatedAttributeFunctions: {
            'massEmails':  function () {
                return {
                    name: this.model.get('name') + ' ' + this.getDateTime().getToday()
                };
            }
        }

    });
});


