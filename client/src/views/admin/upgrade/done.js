

Espo.define('views/admin/upgrade/done', 'views/modal', function (Dep) {

    return Dep.extend({

        cssName: 'done-modal',

        header: false,

        template: 'admin/upgrade/done',

        createButton: true,

        data: function () {
            return {
                version: this.options.version,
                text: this.translate('upgradeDone', 'messages', 'Admin').replace('{version}', this.options.version)
            };
        },

        setup: function () {
            this.on('remove', function () {
                window.location.reload();
            });

            this.buttonList = [
                {
                    name: 'close',
                    label: 'Close',
                    onClick: function (dialog) {
                        setTimeout(function () {
                            this.getRouter().navigate('#Admin', {trigger: true});
                        }.bind(this), 500);
                        dialog.close();
                    }.bind(this)
                }
            ];

            this.header = this.getLanguage().translate('Upgraded successfully', 'labels', 'Admin');

        },

    });
});

