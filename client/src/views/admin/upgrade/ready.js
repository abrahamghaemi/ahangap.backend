

Espo.define('views/admin/upgrade/ready', 'views/modal', function (Dep) {

    return Dep.extend({

        cssName: 'ready-modal',

        header: false,

        template: 'admin/upgrade/ready',

        createButton: true,

        data: function () {
            return {
                version: this.upgradeData.version,
                text: this.translate('upgradeVersion', 'messages', 'Admin').replace('{version}', this.upgradeData.version)
            };
        },

        setup: function () {
            this.buttonList = [
                {
                    name: 'run',
                    label: this.translate('Run Upgrade', 'labels', 'Admin'),
                    style: 'danger'
                },
                {
                    name: 'cancel',
                    label: 'Cancel'
                }
            ];

            this.upgradeData = this.options.upgradeData;

            this.header = this.getLanguage().translate('Ready for upgrade', 'labels', 'Admin');

        },

        actionRun: function () {
            this.trigger('run');
            this.remove();
        }
    });
});

