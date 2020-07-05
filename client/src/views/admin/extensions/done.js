

Espo.define('views/admin/extensions/done', 'views/modal', function (Dep) {

    return Dep.extend({

        cssName: 'done-modal',

        header: false,

        template: 'admin/extensions/done',

        createButton: true,

        data: function () {
            return {
                version: this.options.version,
                name: this.options.name,
                text: this.translate('extensionInstalled', 'messages', 'Admin').replace('{version}', this.options.version)
                                                                               .replace('{name}', this.options.name)
            };
        },

        setup: function () {
            this.on('remove', function () {
                window.location.reload();
            });

            this.buttonList = [
                {
                    name: 'close',
                    label: 'Close'
                }
            ];

            this.header = this.getLanguage().translate('Installed successfully', 'labels', 'Admin');
        }

    });
});

