

define('controllers/user', 'controllers/record', function (Dep) {

    return Dep.extend({

        getCollection: function (callback, context, usePreviouslyFetched) {
            context = context || this;
            Dep.prototype.getCollection.call(this, function (collection) {
                collection.data.filterList = ['internal'];
                callback.call(context, collection);
            }, context, usePreviouslyFetched);
        },

        createViewView: function (options, model) {
            if (model.isPortal()) {
                this.getRouter().dispatch('PortalUser', 'view', {id: model.id, model: model});
                return;
            }
            if (model.isApi()) {
                this.getRouter().dispatch('ApiUser', 'view', {id: model.id, model: model});
                return;
            }
            Dep.prototype.createViewView.call(this, options, model);
        }

    });
});
