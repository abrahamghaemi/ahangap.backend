

Espo.define('crm:views/target-list/record/panels/relationship', 'views/record/panels/relationship', function (Dep) {

    return Dep.extend({

        fetchOnModelAfterRelate: true,

        actionOptOut: function (data) {
            this.confirm(this.translate('confirmation', 'messages'), function () {
                $.ajax({
                    url: 'TargetList/action/optOut',
                    type: 'POST',
                    data: JSON.stringify({
                        id: this.model.id,
                        targetId: data.id,
                        targetType: data.type
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
                        id: this.model.id,
                        targetId: data.id,
                        targetType: data.type
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
