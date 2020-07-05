

define('views/record/panels-container', 'view', function (Dep) {

    return Dep.extend({

        data: function () {
            return {
                panelList: this.panelList,
                scope: this.scope,
                entityType: this.entityType
            };
        },

        events: {
            'click .action': function (e) {
                var $target = $(e.currentTarget);
                var action = $target.data('action');
                var panel = $target.data('panel');
                var data = $target.data();
                if (action) {
                    var method = 'action' + Espo.Utils.upperCaseFirst(action);
                    var d = _.clone(data);
                    delete d['action'];
                    delete d['panel'];
                    var view = this.getView(panel);
                    if (view && typeof view[method] == 'function') {
                        view[method].call(view, d, e);
                    }
                }
            }
        },

        setReadOnly: function () {
            this.readOnly = true;
        },

        setNotReadOnly: function (onlyNotSetAsReadOnly) {
            this.readOnly = false;

            if (onlyNotSetAsReadOnly) {
                this.panelList.forEach(function (item) {
                    this.applyAccessToActions(item.buttonList);
                    this.applyAccessToActions(item.actionList);

                    if (this.isRendered()) {
                        var actionsView = this.getView(item.actionsViewKey);
                        if (actionsView) {
                            actionsView.reRender();
                        }
                    }
                }, this);
            }
        },

        applyAccessToActions: function (actionList) {
            if (!actionList) return;
            actionList.forEach(function (item) {
                if (Espo.Utils.checkActionAccess(this.getAcl(), this.model, item, true)) {
                    if (item.isHiddenByAcl) {
                        item.isHiddenByAcl = false;
                        item.hidden = false;
                    }
                } else {
                    if (!item.hidden) {
                        item.isHiddenByAcl = true;
                        item.hidden = true;
                    }
                }
            }, this);
        },

        setupPanelViews: function () {
            this.panelList.forEach(function (p) {

                var name = p.name;
                var options = {
                    model: this.model,
                    panelName: name,
                    el: this.options.el + ' .panel[data-name="' + name + '"] > .panel-body',
                    defs: p,
                    mode: this.mode,
                    recordHelper: this.recordHelper,
                    inlineEditDisabled: this.inlineEditDisabled,
                    readOnly: this.readOnly,
                    disabled: p.hidden || false,
                    recordViewObject: this.recordViewObject
                };
                options = _.extend(options, p.options);
                this.createView(name, p.view, options, function (view) {
                    if ('getActionList' in view) {
                        p.actionList = view.getActionList();
                        this.applyAccessToActions(p.actionList);
                    }
                    if ('getButtonList' in view) {
                        p.buttonList = view.getButtonList();
                        this.applyAccessToActions(p.buttonList);
                    }

                    if (view.titleHtml) {
                        p.titleHtml = view.titleHtml;
                    } else {
                        if (p.label) {
                            p.title = this.translate(p.label, 'labels', this.scope);
                        } else {
                            p.title = view.title;
                        }
                    }

                    this.createView(name + 'Actions', 'views/record/panel-actions', {
                        el: this.getSelector() + '.panel[data-name="'+p.name+'"] > .panel-heading > .panel-actions-container',
                        model: this.model,
                        defs: p,
                        scope: this.scope,
                        entityType: this.entityType
                    });
                }, this);
            }, this);
        },

        setupPanels: function () {},

        getFieldViews: function (withHidden) {
            var fields = {};
            this.panelList.forEach(function (p) {
                var panelView = this.getView(p.name);
                if ((!panelView.disabled || withHidden) && 'getFieldViews' in panelView) {
                    fields = _.extend(fields, panelView.getFieldViews());
                }
            }, this);
            return fields;
        },

        getFields: function () {
            return this.getFieldViews();
        },

        fetch: function () {
            var data = {};

            this.panelList.forEach(function (p) {
                var panelView = this.getView(p.name);
                if (!panelView.disabled && 'fetch' in panelView) {
                    data = _.extend(data, panelView.fetch());
                }
            }, this);
            return data;
        },

        showPanel: function (name, callback) {
            this.recordHelper.setPanelStateParam(name, 'hidden', false);

            var isFound = false;
            this.panelList.forEach(function (d) {
                if (d.name == name) {
                    d.hidden = false;
                    isFound = true;
                }
            }, this);
            if (!isFound) return;

            if (this.isRendered()) {
                var view = this.getView(name);
                if (view) {
                    view.$el.closest('.panel').removeClass('hidden');
                    view.disabled = false;
                    view.trigger('show');
                }
                if (callback) {
                    callback.call(this);
                }
            } else {
                if (callback) {
                    this.once('after:render', function () {
                        callback.call(this);
                    }, this);
                }
            }
        },

        hidePanel: function (name, callback) {
            this.recordHelper.setPanelStateParam(name, 'hidden', true);

            var isFound = false;
            this.panelList.forEach(function (d) {
                if (d.name == name) {
                    d.hidden = true;
                    isFound = true;
                }
            }, this);
            if (!isFound) return;

            if (this.isRendered()) {
                var view = this.getView(name);
                if (view) {
                    view.$el.closest('.panel').addClass('hidden');
                    view.disabled = true;
                    view.trigger('hide');
                }
                if (callback) {
                    callback.call(this);
                }
            } else {
                if (callback) {
                    this.once('after:render', function () {
                        callback.call(this);
                    }, this);
                }
            }
        }

    });
});
