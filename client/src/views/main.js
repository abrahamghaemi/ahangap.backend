

Espo.define('views/main', 'view', function (Dep) {

    return Dep.extend({

        scope: null,

        name: null,

        menu: null,

        events: {
            'click .action': function (e) {
                Espo.Utils.handleAction(this, e);
            },
        },

        init: function () {
            this.scope = this.options.scope || this.scope;
            this.menu = {};

            this.options.params = this.options.params || {};

            if (this.name && this.scope) {
                this.menu = this.getMetadata().get('clientDefs.' + this.scope + '.menu.' + this.name.charAt(0).toLowerCase() + this.name.slice(1)) || {};
            }

            this.menu = Espo.Utils.cloneDeep(this.menu);

            ['buttons', 'actions', 'dropdown'].forEach(function (type) {
                this.menu[type] = this.menu[type] || [];
            }, this);

            this.updateLastUrl();
        },

        updateLastUrl: function () {
            this.lastUrl = this.getRouter().getCurrentUrl();
        },

        getMenu: function () {
            if (this.menuDisabled) return {};

            var menu = {};

            if (this.menu) {
                ['buttons', 'actions', 'dropdown'].forEach(function (type) {
                    (this.menu[type] || []).forEach(function (item) {
                        menu[type] = menu[type] || [];
                        if (Espo.Utils.checkActionAccess(this.getAcl(), this.model || this.scope, item)) {
                            menu[type].push(item);
                        }
                        item.name = item.name || item.action;
                        item.action = item.action || this.name;
                    }, this);
                }, this);
            }

            return menu;
        },

        getHeader: function () {},

        buildHeaderHtml: function (arr) {
            var a = [];
            arr.forEach(function (item) {
                a.push('<div class="pull-left">' + item + '</div>');
            }, this);

            return '<div class="clearfix header-breadcrumbs">' + a.join('<div class="pull-left breadcrumb-separator"> &raquo </div>') + '</div>';
        },

        getHeaderIconHtml: function () {
            return this.getHelper().getScopeColorIconHtml(this.scope);
        },

        actionShowModal: function (data) {
            var view = data.view;
            if (!view) {
                return;
            };
            this.createView('modal', view, {
                model: this.model,
                collection: this.collection
            }, function (view) {
                view.render();
                this.listenTo(view, 'after:save', function () {
                    if (this.model) {
                        this.model.fetch();
                    }
                    if (this.collection) {
                        this.collection.fetch();
                    }
                }, this);
            }.bind(this));
        },

        addMenuItem: function (type, item, toBeginnig, doNotReRender) {
            if (item) {
                item.name = item.name || item.action;
                var name = item.name;

                if (name) {
                    var index = -1;
                    this.menu[type].forEach(function (data, i) {
                        data = data || {};
                        if (data.name === name) {
                            index = i;
                            return;
                        }
                    }, this);
                    if (~index) {
                        this.menu[type].splice(index, 1);
                    }
                }
            }

            var method = 'push';
            if (toBeginnig) {
                method  = 'unshift';
            }
            this.menu[type][method](item);

            if (!doNotReRender && this.isRendered()) {
                this.getView('header').reRender();
            }
        },

        removeMenuItem: function (name, doNotReRender) {
            var index = -1;
            var type = false;

            ['actions', 'dropdown', 'buttons'].forEach(function (t) {
                this.menu[t].forEach(function (item, i) {
                    item = item || {};
                    if (item.name == name) {
                        index = i;
                        type = t;
                    }
                }, this);
            }, this);

            if (~index && type) {
                this.menu[type].splice(index, 1);
            }

            if (!doNotReRender && this.isRendered()) {
                this.getView('header').reRender();
            }
        },

        disableMenuItem: function (name) {
            this.$el.find('.header .header-buttons [data-name="'+name+'"]').addClass('disabled').attr('disabled');
        },

        enableMenuItem: function (name) {
            this.$el.find('.header .header-buttons [data-name="'+name+'"]').removeClass('disabled').removeAttr('disabled');
        },

        actionNavigateToRoot: function (data, e) {
            e.stopPropagation();

            this.getRouter().checkConfirmLeaveOut(function () {
                var options = {
                    isReturn: true
                };
                var rootUrl = this.options.rootUrl || this.options.params.rootUrl || '#' + this.scope;
                this.getRouter().navigate(rootUrl, {trigger: false});
                this.getRouter().dispatch(this.scope, null, options);
            }, this);
        },

        hideHeaderActionItem: function (name) {
            ['actions', 'dropdown', 'buttons'].forEach(function (t) {
                this.menu[t].forEach(function (item, i) {
                    item = item || {};
                    if (item.name == name) {
                        item.hidden = true;
                    }
                }, this);
            }, this);
            if (!this.isRendered()) return;
            this.$el.find('.page-header li > .action[data-name="'+name+'"]').parent().addClass('hidden');
            this.$el.find('.page-header a.action[data-name="'+name+'"]').addClass('hidden');
        },

        showHeaderActionItem: function (name) {
            ['actions', 'dropdown', 'buttons'].forEach(function (t) {
                this.menu[t].forEach(function (item, i) {
                    item = item || {};
                    if (item.name == name) {
                        item.hidden = false;
                    }
                }, this);
            }, this);
            if (!this.isRendered()) return;
            this.$el.find('.page-header li > .action[data-name="'+name+'"]').parent().removeClass('hidden');
            this.$el.find('.page-header a.action[data-name="'+name+'"]').removeClass('hidden');
        }

    });
});
