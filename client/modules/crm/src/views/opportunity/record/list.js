

Espo.define('crm:views/opportunity/record/list', 'views/record/list', function (Dep) {

    return Dep.extend({

        setupMassActionItems: function () {
            Dep.prototype.setupMassActionItems.call(this);

            if (this.getConfig().get('currencyList').length > 1) {
                if (
                    this.getAcl().checkScope(this.scope, 'edit')
                    &&
                    !~this.getAcl().getScopeForbiddenFieldList(this.scope, 'edit').indexOf('amount')
                ) {
                    this.addMassAction('convertCurrency', true);
                }
            }
        },

        massActionConvertCurrency: function () {
            var ids = false;
            var allResultIsChecked = this.allResultIsChecked;
            if (!allResultIsChecked) {
                ids = this.checkedList;
            }

            this.createView('modalConvertCurrency', 'views/modals/mass-convert-currency', {
                entityType: this.scope,
                field: 'amount',
                ids: ids,
                where: this.collection.getWhere(),
                selectData: this.collection.data,
                byWhere: this.allResultIsChecked
            }, function (view) {
                view.render();
                this.listenToOnce(view, 'after:update', function (count) {
                    this.listenToOnce(this.collection, 'sync', function () {
                        if (count) {
                            var msg = 'massUpdateResult';
                            if (count == 1) {
                                msg = 'massUpdateResultSingle'
                            }
                            Espo.Ui.success(this.translate(msg, 'messages').replace('{count}', count));
                        } else {
                            Espo.Ui.warning(this.translate('noRecordsUpdated', 'messages'));
                        }
                        if (allResultIsChecked) {
                            this.selectAllResult();
                        } else {
                            ids.forEach(function (id) {
                                this.checkRecord(id);
                            }, this);
                        }
                    }, this);
                    this.collection.fetch();
                }, this);
            });
        }

    });
});
