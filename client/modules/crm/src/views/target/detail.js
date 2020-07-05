

Espo.define('Crm:Views.Target.Detail', 'Views.Detail', function (Dep) {

    return Dep.extend({

        actionConvertToLead: function () {
            var id = this.model.id;
            var self = this;

            this.confirm(this.translate('confirmation', 'messages'), function () {
                self.notify('Please wait...');
                $.ajax({
                    url: 'Target/action/convert',
                    data: JSON.stringify({id: id}),
                    type: 'POST',
                    success: function (data) {
                        self.getRouter().navigate('#Lead/view/' + data.id, {trigger: true});
                        self.notify('Converted', 'success');
                    }
                });
            }, this);
        },

    });
});

