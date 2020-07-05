

Espo.define('crm:views/case/record/detail', 'views/record/detail', function (Dep) {

    return Dep.extend({

        selfAssignAction: true,

        setupActionItems: function () {
            Dep.prototype.setupActionItems.call(this);
            if (this.getAcl().checkModel(this.model, 'edit')) {
                if (['Closed', 'Rejected', 'Duplicate'].indexOf(this.model.get('status')) == -1) {
                    this.dropdownItemList.push({
                        'label': 'Close',
                        'name': 'close'
                    });
                    this.dropdownItemList.push({
                        'label': 'Reject',
                        'name': 'reject'
                    });
                }
            }
        },

        manageAccessEdit: function (second) {
            Dep.prototype.manageAccessEdit.call(this, second);

            if (second) {
                if (!this.getAcl().checkModel(this.model, 'edit', true)) {
                    this.hideActionItem('close');
                    this.hideActionItem('reject');
                }
            }
        },

        actionClose: function () {
            this.model.save({
                status: 'Closed'
            }, {
                patch: true,
                success: function () {
                    Espo.Ui.success(this.translate('Closed', 'labels', 'Case'));
                    this.removeButton('close');
                    this.removeButton('reject');
                }.bind(this),
            });
        },

        actionReject: function () {
            this.model.save({
                status: 'Rejected'
            }, {
                patch: true,
                success: function () {
                    Espo.Ui.success(this.translate('Rejected', 'labels', 'Case'));
                    this.removeButton('close');
                    this.removeButton('reject');
                }.bind(this),
            });
        },

        getSelfAssignAttributes: function () {
            if (this.model.get('status') === 'New') {
                if (~(this.getMetadata().get(['entityDefs', 'Case', 'fields', 'status', 'options']) || []).indexOf('Assigned')) {
                    return {
                        'status': 'Assigned'
                    };
                }
            }
        }

    });
});

