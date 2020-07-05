

Espo.define('views/inbound-email/fields/folder', 'views/fields/base', function (Dep) {

    return Dep.extend({

        editTemplate: 'inbound-email/fields/folder/edit',

        events: {
            'click [data-action="selectFolder"]': function () {
                Espo.Ui.notify(this.translate('pleaseWait', 'messages'));

                var data = {
                    host: this.model.get('host'),
                    port: this.model.get('port'),
                    ssl: this.model.get('ssl'),
                    username: this.model.get('username'),
                };

                if (this.model.has('password')) {
                    data.password = this.model.get('password');
                } else {
                    if (!this.model.isNew()) {
                        data.id = this.model.id;
                    }
                }

                Espo.Ajax.postRequest('InboundEmail/action/getFolders', data).then(function (folders) {
                    this.createView('modal', 'views/inbound-email/modals/select-folder', {
                        folders: folders
                    }, function (view) {
                        this.notify(false);
                        view.render();

                        this.listenToOnce(view, 'select', function (folder) {
                            view.close();
                            this.addFolder(folder);
                        }, this);
                    });
                }.bind(this)).fail(function () {
                    Espo.Ui.error(this.translate('couldNotConnectToImap', 'messages', 'InboundEmail'));
                    xhr.errorIsHandled = true;
                }.bind(this));
            }
        },

        addFolder: function (folder) {
            this.$element.val(folder);
        },
    });
});
