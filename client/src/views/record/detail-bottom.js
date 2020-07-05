

define('views/record/detail-bottom', 'views/record/panels-container', function (Dep) {

    return Dep.extend({

        template: 'record/bottom',

        mode: 'detail',

        streamPanel: true,

        relationshipPanels: true,

        readOnly: false,

        portalLayoutDisabled: false,

        setupPanels: function () {
            var scope = this.scope;

            this.panelList = Espo.Utils.clone(this.getMetadata().get('clientDefs.' + scope + '.bottomPanels.' + this.type) || this.panelList || []);

            if (this.streamPanel && this.getMetadata().get('scopes.' + scope + '.stream')) {
                this.setupStreamPanel();
            }
        },

        setupStreamPanel: function () {
            var streamAllowed = this.getAcl().checkModel(this.model, 'stream', true);
            if (streamAllowed === null) {
                this.listenToOnce(this.model, 'sync', function () {
                    streamAllowed = this.getAcl().checkModel(this.model, 'stream', true);
                    if (streamAllowed) {
                        this.showPanel('stream', function () {
                            this.getView('stream').collection.fetch();
                            this.getView('stream').subscribeToWebSocket();
                        });
                    }
                }, this);
            }
            if (streamAllowed !== false) {
                this.panelList.push({
                    "name":"stream",
                    "label":"Stream",
                    "view":"views/stream/panel",
                    "sticked": true,
                    "hidden": !streamAllowed,
                    "order": 2
                });
            }
        },

        init: function () {
            this.recordHelper = this.options.recordHelper;
            this.scope = this.entityType = this.model.name;

            this.readOnlyLocked = this.options.readOnlyLocked || this.readOnly;
            this.readOnly = this.options.readOnly || this.readOnly;
            this.inlineEditDisabled = this.options.inlineEditDisabled || this.inlineEditDisabled;

            this.portalLayoutDisabled = this.options.portalLayoutDisabled || this.portalLayoutDisabled;

            this.recordViewObject = this.options.recordViewObject;
        },

        setup: function () {
            this.type = this.mode;
            if ('type' in this.options) {
                this.type = this.options.type;
            }

            this.panelList = [];

            this.setupPanels();

            this.wait(true);

            Promise.all([
                new Promise(function (resolve) {
                    if (this.relationshipPanels) {
                        this.loadRelationshipsLayout(function () {
                            resolve();
                        });
                    } else {
                        resolve();
                    }
                }.bind(this))
            ]).then(function () {
                this.panelList = this.panelList.filter(function (p) {
                    if (p.aclScope) {
                        if (!this.getAcl().checkScope(p.aclScope)) {
                            return;
                        }
                    }
                    if (p.accessDataList) {
                        if (!Espo.Utils.checkAccessDataList(p.accessDataList, this.getAcl(), this.getUser())) {
                            return false;
                        }
                    }
                    return true;
                }, this);

                if (this.relationshipPanels) {
                    this.setupRelationshipPanels();
                }

                if (this.recordViewObject && this.recordViewObject.dynamicLogic) {
                    var dynamicLogic = this.recordViewObject.dynamicLogic;
                    this.panelList.forEach(function (item) {
                        if (item.dynamicLogicVisible) {
                            dynamicLogic.addPanelVisibleCondition(item.name, item.dynamicLogicVisible);
                        }
                    }, this);
                }

                this.panelList = this.panelList.map(function (p) {
                    var item = Espo.Utils.clone(p);
                    if (this.recordHelper.getPanelStateParam(p.name, 'hidden') !== null) {
                        item.hidden = this.recordHelper.getPanelStateParam(p.name, 'hidden');
                    } else {
                        this.recordHelper.setPanelStateParam(p.name, item.hidden || false);
                    }
                    return item;
                }, this);

                this.panelList.sort(function(item1, item2) {
                    var order1 = item1.order || 0;
                    var order2 = item2.order || 0;
                    return order1 - order2;
                });

                this.panelList.forEach(function (item) {
                    item.actionsViewKey = item.name + 'Actions';
                }, this);

                this.setupPanelViews();
                this.wait(false);

            }.bind(this));
        },

        setReadOnly: function () {
            this.readOnly = true;
        },

        loadRelationshipsLayout: function (callback) {
            var layoutName = 'relationships';
            if (this.getUser().isPortal() && !this.portalLayoutDisabled) {
                if (this.getMetadata().get(['clientDefs', this.scope, 'additionalLayouts', layoutName + 'Portal'])) {
                    layoutName += 'Portal';
                }
            }
            this._helper.layoutManager.get(this.model.name, layoutName, function (layout) {
                this.relationshipsLayout = layout;
                callback.call(this);
            }.bind(this));
        },

        setupRelationshipPanels: function () {
            var scope = this.scope;

            var scopesDefs = this.getMetadata().get('scopes') || {};

            var panelList = this.relationshipsLayout;
            panelList.forEach(function (item) {
                var p;
                if (typeof item == 'string' || item instanceof String) {
                    p = {name: item};
                } else {
                    p = Espo.Utils.clone(item || {});
                }
                if (!p.name) {
                    return;
                }

                var name = p.name;

                var links = (this.model.defs || {}).links || {};
                if (!(name in links)) {
                    return;
                }

                var foreignScope = links[name].entity;

                if ((scopesDefs[foreignScope] || {}).disabled) return;

                if (!this.getAcl().check(foreignScope, 'read')) {
                    return;
                }

                var defs = this.getMetadata().get('clientDefs.' + scope + '.relationshipPanels.' + name) || {};
                defs = Espo.Utils.clone(defs);

                for (var i in defs) {
                    if (i in p) continue;
                    p[i] = defs[i];
                }

                if (!p.view) {
                    p.view = 'views/record/panels/relationship';
                }

                p.order = 5;

                if (this.recordHelper.getPanelStateParam(p.name, 'hidden') !== null) {
                    p.hidden = this.recordHelper.getPanelStateParam(p.name, 'hidden');
                } else {
                    this.recordHelper.setPanelStateParam(p.name, p.hidden || false);
                }

                this.panelList.push(p);
            }, this);
        },
    });
});
