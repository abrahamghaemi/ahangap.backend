

Espo.define('views/inbound-email/modals/select-folder', 'views/modal', function (Dep) {

    return Dep.extend({

        cssName: 'select-folder-modal',

        template: 'inbound-email/modals/select-folder',

        data: function () {
            return {
                folders: this.options.folders,
            };
        },

        events: {
            'click button[data-action="select"]': function (e) {
                var value = $(e.currentTarget).data('value');
                this.trigger('select', value);
            },
        },

        setup: function () {
            this.buttonList = [
                {
                    name: 'cancel',
                    label: 'Cancel',
                }
            ];

        },

    });
});

