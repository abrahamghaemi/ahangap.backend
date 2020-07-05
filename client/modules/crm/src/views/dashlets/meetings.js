

Espo.define('crm:views/dashlets/meetings', 'views/dashlets/abstract/record-list', function (Dep) {

    return Dep.extend({

        name: 'Meetings',

        scope: 'Meeting',

        listView: 'crm:views/meeting/record/list-expanded',

        rowActionsView: 'crm:views/meeting/record/row-actions/dashlet'

    });
});

