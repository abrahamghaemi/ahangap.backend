

Espo.define('crm:views/record/panels/target-lists', 'views/record/panels/relationship', function (Dep) {

    return Dep.extend({

        actionOptOut: function (data) {
            this.confirm(this.translate('confirmation', 'messages'), function () {
                $.ajax({
                    url: 'TargetList/action/optOut',
                    type: 'POST',
                    data: JSON.stringify({
                        id: data.id,
                        targetId: this.model.id,
                        targetType: this.model.name
                    })
                }).done(function () {
                    this.collection.fetch();
                    Espo.Ui.success(this.translate('Done'));
                    this.model.trigger('opt-out');
                }.bind(this));
            }, this);
        },

        actionCancelOptOut: function (data) {
            this.confirm(this.translate('confirmation', 'messages'), function () {
                $.ajax({
                    url: 'TargetList/action/cancelOptOut',
                    type: 'POST',
                    data: JSON.stringify({
                        id: data.id,
                        targetId: this.model.id,
                        targetType: this.model.name
                    })
                }).done(function () {
                    this.collection.fetch();
                    Espo.Ui.success(this.translate('Done'));
                    this.collection.fetch();
                    this.model.trigger('cancel-opt-out');
                }.bind(this));
            }, this);
        }

    });
});
