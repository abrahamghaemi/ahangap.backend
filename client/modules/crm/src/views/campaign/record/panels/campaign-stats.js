


Espo.define('crm:views/campaign/record/panels/campaign-stats', 'views/record/panels/side', function (Dep) {

    return Dep.extend({

    	controlStatsFields: function () {
    		var type = this.model.get('type');
            var fieldList = [];
    		switch (type) {
    			case 'Email':
    			case 'Newsletter':
    				fieldList = ['sentCount', 'openedCount', 'clickedCount', 'optedOutCount', 'bouncedCount', 'leadCreatedCount', 'optedInCount', 'revenue'];
    				break;
    			case 'Web':
                    fieldList = ['leadCreatedCount', 'optedInCount', 'revenue'];
                    break;
    			case 'Television':
    			case 'Radio':
    				fieldList = ['leadCreatedCount', 'revenue'];
    				break;
    			case 'Mail':
    				fieldList = ['sentCount', 'leadCreatedCount', 'optedInCount', 'revenue'];
    				break;
    			default:
    				fieldList = ['leadCreatedCount', 'revenue'];
    		}

            if (!this.getConfig().get('massEmailOpenTracking')) {
                var i = fieldList.indexOf('openedCount')
                if (~i) fieldList.splice(i, 1);
            }

            this.statsFieldList.forEach(function (item) {
                this.options.recordViewObject.hideField(item);
            }, this);

            fieldList.forEach(function (item) {
                this.options.recordViewObject.showField(item);
            }, this);
    	},

    	setupFields: function () {
    		this.fieldList = ['sentCount', 'openedCount', 'clickedCount', 'optedOutCount', 'bouncedCount', 'leadCreatedCount', 'optedInCount', 'revenue'];
            this.statsFieldList = this.fieldList;
    	},

        setup: function () {
            Dep.prototype.setup.call(this);

            this.controlStatsFields();
            this.listenTo(this.model, 'change:type', function () {
                this.controlStatsFields();
            }, this);
        },

        actionRefresh: function () {
            this.model.fetch();
        }

    });
});
