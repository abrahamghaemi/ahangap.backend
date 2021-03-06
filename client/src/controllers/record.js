

define('controllers/record', 'controller', function (Dep) {

    return Dep.extend({

        viewMap: null,

        defaultAction: 'list',

        checkAccess: function (action) {
            if (this.getAcl().check(this.name, action)) {
                return true;
            }
            return false;
        },

        initialize: function () {
            this.viewMap = this.viewMap || {};
            this.viewsMap = this.viewsMap || {};
            this.collectionMap = {};
        },

        getViewName: function (type) {
            return this.viewMap[type] || this.getMetadata().get(['clientDefs', this.name, 'views', type]) || 'views/' + Espo.Utils.camelCaseToHyphen(type);
        },

        beforeList: function () {
            this.handleCheckAccess('read');
        },

        actionList: function (options) {
            var isReturn = options.isReturn;
            if (this.getRouter().backProcessed) {
                isReturn = true;
            }

            var key = this.name + 'List';

            if (!isReturn) {
                var stored = this.getStoredMainView(key);
                if (stored) {
                    this.clearStoredMainView(key);
                }
            }

            this.getCollection(function (collection) {
                this.listenToOnce(this.baseController, 'action', function () {
                    collection.abortLastFetch();
                }, this);

                this.main(this.getViewName('list'), {
                    scope: this.name,
                    collection: collection,
                    params: options
                }, null, isReturn, key);
            }, this, false);
        },

        beforeView: function () {
            this.handleCheckAccess('read');
        },

        createViewView: function (options, model, view) {
            var view = view || this.getViewName('detail');
            this.main(view, {
                scope: this.name,
                model: model,
                returnUrl: options.returnUrl,
                returnDispatchParams: options.returnDispatchParams,
                params: options
            });
        },

        prepareModelView: function (model, options) {},

        actionView: function (options) {
            var id = options.id;

            var isReturn = this.getRouter().backProcessed;
            if (isReturn) {
                if (this.lastViewActionOptions && this.lastViewActionOptions.id === id) {
                    options = this.lastViewActionOptions;
                }
            } else {
                delete this.lastViewActionOptions;
            }
            this.lastViewActionOptions = options;

            var createView = function (model) {
                this.prepareModelView(model, options);
                this.createViewView.call(this, options, model);
            }.bind(this);

            if ('model' in options) {
                var model = options.model;
                createView(model);

                this.showLoadingNotification();

                model.fetch().then(function () {
                    this.hideLoadingNotification();
                }.bind(this));

                this.listenToOnce(this.baseController, 'action', function () {
                    model.abortLastFetch();
                }, this);
            } else {
                this.getModel().then(function (model) {
                    model.id = id;

                    this.showLoadingNotification();

                    model.fetch({main: true}).then(function () {
                        if (model.get('deleted')) {
                            this.listenToOnce(model, 'after:restore-deleted', function () {
                                createView(model);
                            }, this);

                            this.prepareModelView(model, options);
                            this.createViewView(options, model, 'views/deleted-detail');
                            return;
                        }
                        createView(model);
                    }.bind(this));

                    this.listenToOnce(this.baseController, 'action', function () {
                        model.abortLastFetch();
                    }, this);
                }.bind(this));
            }
        },

        beforeCreate: function () {
            this.handleCheckAccess('create');
        },

        prepareModelCreate: function (model, options) {
            this.listenToOnce(model, 'before:save', function () {
                var key = this.name + 'List';
                var stored = this.getStoredMainView(key);
                if (stored && !stored.storeViewAfterCreate) {
                    this.clearStoredMainView(key);
                }
            }, this);

            this.listenToOnce(model, 'after:save', function () {
                var key = this.name + 'List';
                var stored = this.getStoredMainView(key);
                if (stored && stored.storeViewAfterCreate && stored.collection) {
                    this.listenToOnce(stored, 'after:render', function () {
                        stored.collection.fetch();
                    });
                }
            }, this);
        },

        create: function (options) {
            options = options || {};
            this.getModel().then(function (model) {
                if (options.relate) {
                    model.setRelate(options.relate);
                }

                var o = {
                    scope: this.name,
                    model: model,
                    returnUrl: options.returnUrl,
                    returnDispatchParams: options.returnDispatchParams,
                    params: options
                };

                if (options.attributes) {
                    model.set(options.attributes);
                }

                this.prepareModelCreate(model, options);

                this.main(this.getViewName('edit'), o);
            }.bind(this));
        },

        actionCreate: function (options) {
            this.create(options);
        },

        beforeEdit: function () {
            this.handleCheckAccess('edit');
        },

        prepareModelEdit: function (model, options) {
            this.listenToOnce(model, 'before:save', function () {
                var key = this.name + 'List';
                var stored = this.getStoredMainView(key);
                if (stored && !stored.storeViewAfterUpdate) {
                    this.clearStoredMainView(key);
                }
            }, this);
        },

        actionEdit: function (options) {
            var id = options.id;

            this.getModel().then(function (model) {
                model.id = id;
                if (options.model) {
                    model = options.model;
                }

                this.prepareModelEdit(model, options);

                this.showLoadingNotification();
                this.listenToOnce(model, 'sync', function () {
                    var o = {
                        scope: this.name,
                        model: model,
                        returnUrl: options.returnUrl,
                        returnDispatchParams: options.returnDispatchParams,
                        params: options
                    };

                    if (options.attributes) {
                        o.attributes = options.attributes;
                    }

                    this.main(this.getViewName('edit'), o);
                }, this);
                model.fetch({main: true});

                this.listenToOnce(this.baseController, 'action', function () {
                    model.abortLastFetch();
                }, this);
            }.bind(this));
        },

        beforeMerge: function () {
            this.handleCheckAccess('edit');
        },

        actionMerge: function (options) {
            var ids = options.ids.split(',');

            this.getModel().then(function (model) {
                var models = [];

                var proceed = function () {
                    this.main('views/merge', {
                        models: models,
                        scope: this.name,
                        collection: options.collection
                    });
                }.bind(this);

                var i = 0;
                ids.forEach(function (id) {
                    var current = model.clone();
                    current.id = id;
                    models.push(current);
                    this.listenToOnce(current, 'sync', function () {
                        i++;
                        if (i == ids.length) {
                            proceed();
                        }
                    });
                    current.fetch();
                }.bind(this));
            }.bind(this));
        },

        /**
         * Get collection for the current controller.
         * @param {collection}.
         */
        getCollection: function (callback, context, usePreviouslyFetched) {
            context = context || this;

            if (!this.name) {
                throw new Error('No collection for unnamed controller');
            }
            var collectionName = this.entityType || this.name;
            if (usePreviouslyFetched) {
                if (collectionName in this.collectionMap) {
                    var collection = this.collectionMap[collectionName];// = this.collectionMap[collectionName].clone();
                    callback.call(context, collection);
                    return;
                }
            }
            return this.collectionFactory.create(collectionName, function (collection) {
                this.collectionMap[collectionName] = collection;
                this.listenTo(collection, 'sync', function () {
                    collection.isFetched = true;
                }, this);
                if (callback) {
                    callback.call(context, collection);
                }
            }, context);
        },

        /**
         * Get model for the current controller.
         * @param {model}.
         */
        getModel: function (callback, context) {
            context = context || this;

            if (!this.name) {
                throw new Error('No collection for unnamed controller');
            }
            var modelName = this.entityType || this.name;

            return this.modelFactory.create(modelName, function (model) {
                if (callback) {
                    callback.call(context, model);
                }
            }, context);
        },

    });
});
