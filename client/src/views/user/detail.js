

Espo.define('views/user/detail', 'views/detail', function (Dep) {

    return Dep.extend({

        setup: function () {
            Dep.prototype.setup.call(this);

            if (this.model.id == this.getUser().id || this.getUser().isAdmin()) {

                if (this.model.isRegular() || this.model.isAdmin() || this.model.isPortal()) {
                    this.addMenuItem('dropdown', {
                        name: 'preferences',
                        label: 'Preferences',
                        style: 'default',
                        action: "preferences",
                        link: '#Preferences/edit/' + this.getUser().id
                    });
                }

                if (this.model.isRegular() || this.model.isAdmin()) {
                    if ((this.getAcl().check('EmailAccountScope') && this.model.id == this.getUser().id) || this.getUser().isAdmin()) {
                        this.addMenuItem('dropdown', {
                            name: 'emailAccounts',
                            label: "Email Accounts",
                            style: 'default',
                            action: "emailAccounts",
                            link: '#EmailAccount/list/userId=' + this.model.id + '&userName=' + encodeURIComponent(this.model.get('name'))
                        });
                    }

                    if (this.model.id == this.getUser().id && this.getAcl().checkScope('ExternalAccount')) {
                        this.menu.buttons.push({
                            name: 'externalAccounts',
                            label: 'External Accounts',
                            style: 'default',
                            action: "externalAccounts",
                            link: '#ExternalAccount'
                        });
                    }
                }
            }

            if (this.getAcl().checkScope('Calendar') && (this.model.isRegular() || this.model.isAdmin())) {
                var showActivities = this.getAcl().checkUserPermission(this.model);
                if (!showActivities) {
                    if (this.getAcl().get('userPermission') === 'team') {
                        if (!this.model.has('teamsIds')) {
                            this.listenToOnce(this.model, 'sync', function () {
                                if (this.getAcl().checkUserPermission(this.model)) {
                                    this.showHeaderActionItem('calendar');
                                }
                            }, this);
                        }
                    }
                }
                this.menu.buttons.push({
                    name: 'calendar',
                    html: this.translate('Calendar', 'scopeNames'),
                    style: 'default',
                    link: '#Calendar/show/userId=' + this.model.id + '&userName=' + encodeURIComponent(this.model.get('name')),
                    hidden: !showActivities
                });
            }
        },

        actionPreferences: function () {
            this.getRouter().navigate('#Preferences/edit/' + this.model.id, {trigger: true});
        },

        actionEmailAccounts: function () {
            this.getRouter().navigate('#EmailAccount/list/userId=' + this.model.id + '&userName=' + encodeURIComponent(this.model.get('name')), {trigger: true});
        },

        actionExternalAccounts: function () {
            this.getRouter().navigate('#ExternalAccount', {trigger: true});
        },
    });
});
