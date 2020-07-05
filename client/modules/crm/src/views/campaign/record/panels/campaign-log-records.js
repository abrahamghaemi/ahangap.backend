


Espo.define('crm:views/campaign/record/panels/campaign-log-records', 'views/record/panels/relationship', function (Dep) {

    return Dep.extend({

    	filterList: ["all", "sent", "opened", "optedOut", "bounced", "clicked", "optedIn", "leadCreated"],

    	data: function () {
    		return _.extend({
    			filterList: this.filterList,
    			filterValue: this.filterValue
    		}, Dep.prototype.data.call(this));
    	},

    	setup: function () {
            if (this.getAcl().checkScope('TargetList', 'create')) {
                this.actionList.push({
                    action: 'createTargetList',
                    label: 'Create Target List'
                });
            }
            this.filterList = Espo.Utils.clone(this.filterList);

            if (!this.getConfig().get('massEmailOpenTracking')) {
                var i = this.filterList.indexOf('opened')
                if (~i) this.filterList.splice(i, 1);
            }

    		Dep.prototype.setup.call(this);
    	},

        actionCreateTargetList: function () {
            var attributes = {
                sourceCampaignId: this.model.id,
                sourceCampaignName: this.model.get('name')
            };

            if (!this.collection.data.primaryFilter) {
                attributes.includingActionList = [];
            } else {
                var status = Espo.Utils.upperCaseFirst(this.collection.data.primaryFilter).replace(/([A-Z])/g, ' $1');
                attributes.includingActionList = [status];
            }

            var viewName = this.getMetadata().get('clientDefs.TargetList.modalViews.edit') || 'views/modals/edit';
            this.createView('quickCreate', viewName, {
                scope: 'TargetList',
                attributes: attributes,
                fullFormDisabled: true,
                layoutName: 'createFromCampaignLog'
            }, function (view) {
                view.render();
                var recordView = view.getView('edit');
                if (recordView) {
                    recordView.setFieldRequired('includingActionList');
                }
                this.listenToOnce(view, 'after:save', function () {
                    Espo.Ui.success(this.translate('Done'));
                }, this);
            }, this);
        }

    });
});
