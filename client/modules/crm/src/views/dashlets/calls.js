

Espo.define('crm:views/dashlets/calls', 'views/dashlets/abstract/record-list', function (Dep) {

    return Dep.extend({

        name: 'Calls',

        scope: 'Call',

        listView: 'crm:views/call/record/list-expanded',

        rowActionsView: 'crm:views/call/record/row-actions/dashlet'

    });
});

