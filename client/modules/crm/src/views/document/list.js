

Espo.define('crm:views/document/list', 'views/list-with-categories', function (Dep) {

    return Dep.extend({

        quickCreate: true,

        currentCategoryId: null,

        currentCategoryName: '',

        categoryScope: 'DocumentFolder',

        categoryField: 'folder',

        categoryFilterType: 'inCategory'

    });

});
