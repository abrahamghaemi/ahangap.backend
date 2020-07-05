

Espo.define('crm:views/calendar/fields/users', 'views/fields/link-multiple', function (Dep) {

    return Dep.extend({

        foreignScope: 'User',

        sortable: true,

        getSelectBoolFilterList: function () {
            if (this.getAcl().get('userPermission') === 'team') {
                return ['onlyMyTeam'];
            }
        },

        getSelectPrimaryFilterName: function () {
            return 'active';
        }
    });
});

