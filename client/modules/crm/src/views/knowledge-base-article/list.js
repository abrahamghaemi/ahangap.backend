

Espo.define('crm:views/knowledge-base-article/list', 'views/list-with-categories', function (Dep) {

    return Dep.extend({

        categoryScope: 'KnowledgeBaseCategory',

        categoryField: 'categories',

        categoryFilterType: 'inCategory'

    });

});
