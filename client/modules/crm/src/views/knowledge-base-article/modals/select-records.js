

Espo.define('crm:views/knowledge-base-article/modals/select-records', 'crm:views/document/modals/select-records', function (Dep) {

    return Dep.extend({

        categoryScope: 'KnowledgeBaseCategory',

        categoryField: 'categories',

        categoryFilterType: 'inCategory'

    });

});
