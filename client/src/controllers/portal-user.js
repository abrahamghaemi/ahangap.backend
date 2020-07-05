

define('controllers/portal-user', 'controllers/record', function (Dep) {

    return Dep.extend({

        entityType: 'User',

        getCollection: function (callback, context, usePreviouslyFetched) {
            context = context || this;
            Dep.prototype.getCollection.call(this, function (collection) {
                collection.data.filterList = ['portal'];
                callback.call(context, collection);
            }, context, usePreviouslyFetched);
        },

        createViewView: function (options, model) {
            if (!model.isPortal()) {
                if (model.isApi()) {
                    this.getRouter().dispatch('ApiUser', 'view', {id: model.id, model: model});
                    return;
                }
                this.getRouter().dispatch('User', 'view', {id: model.id, model: model});
                return;
            }
            Dep.prototype.createViewView.call(this, options, model);
        },

        actionCreate: function (options) {
            options = options || {};
            options.attributes = options.attributes  || {};
            options.attributes.type = 'portal';
            Dep.prototype.actionCreate.call(this, options);
        }

    });
});
