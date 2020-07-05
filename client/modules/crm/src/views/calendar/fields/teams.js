

Espo.define('crm:views/calendar/fields/teams', 'views/fields/link-multiple', function (Dep) {

    return Dep.extend({

        foreignScope: 'Team',

        getSelectBoolFilterList: function () {
            if (this.getAcl().get('userPermission') === 'team') {
                return ['onlyMy'];
            }
        }
    });
});
