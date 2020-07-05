

Espo.define('crm:views/campaign/fields/template', 'views/fields/link', function (Dep) {

    return Dep.extend({

        createDisabled: true,

        getSelectFilters: function () {
            return {
                entityType: {
                    type: 'in',
                    value: [this.getMetadata().get(['entityDefs', 'Campaign', 'fields', this.name, 'targetEntityType'])]
                }
            };
        }
    });

});
