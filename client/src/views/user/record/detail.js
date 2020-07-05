

Espo.define('views/user/record/detail', 'views/record/detail', function (Dep) {

    return Dep.extend({

        sideView: 'views/user/record/detail-side',

        bottomView: 'views/user/record/detail-bottom',

        editModeDisabled: true,

        setup: function () {
            Dep.prototype.setup.call(this);

            this.setupNonAdminFieldsAccess();

            if (this.getUser().isAdmin()) {
                if (!this.model.isPortal()) {
                    this.addButton({
                        name: 'access',
                        label: 'Access',
                        style: 'default'
                    });
                }
            }

            if (
                this.model.id == this.getUser().id
                &&
                !this.model.isApi()
                &&
                (this.getUser().isAdmin() || !this.getHelper().getAppParam('passwordChangeForNonAdminDisabled'))
            ) {
                this.addDropdownItem({
                    name: 'changePassword',
                    label: 'Change Password',
                    style: 'default'
                });
            }

            if (this.model.isPortal() || this.model.isApi()) {
                this.hideActionItem('duplicate');
            }

            if (this.model.id == this.getUser().id) {
                this.listenTo(this.model, 'after:save', function () {
                    this.getUser().set(this.model.toJSON());
                }.bind(this));
            }

            this.setupFieldAppearance();
        },

        setupActionItems: function () {
            Dep.prototype.setupActionItems.call(this);

            if (this.model.isApi() && this.getUser().isAdmin()) {
                this.addDropdownItem({
                    'label': 'Generate New API Key',
                    'name': 'generateNewApiKey'
                });
            }
        },

        setupNonAdminFieldsAccess: function () {
            if (this.getUser().isAdmin()) return;

            var nonAdminReadOnlyFieldList = [
                'userName',
                'isActive',
                'teams',
                'roles',
                'password',
                'portals',
                'portalRoles',
                'contact',
                'accounts',
                'type'
            ];

            nonAdminReadOnlyFieldList.forEach(function (field) {
                this.setFieldReadOnly(field, true);
            }, this);

            if (!this.getAcl().checkScope('Team')) {
                this.setFieldReadOnly('defaultTeam', true);
            }
        },

        setupFieldAppearance: function () {

            this.controlFieldAppearance();
            this.listenTo(this.model, 'change', function () {
                this.controlFieldAppearance();
            }, this);
        },

        controlFieldAppearance: function () {
            if (this.model.get('type') === 'portal') {
                this.hideField('roles');
                this.hideField('teams');
                this.hideField('defaultTeam');
                this.showField('portals');
                this.showField('portalRoles');
                this.showField('contact');
                this.showField('accounts');
                this.showPanel('portal');
                this.hideField('title');
            } else {
                this.showField('roles');
                this.showField('teams');
                this.showField('defaultTeam');
                this.hideField('portals');
                this.hideField('portalRoles');
                this.hideField('contact');
                this.hideField('accounts');
                this.hidePanel('portal');

                if (this.model.get('type') === 'api') {
                    this.hideField('title');
                    this.hideField('emailAddress');
                    this.hideField('phoneNumber');
                    this.hideField('name');
                    this.hideField('gender');

                    if (this.model.get('authMethod') === 'Hmac') {
                        this.showField('secretKey');
                    } else {
                        this.hideField('secretKey');
                    }

                } else {
                    this.showField('title');
                }
            }

            if (this.model.id === this.getUser().id) {
                this.setFieldReadOnly('type');
            } else {
                if (this.model.get('type') == 'admin' || this.model.get('type') == 'regular') {
                    this.setFieldNotReadOnly('type');
                    this.setFieldOptionList('type', ['regular', 'admin']);
                } else {
                    this.setFieldReadOnly('type');
                }
            }
        },

        actionChangePassword: function () {
            this.notify('Loading...');

            this.createView('changePassword', 'views/modals/change-password', {
                userId: this.model.id
            }, function (view) {
                view.render();
                this.notify(false);

                this.listenToOnce(view, 'changed', function () {
                    setTimeout(function () {
                        this.getBaseController().logout();
                    }.bind(this), 2000);
                }, this);

            }.bind(this));
        },

        actionPreferences: function () {
            this.getRouter().navigate('#Preferences/edit/' + this.model.id, {trigger: true});
        },

        actionEmailAccounts: function () {
            this.getRouter().navigate('#EmailAccount/list/userId=' + this.model.id, {trigger: true});
        },

        actionExternalAccounts: function () {
            this.getRouter().navigate('#ExternalAccount', {trigger: true});
        },

        actionAccess: function () {
            this.notify('Loading...');

            $.ajax({
                url: 'User/action/acl',
                type: 'GET',
                data: {
                    id: this.model.id,
                }
            }).done(function (aclData) {
                this.createView('access', 'views/user/modals/access', {
                    aclData: aclData,
                    model: this.model,
                }, function (view) {
                    this.notify(false);
                    view.render();
                }.bind(this));
            }.bind(this));
        },

        getGridLayout: function (callback) {
            this._helper.layoutManager.get(this.model.name, this.options.layoutName || this.layoutName, function (simpleLayout) {
                var layout = Espo.Utils.cloneDeep(simpleLayout);

                if (!this.getUser().isPortal()) {
                    layout.push({
                        "label": "Teams and Access Control",
                        "name": "accessControl",
                        "rows": [
                            [{"name":"type"}, {"name":"isActive"}],
                            [{"name":"teams"}, {"name":"defaultTeam"}],
                            [{"name":"roles"}, false]
                        ]
                    });

                    if (this.model.isPortal()) {
                        layout.push({
                            "label": "Portal",
                            "name": "portal",
                            "rows": [
                                [{"name":"portals"}, {"name":"contact"}],
                                [{"name":"portalRoles"}, {"name":"accounts"}]
                            ]
                        });
                    }
                }

                if (this.getUser().isAdmin() && this.model.isApi()) {
                    layout.push({
                        "name": "auth",
                        "rows": [
                            [{"name":"authMethod"}, false],
                            [{"name":"apiKey"}, {"name":"secretKey"}],
                        ]
                    });
                }

                var gridLayout = {
                    type: 'record',
                    layout: this.convertDetailLayout(layout),
                };

                callback(gridLayout);
            }.bind(this));
        },

        actionGenerateNewApiKey: function () {
            this.confirm(this.translate('confirmation', 'messages'), function () {
                this.ajaxPostRequest('User/action/generateNewApiKey', {
                    id: this.model.id
                }).then(function (data) {
                    this.model.set(data);
                }.bind(this));
            }.bind(this));
        }

    });
});
