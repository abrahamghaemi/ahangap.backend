

Espo.define('crm:views/knowledge-base-article/record/list', 'views/record/list', function (Dep) {

    return Dep.extend({

        rowActionsView: 'crm:views/knowledge-base-article/record/row-actions/default',

        actionMoveToTop: function (data) {
            var model = this.collection.get(data.id);
            if (!model) return;

            var index = this.collection.indexOf(model);
            if (index === 0) return;

            this.ajaxPostRequest('knowledgeBaseArticle/action/moveToTop', {
                id: model.id,
                where: this.collection.getWhere()
            }).then(function () {
                this.collection.fetch();
            }.bind(this));
        },

        actionMoveUp: function (data) {
            var model = this.collection.get(data.id);
            if (!model) return;

            var index = this.collection.indexOf(model);
            if (index === 0) return;

            this.ajaxPostRequest('knowledgeBaseArticle/action/moveUp', {
                id: model.id,
                where: this.collection.getWhere()
            }).then(function () {
                this.collection.fetch();
            }.bind(this));
        },

        actionMoveDown: function (data) {
            var model = this.collection.get(data.id);
            if (!model) return;

            var index = this.collection.indexOf(model);
            if ((index === this.collection.length - 1) && (this.collection.length === this.collection.total)) return;

            this.ajaxPostRequest('knowledgeBaseArticle/action/moveDown', {
                id: model.id,
                where: this.collection.getWhere()
            }).then(function () {
                this.collection.fetch();
            }.bind(this));
        },

        actionMoveToBottom: function (data) {
            var model = this.collection.get(data.id);
            if (!model) return;

            var index = this.collection.indexOf(model);
            if ((index === this.collection.length - 1) && (this.collection.length === this.collection.total)) return;

            this.ajaxPostRequest('knowledgeBaseArticle/action/moveToBottom', {
                id: model.id,
                where: this.collection.getWhere()
            }).then(function () {
                this.collection.fetch();
            }.bind(this));
        }

    });
});

