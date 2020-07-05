

define('crm:views/task/modals/detail', 'views/modals/detail', function (Dep) {

    return Dep.extend({

        setupRecordButtons: function () {
            this.addButton({
                name: 'setCompleted',
                label: 'Complete',
            }, true);


            Dep.prototype.setupRecordButtons.call(this);
        },

        controlRecordButtonsVisibility: function () {
            if (
                !~['Completed', 'Canceled'].indexOf(this.model.get('status'))
                &&
                this.getAcl().check(this.model, 'edit')
            ) {
                this.showButton('setCompleted');
            } else {
                this.hideButton('setCompleted');
            }

            Dep.prototype.controlRecordButtonsVisibility.call(this);
        },

        actionSetCompleted: function () {
            this.model.save({
                status: 'Completed'
            }, {
                patch: true,
                success: function () {
                    this.hideButton('setCompleted');
                    Espo.Ui.success(this.getLanguage().translateOption('Completed', 'status', 'Task'));
                    this.trigger('after:save', this.model);
                }.bind(this),
            });
        }
    });
});
