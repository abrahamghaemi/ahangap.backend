

 define('collection-factory', [], function () {

    var CollectionFactory = function (loader, modelFactory) {
        this.loader = loader;
        this.modelFactory = modelFactory;
    };

    _.extend(CollectionFactory.prototype, {

        loader: null,

        modelFactory: null,

        create: function (name, callback, context) {
            return new Promise(function (resolve) {
                context = context || this;
                this.modelFactory.getSeed(name, function (seed) {
                    var orderBy = this.modelFactory.metadata.get(['entityDefs', name, 'collection', 'orderBy']);
                    var order = this.modelFactory.metadata.get(['entityDefs', name, 'collection', 'order']);
                    var className = this.modelFactory.metadata.get(['clientDefs', name, 'collection']) || 'collection';
                    Espo.loader.require(className, function (collectionClass) {
                        var collection = new collectionClass(null, {
                            name: name,
                            orderBy: orderBy,
                            order: order
                        });
                        collection.model = seed;
                        collection._user = this.modelFactory.user;
                        collection.entityType = name;
                        if (callback) {
                            callback.call(context, collection);
                        }
                        resolve(collection);
                    }.bind(this));
                }.bind(this));
            }.bind(this));
        }
    });

    return CollectionFactory;

});
