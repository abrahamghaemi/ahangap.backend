

Espo.define('crm:views/document/modals/select-records', 'views/modals/select-records-with-categories', function (Dep) {

    return Dep.extend({

        categoryScope: 'DocumentFolder',

        categoryField: 'folder',

        categoryFilterType: 'inCategory'

    });

});
